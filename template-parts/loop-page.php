<?php
/**
 * Main content for the page view. Included by both the classic
 * page.php template and the minimalistmadness/loop-page block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<main class="site-main">

  <section class="block block-page has-light-bg">

    <div class="article-content">

      <h1 id="content" class="entry-header"><?php the_title(); ?></h1>
      <?php wp_reset_postdata(); the_content(); ?>

      <?php if ( get_edit_post_link() ) {
        edit_post_link( sprintf( wp_kses( __( 'Muokkaa <span class="screen-reader-text">%s</span>', 'minimalistmadness' ), [ 'span' => [ 'class' => [] ] ] ), get_the_title() ), '<p class="edit-link">', '</p>' );
      } ?>

    </div>

  </section>

</main>

