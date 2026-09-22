<?php
/**
 * Diary (lokikirja) REST API for the rollemaa MCP server.
 *
 * Reading goes through core /wp/v2. These routes cover what core cannot:
 * the quick post defaults (day number, weather, now playing, location) and
 * creating an entry exactly as quick-post.php does, through the same helpers
 * in inc/diary-entry.php.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

function register_diary_api() {
  $can_edit = function () {
    return current_user_can( 'edit_posts' );
  };

  $choices = diary_meta_choices();
  $text    = [ 'type' => 'string' ];
  $scale   = [
    'type'    => 'integer',
    'minimum' => 0,
    'maximum' => 100,
  ];

  $fields = [
    'title'              => $text,
    'content'            => $text,
    'date'               => $text,
    'status'             => [
      'type' => 'string',
      'enum' => [ 'publish', 'draft' ],
    ],
    'habits_percent'     => $scale,
    'energy_scale'       => $scale,
    'anxiety_scale'      => $scale,
    'mood_scale'         => $scale,
    'productivity_scale' => $scale,
    'gratitude'          => $text,
    'highlight'          => $text,
    'location'           => $text,
    'temperature'        => $text,
    'weather_text'       => $text,
    'np'                 => $text,
    'np_link'            => $text,
    'drink_text'         => $text,
  ];

  foreach ( [ 'mood', 'device', 'weather_icon' ] as $field ) {
    $fields[ $field ] = [
      'type' => 'string',
      'enum' => array_keys( $choices[ $field ] ),
    ];
  }

  register_rest_route( 'rollemaa/v1', '/diary/defaults', [
    'methods'             => 'GET',
    'callback'            => __NAMESPACE__ . '\diary_api_defaults',
    'permission_callback' => $can_edit,
    'args'                => [ 'date' => $text ],
  ] );

  register_rest_route( 'rollemaa/v1', '/diary', [
    'methods'             => 'POST',
    'callback'            => __NAMESPACE__ . '\diary_api_create',
    'permission_callback' => $can_edit,
    'args'                => array_merge( $fields, [
      'content' => [
        'type'     => 'string',
        'required' => true,
      ],
    ] ),
  ] );

  register_rest_route( 'rollemaa/v1', '/diary/(?P<id>\d+)', [
    'methods'             => 'POST',
    'callback'            => __NAMESPACE__ . '\diary_api_update',
    'permission_callback' => $can_edit,
    'args'                => $fields,
  ] );
}

/**
 * The entry's date in the diary's timezone, now when none is given.
 */
function diary_api_date( $value, $qp ) {
  $tz = new \DateTimeZone( $qp['timezone'] );
  if ( ! $value ) {
    return new \DateTime( 'now', $tz );
  }

  try {
    return ( new \DateTime( $value, $tz ) )->setTimezone( $tz );
  } catch ( \Exception $e ) {
    return new \WP_Error( 'rest_invalid_param', 'date must look like 2026-09-22 21:30', [ 'status' => 400 ] );
  }
}

/**
 * The diary meta fields present in the request.
 */
function diary_api_meta( $request ) {
  $names = array_diff( diary_meta_fields(), [ 'drink_icon' ] );
  return array_intersect_key( $request->get_params(), array_flip( $names ) );
}

function diary_api_defaults( $request ) {
  $qp   = qp_config();
  $date = diary_api_date( $request['date'], $qp );
  if ( is_wp_error( $date ) ) {
    return $date;
  }

  $now      = new \DateTime( 'now', new \DateTimeZone( $qp['timezone'] ) );
  $defaults = qp_defaults( $qp, $now, $request->get_header( 'user_agent' ) ?? '' );
  $day      = qp_day_number( $qp, $date );

  return array_merge( $defaults, [
    'day_number'    => $day,
    'title'         => "Päivä $day",
    'date'          => $date->format( 'Y-m-d H:i' ),
    'recent_drinks' => qp_drinks() ?: [],
    'choices'       => array_intersect_key( diary_meta_choices(), array_flip( [ 'mood', 'device', 'weather_icon' ] ) ),
  ] );
}

function diary_api_create( $request ) {
  if ( '' === trim( (string) $request['content'] ) ) {
    return new \WP_Error( 'rest_invalid_param', 'content is required.', [ 'status' => 400 ] );
  }

  $qp   = qp_config();
  $date = diary_api_date( $request['date'], $qp );
  if ( is_wp_error( $date ) ) {
    return $date;
  }

  $now = new \DateTime( 'now', new \DateTimeZone( $qp['timezone'] ) );
  $in  = array_merge(
    qp_defaults( $qp, $now, $request->get_header( 'user_agent' ) ?? '' ),
    [ 'title' => 'Päivä ' . qp_day_number( $qp, $date ) ],
    diary_api_meta( $request ),
    array_filter( [
      'title'   => $request['title'],
      'content' => $request['content'],
    ] ),
    [ 'post_date' => $date->format( 'Y-m-d\TH:i' ) ]
  );

  $post_id = qp_insert_diary( wp_slash( $in ), $request['status'] ?? 'draft', $qp );
  if ( is_wp_error( $post_id ) ) {
    return $post_id;
  }

  qp_purge( home_url( '/purge' ), get_permalink( $post_id ) );
  return diary_api_result( $post_id );
}

function diary_api_update( $request ) {
  $post = get_post( (int) $request['id'] );
  if ( ! $post || 'diary' !== $post->post_type ) {
    return new \WP_Error( 'rest_post_invalid_id', 'No such diary entry.', [ 'status' => 404 ] );
  }

  $args = [ 'ID' => $post->ID ];
  if ( isset( $request['title'] ) ) {
    $args['post_title'] = sanitize_text_field( $request['title'] );
  }
  if ( isset( $request['content'] ) ) {
    $args['post_content'] = qp_to_gutenberg( $request['content'] );
  }
  if ( isset( $request['status'] ) ) {
    $args['post_status'] = $request['status'];
  }
  if ( isset( $request['date'] ) ) {
    $date = diary_api_date( $request['date'], qp_config() );
    if ( is_wp_error( $date ) ) {
      return $date;
    }
    $args['post_date']     = $date->format( 'Y-m-d H:i:s' );
    $args['post_date_gmt'] = get_gmt_from_date( $args['post_date'] );
    $args['edit_date']     = true;
  }

  $result = wp_update_post( wp_slash( $args ), true );
  if ( is_wp_error( $result ) ) {
    return $result;
  }

  qp_save_meta( $post->ID, wp_slash( diary_api_meta( $request ) ) );
  qp_purge( home_url( '/purge' ), get_permalink( $post->ID ) );
  return diary_api_result( $post->ID );
}

function diary_api_result( $post_id ) {
  $meta = [];
  foreach ( diary_meta_fields() as $field ) {
    $meta[ $field ] = get_post_meta( $post_id, $field, true );
  }

  return [
    'id'     => $post_id,
    'title'  => get_post_field( 'post_title', $post_id ),
    'date'   => get_post_field( 'post_date', $post_id ),
    'status' => get_post_status( $post_id ),
    'link'   => get_permalink( $post_id ),
    'meta'   => $meta,
  ];
}
