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
