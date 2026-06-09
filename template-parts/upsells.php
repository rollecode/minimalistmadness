<?php
/**
 * Selected posts.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

// Fields
$selected_posts = get_field( 'selected_posts', 'option' );
?>

<?php if ( $selected_posts ) : ?>

  <section class="block block-four-posts block-upsells">
    <div class="container">

      <header class="post-head">
        <h2><span><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Valitsemiani juttuja juuri sinulle</span></h2>
      </header>

      <div class="post-feed">
        <?php foreach ( $selected_posts as $selected_post ) :

          $words = str_word_count( strip_tags( $selected_post->post_content ) ); // phpcs:ignore
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

          $reading_time = '<span class="time-to-read">' . $estimated_time . ' lukukokemus</span>';
          ?>

          <article class="entry post-card post">
            <div class="post-card-content">
              <a href="<?php echo esc_url( get_the_permalink( $selected_post->ID ) ); ?>" class="global-link" aria-hidden="true" tabindex="-1"></a>

              <h2 class="post-card-title"><a href="<?php echo esc_url( get_the_permalink( $selected_post->ID ) ); ?>"><?php echo esc_attr( get_the_title( $selected_post->ID ) ); ?></a></h2>

              <div class="post-card-image"><div class="img"><p class="post-card-details"><time datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'j.' ); ?> <?php the_time( 'F' ); ?>ta <?php the_time( 'Y' ); ?></time><br /><?php echo esc_html( $reading_time ); ?></p><div class="image image-background image-background-layer"><?php if ( has_post_thumbnail( $selected_post->ID ) ) { native_lazyload_tag( get_post_thumbnail_id( $selected_post->ID, 'large' ), [ 'sizes' => [ 'big' => 'large' ] ] ); } ?></div></div></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

<?php endif; ?>
