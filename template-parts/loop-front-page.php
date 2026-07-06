<?php
/**
 * Main content for the front-page view. Included by both the classic
 * front-page.php template and the minimalistmadness/loop-front-page block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

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

