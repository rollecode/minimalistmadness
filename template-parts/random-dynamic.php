<?php
/**
 * Random.
 *
 * Dynamic version that loads up via JS.
 *
 * @package khonsu
 */

namespace Air_Light;

$args = array(
  'post_type' => 'post',
  'posts_per_page' => 4,
  'orderby' => 'rand',
  'post_status' => 'publish',
  'no_found_rows' => true,

  // Ignore kuulumiset, elämä
  'tag__not_in' => '79, 7',

  // Don't show too old posts
  'date_query' => array(
    'after' => '2007-01-01',
    'inclusive' => true,
  ),

  // Show only posts with featured image
  // 'meta_query' => array(
    // array(
      // 'key' => '_thumbnail_id',
      // 'compare' => 'EXISTS',
   // ),
  // ),
);

$query = new \WP_Query( $args );
if ( $query->have_posts() ) : ?>

  <?php
  while ( $query->have_posts() ) :
    $query->the_post();
    ?>

    <article class="entry post-card post">
      <div class="post-card-content">
        <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="global-link" aria-hidden="true" tabindex="-1"><span class="screen-reader-text"><?php echo esc_attr( get_the_title() ); ?></span></a>

        <h2 class="post-card-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_attr( get_the_title() ); ?></a></h2>
        <div class="post-card-image"><div class="img"><p class="post-card-details"><time datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'j.' ); ?> <?php the_time( 'F' ); ?>ta <?php the_time( 'Y' ); ?></time><br /><?php echo khonsu_estimated_reading_time(); // phpcs:ignore ?></p><div class="image image-background image-background-layer"><?php if ( has_post_thumbnail( $post->ID ) ) { native_lazyload_tag( get_post_thumbnail_id( $post->ID, 'large' ), [ 'sizes' => [ 'big' => 'large' ] ] ); } else { native_lazyload_tag( khonsu_get_random_image_id() ); } ?></div></div></div>
      </div>
    </article>

  <?php endwhile; ?>

  <?php
endif;
wp_reset_postdata();
