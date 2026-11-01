<?php
/**
 * Hero
 *
 * @Author: Rolle Laukkarinen
 * @Date: 2020-02-28 15:38:00
 * @Last Modified by:   Rolle Laukkarinen
 * @Last Modified time: 2021-11-14 01:00:37
 * @package minimalistmadness
 */

namespace Air_Light;

?>

<section class="block block-hero">
  <div class="page-head">
    <h1>Rollemaa on Rollen blogi, joka sisältää <?php echo esc_attr( wp_count_posts( 'post' )->publish + wp_count_posts( 'diary' )->publish ); ?> kirjoitusta, eli <?php echo esc_html( get_word_count_from_posts() ); ?> sanaa.</h1>
  </div>
</section>
