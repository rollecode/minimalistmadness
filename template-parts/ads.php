<?php
/**
 * Ads
 *
 * Ads for the middle section of the page
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2021-08-10 09:32:58
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-04-06 16:19:42
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

if ( is_home() && ! is_paged() || is_front_page() && ! is_paged() ) : ?>

<div id="placement">
<?php $args = array(
  'post_type' => 'ads',
  'posts_per_page' => 1,
  'no_found_rows' => true,
  'post_status' => 'publish',
);

$ad = new WP_Query( $args );
if ( $ad->have_posts() ) :
while ( $ad->have_posts() ) :
$ad->the_post();

if ( 'etusivu' === get_post_meta( get_the_ID(), 'slotti', true ) ) :
if ( strtotime( get_post_meta( get_the_ID(), 'eraantymispaiva', true ) ) > time() ) : ?>

  <div class="advertisement ad advert textad">
    <div class="ad-top ad textad">
      <?php echo get_post_meta( get_the_ID(), 'mainoskoodi', true ); // phpcs:ignore ?>
    </div><!-- .textad -->
  </div><!-- .ad -->

<?php
endif;
endif;
endwhile;
endif; ?>
</div>
<?php endif;
