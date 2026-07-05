<?php
/**
 * Selected posts.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

// Fields (formerly an ACF option; empty by default)
$selected_posts = (array) get_option( 'rollemaa_selected_posts', [] );

$query = new \WP_Query(array(
  'post_type' => 'post',
  'posts_per_page'  => 1,
  'post__in' => $selected_posts,
  'post_status' => 'any',
  'orderby' => 'post__in',
));
?>

<?php if ( $selected_posts ) { ?>
<section class="block block-upsell-big">
  <div class="container">

    <header class="post-head inverted">
      <h2><span><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Nostettu artikkeli</span></h2>
    </header>

    <div class="post-feed">
      <?php while ( $query->have_posts() ) {
        $query->the_post();

        if ( has_excerpt() ) {
          $excerpt = wpautop( get_the_excerpt() ); // phpcs:ignore
        } else {
          $sentence = preg_match( '/^([^.!?]*[\.!?]+){0,2}/', strip_tags( get_the_content() ), $summary ); // phpcs:ignore
          $excerpt = wpautop( strip_shortcodes( $summary[0] ) ); // phpcs:ignore
        }
      ?>

      <article class="entry post-card post no-animation">
        <div class="post-card-content">
          <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="global-link" aria-hidden="true" tabindex="-1"></a>

          <div class="post-card-image"><div class="img"><p class="post-card-details"><time datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'j.' ); ?> <?php the_time( 'F' ); ?>ta <?php the_time( 'Y' ); ?></time><br /><?php echo khonsu_estimated_reading_time(); // phpcs:ignore ?></p><div class="image image-background image-background-layer"><?php if ( has_post_thumbnail($post->ID) ) { ?>
            <img src="<?php echo get_the_post_thumbnail_url( $post->ID, 'full' ); ?>" alt="<?php echo esc_attr( get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) ); ?>" loading="eager" />
          <?php } ?></div></div></div>

          <div class="post-card-information">
            <span class="featured-label global-tag"><svg role="img" width="12" height="12" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.9712403,8.05987765 L16.2291373,8.05987765 L12.796794,0.459688839 C12.5516266,-0.153229613 11.4483734,-0.153229613 11.0806223,0.459688839 L7.64827899,8.05987765 L0.906176009,8.05987765 C0.538424938,8.05987765 0.170673866,8.30504503 0.0480901758,8.6727961 C-0.0744935148,9.04054717 0.0480901758,9.40829825 0.293257557,9.65346563 L5.31918887,14.3116459 L3.11268244,22.4021694 C2.99009875,22.7699205 3.11268244,23.1376716 3.48043351,23.382839 C3.72560089,23.6280063 4.21593565,23.6280063 4.46110303,23.5054227 L11.9387082,19.2149935 L19.4163133,23.5054227 C19.538897,23.6280063 19.6614807,23.6280063 19.906648,23.6280063 C20.1518154,23.6280063 20.2743991,23.5054227 20.5195665,23.382839 C20.7647339,23.1376716 20.8873176,22.7699205 20.8873176,22.4021694 L18.6808111,14.3116459 L23.7067424,9.65346563 C23.9519098,9.40829825 24.0744935,9.04054717 23.9519098,8.6727961 C23.7067424,8.30504503 23.3389914,8.05987765 22.9712403,8.05987765 Z"></path></svg> Juuri nyt</span>
            <h2 class="post-card-title-large"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_attr( get_the_title() ); ?></a></h2>

            <?php echo $excerpt; // phpcs:ignore ?>
          </div>
        </div>
      </article>

    <?php } ?>
    </div>
  </div>
</section>
<?php }
wp_reset_postdata();
