<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @Date:   2019-10-15 12:30:02
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2021-01-12 16:10:58
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

namespace Air_Light;

the_post();
get_header(); ?>

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

<?php get_footer();
