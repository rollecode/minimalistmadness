<?php
/**
 * Advertisement post type
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * Registers the Your Post Type post type.
 */
class Diary extends Post_Type {

  public function register() {

    // Modify all the i18ized strings here.
    $generated_labels = [
      'name'                  => _x( 'Lokikirja', 'Post Type General Name', 'minimalistmadness' ),
      'singular_name'         => _x( 'Lokikirja', 'Post Type Singular Name', 'minimalistmadness' ),
      'menu_name'             => __( 'Lokikirja', 'minimalistmadness' ),
      'name_admin_bar'        => __( 'Lokikirja', 'minimalistmadness' ),
      'archives'              => __( 'Lokikirja', 'minimalistmadness' ),
      'attributes'            => __( 'Lokikirjan attribuutit', 'minimalistmadness' ),
      'parent_item_colon'     => __( 'Pääasiallinen merkintä:', 'minimalistmadness' ),
      'all_items'             => __( 'Kaikki merkinnät', 'minimalistmadness' ),
      'add_new_item'          => __( 'Lisää merkintä', 'minimalistmadness' ),
      'add_new'               => __( 'Lisää merkintä', 'minimalistmadness' ),
      'new_item'              => __( 'Uusi merkintä', 'minimalistmadness' ),
      'edit_item'             => __( 'Muokkaa merkintää', 'minimalistmadness' ),
      'update_item'           => __( 'Päivitä merkintää', 'minimalistmadness' ),
      'view_item'             => __( 'Katso merkintä', 'minimalistmadness' ),
      'view_items'            => __( 'Katso lokikirjamerkintöjä', 'minimalistmadness' ),
      'search_items'          => __( 'Etsi lokikirjamerkintöjä', 'minimalistmadness' ),
      'not_found'             => __( 'Merkintöjä ei löytynyt.', 'minimalistmadness' ),
      'not_found_in_trash'    => __( 'Merkintöjä ei löytynyt.', 'minimalistmadness' ),
      'featured_image'        => __( 'Artikkelikuva', 'minimalistmadness' ),
      'set_featured_image'    => __( 'Aseta artikkelikuva', 'minimalistmadness' ),
      'remove_featured_image' => __( 'Poista artikkelikuva', 'minimalistmadness' ),
      'use_featured_image'    => __( 'Käytä artikkelikuvana', 'minimalistmadness' ),
      'insert_into_item'      => __( 'Lisää kohteeseen', 'minimalistmadness' ),
      'uploaded_to_this_item' => __( 'Lisätty kohteeseen', 'minimalistmadness' ),
      'items_list'            => __( 'merkinnät', 'minimalistmadness' ),
      'items_list_navigation' => __( 'Merkintöjen selaus', 'minimalistmadness' ),
      'filter_items_list'     => __( 'Suodata merkintöjä', 'minimalistmadness' ),
    ];

    // Definition of the post type arguments. For full list see:
    // http://codex.wordpress.org/Function_Reference/register_post_type
    $args = [
      'rewrite' => [
        'slug'       => 'lokikirja',
        'with_front' => false,
        'pages'      => true,
        'feeds'      => true,
      ],
      'paged'                 => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
      'label'                 => __( 'Lokikirja', 'minimalistmadness' ),
      'description'           => __( 'Merkinnät', 'minimalistmadness' ),
      'labels'                => $generated_labels,
      'supports'              => array( 'title', 'editor', 'author', 'revisions', 'post-formats', 'thumbnail', 'custom-fields' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'show_in_rest'          => true,
      'menu_position'         => 1,
      'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path fill="currentColor" d="M24 6.278l-11.16 12.722-6.84-6 1.319-1.49 5.341 4.686 9.865-11.196 1.475 1.278zm-22.681 5.232l6.835 6.01-1.314 1.48-6.84-6 1.319-1.49zm9.278.218l5.921-6.728 1.482 1.285-5.921 6.756-1.482-1.313z"/></svg>' ), // phpcs:ignore
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'capability_type'       => 'post',
    ];

    $this->register_wp_post_type( $this->slug, $args );
  }
}
