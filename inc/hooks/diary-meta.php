<?php
/**
 * Diary (lokikirja) metadata, core-native replacement for ACF.
 *
 * The 17 daily-metadata fields were previously an ACF field group. They are
 * stored as plain post meta, so reading them needs no data migration. Here we
 * register them with the REST API (so the block editor sidebar panel in
 * js/diary-meta-panel.js can read/write them) and expose the select/button
 * choice labels used both by the front end and the editor panel.
 *
 * @package minimalistmadness
 */

namespace Air_Light;

/**
 * Choice maps for the select / button_group diary fields.
 *
 * @return array<string, array<string, string>> field name => value => label
 */
function diary_meta_choices() {
  return [
    'mood' => [
      'bad' => 'Huono', 'okay' => 'Ihan OK', 'good' => 'Hyvä', 'excellent' => 'Mahtava',
      'tired' => 'Väsynyt', 'shiny' => 'Loistava', 'noseblow' => 'Kipeänä',
      'headpatch' => 'Päätä särkee', 'scared' => 'Jännittää', 'cowboy' => 'Cowboy',
      'sadcrying' => 'Surullinen', 'explosion' => 'Mind blown', 'disappointed' => 'Pettynyt',
      'crazy' => 'Sekaisin', 'eyesonly' => 'Ei oikein mikään fiilis', 'fever' => 'Kuumeessa',
      'smirk' => '"Dealing with it"', 'indifferent' => 'Mikään ei oikein nappaa',
    ],
    'device' => [
      'pc' => 'Pöytäkone', 'macbook' => 'MacBook Pro',
      'mobile' => 'OnePlus 8T -älypuhelin', 'iphone' => 'iPhone 14 Pro',
    ],
    'weather_icon' => [
      'sun' => 'Aurinkoista', 'cloud' => 'Pilvistä', 'rain' => 'Sataa kaatamalla',
      'drizzle' => 'Tihkusadetta', 'snow' => 'Lumisadetta', 'lightning' => 'Ukkostaa',
    ],
    'drink_icon' => [
      'coffee' => 'Kahvi', 'can' => 'Tölkki', 'droplet' => 'Tippa',
    ],
  ];
}

/**
 * Look up the human label for a stored choice value.
 *
 * Replaces ACF's array return format ($field['label']) for mood and device.
 *
 * @param string $field Field name.
 * @param string $value Stored value.
 * @return string Label, or the raw value as a fallback.
 */
function diary_meta_label( $field, $value ) {
  $choices = diary_meta_choices();
  return $choices[ $field ][ $value ] ?? (string) $value;
}

/**
 * Diary meta fields and their REST types. Stored values are strings (as ACF
 * wrote them), so we register them as strings to stay compatible with the
 * 1690 existing entries; the editor panel parses ranges to numbers in the UI.
 *
 * @return string[] Field names.
 */
function diary_meta_fields() {
  return [
    'gratitude', 'habits_percent', 'energy_scale', 'anxiety_scale', 'mood_scale',
    'productivity_scale', 'mood', 'device', 'location', 'temperature', 'weather_text',
    'weather_icon', 'highlight', 'np', 'np_link', 'drink_icon', 'drink_text',
  ];
}

/**
 * Register the diary meta with the REST API for the block editor panel.
 */
function register_diary_meta() {
  foreach ( diary_meta_fields() as $field ) {
    register_post_meta( 'diary', $field, [
      'type'              => 'string',
      'single'            => true,
      'show_in_rest'      => true,
      'sanitize_callback' => 'wp_kses_post',
      'auth_callback'     => function () {
        return current_user_can( 'edit_posts' );
      },
    ] );
  }
}

/**
 * Enqueue the diary metadata sidebar panel, diary edit screen only.
 */
function enqueue_diary_meta_panel() {
  $screen = get_current_screen();
  if ( ! $screen || 'diary' !== $screen->post_type ) {
    return;
  }

  $rel = '/js/diary-meta-panel.js';
  wp_enqueue_script(
    'rollemaa-diary-meta',
    get_theme_file_uri( $rel ),
    [ 'wp-plugins', 'wp-editor', 'wp-components', 'wp-element', 'wp-data', 'wp-core-data', 'wp-i18n' ],
    filemtime( get_theme_file_path( $rel ) ),
    true
  );

  wp_localize_script( 'rollemaa-diary-meta', 'rollemaaDiaryMeta', [
    'choices' => diary_meta_choices(),
  ] );
}
