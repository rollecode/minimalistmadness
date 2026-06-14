<?php
/**
 * Year from now
 *
 * The part that displays a post one year from now.
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2021-11-11 12:59:35
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-12-07 19:09:09
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

?>

  <?php
  wp_reset_postdata();
  $args = array(
    'post_type'      => 'diary',
    'posts_per_page' => 1,
    'no_found_rows'  => true,
    'post_status'    => 'publish',
    'date_query'     => array(
      array(
        'year'  => get_the_time( 'Y' ) - 1,
        'month' => get_the_time( 'm' ),
        'day'   => get_the_time( 'd' ),
      ),
    ),
  );

  $year_since_now = new \WP_Query( $args ); ?>

  <?php if ( $year_since_now->have_posts() ) : ?>

    <article class="post-past post diary type-diary metadata">
      <?php while ( $year_since_now->have_posts() ) :
      $year_since_now->the_post();
      ?>

      <div class="post-past__inner">
        <h2 class="post-past__title meta-prefix">
          <?php esc_html_e( 'Vuosi sitten tänä päivänä', 'minimalistmadness' ); ?>
        </h2>

        <div class="post-past__content">
          <h3 class="post-past__content-title prefix">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h3>

          <p class="diary-title">
            <time datetime="<?php the_time( 'c' ); ?>"><?php echo esc_html( ucfirst( get_the_time( 'l' ) ) ); ?>na, <?php the_time( 'j.' ) ?> <?php the_time( 'F' ) ?>ta <?php the_time( 'Y' ) ?><br><span class="time">Kello on <?php the_time( 'H:i' ) ?></span></time>
          </p>

          <div class="post-past__content-excerpt">
            <?php the_content(); ?>
          </div>
        </div>
      </div>

      <?php
    endwhile; ?>

    </article>
  <?php endif;
  wp_reset_postdata();
