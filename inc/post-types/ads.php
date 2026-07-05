<?php
/**
 * Advertisement post type
 *
 * @package minimalistmadness
 */

// Register Custom Post Type
function ads() {

  $labels = array(
    'name'                  => _x( 'Mainokset', 'Post Type General Name', 'minimalistmadness' ),
    'singular_name'         => _x( 'Mainos', 'Post Type Singular Name', 'minimalistmadness' ),
    'menu_name'             => __( 'Mainokset', 'minimalistmadness' ),
    'name_admin_bar'        => __( 'Mainokset', 'minimalistmadness' ),
    'archives'              => __( 'Mainos-arkistot', 'minimalistmadness' ),
    'attributes'            => __( 'Mainoksen attribuutit', 'minimalistmadness' ),
    'parent_item_colon'     => __( 'Isäntämainos:', 'minimalistmadness' ),
    'all_items'             => __( 'Kaikki mainokset', 'minimalistmadness' ),
    'add_new_item'          => __( 'Lisää mainos', 'minimalistmadness' ),
    'add_new'               => __( 'Lisää mainos', 'minimalistmadness' ),
    'new_item'              => __( 'Uusi mainos', 'minimalistmadness' ),
    'edit_item'             => __( 'Muokkaa mainosta', 'minimalistmadness' ),
    'update_item'           => __( 'Päivitä mainos', 'minimalistmadness' ),
    'view_item'             => __( 'Katso mainos', 'minimalistmadness' ),
    'view_items'            => __( 'Katso mainoksia', 'minimalistmadness' ),
    'search_items'          => __( 'Etsi mainos', 'minimalistmadness' ),
    'not_found'             => __( 'Ei löydy', 'minimalistmadness' ),
    'not_found_in_trash'    => __( 'Ei löydy roskista', 'minimalistmadness' ),
    'featured_image'        => __( 'Artikkelikuva', 'minimalistmadness' ),
    'set_featured_image'    => __( 'Aseta artikkelikuva', 'minimalistmadness' ),
    'remove_featured_image' => __( 'Poista artikkelikuva', 'minimalistmadness' ),
    'use_featured_image'    => __( 'Käytä artikkelikuvana', 'minimalistmadness' ),
    'insert_into_item'      => __( 'Lisää kohteeseen', 'minimalistmadness' ),
    'uploaded_to_this_item' => __( 'Lisätty kohteeseen', 'minimalistmadness' ),
    'items_list'            => __( 'Mainoslista', 'minimalistmadness' ),
    'items_list_navigation' => __( 'Mainoslistan navigointi', 'minimalistmadness' ),
    'filter_items_list'     => __( 'Suodata listaa', 'minimalistmadness' ),
  );
  $args = array(
    'label'                 => __( 'Mainos', 'minimalistmadness' ),
    'description'           => __( 'Ad slots', 'minimalistmadness' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'revisions', 'post-formats', 'custom-fields' ),
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'menu_icon'             => 'dashicons-forms',
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => false,
    'exclude_from_search'   => false,
    'publicly_queryable'    => false,
    'capability_type'       => 'page',
  );
  register_post_type( 'ads', $args );

}
add_action( 'init', 'ads', 0 );
