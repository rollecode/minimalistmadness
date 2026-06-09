<?php
/**
 * Random.
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

  <section class="block block-four-posts block-most-popular">
    <div class="container">

    <header class="post-head inverted">
      <h2><span><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Satunnaiset</span><button class="load-more-random"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M19.78 3h-8.56C10.55 3 10 3.55 10 4.22V8h6v6h3.78c.67 0 1.22-.55 1.22-1.22V4.22C21 3.55 20.45 3 19.78 3m-7.34 3.67a1.23 1.23 0 1 1 0-2.46 1.23 1.23 0 0 1 1.23 1.23c0 .68-.55 1.23-1.23 1.23m6.12 6.11a1.23 1.23 0 1 1-.02-2.46c.68-.01 1.23.54 1.24 1.24-.01.67-.55 1.21-1.22 1.22m0-6.11a1.23 1.23 0 1 1-.02-2.46 1.231 1.231 0 0 1 .02 2.46M4.22 10h8.56A1.22 1.22 0 0 1 14 11.22v8.56c0 .67-.55 1.22-1.22 1.22H4.22C3.55 21 3 20.45 3 19.78v-8.56c0-.67.55-1.22 1.22-1.22m4.28 4.28c-.67 0-1.22.55-1.22 1.22 0 .67.55 1.22 1.22 1.22.67 0 1.22-.55 1.22-1.22a1.22 1.22 0 0 0-1.22-1.22m-3.06-3.06c-.67 0-1.22.55-1.22 1.22a1.22 1.22 0 0 0 1.22 1.22c.67 0 1.22-.55 1.22-1.22 0-.67-.55-1.22-1.22-1.22m6.11 6.11c-.67 0-1.22.55-1.22 1.22 0 .67.55 1.22 1.22 1.22a1.22 1.22 0 0 0 1.22-1.22c0-.67-.54-1.21-1.21-1.22h-.01z"/></svg>Arvo lisää</button></h2>
    </header>

      <div class="post-feed dynamic-content">
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
      </div>

    </div>
  </section>
  <?php
endif;
wp_reset_postdata();
