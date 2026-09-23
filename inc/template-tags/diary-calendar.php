<?php
/**
 * Diary calendar: core get_calendar() for the diary post type, with day and
 * month links pointed at the theme's /lokikirja/Y/m(/d)/ date archives.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

function diary_calendar() {
  $day_link = function ( $link, $year, $month, $day ) {
    return home_url( sprintf( 'lokikirja/%04d/%02d/%02d/', $year, $month, $day ) );
  };

  $month_link = function ( $link, $year, $month ) {
    return home_url( sprintf( 'lokikirja/%04d/%02d/', $year, $month ) );
  };

  add_filter( 'day_link', $day_link, 10, 4 );
  add_filter( 'month_link', $month_link, 10, 3 );
  ?>
  <section class="widget widget_calendar">
    <h2 class="widget-title">Kirjoitukset kalenterissa</h2>
    <div class="calendar_wrap"><?php get_calendar( [ 'post_type' => 'diary' ] ); ?></div>
  </section>
  <?php
  remove_filter( 'day_link', $day_link, 10 );
  remove_filter( 'month_link', $month_link, 10 );
}
