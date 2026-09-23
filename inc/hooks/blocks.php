<?php
/**
 * Theme blocks and the head/body scripts shared by classic and block
 * template rendering paths.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * Register the theme's dynamic blocks: every block.json under blocks/,
 * server rendered, no build step.
 */
function register_theme_blocks() {
  foreach ( glob( get_theme_file_path( 'blocks/*/block.json' ) ) as $block ) {
    register_block_type( $block );
  }
}

/**
 * Apply the dark/light theme class before first paint to avoid a flash of
 * the wrong theme. Printed at the very top of <head> on both rendering
 * paths (classic header.php and the block template canvas).
 */
function print_theme_prepaint_script() {
  ?>
  <script>
    (function () {
      var theme = localStorage.getItem('theme');
      if (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        theme = 'theme-dark';
      }
      document.documentElement.className = 'theme-dark' === theme ? 'theme-dark' : 'theme-light';
    })();
  </script>
  <script defer data-domain="rollemaa.fi" src="https://analytics.dude.fi/js/plausible.js"></script>
  <?php
}

/**
 * Dark/light toggle helpers, bound to the footer radio group. Printed right
 * after <body> opens on both rendering paths.
 */
function print_theme_toggle_script() {
  ?>
  <script>
    function setTheme(themeName) {
      localStorage.setItem('theme', themeName);
      document.documentElement.className = themeName;
    }

    function toggleTheme() {
      if (localStorage.getItem('theme') === 'theme-dark') {
        setTheme('theme-light');
      } else {
        setTheme('theme-dark');
      }
    }

    function themeSetup() {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('theme') !== 'theme-light') {
        setTheme('theme-dark');
      }

      if (localStorage.getItem('theme') === 'theme-dark') {
        setTheme('theme-dark');
        document.getElementById('color-scheme-toggle-dark').checked = true;
        document.getElementById('color-scheme-toggle-light').checked = false;
        document.getElementById('color-scheme-toggle-auto').checked = false;
      } else {
        setTheme('theme-light');
        document.getElementById('color-scheme-toggle-dark').checked = false;
        document.getElementById('color-scheme-toggle-light').checked = true;
        document.getElementById('color-scheme-toggle-auto').checked = false;
      }

      document.querySelectorAll('#dark-mode-footer-toggle input').forEach(function(el) {
        el.addEventListener('change', function() {
          if (this.value === 'light') {
            setTheme('theme-light');
          } else if (this.value === 'dark') {
            setTheme('theme-dark');
          } else {
            localStorage.removeItem('theme');

            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
              setTheme('theme-dark');
            } else {
              setTheme('theme-light');
            }
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      themeSetup();
    });
  </script>
  <?php
}
