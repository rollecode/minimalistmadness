<?php
/**
 * Kaikki artikkelit.
 *
 * Template Name: Kaikki kirjoitukset
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

namespace Air_Light;

the_post();

get_header(); ?>

<div class="content-area">
  <main role="main" id="main" class="site-main">

    <section class="block block-page block-archive-all">

      <div class="article-content">

        <header class="post-head inverted archive-all">
          <h1 id="content" class="post-head-title"><svg aria-hidden="true" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 24 24"><path fill="currentColor" d="M11 2.206l-6.235 7.528-.765-.645 7.521-9 7.479 9-.764.646-6.236-7.53v21.884h-1v-21.883z"/></svg>Kaikki Rollemaan <?php echo esc_attr( wp_count_posts()->publish ); ?> kirjoitusta</h1>
        </header>

      <?php
      global $wpdb;

      $limit     = 0;
      $year_prev = null;

      $months = $wpdb->get_results( "SELECT DISTINCT MONTH( post_date ) AS month ,  YEAR( post_date ) AS year, COUNT( id ) as post_count FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month , year ORDER BY post_date DESC" ); // phpcs:ignore

      foreach ( $months as $month ) :
        $year_current = $month->year;

        if ( $year_current !== $year_prev ) {
          if ( null !== $year_prev ) { ?>
          <?php } ?>
            <h2 class="year"><?php echo esc_html( $month->year ); ?></h2>
          <?php } ?>

          <div class="posts-feed-all">
          <h3 class="month"><?php echo wp_kses_post( date_i18n( 'F', mktime( 0, 0, 0, $month->month, 1, $month->year ) ) ); ?></h3>

          <?php
          // WP_Query arguments
          $args = array(
            'year'           => $month->year,
            'monthnum'       => $month->month,
          	'posts_per_page' => '-1',
          );

          // The Query
          $query = new \WP_Query( $args );

          // The Loop
          if ( $query->have_posts() ) { ?>
          <ul class="post-feed-simplified is-style-no-bullets">
			      <?php while ( $query->have_posts() ) {
				      $query->the_post(); ?>
			          <li>
                  <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="global-link" aria-hidden="true" tabindex="-1"></a>
                  <p class="post-card-details">
                    <time datetime="<?php echo esc_html( get_the_time( 'c' ) ); ?>">
                      <?php echo esc_html( get_the_time( 'm' ) ); ?>/<?php echo esc_html( get_the_time( 'd' ) ); ?>
                    </time>
                  </p>
                  <h4 class="post-card-title">
                    <a href="<?php echo esc_url( get_the_permalink() ); ?>">
                      <?php echo esc_html( get_the_title() ); ?>
                    </a>
                  </h4>
                </li>
					    <?php }
            } ?>
          </ul>
          </div>

          <?php wp_reset_postdata(); ?>

        <?php $year_prev = $year_current;
          // if ( ++$limit >= 18 ) {
          //   break;
          // }

          endforeach;
        ?>

        <?php if ( get_edit_post_link() ) : ?>
          <footer class="entry-footer">
            <?php edit_post_link(
              sprintf(
                /* translators: %s: Name of current post. Only visible to screen readers */
                wp_kses(
                  __( 'Muokkaa <span class="screen-reader-text">%s</span>', 'minimalistmadness' ),
                  [
                    'span' => [
                      'class' => [],
                    ],
                  ]
                ),
                get_the_title()
              ),
              '<p class="edit-link">',
              '</p>'
            ); ?>
          </footer><!-- .entry-footer -->
        <?php endif; ?>

      </div>
    </section>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
