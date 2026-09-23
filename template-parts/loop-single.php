<?php
/**
 * Main content for the single view. Included by both the classic
 * single.php template and the minimalistmadness/loop-single block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php
    // On the block template path core has already consumed the singular
    // main loop (get_the_block_template_html wraps do_blocks in the loop),
    // so rewind before iterating; harmless on the classic path.
    rewind_posts();
    while ( have_posts() ) :
    the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php get_template_part( 'template-parts/hero-single' ); ?>

      <div class="container container-article" id="article-text-content">
        <article class="entry-content article-content">


          <?php
          $post_year = get_the_time( 'Y' );
          $now_year = date( 'Y' );
          ?>

          <?php if ( $post_year <= $now_year - 2 ) : ?>
            <div class="notification-box old">
              <svg xmlns="http://www.w3.org/2000/svg" width="80" height="100" fill="currentColor" viewBox="0 0 64 80"><path d="M33 9.021V8a1 1 0 0 0-2 0v1.022C17.677 9.464 7 18.702 7 30a1 1 0 0 0 1 1h.349c.423 0 .801-.267.943-.666A5.006 5.006 0 0 1 14 27c1.678 0 3.235.837 4.167 2.24.371.559 1.295.559 1.666 0A4.996 4.996 0 0 1 24 27a5.005 5.005 0 0 1 4.708 3.333c.142.4.52.667.943.667H31v21c0 1.654-1.346 3-3 3s-3-1.346-3-3a1 1 0 1 0-2 0c0 2.757 2.243 5 5 5s5-2.243 5-5V31h1.349a.999.999 0 0 0 .942-.666A5.009 5.009 0 0 1 40 27c1.678 0 3.236.837 4.167 2.24.371.558 1.295.558 1.666 0A4.994 4.994 0 0 1 50 27c2.11 0 4.002 1.34 4.709 3.333a1 1 0 0 0 .942.667H56a1 1 0 0 0 1-1C57 18.702 46.322 9.464 33 9.021z"/></svg>
              <h2>Arkistomatskua</h2>
              <p>Otathan huomioon, että tämä on yli <b><?php echo esc_attr( $now_year ) - esc_attr( $post_year ); ?> vuotta vanha</b> artikkeli, joten sisältö ei ole välttämättä ihan ajan tasalla. Olin artikkelin kirjoittamishetkellä <?php $post_year = get_the_time( 'Y' ); $age = $post_year - 1988; echo esc_attr( $age ); ?>-vuotias.</p>
            </div>
          <?php endif; ?>

          <?php
          the_content( sprintf(
            wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'minimalistmadness' ), array( 'span' => array( 'class' => array() ) ) ),
            the_title( '<span class="screen-reader-text">"', '"</span>', false )
          ) );

          wp_link_pages( array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'minimalistmadness' ),
            'after'  => '</div>',
          ) );
          ?>

          <?php if ( is_singular( 'post' ) && function_exists( 'do_blocks' ) ) {
            echo do_blocks('<!-- wp:activitypub/reactions /-->');
          } ?>

          <?php if ( get_edit_post_link() ) { ?>
            <footer class="entry-footer">
              <?php edit_post_link(
                sprintf(
                  /* translators: %s: Name of current post. Only visible to screen readers */
                  wp_kses(
                    __( 'Muokkaa <span class="screen-reader-text">%s</span>', 'minimalistmadness' ),
                    array(
                      'span' => array(
                        'class' => array(),
                      ),
                    )
                  ),
                  get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
              ); ?>
            </footer><!-- .entry-footer -->
          <?php } ?>

          <div class="author-info">
            <div class="author-col author-col-avatar">
              <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/author-info.jpg" alt="Kuva Rolle Laukkarisesta" />
            </div>

            <div class="author-col author-col-info">
              <h3>Rolle Laukkarinen</h3>
              <p class="description">Kirjoittaja on <?php echo esc_attr( khonsu_calculate_age( '1988/11/01' ) ); ?>-vuotias elämäntapanörtti, ammatiltaan yrittäjä ja teknologiajohtaja perustamassaan <a href="https://www.dude.fi" class="author-link no-external-link-indicator">digitoimistossa</a>, verkkosivujen tekijä, koukussa kirjoittamiseen 5-vuotiaasta. Päivät kuluu <a href="https://twitter.com/streetlazer" class="author-link no-external-link-indicator">monipuolisen</a> <a href="https://www.last.fm/user/rolle-" class="author-link no-external-link-indicator">musiikkiharrastuksen</a>, retropelien ja koodaamisen parissa, mutta arkea piristyttää myös vaimo ja kaksi lasta. <a href="https://mementomori.social/@rolle" rel="me" class="author-link no-external-link-indicator">Mastodon</a> ja <a href="https://www.rollekino.fi" class="author-link no-external-link-indicator">leffat</a> lähellä sydäntä.</p>

              <p class="button-paragraph"><a class="button" href="<?php echo esc_url( get_page_link( 2768 ) ); ?>">Lue Rollesta lisää</a></p>
            </div>
          </div>
        </article><!-- .entry-content -->

      </div><!-- .container-article -->

      <?php the_related_posts(); ?>

      <?php
      // If comments are open or we have at least one comment, load up the comment template.
      if ( comments_open() || get_comments_number() ) :
        comments_template();
      endif; ?>

      </article><!-- #post-## -->

    <?php endwhile; ?>

  </main><!-- #main -->
</div><!-- #primary -->

