<?php
/**
 * Performance related hooks
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * Upgrade core speculative loading from conservative prefetch to moderate
 * prerender: links prerender on hover/pointerdown so the next page paints
 * instantly. Together with cross-document View Transitions this replaces
 * the removed swup.js (preload plugin + transition animations).
 *
 * Core handles the exclusions (wp-admin, query strings, nofollow,
 * .no-prefetch) and disables speculative loading for logged-in users.
 */
add_filter( 'wp_speculation_rules_configuration', function ( $config ) {
  if ( is_array( $config ) ) {
    $config['mode']      = 'prerender';
    $config['eagerness'] = 'moderate';
  }

  return $config;
} );

/**
 * Drop the emoji detection script and styles from every page. Native emoji
 * rendering has been fine on every platform for years; the twemoji swap
 * only added an inline script, inline styles and a DNS lookup.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'emoji_svg_url', '__return_false' );

/**
 * Preload the two body font weights used above the fold so text paints
 * without waiting for the CSS to discover the @font-face rules.
 */
add_action( 'wp_head', function () {
  foreach ( [ 'Inter-Regular', 'Inter-Bold' ] as $font ) {
    printf(
      '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
      esc_url( get_theme_file_uri( 'fonts/' . $font . '.woff2' ) )
    );
  }
}, 2 );
