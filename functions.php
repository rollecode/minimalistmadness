<?php
/**
 * Gather all bits and pieces together.
 * If you end up having multiple post types, taxonomies,
 * hooks and functions - please split those to their
 * own files under /inc and just require here.
 *
 * @Date: 2019-10-15 12:30:02
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2021-05-20 18:19:49
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * The current version of the theme.
 */
define( 'AIR_LIGHT_VERSION', '5.0.7' );

// We need to have some defaults as comments or empties so let's allow this:
// phpcs:disable Squiz.Commenting.InlineComment.SpacingBefore, WordPress.Arrays.ArrayDeclarationSpacing.SpaceInEmptyArray

/**
 * Theme settings
 */
add_action( 'after_setup_theme', function() {
  $theme_settings = [
    /**
     * Theme textdomain
     */
    'textdomain' => 'minimalistmadness',

    /**
     * Image and content sizes
     */
    'image_sizes' => [
      'small'   => 300,
      'medium'  => 700,
      'large'   => 1200,
    ],
    'content_width' => 800,

    /**
     * Logo and featured image
     */
    'default_featured_image'  => null,
    'logo'                    => '/svg/logo.svg',

    /**
     * Custom setting group post ids when using Air Helper's custom setting
     * feature and settings CPT. On multilingual sites using Polylang,
     * translations are handled automatically.
     */
    'custom_settings_post_ids' => [
      // 'setting-group' => 0,
    ],

    'social_media_accounts'  => [
      // 'twitter' => [
      //   'title' => 'Twitter',
      //   'url'   => 'https://twitter.com/digitoimistodude',
      // ],
    ],

    /**
     * Menu locations
     */
    'menu_locations' => [
      'primary' => __( 'Primary Menu', 'minimalistmadness' ),
    ],

    /**
     * Taxonomies
     *
     * See the instructions:
     * https://github.com/digitoimistodude/air-light#custom-taxonomies
     */
    'taxonomies' => [
      // 'example' => [
      //    'name' => 'Example',
      //    'post_types' => [ 'diary' ],
      // ],
    ],

    /**
     * Post types
     *
     * See the instructions:
     * https://github.com/digitoimistodude/air-light#custom-post-types
     */
    'post_types' => [
      'diary' => 'Diary',
    ],

    /**
     * Gutenberg -related settings
     */
    // Restrict to only selected blocks
    // Set the value to 'all' to allow all blocks everywhere
    'allowed_blocks' => 'all',

    // If you want to use classic editor somewhere, define it here
    'use_classic_editor' => [],

    // Add your own settings and use them wherever you need, for example THEME_SETTINGS['my_custom_setting']
    'my_custom_setting' => true,
  ];

  $theme_settings = apply_filters( 'minimalistmadness_theme_settings', $theme_settings );

  define( 'THEME_SETTINGS', $theme_settings );
} ); // end action after_setup_theme

/**
 * Required files
 */
require get_theme_file_path( '/inc/hooks.php' );
require get_theme_file_path( '/inc/post-types/ads.php' );
require get_theme_file_path( '/inc/includes.php' );
require get_theme_file_path( '/inc/template-tags.php' );

// Run theme setup
add_action( 'init', __NAMESPACE__ . '\theme_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\build_theme_support' );

/**
 * Add custom post types to main RSS feed
 **/
function add_to_rss_request( $qv ) {
  if ( isset( $qv['feed'] ) && ! isset( $qv['post_type'] ) )

  $qv['post_type'] = array( 'post', 'diary' );
    return $qv;
}
add_filter( 'request', __NAMESPACE__ . '\add_to_rss_request' );


/**
 * Custom post type date archives
 */
add_permastruct(
  'diary',
  '/lokikirja/%year%/%monthnum%/%day%/page/%paged%',
  [
    'with_front' => false,
    'paged' => true,
  ]
);

add_rewrite_rule(
  '^lokikirja/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$',
  'index.php?post_type=diary&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]',
  'top'
);

add_rewrite_rule(
  '^lokikirja/([0-9]{4})/([0-9]{1,2})/?$',
  'index.php?post_type=diary&year=$matches[1]&monthnum=$matches[2]',
  'top'
);

add_rewrite_rule(
  '^lokikirja/([0-9]{4})/?$',
  'index.php?post_type=diary&year=$matches[1]',
  'top'
);

add_rewrite_rule(
  '^lokikirja/(.+?)/(.+?)/(.+?)/page/([0-9])+$',
  'index.php?post_type=diary&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]',
  'top'
);


add_rewrite_rule(
  '^lokikirja/([0-9]{4})/([0-9]{1,2})/page/([0-9])+$',
  'index.php?post_type=diary&year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]',
  'top'
);

add_rewrite_rule(
  '^lokikirja/([0-9]{4})/page/([0-9])+$',
  'index.php?post_type=diary&year=$matches[1]&paged=$matches[2]',
  'top'
);

function review_permalinks( $url, $post ) {
  if ( 'diary' === get_post_type( $post ) ) {
      $url = str_replace( '%year%', get_the_date( 'Y' ), $url );
      $url = str_replace( '%monthnum%', get_the_date( 'm' ), $url );
      $url = str_replace( '%day%', get_the_date( 'd' ), $url );
  }

  return $url;
}

add_filter( 'post_type_link', __NAMESPACE__ . '\review_permalinks', 10, 2 );
