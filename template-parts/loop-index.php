<?php
/**
 * Main loop for the index view. Included by both the classic index.php
 * template and the minimalistmadness/loop-index block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<div class="content-area">
  <main role="main" id="main" class="site-main">

    <div class="container">

      <?php if ( have_posts() ) : ?>

        <?php if ( is_home() && ! is_front_page() ) : ?>

        <header>
          <h1 id="content" class="entry-title screen-reader-text">
            <?php single_post_title(); ?>
          </h1>
        </header>

      <?php endif; ?>

      <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'template-parts/content', get_post_type() ); ?>

      <?php endwhile; ?>

      <?php the_posts_navigation(); ?>

      <?php else : ?>

        <?php get_template_part( 'template-parts/content', 'none' ); ?>

      <?php endif; ?>

    </div><!-- .container -->
  </main><!-- #main -->
</div><!-- #primary -->

