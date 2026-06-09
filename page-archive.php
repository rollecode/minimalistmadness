<?php
/**
 * Arkisto.
 *
 * Template Name: Arkisto
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

namespace Air_Light;

the_post();

get_header(); ?>

<div class="content-area template-arkisto">
  <main role="main" id="main" class="site-main">

    <section class="block block-page">
      <div class="article-content">

          <header class="entry-header">
            <h1 class="entry-title">Arkisto</h1>
          </header><!-- .entry-header -->

            <h2 id="content">Historian lehtien havinaa</h2>
            <p>Aika kuluu. Olin joskus nuorempi. Täällä ne kuitenkin ovat, kaikki merkinnät, jotka olen saanut pelastettua, vuodesta 2005 eteenpäin. Vanhempiakin oli jossain vaiheessa näkyvillä, mutta niitä on tuhoutunut ja suurimman osan vedin pois netistä niiden sisällöttömyyden ja naiiviuden vuoksi.</p>

            <h2 class="title-grouped">Avainsanapilvi</h2>
            <h4>Avainsanoja käytetty yhteensä
              <?php $tags = get_tags();
              echo count( $tags ); ?>. Alla käytetyimmät.</h4>

              <div class="tag-cloud">
                <ul class="is-style-no-bullets">
                  <?php
                  foreach ( $tags as $key => $tag ) :
                    if ( 'twitter-tilapäivitys' !== $tag->name ) :
                      if ( $tag->count > 20 ) :
                        echo '<li><a href="' . esc_url( get_home_url() ) . '/avainsana/' . esc_attr( $tag->slug ) . '" title="' . esc_attr( $tag->name ) . ' - käytetty ' . esc_attr( $tag->count ) . ' kertaa!" style="font-size:';

                        if ( $tag->count > 100 ) :
                          echo esc_attr( $tag->count ) / 1.2;
                        else :
                          echo esc_attr( $tag->count ) * 3;
                        endif;

                        echo '%';
                        echo ' !important;"> ' . esc_attr( $tag->name ) . '</a></li>';
                      endif;
                    endif;
                  endforeach;
                  ?>
                </ul>
              </div>

              <h2 id="kohut">30 kaikkien aikojen luetuinta kirjoitusta</h2>

              <?php
              if ( function_exists( 'get_most_popular_posts' ) ) :
                $query = get_most_popular_posts( 'alltime', array(
                  'posts_per_page' => 30,
                ) );

                if ( $query->have_posts() ) : ?>
                <ul>
                  <?php while ( $query->have_posts() ) :
                  $query->the_post(); ?>
                  <li><a href="<?php echo esc_url( get_the_permalink() ); ?>" rel="bookmark"><?php echo esc_attr( get_the_title() ); ?> (<?php echo esc_attr( get_post_read_count( get_the_id(), 'alltime' ) ); ?>)</a></li>
                <?php endwhile; ?>
              </ul>
            <?php endif;
            wp_reset_postdata();
            endif; ?>

            <h2>30 kaikkien aikojen kommentoiduinta kirjoitusta</h2>

            <ul>
              <?php
              query_posts( 'orderby=comment_count&posts_per_page=30' );

              if ( have_posts() ) :
                while ( have_posts() ) :
                  the_post();
                  ?>
                  <li><a href="<?php the_permalink() ?>" title="Permanent Link to: <?php the_title_attribute(); ?>"><?php the_title(); ?></a> <?php echo '(' . esc_attr( get_comments_number() ) . ')'; ?></li>

                  <?php
                endwhile;
              endif;
              wp_reset_query();
              ?>
            </ul>

            <div id="spawn-slot"></div>

            <h2>Täydellinen blogiarkisto</h2>

            <p>Rollemaa sisältää yhteensä <?php echo esc_attr( wp_count_posts()->publish ); ?> kirjoitusta, joka pitää sisällään yhteensä <?php echo esc_attr( get_word_count_from_posts() ); ?> sanaa. Voit selata listausta <a href="<?php echo esc_url( get_page_link( 8609 ) ); ?>">omalta sivultaan</a>, mutta älä sano etten varoittanut!</p>

          <?php if ( get_edit_post_link() ) { ?>
          <footer class="entry-footer">
            <?php edit_post_link(
              sprintf(
                /* translators: %s: Name of current post */
                esc_html__( 'Muokkaa %s', 'minimalistmadness' ),
                the_title( '<span class="screen-reader-text">"', '"</span>', false )
              ),
              '<span class="edit-link">',
              '</span>'
              ); ?>
            </footer><!-- .entry-footer -->
            <?php } ?>
        </div>

    </section>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
