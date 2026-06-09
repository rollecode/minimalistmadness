<?php
/**
 * Template part for displaying the article hheader for single post.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

?>

<header class="hero-single" itemscope itemtype="http://schema.org/WPHeader">

  <div class="image image-background image-background-layer">
    <?php if ( has_post_thumbnail() ) { ?>
      <img src="<?php echo get_the_post_thumbnail_url( $post->ID, 'full' ); ?>" alt="<?php echo esc_attr( get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) ); ?>" loading="eager" />
    <?php } ?></div>

  <div class="shade" aria-hidden="true"></div>

  <div class="site-branding screen-reader-text">
    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span></a></p>
  </div><!-- .site-branding -->

  <div class="article-title-area">
    <h1 itemprop="headline" id="content"><?php echo esc_attr( get_the_title() ); ?></h1>
    <p itemprop="about" class="article-description">Kirjoittanut <span rel="author"><?php echo esc_attr( get_the_author_meta( 'first_name' ) ); ?> <?php echo esc_attr( get_the_author_meta( 'last_name' ) ); ?></span> <time datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'l' ) ?>na, <?php the_time( 'j.' ) ?> <?php the_time( 'F' ) ?>ta <?php the_time( 'Y' ) ?></time>. Liittyy aiheisiin <?php the_tags( '', ', ', '.' ); ?></p>
  </div>

<aside class="scroll-indicator">
  <a class="animated-mouse" href="#article-text-content" title="<?php echo esc_html_e( 'Hyppää sisältöön', 'minimalistmadness' ); ?>">
    <div class="animated-mouse-pointer"><p class="screen-reader-text"><?php echo esc_html_e( 'Hyppää sisältöön klikkaamalla tästä tai rullaa alaspäin', 'minimalistmadness' ); ?></p></div>
  </a>
  <a class="swiping-hand" href="#article-text-content" title="<?php echo esc_html_e( 'Hyppää sisältöön', 'minimalistmadness' ); ?>">
    <div class="hand-icon"><?php include get_theme_file_path( '/svg/swipe.svg' ); ?><p class="screen-reader-text"><?php echo esc_html_e( 'Hyppää sisältöön pyyhkimällä näyttöä sormella ylöspäin tai klikkaamalla tästä', 'minimalistmadness' ); ?></p></div>
  </a>
</aside>

</header>

<div class="nav-article-wrapper">
  <div class="container container-nav-article">
    <nav id="nav" class="nav-article">

      <?php wp_nav_menu( array(
        'theme_location'    => 'primary',
        'container'         => false,
        'depth'             => 4,
        'menu_class'        => 'menu-items',
        'menu_id'           => 'menu',
        'echo'              => true,
        'fallback_cb'       => 'wp_page_menu',
        'items_wrap'        => '<ul class="%2$s" id="%1$s">%3$s</ul>',
        'walker'            => new Nav_Walker(),
        ) ); ?>

    </nav><!-- #site-navigation -->
  </div>
</div>
