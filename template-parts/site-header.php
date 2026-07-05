<?php
/**
 * Site head shell: rain effect, header with navigation, search overlay.
 * Included by the classic header.php and the minimalistmadness/site-header
 * block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>
<div class="rain-wrapper">
      <div class="rain front-row"></div>
      <div class="rain back-row"></div>
    </div>

    <header class="site-head">
      <div class="site-head-container">
        <a class="nav-burger" href="#">
          <div class="hamburger hamburger--collapse" aria-label="Menu" role="button" aria-controls="navigation">
            <div class="hamburger-box">
              <div class="hamburger-inner"></div>
            </div>
          </div>
        </a>

        <nav class="site-head-left" role="navigation">
          <?php wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'depth'          => 4,
            'menu_class'     => 'nav menu-items',
            'menu_id'        => 'main-menu',
            'echo'           => true,
            'fallback_cb'    => __NAMESPACE__ . '\Nav_Walker::fallback',
            'items_wrap'     => '<ul class="%2$s" role="menu">%3$s</ul>',
            'walker'         => new Nav_Walker(),
          ) ); ?>
        </nav><!-- #nav -->

        <div class="site-head-center">
          <a class="site-head-logo" href="<?php echo esc_url( get_home_url() ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g data-name="Layer 2"><g data-name="umbrella"><rect width="24" height="24" opacity="0"/><path fill="currentColor" d="M12 2A10 10 0 0 0 2 12a1 1 0 0 0 1 1h8v6a3 3 0 0 0 6 0 1 1 0 0 0-2 0 1 1 0 0 1-2 0v-6h8a1 1 0 0 0 1-1A10 10 0 0 0 12 2zm-7.94 9a8 8 0 0 1 15.88 0z"/></g></g></svg>Rollemaa</a>
        </div>
        <div class="site-head-right">
          <button class="search-trigger" type="button"><?php include get_theme_file_path( 'svg/search.svg' ); // phpcs:ignore ?><span>Haku</span></button>
        </div>
      </div>
    </header>

  <div class="overlay overlay-search">
    <div class="container">

      <div class="search-form">
        <button type="button" class="search-icon" aria-label="toggle search">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="375.045 607.885 30.959 30.33"><path fill="currentColor" d="M405.047 633.805l-7.007-6.542a3.041 3.041 0 0 0-.408-.319 12.236 12.236 0 0 0 2.025-6.753c0-6.796-5.51-12.306-12.307-12.306s-12.306 5.51-12.306 12.306 5.509 12.306 12.306 12.306c2.565 0 4.945-.786 6.916-2.128.122.172.257.337.418.488l7.006 6.542c1.122 1.048 2.783 1.093 3.709.101.928-.993.77-2.647-.352-3.695zm-17.696-4.754a8.86 8.86 0 1 1 0-17.72 8.86 8.86 0 0 1 0 17.72z"></path></svg>
          <span class="tcon-visuallyhidden">Toggle search</span>
        </button>

        <input id="search-input" aria-label="Hae kirjoituksia" type="search" name="search" class="search search-input" placeholder="Hae kirjoituksia">

        <button class="button button-close"><?php include get_theme_file_path( '/svg/window-close.svg' ); // phpcs:ignore ?> <span>Sulje</span></button>
      </div>

      <ul id="search-results" class="search-results"></ul>
    </div>
  </div>
