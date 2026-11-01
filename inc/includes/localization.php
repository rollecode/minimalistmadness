<?php
/**
 * @Author: Timi Wahalahti
 * @Date:   2019-12-03 11:03:31
 * @Last Modified by:   Rolle Laukkarinen
 * @Last Modified time: 2021-07-27 12:32:53
 *
 * @package minimalistmadness
 */

namespace Air_Light;

add_filter( 'air_helper_pll_register_strings', function() {
  $strings = [
    // 'Key: String' => 'String',
  ];

  /**
   * Uncomment if you need to have default air-light accessibility strings
   * translatable via Polylang string translations.
   */
  // foreach ( get_default_localization_strings() as $key => $value ) {
  // $strings[ "Accessibility: {$key}" ] = $value;
  // }

  return $strings;
} );

function get_default_localization_strings( $language = 'en' ) {
  $strings = [
    'en'  => [
      'Add a menu'                                   => __( 'Add a menu', 'minimalistmadness' ),
      'Open main menu'                               => __( 'Open main menu', 'minimalistmadness' ),
      'Close main menu'                              => __( 'Close main menu', 'minimalistmadness' ),
      'Main navigation'                              => __( 'Main navigation', 'minimalistmadness' ),
      'Back to top'                                  => __( 'Back to top', 'minimalistmadness' ),
      'Open child menu'                              => __( 'Open child menu', 'minimalistmadness' ),
      'Open child menu for'                          => __( 'Open child menu for', 'minimalistmadness' ),
      'Close child menu'                             => __( 'Close child menu', 'minimalistmadness' ),
      'Close child menu for'                         => __( 'Close child menu for', 'minimalistmadness' ),
      'Skip to content'                              => __( 'Skip to content', 'minimalistmadness' ),
      'External site:'                               => __( 'External site:', 'minimalistmadness' ),
      'opens in a new window'                        => __( 'opens in a new window', 'minimalistmadness' ),
      'Page not found.'                              => __( 'Page not found.', 'minimalistmadness' ),
      'The reason might be mistyped or expired URL.' => __( 'The reason might be mistyped or expired URL.', 'minimalistmadness' ),
      'Search'                                       => __( 'Search', 'minimalistmadness' ),
      'Block missing required data'                  => __( 'Block missing required data', 'minimalistmadness' ),
      'This error is shown only for logged in users' => __( 'This error is shown only for logged in users', 'minimalistmadness' ),
      'No results found for your search'                   => __( 'No results found for your search', 'minimalistmadness' ),
    ],
    'fi'  => [
      'Add a menu'                                   => 'Luo uusi valikko',
      'Open main menu'                               => 'Avaa päävalikko',
      'Close main menu'                              => 'Sulje päävalikko',
      'Main navigation'                              => 'Päävalikko',
      'Back to top'                                  => 'Takaisin ylös',
      'Open child menu'                              => 'Avaa alavalikko',
      'Open child menu for'                          => 'Avaa alavalikko kohteelle',
      'Close child menu'                             => 'Sulje alavalikko',
      'Close child menu for'                         => 'Sulje alavalikko kohteelle',
      'Skip to content'                              => 'Siirry suoraan sisältöön',
      'External site:'                               => 'Ulkoinen sivusto:',
      'opens in a new window'                        => 'avautuu uuteen ikkunaan',
      'Page not found.'                              => 'Hups. Näyttää, ettei sivua löydy.',
      'The reason might be mistyped or expired URL.' => 'Syynä voi olla virheellisesti kirjoitettu tai vanhentunut linkki.',
      'Search'                                       => 'Haku',
      'Block missing required data'                  => 'Lohkon pakollisia tietoja puuttuu',
      'This error is shown only for logged in users' => 'Tämä virhe näytetään vain kirjautuneille käyttäjille',
      'No results for your search'                   => 'Haullasi ei löytynyt tuloksia',
    ],
  ];

  return ( array_key_exists( $language, $strings ) ) ? $strings[ $language ] : $strings['en'];
} // end get_default_localization_strings

function get_default_localization( $string ) {
  if ( function_exists( 'ask__' ) && array_key_exists( "Accessibility: {$string}", apply_filters( 'air_helper_pll_register_strings', [] ) ) ) {
    return ask__( "Accessibility: {$string}" );
  }

  return esc_html( get_default_localization_translation( $string ) );
} // end get_default_localization

function get_default_localization_translation( $string ) {
  $language = get_bloginfo( 'language' );
  if ( function_exists( 'pll_the_languages' ) ) {
    $language = pll_current_language();
  }

  $translations = get_default_localization_strings( $language );

  return ( array_key_exists( $string, $translations ) ) ? $translations[ $string ] : '';
} // end get_default_localization_translation
