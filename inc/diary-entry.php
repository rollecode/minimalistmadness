<?php
/**
 * Diary entry helpers shared by quick-post.php and the rollemaa/v1 diary API.
 *
 * Secrets and private hosts never live here: callers pass them in, the API
 * reads them from the environment.
 *
 * @package minimalistmadness
 */

/**
 * Settings for new diary entries. Private values come from the environment.
 */
function qp_config() {
  return [
    'epoch'         => '1988-11-01',
    'author_id'     => 1,
    'timezone'      => 'Europe/Helsinki',
    'post_type'     => 'diary',
    'lastfm_user'   => 'rolle-',
    'weather_city'  => 'Jyväskylä',
    'lastfm_key'    => getenv( 'LASTFM_API_KEY' ),
    'ruuvi_url'     => getenv( 'RUUVI_API_URL' ),
    'location_url'  => getenv( 'LOCATION_API_URL' ),
    'location_auth' => getenv( 'LOCATION_API_AUTH' ),
  ];
}

function qp_cached( $key, $ttl, $fn ) {
  $v = get_transient( 'qp_' . $key );
  if ( false !== $v ) {
    return $v;
  }
  $v = $fn();
  if ( null !== $v ) {
    set_transient( 'qp_' . $key, $v, $ttl );
  }
  return $v;
}

function qp_lastfm( $user, $key ) {
  return qp_cached( 'lastfm', 120, function () use ( $user, $key ) {
    $r = wp_remote_get( "https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user={$user}&api_key={$key}&format=json&limit=1", [ 'timeout' => 3 ] );
    if ( is_wp_error( $r ) ) {
      return null;
    }
    $d = json_decode( wp_remote_retrieve_body( $r ), true );
    $t = $d['recenttracks']['track'][0] ?? null;
    if ( ! $t ) {
      return null;
    }
    return [
      'artist'  => $t['artist']['#text'] ?? '',
      'track'   => $t['name'] ?? '',
      'url'     => $t['url'] ?? '',
      'playing' => isset( $t['@attr']['nowplaying'] ),
    ];
  } );
}

function qp_ruuvi( $url ) {
  return qp_cached( 'ruuvi', 300, function () use ( $url ) {
    $r = wp_remote_get( $url, [ 'timeout' => 3 ] );
    if ( is_wp_error( $r ) ) {
      return null;
    }
    $d = json_decode( wp_remote_retrieve_body( $r ), true );
    foreach ( ( $d['sensors'] ?? [] ) as $s ) {
      $n = $s['name'] ?? '';
      if ( 'Katto' === $n ) {
        return number_format( $s['temperature'], 2, '.', '' );
      }
    }
    return null;
  } );
}

function qp_weather( $city ) {
  return qp_cached( 'weather', 1800, function () use ( $city ) {
    $r = wp_remote_get( 'https://wttr.in/' . urlencode( $city ) . '?format=j1', [ 'timeout' => 3 ] );
    if ( is_wp_error( $r ) ) {
      return null;
    }
    $d = json_decode( wp_remote_retrieve_body( $r ), true );
    $c = $d['current_condition'][0] ?? null;
    if ( ! $c ) {
      return null;
    }
    $code = (int) ( $c['weatherCode'] ?? 0 );
    $map  = [
      113 => [ 'sun', 'selkeää' ], 116 => [ 'cloud', 'puolipilvistä' ],
      119 => [ 'cloud', 'pilvistä' ], 122 => [ 'cloud', 'pilvistä' ],
      143 => [ 'cloud', 'sumuista' ], 176 => [ 'drizzle', 'sadekuuroja' ],
      179 => [ 'snow', 'lumikuuroja' ], 200 => [ 'lightning', 'ukkosta' ],
      227 => [ 'snow', 'lumipyryä' ], 230 => [ 'snow', 'lumimyrskyä' ],
      248 => [ 'cloud', 'sumuista' ], 263 => [ 'drizzle', 'tihkusadetta' ],
      266 => [ 'drizzle', 'tihkusadetta' ], 293 => [ 'drizzle', 'heikkoa sadetta' ],
      296 => [ 'drizzle', 'heikkoa sadetta' ], 299 => [ 'rain', 'sadetta' ],
      302 => [ 'rain', 'kovaa sadetta' ], 305 => [ 'rain', 'rankkasadetta' ],
      308 => [ 'rain', 'rankkasadetta' ], 323 => [ 'snow', 'lumisadetta' ],
      326 => [ 'snow', 'lumisadetta' ], 329 => [ 'snow', 'lumisadetta' ],
      332 => [ 'snow', 'kovaa lumisadetta' ], 335 => [ 'snow', 'kovaa lumisadetta' ],
      338 => [ 'snow', 'kovaa lumisadetta' ], 353 => [ 'drizzle', 'sadekuuroja' ],
      356 => [ 'rain', 'sadekuuroja' ], 359 => [ 'rain', 'rankkoja kuuroja' ],
      368 => [ 'snow', 'lumikuuroja' ], 371 => [ 'snow', 'lumikuuroja' ],
      386 => [ 'lightning', 'ukkoskuuroja' ], 389 => [ 'lightning', 'ukkosmyrskyjä' ],
    ];
    $info = $map[ $code ] ?? [ 'cloud', 'pilvistä' ];
    return [ 'icon' => $info[0], 'text' => $info[1], 'temp' => $c['temp_C'] ?? '' ];
  } );
}

function qp_drink_icon( $text ) {
  $t = mb_strtolower( $text );
  if ( preg_match( '/kahvi|coffee|warrior|paahti|latte|cappuccino|espresso|caffi/', $t ) ) {
    return 'coffee';
  }
  if ( preg_match( '/vesi|water|pellegrino/', $t ) ) {
    return 'droplet';
  }
  return 'can';
}

function qp_drinks() {
  return qp_cached( 'drinks', 3600, function () {
    global $wpdb;
    return $wpdb->get_col(
      "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'drink_text' AND meta_value != '' GROUP BY meta_value ORDER BY COUNT(*) DESC LIMIT 30"
    );
  } );
}

function qp_youtube_url( $artist, $track ) {
  $q = urlencode( $artist . ' ' . $track . ' official video' );
  return qp_cached( 'yt_' . md5( $artist . $track ), 3600, function () use ( $q ) {
    $r = wp_remote_get( 'https://www.youtube.com/results?search_query=' . $q, [
      'timeout' => 4,
      'headers' => [ 'User-Agent' => 'Mozilla/5.0 (compatible)' ],
    ] );
    if ( is_wp_error( $r ) ) {
      return null;
    }
    $body = wp_remote_retrieve_body( $r );
    if ( preg_match( '/"videoId":"([a-zA-Z0-9_-]{11})"/', $body, $m ) ) {
      return 'https://www.youtube.com/watch?v=' . $m[1];
    }
    return null;
  } );
}

/**
 * Where Rolle is, from OwnTracks. $auth is "user:password".
 */
function qp_location( $url, $auth ) {
  if ( ! $url ) {
    return 'Kotona';
  }
  return qp_cached( 'location', 300, function () use ( $url, $auth ) {
    $r = wp_remote_get( $url, [
      'timeout' => 3,
      'headers' => [
        'Authorization' => 'Basic ' . base64_encode( $auth ), // phpcs:ignore
      ],
    ] );
    if ( is_wp_error( $r ) ) {
      return 'Kotona';
    }
    $d = json_decode( wp_remote_retrieve_body( $r ), true );
    if ( ! $d ) {
      return 'Kotona';
    }
    $haystack = mb_strtolower( json_encode( $d ) );
    if ( false !== strpos( $haystack, 'kauppakatu 14' ) ) {
      return 'Toimistolla';
    }
    if ( false !== strpos( $haystack, 'vaihdekuja' ) ) {
      return 'Kotona';
    }
    foreach ( [ 'address', 'location', 'display_name', 'desc', 'name' ] as $k ) {
      if ( ! empty( $d[ $k ] ) && is_string( $d[ $k ] ) ) {
        return $d[ $k ];
      }
    }
    return 'Kotona';
  } );
}

function qp_device( $ua ) {
  if ( false !== stripos( $ua, 'iPhone' ) ) {
    return 'iphone';
  }
  if ( false !== stripos( $ua, 'Macintosh' ) ) {
    return 'macbook';
  }
  if ( false !== stripos( $ua, 'Android' ) ) {
    return 'mobile';
  }
  return 'pc';
}

/**
 * Days since the epoch (the epoch itself is day 0).
 */
function qp_day_number( $qp, DateTime $date ) {
  return ( new DateTime( $qp['epoch'], new DateTimeZone( $qp['timezone'] ) ) )->diff( $date )->days;
}

/**
 * Everything the quick post form fills in on its own, as of $now.
 */
function qp_defaults( $qp, DateTime $now, $ua ) {
  $lastfm  = qp_lastfm( $qp['lastfm_user'], $qp['lastfm_key'] );
  $ruuvi   = qp_ruuvi( $qp['ruuvi_url'] );
  $weather = qp_weather( $qp['weather_city'] );

  $temp   = null !== $ruuvi ? $ruuvi . '°C' : ( ( $weather['temp'] ?? '' ) ? $weather['temp'] . '°C' : '' );
  $w_icon = $weather['icon'] ?? 'cloud';

  // No sun at night.
  $hour = (int) $now->format( 'G' );
  if ( ( $hour >= 21 || $hour < 7 ) && 'sun' === $w_icon ) {
    $w_icon = 'cloud';
  }

  $playing = $lastfm && ! empty( $lastfm['playing'] );
  $day     = qp_day_number( $qp, $now );

  return [
    'day_number'   => $day,
    'title'        => "Päivä $day",
    'temperature'  => $temp,
    'weather_icon' => $w_icon,
    'weather_text' => $weather['text'] ?? 'pilvistä',
    'np'           => $playing ? ( $lastfm['artist'] . ' – ' . $lastfm['track'] ) : '',
    'np_link'      => $playing ? ( qp_youtube_url( $lastfm['artist'], $lastfm['track'] ) ?: $lastfm['url'] ) : '',
    'device'       => qp_device( $ua ),
    'location'     => qp_location( $qp['location_url'], $qp['location_auth'] ),
  ];
}

function qp_to_gutenberg( $raw ) {
  $raw = trim( $raw );
  if ( '' === $raw ) {
    return '';
  }
  $blocks = [];
  foreach ( preg_split( '/\n\s*\n/', $raw ) as $p ) {
    $p = trim( $p );
    if ( '' === $p ) {
      continue;
    }
    // Inserted image blocks pass through; the prose around them still gets wrapped
    if ( 0 === strpos( $p, '<!-- wp:' ) ) {
      $blocks[] = $p;
    } elseif ( preg_match( '#^https?://(www\.)?(youtube\.com/watch|youtu\.be/|vimeo\.com/)#i', $p ) ) {
      $u        = esc_url( $p );
      $blocks[] = "<!-- wp:embed {\"url\":\"$u\",\"type\":\"video\"} -->\n<figure class=\"wp-block-embed is-type-video\"><div class=\"wp-block-embed__wrapper\">\n$u\n</div></figure>\n<!-- /wp:embed -->";
    } elseif ( preg_match( '#^https?://(twitter\.com|x\.com)/#i', $p ) ) {
      $u        = esc_url( $p );
      $blocks[] = "<!-- wp:embed {\"url\":\"$u\",\"type\":\"rich\"} -->\n<figure class=\"wp-block-embed is-type-rich\"><div class=\"wp-block-embed__wrapper\">\n$u\n</div></figure>\n<!-- /wp:embed -->";
    } elseif ( preg_match( '/^<(iframe|blockquote|figure|div|video|audio|table)/i', $p ) ) {
      $blocks[] = "<!-- wp:html -->\n$p\n<!-- /wp:html -->";
    } elseif ( preg_match( '/^<h([1-6])[^>]*>/i', $p, $m ) ) {
      $blocks[] = "<!-- wp:heading {\"level\":{$m[1]}} -->\n$p\n<!-- /wp:heading -->";
    } elseif ( preg_match( '/^<[uo]l/i', $p ) ) {
      $blocks[] = "<!-- wp:list -->\n$p\n<!-- /wp:list -->";
    } else {
      $t = str_replace( "\n", '<br>', $p );
      if ( ! preg_match( '/^<p[\s>]/i', $t ) ) {
        $t = "<p>$t</p>";
      }
      $blocks[] = "<!-- wp:paragraph -->\n$t\n<!-- /wp:paragraph -->";
    }
  }
  return implode( "\n\n", $blocks );
}

/**
 * Create a diary entry. $in is slashed, like $_POST: title, content,
 * post_date (Y-m-d\TH:i) and the meta fields by name.
 *
 * @return int|WP_Error Post id.
 */
function qp_insert_diary( $in, $status, $qp ) {
  $args = [
    'post_title'   => sanitize_text_field( $in['title'] ?? '' ),
    'post_content' => qp_to_gutenberg( $in['content'] ?? '' ),
    'post_status'  => 'publish' === $status ? 'publish' : 'draft',
    'post_type'    => $qp['post_type'],
    'post_author'  => $qp['author_id'],
  ];

  if ( ! empty( $in['post_date'] ) ) {
    $args['post_date']     = sanitize_text_field( $in['post_date'] ) . ':00';
    $args['post_date_gmt'] = get_gmt_from_date( $args['post_date'] );
  }

  $post_id = wp_insert_post( $args, true );
  if ( is_wp_error( $post_id ) ) {
    return $post_id;
  }

  qp_save_meta( $post_id, $in );
  return $post_id;
}

/**
 * Store the diary meta present in slashed $in. Empty values are skipped.
 */
function qp_save_meta( $post_id, $in ) {
  $ranges = [ 'habits_percent', 'energy_scale', 'anxiety_scale', 'mood_scale', 'productivity_scale' ];
  $all    = array_merge( $ranges, [
    'gratitude', 'mood', 'device', 'location', 'temperature',
    'weather_text', 'weather_icon', 'highlight', 'np', 'np_link',
  ] );

  foreach ( $all as $f ) {
    if ( isset( $in[ $f ] ) && '' !== $in[ $f ] ) {
      $v = in_array( $f, $ranges, true ) ? (int) $in[ $f ] : sanitize_text_field( $in[ $f ] );
      update_post_meta( $post_id, $f, $v );
    }
  }

  // The icon follows the drink.
  $drink = sanitize_text_field( $in['drink_text'] ?? '' );
  if ( $drink ) {
    update_post_meta( $post_id, 'drink_text', $drink );
    update_post_meta( $post_id, 'drink_icon', qp_drink_icon( $drink ) );
  }
}

/**
 * Purge the fastcgi cache for the diary archive, the entry and the front page.
 */
function qp_purge( $base, $permalink ) {
  wp_remote_get( $base . '/lokikirja/', [ 'timeout' => 2 ] );
  $path = wp_parse_url( $permalink, PHP_URL_PATH );
  if ( $path ) {
    wp_remote_get( $base . $path, [ 'timeout' => 2 ] );
  }
  wp_remote_get( $base . '/', [ 'timeout' => 2 ] );
}
