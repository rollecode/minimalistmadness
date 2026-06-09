<?php
/**
 * The template for displaying front page
 *
 * Contains the closing of the #content div and all content after.
 * Initial styles for front page template.
 *
 * @Date:   2019-10-15 12:30:02
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2020-04-02 21:47:12
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

get_header(); ?>

<div id="content" class="content-area">
  <main role="main" id="main" class="site-main">

    <?php
    if ( is_paged() ) {
      if ( have_posts() ) {
        while ( have_posts() ) {
          the_post();
          get_template_part( 'template-parts/content' );
        }

        khonsu_pagination();

      } else {
        get_template_part( 'template-parts/content', 'none' );
      }
    } else {
      include get_theme_file_path( 'template-parts/hero.php' );
      include get_theme_file_path( 'template-parts/upsell-big.php' );
      include get_theme_file_path( 'template-parts/four-posts.php' );
      include get_theme_file_path( 'template-parts/most-popular.php' );
      include get_theme_file_path( 'template-parts/random.php' );
      include get_theme_file_path( 'template-parts/ads.php' );
      include get_theme_file_path( 'template-parts/who.php' );
    }
    ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
