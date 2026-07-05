<?php
/**
 * Main loop for the archive view. Included by both the classic archive.php
 * template and the minimalistmadness/loop-archive block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<div class="content-area">
	<main role="main" id="main" class="site-main block block-page">

    <div class="container container-archive container-article">
      <div class="article">

      <?php if ( have_posts() ) :
        $count = 0; ?>

          <?php if ( is_tag() ) : ?>
            <div class="notification-box old">
              <h2 id="content">Arkisto avainsanalle &quot;<?php echo single_tag_title( '', false ); ?>&quot;</h2>

              <p>Avainsanalla &quot;<?php echo single_tag_title( '', false ); ?>&quot; tägättyjä artikkeleja Rollemaassa on tällä hetkellä yhteensä huimat <?php $tag = $wp_query->get_queried_object(); echo esc_attr( $tag->count ); ?> kpl.</strong></p>
            </div>
          <?php endif; ?>

          <?php if ( is_category() ) : ?>

            <div class="notification-box old">
              <h2 id="content">Kirjoitukset kategoriassa &quot;<?php echo single_cat_title(); ?>&quot;</h2>
              <p>Aihealueeseen &quot;<?php echo single_cat_title(); ?>&quot; liittyvää on kirjoiteltu yhteensä <?php $cat = get_the_category(); $cat = $cat[0]; echo esc_attr( $cat->category_count ); ?> kpl.</p>
            </div>

          <?php elseif ( is_day() ) : ?>

            <?php
            $current_month = get_the_time( 'm' );
            $current_year = get_the_time( 'Y' );
            $current_day = get_the_time( 'j' );
            $countposts = get_posts( 'year=$current_year&monthnum=$current_month&day=$current_day' );
            ?>

            <div class="notification-box old">
              <h2 id="content"><?php echo esc_attr( ucfirst( get_the_time( 'l' ) ) ); ?>, <?php the_time( 'j.' ); ?> <?php the_time( 'F' ); ?>ta <?php the_time( 'Y' ); ?></h2>
              <p>Kaikki <?php echo esc_attr( ucfirst( get_the_time( 'l' ) ) ); ?>na, <?php the_time( 'j.' ); ?> <?php the_time( 'F' ); ?>ta <?php the_time( 'Y' ); ?> julkaisemani kirjoitukset näet tällä sivulla. Kirjoituksia kertyi yhteensä <?php echo count( $countposts ); ?>.</p>
            </div>

          <?php elseif ( is_month() ) : ?>

            <?php
            $current_month = get_the_time( 'm' );
            $current_year = get_the_time( 'Y' );
            $countposts = get_posts( 'year=$current_year&monthnum=$current_month' );
            ?>

            <div class="notification-box old">
              <h2 id="content"><?php echo esc_attr( ucfirst( get_the_time( 'F' ) ) ); ?>, <?php the_time( 'Y' ); ?></h2>
              <p><?php echo esc_attr( ucfirst( get_the_time( 'F' ) ) ); ?>ssa vuonna <?php the_time( 'Y' ); ?> naputtelin kirjoituksia yhteensä huimat <?php echo count( $countposts ); ?> kpl. Tällä sivulla näet ne kaikki kätevästi listattuna.</p>
            </div>

          <?php elseif ( is_year() ) : ?>

            <div class="notification-box old">
              <h2 id="content">Anno domini <?php the_time( 'Y' ); ?></h2>
              <p>Herran vuonna <?php the_time( 'Y' ); ?> kirjoitetut artikkelit.</p>
            </div>

          <?php elseif ( is_search() ) : ?>

            <div class="notification-box old">
              <h1 id="content">Hakutulokset</h1>
              <h3><?php $cat = get_the_category();
              $cat = $cat[0];
              echo esc_attr( $cat->category_count ); ?> löytyi!</h3>
            </div>

          <?php elseif ( is_author() ) : ?>
            <br>
          <?php endif; ?>        

        <?php while ( have_posts() ) :
          the_post();
          get_template_part( 'template-parts/content' );
        endwhile;

        khonsu_pagination();

        else :
          get_template_part( 'template-parts/content', 'none' );
        endif; ?>

      </div>
    </div><!-- .container -->

  </main><!-- #main -->
</div><!-- #primary -->

