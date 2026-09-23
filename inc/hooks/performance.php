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
 * Just-in-time block styles. Core block styles load per block instead of as
 * one wp-block-library bundle, and only when the block is actually rendered
 * on the page (WP 6.8+ on-demand loading). The theme's own per-block styles
 * (css/{dev,prod}/blocks/*.css) register the same way with 'path' set, so
 * WordPress INLINES them into the page — each page carries only the styles
 * for the blocks it displays. Pre-Gutenberg posts (no block markers) load
 * content-legacy.css instead, handled in scripts-styles.php.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'wp_should_load_block_assets_on_demand', '__return_true' );

// Allow all our small per-block styles to inline (default budget is 20 KB)
add_filter( 'styles_inline_size_limit', function () {
  return 50000;
} );

add_action( 'init', function () {
  $block_styles = [
    'core/quote'            => [ 'core-blockquote' ],
    'core/code'             => [ 'core-code' ],
    'core/columns'          => [ 'core-columns' ],
    'core/cover'            => [ 'core-cover' ],
    'core/embed'            => [ 'core-embed' ],
    'core/gallery'          => [ 'core-gallery' ],
    'core/separator'        => [ 'core-separator' ],
    'core/heading'          => [ 'core-heading' ],
    'core/image'            => [ 'core-image' ],
    'core/paragraph'        => [ 'core-paragraph', 'boxed' ],
    'core/preformatted'     => [ 'core-preformatted' ],
    'core/pullquote'        => [ 'core-pullquote' ],
    'core/table'            => [ 'core-table' ],
    'core/text-columns'     => [ 'core-text-columns' ],
    'core/verse'            => [ 'core-verse' ],
    'core/video'            => [ 'core-video' ],
    'core/list'             => [ 'core-list', 'no-bullets' ],
    'core/button'           => [ 'button' ],
    'core/file'             => [ 'button-file' ],
    'activitypub/reactions' => [ 'activitypub' ],
  ];

  foreach ( $block_styles as $block => $files ) {
    foreach ( $files as $file ) {
      $asset = get_asset_file( 'blocks/' . $file . '.css' );

      wp_enqueue_block_style( $block, [
        'handle' => 'mm-block-' . $file,
        'src'    => get_theme_file_uri( $asset ),
        'path'   => get_theme_file_path( $asset ),
        'ver'    => filemtime( get_theme_file_path( $asset ) ),
      ] );
    }
  }
} );

/**
 * Preload the two body font weights used above the fold so text paints
 * without waiting for the CSS to discover the @font-face rules.
 */
add_action( 'wp_head', function () {
  foreach ( [ 'Inter-Regular', 'Inter-SemiBold', 'Inter-Bold' ] as $font ) {
    printf(
      '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
      esc_url( get_theme_file_uri( 'fonts/' . $font . '-latin.woff2' ) )
    );
  }
}, 2 );

/**
 * Stylesheets nothing above the fold needs load without blocking the first
 * paint: the JS-filled rain effect, forms and the footer everywhere, and on
 * singles the related-post cards and the JS-injected own ad too.
 */
const NON_BLOCKING_STYLES          = [ 'styles-rain', 'styles-forms', 'styles-footer' ];
const NON_BLOCKING_SINGULAR_STYLES = [ 'styles-cards', 'styles-ads' ];

add_filter( 'style_loader_tag', function ( $tag, $handle ) {
  $short_page = is_404() || is_search();
  $below_fold = ( in_array( $handle, NON_BLOCKING_STYLES, true ) && ! ( $short_page && 'styles-footer' === $handle ) )
    || ( is_singular() && ! is_front_page() && in_array( $handle, NON_BLOCKING_SINGULAR_STYLES, true ) );

  if ( ! $below_fold ) {
    return $tag;
  }

  return str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $tag );
}, 10, 2 );

/**
 * Generated image sizes are WebP; the uploaded original stays as it was.
 */
add_filter( 'image_editor_output_format', function ( $formats ) {
  return $formats + [
    'image/jpeg' => 'image/webp',
    'image/png'  => 'image/webp',
  ];
} );
