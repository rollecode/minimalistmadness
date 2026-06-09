<?php
/**
 * Hooks
 *
 * All hooks that are run in the theme are listed here
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2022-09-19 11:07:30
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-11-01 11:21:48
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

/**
 * General hooks
 */
require get_theme_file_path( 'inc/hooks/general.php' );
add_action( 'widgets_init', __NAMESPACE__ . '\widgets_init' );

/**
 * Scripts and styles associated hooks
 */
require get_theme_file_path( 'inc/hooks/scripts-styles.php' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_theme_scripts' );

/**
 * Performance associated hooks
 */
require get_theme_file_path( 'inc/hooks/performance.php' );

/**
 * Diary metadata (core-native replacement for ACF)
 */
require get_theme_file_path( 'inc/hooks/diary-meta.php' );
add_action( 'init', __NAMESPACE__ . '\register_diary_meta' );

/**
 * Gutenberg associated hooks
 */
require get_theme_file_path( 'inc/hooks/gutenberg.php' );
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\allowed_block_types', 10, 2 );
add_filter( 'use_block_editor_for_post_type', __NAMESPACE__ . '\use_block_editor_for_post_type', 10, 2 );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\register_block_editor_assets' );
add_filter( 'block_editor_settings', __NAMESPACE__ . '\remove_gutenberg_inline_styles', 10, 2 );
add_action( 'after_setup_theme', __NAMESPACE__ . '\setup_editor_styles' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\block_editor_title_input_styles' );

/**
 * Rest API hooks
 */
require get_theme_file_path( 'inc/hooks/rest-api.php' );

/**
 * Add required attributes to Gravity Forms fields to enable native validation
 */
add_filter( 'gform_field_content', __NAMESPACE__ . '\add_custom_attr', 10, 5 );
function add_custom_attr( $field_content, $field, $value, $form_id ) {

  // Add type attribute to file upload button, otherwise it tries to send
  if ( 'fileupload' === $field->type ) {
    $field_content = str_replace( '<button', '<button type="button"', $field_content );
  }

  // Add required to get native HTML validation instead of GF jQuery version
  if ( true === $field->isRequired ) { // phpcs:ignore
    $field_content = str_replace( 'type=', 'required type=', $field_content );
  }

  return $field_content;
 }

/**
 * Change gravity forms input to button that validates natively (remove onclick and onkeypress events)
 */
add_filter( 'gform_submit_button', __NAMESPACE__ . '\form_submit_button', 10, 2 );
function form_submit_button( $button, $form ) {
  return "<button type='submit' class='button gform_button' id='gform_submit_button_{$form['id']}'>Lähetä</button>";
}

/**
* Gravity Forms Scroll To Anchor Tag
*
* @link https://docs.gravityforms.com/gform_confirmation_anchor/
*/
add_filter( 'gform_confirmation_anchor', '__return_true' );

/**
 * ACF blocks
 */
require get_theme_file_path( 'inc/hooks/acf-blocks.php' );
add_filter( 'block_categories_all', __NAMESPACE__ . '\acf_blocks_add_category_in_gutenberg', 10, 2 );
add_action( 'acf/init', __NAMESPACE__ . '\acf_blocks_init' );
