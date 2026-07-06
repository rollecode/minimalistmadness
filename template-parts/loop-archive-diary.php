<?php
/**
 * Main content for the archive-diary view. Included by both the classic
 * archive-diary.php template and the minimalistmadness/loop-archive-diary block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<div class="content-area">
	<main role="main" id="main" class="site-main block block-page">

    <?php get_template_part( 'template-parts/heatmap' ); ?>

    <?php if ( have_posts() ) :
      $count = 0; ?>

      <?php while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/content-diary' );
      endwhile;

      khonsu_pagination();

    else :
      get_template_part( 'template-parts/content', 'none' );
    endif; ?>

    <?php dynamic_sidebar(); ?>

  </main><!-- #main -->
</div><!-- #primary -->

