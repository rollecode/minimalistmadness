<?php
/**
 * Most read posts: daily read counts per post, kept in the table the old
 * Dude Most Read Posts plugin created so the history since 2017 stays.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

const MOST_READ_CACHE_TTL = HOUR_IN_SECONDS;

function most_read_table() {
  global $wpdb;

  return $wpdb->prefix . 'dude_most_read_posts';
}

function register_most_read_api() {
  register_rest_route( 'rollemaa/v1', '/read/(?P<id>\d+)', [
    'methods'             => 'POST',
    'callback'            => __NAMESPACE__ . '\count_read',
    'permission_callback' => '__return_true',
  ] );
}

function count_read( \WP_REST_Request $request ) {
  global $wpdb;

  $id = (int) $request['id'];
  if ( 'post' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
    return new \WP_REST_Response( null, 404 );
  }

  $table = most_read_table();
  $today = gmdate( 'Y-m-d' );

  $updated = $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET count = count + 1 WHERE post_id = %d AND time = %s", $id, $today ) ); // phpcs:ignore
  if ( ! $updated ) {
    $wpdb->insert( $table, [ 'post_id' => $id, 'time' => $today, 'count' => 1 ] );
  }

  return new \WP_REST_Response( null, 204 );
}

/**
 * Read counts keyed by post ID, most read first. Period: week or alltime.
 */
function most_read_counts( $period ) {
  global $wpdb;

  $cache_key = 'most_read_' . $period;
  $counts    = get_transient( $cache_key );
  if ( false !== $counts ) {
    return $counts;
  }

  $start  = 'week' === $period ? gmdate( 'Y-m-d', strtotime( '-1 week' ) ) : '1970-01-01';
  $table  = most_read_table();
  $rows   = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, SUM(count) AS total FROM {$table} WHERE time >= %s GROUP BY post_id ORDER BY total DESC LIMIT 100", $start ) ); // phpcs:ignore
  $counts = wp_list_pluck( $rows, 'total', 'post_id' );

  set_transient( $cache_key, $counts, MOST_READ_CACHE_TTL );

  return $counts;
}

function most_read_query( $period, $per_page ) {
  $ids = array_keys( most_read_counts( $period ) );
  if ( empty( $ids ) ) {
    return null;
  }

  return new \WP_Query( [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'post__in'            => $ids,
    'orderby'             => 'post__in',
    'posts_per_page'      => $per_page,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
  ] );
}

function most_read_count( $post_id, $period ) {
  return most_read_counts( $period )[ $post_id ] ?? 0;
}
