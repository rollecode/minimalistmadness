<?php
/**
 * Gutenberg related settings
 *
 * @Author: Niku Hietanen
 * @Date: 2020-02-20 13:46:50
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-01-29 12:13:19
 *
 * @package air-light
 */

namespace Air_Light;

/**
 * Restrict blocks to only allowed blocks in the settings
 */
function allowed_block_types( $allowed_blocks, $post ) {
  if ( ! isset( THEME_SETTINGS['allowed_blocks'] ) || 'all' === THEME_SETTINGS['allowed_blocks'] ) {
    return $allowed_blocks;
  }

  // Add the default allowed blocks
  $allowed_blocks = isset( THEME_SETTINGS['allowed_blocks']['default'] ) ? THEME_SETTINGS['allowed_blocks']['default'] : [];

  // If there is post type specific blocks, add them to the allowed blocks list
  if ( isset( THEME_SETTINGS['allowed_blocks'][ $post->post_type ] ) ) {
    $allowed_blocks = array_merge( $allowed_blocks, THEME_SETTINGS['allowed_blocks'][ $post->post_type ] );
  }

  return $allowed_blocks;
} // end allowed_block_types

/**
 * Check whether to use classic or block editor for a certain post type as defined in the settings
 */
function use_block_editor_for_post_type( $use_block_editor, $post_type ) {
  if ( in_array( $post_type, THEME_SETTINGS['use_classic_editor'], true ) ) {
    return false;
  }

  return true;
} // end use_block_editor_for_post_type

/**
 * Enqueue block editor JavaScript and CSS
 */
function register_block_editor_assets() {

  // Dependencies
  $dependencies = [
    'wp-blocks',    // Provides useful functions and components for extending the editor
    'wp-i18n',      // Provides localization functions
    'wp-element',   // Provides React.Component
    'wp-components', // Provides many prebuilt components and controls
  ];

  // Enqueue the block editor JS file (buildless, served as-is)
  wp_enqueue_script(
    'block-editor-js',
    get_theme_file_uri( 'js/gutenberg-editor.js' ),
    $dependencies,
    filemtime( get_theme_file_path( 'js/gutenberg-editor.js' ) ),
    'all'
  );

} // end register_block_editor_assets

/**
 * Diary entries are narrower and set in a smaller size than articles. The
 * editor canvas gets that from theme.json data, not from an editor stylesheet.
 */
function diary_editor_theme_json( $theme_json ) {
  if ( ! is_admin() || 'diary' !== ( $GLOBALS['typenow'] ?? '' ) ) {
    return $theme_json;
  }

  return $theme_json->update_with( [
    'version'  => 3,
    'settings' => [ 'layout' => [ 'contentSize' => '600px' ] ],
    'styles'   => [ 'typography' => [ 'fontSize' => '17px' ] ],
  ] );
}
