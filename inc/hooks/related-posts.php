<?php
/**
 * Related posts under an article: the articles sharing the most tags,
 * newest first on ties. Cached per post for a week.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

const RELATED_POSTS_COUNT = 4;

function related_post_ids( $post_id ) {
  global $wpdb;

  $cache_key = 'related_posts_' . $post_id;
  $ids       = get_transient( $cache_key );
  if ( false !== $ids ) {
    return $ids;
  }

  $tag_ids = wp_get_post_tags( $post_id, [ 'fields' => 'tt_ids' ] );
  $ids     = [];

  if ( $tag_ids ) {
    $placeholders = implode( ',', array_fill( 0, count( $tag_ids ), '%d' ) );
    $ids          = $wpdb->get_col( $wpdb->prepare( // phpcs:ignore
      "SELECT tr.object_id FROM {$wpdb->term_relationships} tr
      JOIN {$wpdb->posts} p ON p.ID = tr.object_id
      WHERE tr.term_taxonomy_id IN ($placeholders) AND tr.object_id != %d AND p.post_type = 'post' AND p.post_status = 'publish'
      GROUP BY tr.object_id ORDER BY COUNT(*) DESC, p.post_date DESC LIMIT %d",
      array_merge( $tag_ids, [ $post_id, RELATED_POSTS_COUNT ] )
    ) );
  }

  $ids = array_map( 'intval', $ids );
  set_transient( $cache_key, $ids, WEEK_IN_SECONDS );

  return $ids;
}

function the_related_posts() {
  $related_posts = related_post_ids( get_the_ID() );

  include get_theme_file_path( 'template-parts/related-posts.php' );
}
