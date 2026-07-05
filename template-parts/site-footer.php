<?php
/**
 * Site footer: social links, credits, dark mode toggle. Included by the
 * classic footer.php and the minimalistmadness/site-footer block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>
<footer role="contentinfo" id="colophon" class="site-footer">

    <div class="container">
      <div class="footer-space">
        <div class="footer-choices">
          <ul class="social-media">
            <li><a class="no-external-link-indicator" href="https://twitter.com/rolle"><span class="screen-reader-text">Twitter</span><?php include get_theme_file_path( '/svg/twitter.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://github.com/ronilaukkarinen"><span class="screen-reader-text">GitHub</span><?php include get_theme_file_path( '/svg/github.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://www.instagram.com/rolle_"><span class="screen-reader-text">Twitter</span><?php include get_theme_file_path( '/svg/instagram.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://www.last.fm/user/rolle-"><span class="screen-reader-text">Last.fm</span><?php include get_theme_file_path( '/svg/lastfm.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://untappd.com/user/rolle"><span class="screen-reader-text">Untappd</span><?php include get_theme_file_path( '/svg/untappd.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://retroachievements.org/user/rolle"><span class="screen-reader-text">Retro Achievements</span><?php include get_theme_file_path( '/svg/joystick.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://www.overbuff.com/players/pc/Qllervo-2545"><span class="screen-reader-text">Overwatch</span><?php include get_theme_file_path( '/svg/overwatch.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://rawg.io/@rolle"><span class="screen-reader-text">Rawg.io</span><?php include get_theme_file_path( '/svg/rawg.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="http://trakt.tv/users/rolle"><span class="screen-reader-text">Trakt</span><?php include get_theme_file_path( '/svg/trakt.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://keybase.io/ronilaukkarinen"><span class="screen-reader-text">Keybase</span><?php include get_theme_file_path( '/svg/keybase.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://profiles.wordpress.org/rolle"><span class="screen-reader-text">WordPress</span><?php include get_theme_file_path( '/svg/wordpress.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://www.linkedin.com/in/rolaukka/"><span class="screen-reader-text">WordPress</span><?php include get_theme_file_path( '/svg/linkedin.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://www.strava.com/athletes/16582440"><span class="screen-reader-text">Strava</span><?php include get_theme_file_path( '/svg/strava.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" href="https://goodreads.com/rolle"><span class="screen-reader-text">Goodreads</span><?php include get_theme_file_path( '/svg/goodreads.svg' ); ?></a></li>
            <li><a class="no-external-link-indicator" rel="me" href="https://mementomori.social/@rolle"><span class="screen-reader-text">Mastodon</span><?php include get_theme_file_path( '/svg/mastodon.svg' ); ?></a></li>
          </ul>
        </div>

        <div class="footer-credits">
          <p>Oikeudet omistaa Rolle Laukkarinen, 1999-<?php echo esc_attr( date( 'Y' ) ); ?>.</p>

          <div role="radiogroup" aria-label="Valitse väriteema" id="dark-mode-footer-toggle" class="dark-mode-footer-toggle">

            <input id="color-scheme-toggle-light" type="radio" name="colorModeToggle" value="light">
            <label tabindex="-1" for="color-scheme-toggle-light" class="dark-mode-footer-label">Vaalea</label>

            <input id="color-scheme-toggle-dark" type="radio" name="colorModeToggle" value="dark">
            <label tabindex="-1" for="color-scheme-toggle-dark" class="dark-mode-footer-label">Tumma</label>

            <input id="color-scheme-toggle-auto" type="radio" name="colorModeToggle" value="auto" checked>
            <label tabindex="0" for="color-scheme-toggle-auto" class="dark-mode-footer-label">Automaattinen</label>

          </div>

        </div>
      </div>
    </div>

	</footer><!-- #colophon -->
