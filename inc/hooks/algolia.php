<?php
/**
 * Algolia search: records for articles, diary entries and pages, kept in
 * sync on save and rebuilt with `wp rollemaa-algolia reindex`. The admin key
 * comes from ALGOLIA_ADMIN_KEY in .env; the app ID and search-only key are
 * public by design and go to the browser.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

const ALGOLIA_APP_ID       = 'T5LUJPIHVH';
const ALGOLIA_SEARCH_KEY   = 'b54edbc9a6779fe3bf83d7e2f33aeae1';
const ALGOLIA_INDEX        = 'rollemaa';
const ALGOLIA_POST_TYPES   = [ 'post', 'diary', 'page' ];
const ALGOLIA_CHUNK_BYTES  = 6000; // Free plan caps a record at 10 KB; JSON-escaped ä/ö grow it
const ALGOLIA_BATCH_SIZE   = 500;

function algolia_request( $method, $path, $body = null ) {
  $key = getenv( 'ALGOLIA_ADMIN_KEY' );
  if ( ! $key ) {
    return new \WP_Error( 'algolia_no_key', 'ALGOLIA_ADMIN_KEY missing from .env' );
  }

  $response = wp_remote_request( 'https://' . ALGOLIA_APP_ID . '.algolia.net/1/indexes/' . ALGOLIA_INDEX . $path, [
    'method'  => $method,
    'timeout' => 30,
    'headers' => [
      'X-Algolia-Application-Id' => ALGOLIA_APP_ID,
      'X-Algolia-API-Key'        => $key,
      'Content-Type'             => 'application/json',
    ],
    'body'    => null === $body ? null : wp_json_encode( $body ),
  ] );

  if ( is_wp_error( $response ) ) {
    return $response;
  }

  $code = wp_remote_retrieve_response_code( $response );
  if ( $code >= 300 ) {
    return new \WP_Error( 'algolia_http_' . $code, wp_remote_retrieve_body( $response ) );
  }

  return json_decode( wp_remote_retrieve_body( $response ), true );
}

function algolia_settings() {
  return [
    'searchableAttributes'  => [ 'unordered(title)', 'unordered(content)' ],
    'attributesForFaceting' => [ 'filterOnly(post_id)', 'post_type' ],
    'attributeForDistinct'  => 'post_id',
    'distinct'              => 1,
    'customRanking'         => [ 'desc(date)' ],
    'attributesToRetrieve'  => [ 'title', 'url', 'type_label', 'date_readable' ],
    'attributesToHighlight' => [ 'title' ],
    'attributesToSnippet'   => [ 'content:30' ],
    'highlightPreTag'       => '<mark>',
    'highlightPostTag'      => '</mark>',
    'snippetEllipsisText'   => '…',
    'indexLanguages'        => [ 'fi' ],
    'queryLanguages'        => [ 'fi' ],
    'hitsPerPage'           => 15,
  ];
}

function algolia_type_label( $post_type ) {
  return [
    'post'  => 'Artikkeli',
    'diary' => 'Lokikirja',
    'page'  => 'Sivu',
  ][ $post_type ] ?? '';
}

/**
 * Plain text split on word boundaries into chunks that fit a record.
 */
function algolia_chunks( $text ) {
  $chunks  = [];
  $current = '';

  foreach ( preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY ) as $word ) {
    if ( strlen( $current ) + strlen( $word ) + 1 > ALGOLIA_CHUNK_BYTES ) {
      $chunks[] = $current;
      $current  = '';
    }
    $current .= ( '' === $current ? '' : ' ' ) . $word;
  }

  if ( '' !== $current || empty( $chunks ) ) {
    $chunks[] = $current;
  }

  return $chunks;
}

function algolia_records( \WP_Post $post ) {
  $text = html_entity_decode( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), ENT_QUOTES, 'UTF-8' );
  $base = [
    'post_id'       => $post->ID,
    'post_type'     => $post->post_type,
    'type_label'    => algolia_type_label( $post->post_type ),
    'title'         => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
    'url'           => get_permalink( $post ),
    'date'          => (int) get_post_time( 'U', true, $post ),
    'date_readable' => 'Kirjoitettu ' . get_the_time( 'l', $post ) . 'na, ' . get_the_time( 'j.', $post ) . ' ' . get_the_time( 'F', $post ) . 'ta ' . get_the_time( 'Y', $post ),
  ];

  $records = [];
  foreach ( algolia_chunks( $text ) as $i => $chunk ) {
    $records[] = $base + [
      'objectID' => $post->ID . '-' . $i,
      'content'  => $chunk,
    ];
  }

  return $records;
}

function algolia_is_indexable( \WP_Post $post ) {
  return in_array( $post->post_type, ALGOLIA_POST_TYPES, true ) && 'publish' === $post->post_status && '' === $post->post_password;
}

function algolia_delete_post( $post_id ) {
  return algolia_request( 'POST', '/deleteByQuery', [ 'params' => 'filters=post_id:' . (int) $post_id ] );
}

function algolia_batch( $records ) {
  $requests = array_map( function ( $record ) {
    return [ 'action' => 'updateObject', 'body' => $record ];
  }, $records );

  return algolia_request( 'POST', '/batch', [ 'requests' => $requests ] );
}

/**
 * Keep the index in sync: replace a post's records when it is saved as
 * published, drop them otherwise. Runs after meta and terms are saved.
 */
function algolia_sync_post( $post_id, $post ) {
  if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! in_array( $post->post_type, ALGOLIA_POST_TYPES, true ) ) {
    return;
  }

  algolia_delete_post( $post_id );

  if ( algolia_is_indexable( $post ) ) {
    algolia_batch( algolia_records( $post ) );
  }
}

function algolia_sync_deleted( $post_id, $post ) {
  if ( in_array( $post->post_type, ALGOLIA_POST_TYPES, true ) ) {
    algolia_delete_post( $post_id );
  }
}

/**
 * `wp rollemaa-algolia reindex`: push settings, clear and rebuild everything.
 */
function algolia_reindex() {
  $settings = algolia_request( 'PUT', '/settings', algolia_settings() );
  if ( is_wp_error( $settings ) ) {
    \WP_CLI::error( $settings->get_error_message() );
  }

  algolia_request( 'POST', '/clear' );

  $ids     = get_posts( [ 'post_type' => ALGOLIA_POST_TYPES, 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => -1, 'fields' => 'ids' ] );
  $pending = [];
  $total   = 0;

  foreach ( $ids as $id ) {
    $pending = array_merge( $pending, algolia_records( get_post( $id ) ) );

    if ( count( $pending ) >= ALGOLIA_BATCH_SIZE ) {
      $total  += count( $pending );
      $result  = algolia_batch( $pending );
      $pending = [];
      if ( is_wp_error( $result ) ) {
        \WP_CLI::error( $result->get_error_message() );
      }
    }
  }

  if ( $pending ) {
    $total += count( $pending );
    algolia_batch( $pending );
  }

  \WP_CLI::success( sprintf( 'Indexed %d posts as %d records.', count( $ids ), $total ) );
}
