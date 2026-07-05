<?php
/**
 * Single diary
 *
 * A template for single post.
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2021-11-16 09:38:48
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2023-01-24 00:01:40
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

get_header();
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <p class="back-to-link">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'diary' ) ); ?>">
        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.469669 5.46967C0.176777 5.76257 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989594 6.3033 0.696701C6.01041 0.403807 5.53553 0.403807 5.24264 0.696701L0.469669 5.46967ZM22 5.25L1 5.25L1 6.75L22 6.75L22 5.25Z" fill="currentColor"/></svg>
        Takaisin päiväkirjamerkintöihin
      </a>
    </p>

    <?php get_template_part( 'template-parts/log-header' ); ?>

    <?php while ( have_posts() ) :
    the_post();
      get_template_part( 'template-parts/content-diary' );
    endwhile; ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
