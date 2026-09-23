<?php
/**
 * Related posts under an article.
 *
 * @package minimalistmadness
 */

if ( ! empty( $related_posts ) ) :

function khonsu_get_random_image_id() {
  $query = get_posts(
    array(
      'post_status'     => 'inherit',
      'post_type'       => 'attachment',
      'post_mime_type'  => 'image/jpeg,image/gif,image/jpg,image/png',
      'posts_per_page'  => 1,
      'category_name'   => 'kuvituskuva',
      'no_found_rows'   => true,
      'orderby'         => 'rand',
    )
  );

  if ( ! empty( $query ) ) {
    $return = '';

    foreach ( $query as $attachment ) {
      $return = $attachment->ID;
    }
  }
  return $return;
}

/**
 * Image lazyload helpers.
 *
 * @Author:             Timi Wahalahti, Digitoimisto Dude Oy (https://dude.fi)
 * @Date:               2019-08-07 14:38:34
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2020-04-03 11:45:47
 *
 * @package air-helper
 */

/**
 * Echo image in lazyloading divs.
 *
 * @param  integer $image_id Image attachment id to lazyload.
 * @param  array   $sizes    Custom sizes for lazyloading. Optional.
 * @since  1.11.0
 */
if ( ! function_exists( 'image_lazyload_div' ) ) {
  function image_lazyload_div( $image_id = 0, $sizes = [] ) {
    echo get_image_lazyload_div( $image_id, $sizes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  } // end image_lazyload_div
} // end if

/**
 * Get image lazyloading divs.
 *
 * @param  integer $image_id Image attachment id to lazyload.
 * @param  array   $sizes    Custom sizes for lazyloading. Optional.
 * @return string            String containing lazyloading divs.
 * @since  1.11.0
 */
if ( ! function_exists( 'get_image_lazyload_div' ) ) {
  function get_image_lazyload_div( $image_id = 0, $sizes = [] ) {
    // Get image
    $image_urls = air_helper_get_image_lazyload_sizes( $image_id, $sizes );

    // Check if we have image
    if ( ! $image_urls || ! is_array( $image_urls ) ) {
      return;
    }

    // Do preg match and check if we need to do browser hack
    $browser_hack = false;
    if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
      if ( preg_match( '/Windows Phone|Lumia|iPad|Safari/i', sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) ) {
        $browser_hack = true;
      }
    }

    ob_start();

    // Div for preview image and data for js to use ?>
    <div class="background-image preview lazyload"
      style="background-image: url('<?php echo esc_url( $image_urls['tiny'] ); ?>');"
      data-src="<?php echo esc_url( $image_urls['big'] ); ?>"
      data-src-mobile="<?php echo esc_url( $image_urls['mobile'] ); ?>"></div>

    <?php // Div for full image, hack for browsers that don't support our js well ?>
    <div class="background-image full-image"
      <?php if ( $browser_hack ) : ?>
        style="background-image: url('<?php echo esc_url( $image_urls['big'] ); ?>');"
      <?php endif; ?>></div>

    <?php // Div with full image for browsers without js ?>
    <noscript><div class="background-image full-image" style="background-image: url('<?php echo esc_url( $image_urls['big'] ); ?>');"></div></noscript>

    <?php

    return ob_get_clean();
  } // end get_image_lazyload_div
} // end if

/**
 * Echo image in lazyloading tag.
 *
 * @param  integer $image_id Image attachment id to lazyload.
 * @param  array   $sizes    Custom sizes for lazyloading. Optional.
 * @since  1.11.0
 */
if ( ! function_exists( 'image_lazyload_tag' ) ) {
  function image_lazyload_tag( $image_id = 0, $sizes = [] ) {
    echo get_image_lazyload_tag( $image_id, $sizes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  } // end image_lazyload_tag
} // end if

/**
 * Get image lazyloading tag.
 *
 * @param  integer $image_id Image attachment id to lazyload.
 * @param  array   $sizes    Custom sizes for lazyloading. Optional.
 * @return string            String containing lazyloading divs.
 * @since  1.11.0
 */
if ( ! function_exists( 'get_image_lazyload_tag' ) ) {
  function get_image_lazyload_tag( $image_id = 0, $sizes = [] ) {
    // Get image
    $image_urls = air_helper_get_image_lazyload_sizes( $image_id, $sizes );

    // Check if we have image
    if ( ! $image_urls || ! is_array( $image_urls ) ) {
      return;
    }

    // Get dimensions
    $dimensions = air_helper_get_image_lazyload_dimensions( $image_id, $sizes );

    if ( ! $dimensions ) {
      return;
    }

    // Get the img tag
    ob_start(); ?>
    <img class="lazyload"
      src="<?php echo esc_url( $image_urls['tiny'] ); ?>"
      data-src="<?php echo esc_url( $image_urls['big'] ); ?>"
      data-src-mobile="<?php echo esc_url( $image_urls['mobile'] ); ?>"
      width="<?php echo esc_attr( $dimensions['width'] ); ?>" height="<?php echo esc_attr( $dimensions['height'] ); ?>" />
    <?php

    return ob_get_clean();
  } // end get_image_lazyload_tag
} // end if
?>

  <section class="block block-four-posts block-most-popular block-related">
    <div class="container">

    <header class="post-head inverted">
      <h2><span><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Lisää luettavaa</span></h2>
    </header>

      <div class="post-feed">
        <?php array_walk(
          $related_posts,
          function( $related_post_id ) {
          ?>

          <article class="entry post-card post">
            <div class="post-card-content">
              <a href="<?php echo esc_url( get_permalink( $related_post_id ) ); ?>" class="global-link" aria-hidden="true" tabindex="-1"><span class="screen-reader-text"><?php echo esc_attr( get_the_title( $related_post_id ) ); ?></span></a>

            <h2 class="post-card-title"><a href="<?php echo esc_url( get_permalink( $related_post_id ) ); ?>"><?php echo esc_attr( get_the_title( $related_post_id ) ); ?></a></h2>
            <div class="post-card-image"><div class="img"><p class="post-card-details"><time datetime="<?php echo esc_attr( get_the_time( 'c' ) ); ?>"><?php echo esc_attr( get_the_time( 'j.', $related_post_id ) ); ?> <?php echo esc_attr( get_the_time( 'F', $related_post_id ) ); ?>ta <?php echo esc_attr( get_the_time( 'Y', $related_post_id ) ); ?></time></p><div class="image image-background image-background-layer"><?php if ( has_post_thumbnail( $related_post_id ) ) { native_lazyload_tag( get_post_thumbnail_id( $related_post_id, 'large' ), [ 'sizes' => [ 'big' => 'large' ] ] ); } else { native_lazyload_tag( khonsu_get_random_image_id() ); } ?></div></div></div>
            </div>
          </article>

        <?php }); ?>
      </div>

    </div>
  </section>

<?php endif;
