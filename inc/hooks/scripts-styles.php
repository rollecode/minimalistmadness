<?php
/**
 * Enqueue and localize theme scripts and styles
 *
 * @Author: Roni Laukkarinen
 * @Date: 2020-02-20 13:46:50
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-02-09 11:04:24
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * Fetch a remote /wp-json/words/v1/getposts feed with transient caching.
 *
 * Returns an array of post records (possibly empty). Successful responses are
 * cached for 24h; failed/non-200 responses are cached as empty arrays for 15
 * minutes so a transiently-broken upstream does not get polled on every render.
 */
function fetch_external_words( $url, $cache_key ) {
	$cached = get_transient( $cache_key );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : array();
	}

	$response = wp_remote_get( $url, array( 'timeout' => 5 ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $cache_key, array(), 15 * MINUTE_IN_SECONDS );
		return array();
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		set_transient( $cache_key, array(), 15 * MINUTE_IN_SECONDS );
		return array();
	}

	set_transient( $cache_key, $data, DAY_IN_SECONDS );
	return $data;
}

/**
 * Heatmap stuff
 */
function heatmap_data() {
  global $post;

  // Start local array settings
  $heatmap_args = array(
    'post_type' => 'any',
    'posts_per_page' => 380, // phpcs:ignore
    'no_found_rows' => true,
    'post_status' => 'publish',
  );

  $heatmap_query = get_posts( $heatmap_args );

  // Fetch remote /wp-json/words/v1/getposts feeds and cache them. The same
  // transient key is used for both get_transient() and set_transient() — an
  // earlier version used mismatched keys, which silently disabled the cache
  // and caused every page render to re-hit the upstream endpoints (DEV-1050).
  // Failed fetches are cached as empty arrays for a short window so a single
  // bad upstream does not turn into a per-render polling loop.
  $rollekino_query   = fetch_external_words( 'https://www.rollekino.fi/wp-json/words/v1/getposts', 'rollekino_words_response' );
  $dude_query        = fetch_external_words( 'https://www.dude.fi/wp-json/words/v1/getposts', 'dude_words_response' );
  $rolledesign_query = fetch_external_words( 'https://rolle.design/wp-json/words/v1/getposts', 'rolledesign_words_response' );

  if ( is_array( $heatmap_query ) && is_array( $rollekino_query ) && is_array( $dude_query ) && is_array( $rolledesign_query ) ) {
    $merged = array_merge( $heatmap_query, $rollekino_query, $dude_query, $rolledesign_query );
  } else {
    $merged = $heatmap_query;
  }

  // $heatmap_post_array = array();
  foreach ( $merged as $key => $heatmap_post ) {
		setup_postdata( $heatmap_post );

		// Word count
    if ( is_object( $heatmap_post ) && isset( $heatmap_post->ID ) ) {
      $post_id = $heatmap_post->ID;
      $post_object = get_post( $post_id );
      $content = $post_object->post_content;
      $word_count = post_word_count( $content );
    } elseif ( is_array( $heatmap_post ) && isset( $heatmap_post['post_content'] ) ) {
      $word_count = post_word_count( $heatmap_post['post_content'] );
    } else {
      continue;
    }

    // Timestamps
    if ( is_object( $heatmap_post ) && isset( $heatmap_post->ID ) ) {
      $unix_timestamp = get_post_timestamp( $heatmap_post );
      $day = get_the_time( 'Y-m-d', $post_id );
      $day_in_unix_format = strtotime( get_the_time( 'Y-m-d', $post_id ) );
    } elseif ( is_array( $heatmap_post ) && isset( $heatmap_post['post_date_gmt'] ) ) {
      $unix_timestamp = strtotime( $heatmap_post['post_date_gmt'] );
      $day = gmdate( 'Y-m-d', strtotime( $heatmap_post['post_date_gmt'] ) );
      $day_in_unix_format = strtotime( gmdate( 'Y-m-d', strtotime( $heatmap_post['post_date_gmt'] ) ) );
    } else {
      continue;
    }

    // If same day has multiple posts, combine word counts and show total count for one day
    if ( isset( $heatmap_post_array[ $day_in_unix_format ] ) ) {
      $heatmap_post_array[ $day_in_unix_format ] = $heatmap_post_array[ $day_in_unix_format ] + $word_count;
    } else {
      $heatmap_post_array[ $day_in_unix_format ] = $word_count;
    }
  }

  // Rollemaa data
  return $heatmap_post_array;
}

/**
 * Vue feed queries for Swup.
 */
function paged_query_for_swup() {
  global $post;

  // Posts to exclude from the front-page feed (formerly an ACF options field,
  // now a plain option; empty by default).
  $selected_posts = (array) get_option( 'rollemaa_selected_posts', [] );
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => 6, // NB! When you change this, change also posts_per_page option
    'cache_results' => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,
    'no_found_rows' => true,
    'post_status' => 'publish',
    'post__not_in' => $selected_posts,
  );

  $query = new \WP_Query( $args );

  // Check if we should event show load more button
  // in the first place and save query to js variable for later use.
  if ( $query->found_posts !== $query->post_count ) {
    $query->query['paged'] = 1;
    $posts_query_original = $query->query; // phpcs:ignore
    $posts_query = $query->query; // phpcs:ignore

    $queries = array(
      'posts_query_original' => $posts_query_original,
      'posts_query' => $posts_query,
    );

    return $queries;
  }
}

/**
 * Enqueue scripts and styles.
 */
function enqueue_theme_scripts() {
  // Disable jQuery (included in all.js and normally on wp-admin)
  if ( ! is_admin() ) wp_deregister_script( 'jquery' );
  if ( ! is_admin() ) wp_deregister_script( 'jquery-core' );
  if ( ! is_admin() ) wp_deregister_script( 'jquery-migrate' );

  // Enqueue global.css (site shell + front page + archives, every page)
  wp_enqueue_style( 'styles',
    get_theme_file_uri( get_asset_file( 'global.css' ) ),
    [],
    filemtime( get_theme_file_path( get_asset_file( 'global.css' ) ) )
  );

  // content.css: full article body (post/page/diary single layout, comments,
  // code highlighting, Gutenberg block content). Front page and archives show
  // plain excerpts, so they never load it. The front page is a static page
  // here (show_on_front=page) but front-page.php renders excerpts, not the
  // page body, so it is excluded too.
  if ( is_singular() && ! is_front_page() ) {
    wp_enqueue_style( 'styles-content',
      get_theme_file_uri( get_asset_file( 'content.css' ) ),
      [ 'styles' ],
      filemtime( get_theme_file_path( get_asset_file( 'content.css' ) ) )
    );
  }

  // diary.css: the (large) diary/lokikirja view. Loaded on diary contexts and
  // on no-results pages, where template-parts/content-none.php reuses the
  // diary card markup.
  $no_results = ! is_singular() && isset( $GLOBALS['wp_query'] ) && 0 === (int) $GLOBALS['wp_query']->found_posts;

  if ( is_singular( 'diary' ) || is_post_type_archive( 'diary' ) || is_404() || $no_results ) {
    wp_enqueue_style( 'styles-diary',
      get_theme_file_uri( get_asset_file( 'diary.css' ) ),
      [ 'styles' ],
      filemtime( get_theme_file_path( get_asset_file( 'diary.css' ) ) )
    );
  }

  // Enqueue jquery and front-end.js
  wp_enqueue_script( 'jquery-core' );
  wp_enqueue_script( 'scripts',
    get_theme_file_uri( get_asset_file( 'front-end.js' ) ),
    [],
    filemtime( get_theme_file_path( get_asset_file( 'front-end.js' ) ) ),
    true
  );

  // Required comment-reply script
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
  }

  wp_localize_script( 'scripts', 'minimalistmadness_screenReaderText', array(
    'expand'   => esc_html__( 'Open child menu', 'minimalistmadness' ),
    'collapse' => esc_html__( 'Close child menu', 'minimalistmadness' ),
  ) );

  wp_localize_script( 'scripts', 'heatmapdata', heatmap_data() );
  wp_localize_script( 'scripts', 'paged_query', paged_query_for_swup() );

  wp_localize_script( 'scripts', 'minimalistmadness_screenReaderText', [
    'expand'          => get_default_localization( 'Open child menu' ),
    'collapse'        => get_default_localization( 'Close child menu' ),
    'expand_for'      => get_default_localization( 'Open child menu for' ),
    'collapse_for'    => get_default_localization( 'Close child menu for' ),
    'expand_toggle'   => get_default_localization( 'Open main menu' ),
    'collapse_toggle' => get_default_localization( 'Close main menu' ),
    'external_link'   => get_default_localization( 'External site:' ),
    'target_blank'    => get_default_localization( 'opens in a new window' ),
  ] );

  // Add domains/hosts to disable external link indicators
  wp_localize_script( 'scripts', 'minimalistmadness_externalLinkDomains', [
      'localhost:3000',
      'rollemaa.test',
      'rollemaa.fi',
      'www.rollemaa.fi',
      'rollemaa.org',
      'www.rollemaa.org',
  ] );

  wp_localize_script( 'scripts', 'air', array(
    'nonce'           => wp_create_nonce( 'wp_rest' ),
    'posts_per_page'  => 6,
    'baseurl'         => get_rest_url(),
  ) );

  wp_localize_script( 'scripts', 'dmrp', array(
    'id'              => get_the_id(),
    'nonce'           => wp_create_nonce( 'dmrp' . get_the_id() ),
    'ajax_url'        => admin_url( 'admin-ajax.php' ),
    'cookie_timeout'  => apply_filters( 'dmrp_cookie_timeout', 3600000 ),
  ) );

  // Remove dude-most-read-posts script (included in scripts.js for optimization)
  wp_dequeue_script( 'dmrp' );

}

/**
 * Returns the built asset filename and path depending on
 * current environment.
 *
 * @param string $filename File name with the extension
 * @return string file and path of the asset file
 */
function get_asset_file( $filename ) {

  $env = 'development' === wp_get_environment_type() && ! isset( $_GET['load_production_builds'] ) ? 'dev' : 'prod'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

  $filetype = pathinfo( $filename )['extension'];

  return "${filetype}/${env}/${filename}";
} // end get_asset_file

