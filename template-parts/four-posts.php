<?php
/**
 * Four posts.
 *
 * @package khonsu
 */

namespace Air_Light;

$selected_posts = (array) get_option( 'rollemaa_selected_posts', [] );
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => 6, // NB! When you change this, change also posts_per_page option
    'cache_results' => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,
    'no_found_rows' => true,
    'post_status' => 'publish',
    'post__not_in' => $selected_posts,
  );

  $query = new \WP_Query( $args );

  // No posts, bail
  if ( ! $query->have_posts() ) {
    return;
  }

  // Loop posts to make array
  $posts = array();

  while ( $query->have_posts() ) {
    $query->the_post();

    // Reading time
    $post = get_post();
    $words = str_word_count( strip_tags( $post->post_content ) ); // phpcs:ignore
    $minutes = floor( $words / 120 );
    $seconds = floor( $words % 120 / ( 120 / 60 ) );

    if ( 1 <= $minutes ) :
      if ( 1 === $minutes ) :
        $estimated_time = $minutes . ' min';
      else :
        $estimated_time = $minutes . ' min';
      endif;
    else :
      $estimated_time = 'Alle 1 min';
    endif;

    if ( has_excerpt() ) :
      $excerpt = wpautop( get_the_excerpt() ); // phpcs:ignore
    else :
      $sentence = preg_match( '/^([^.!?]*[\.!?]+){0,2}/', strip_tags( get_the_content() ), $summary ); // phpcs:ignore
      $excerpt = wpautop( strip_shortcodes( $summary[0] ) ); // phpcs:ignore
    endif;

    $post_reading_time = '<span class="time-to-read">' . $estimated_time . ' lukukokemus</span>';
    $post_time = '<time datetime="' . get_the_time( 'c' ) . '">' . get_the_time( 'j.' ) . ' ' . get_the_time( 'F' ) . 'ta ' . get_the_time( 'Y' ) . '</time>';
    $posts[] = array(
      'id'        => get_the_id(),
      'title'     => get_the_title(),
      'image'     => wp_get_attachment_url( get_post_thumbnail_id() ),
      'image_id'  => get_post_thumbnail_id(),
      'permalink' => get_the_permalink(),
      'post_time' => $post_time,
      'post_reading_time' => $post_reading_time,
      'excerpt'   => $excerpt,
    );
  }

  // Reset
  wp_reset_postdata();

  // Bail if no posts for some reason
  if ( empty( $posts ) ) {
    return;
  }
?>

<?php if ( ! empty( $posts ) ) : ?>
<section class="block block-four-posts block-loadable">

  <div class="container">

    <header class="post-head inverted">
      <h2><span><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Viimeisimmät</span></h2>
    </header>

    <div class="post-feed items-vue">
      <?php foreach ( $posts as $post ) : ?>

      <article class="entry post-card post no-animation">
        <div class="post-card-content">
          <a href="<?php echo esc_url( $post['permalink'] ); ?>" class="global-link" aria-hidden="true" tabindex="-1"><span class="screen-reader-text"><?php echo esc_attr( $post['title'] ); ?></span></a>

          <div class="post-card-image no-bottom-radius"><div class="img"><p class="post-card-details"><?php echo $post['post_time'];  // phpcs:ignore ?><br /><?php echo $post['post_reading_time'];  // phpcs:ignore ?></p><div class="image image-background image-background-layer"><?php if ( has_post_thumbnail( $post['id'] ) ) { ?>

            <img src="<?php echo get_the_post_thumbnail_url( $post['id'], 'large' ); ?>" alt="<?php echo esc_attr( get_post_meta( get_post_thumbnail_id( $post['id'] ), '_wp_attachment_image_alt', true ) ); ?>" loading="eager" />

            <?php } else { native_lazyload_tag( khonsu_get_random_image_id() ); } ?></div></div></div>

          <div class="post-card-information">
            <h2 class="post-card-title-large"><a href="<?php echo esc_url( $post['permalink'] ); ?>"><?php echo esc_attr( $post['title'] ); ?></a></h2>

            <?php echo $post['excerpt']; // phpcs:ignore ?>
          </div>

        </div>
      </article>

    <?php endforeach; ?>

    <article class="entry post-card post no-animation item-vue" v-for="(post, index) in posts" v-bind:id="'post-' + post.id">
      <div class="post-card-content">
        <a v-bind:href="post.link" class="global-link" v-bind:aria-label="post.title.rendered" aria-hidden="true" tabindex="-1"></a>

        <div class="post-card-image no-bottom-radius"><div class="img"><div class="post-card-details"><div v-html="post.time_custom">{{ post.time_custom }}</div><div v-html="post.reading_time_custom">{{ post.reading_time_custom }}</div></div><div class="background-image full-image" v-bind:style="{ backgroundImage: 'url(' + post.featured_image_custom + ')' }"></div></div></div>

        <div class="post-card-information">
          <h2 class="post-card-title-large"><a v-html="post.title.rendered" v-bind:href="post.link">{{ post.title.rendered }}</a></h2>

          <p>{{ post.excerpt }}</p>
        </div>

      </div>
    </article>

    </div>

    <div class="load-more-spinner" style="display: none;">
      <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
          <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
          </svg>
      </div>
    </div>

    <div class="load-more load-more-container" data-use-query="posts_query">
      <?php // Check if we should event show load more button in first place and save query to js variable for later use.
        if ( $query->found_posts !== $query->post_count ) : ?>

        <button class="button load-more">Lataa lisää</button>

        <?php endif;
          $query->query['paged'] = 1;
        ?>

      <script>
        var posts_query_original = <?php echo json_encode( $query->query ) // phpcs:ignore ?>;
        var posts_query = <?php echo json_encode( $query->query ) // phpcs:ignore ?>;
      </script>
    </div>

  </div>

</section>
<?php endif;
wp_reset_postdata();
