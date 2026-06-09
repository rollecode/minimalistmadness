<?php
/**
 * The template for content-diary
 *
 * Description of the file called
 * content-diary.
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2021-11-11 08:59:06
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2021-11-11 12:59:35
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

// Fields (core post meta, formerly ACF)
$diary_id = get_the_ID();
$location = get_post_meta( $diary_id, 'location', true );
$habits = get_post_meta( $diary_id, 'habits', true );
$habits_percent = get_post_meta( $diary_id, 'habits_percent', true );
$highlight = get_post_meta( $diary_id, 'highlight', true );
$np = get_post_meta( $diary_id, 'np', true );
$np_link = get_post_meta( $diary_id, 'np_link', true );
$temperature = get_post_meta( $diary_id, 'temperature', true );
$weather_text = get_post_meta( $diary_id, 'weather_text', true );
$weather_icon = get_post_meta( $diary_id, 'weather_icon', true );
$drink_icon = get_post_meta( $diary_id, 'drink_icon', true );
$drink_text = get_post_meta( $diary_id, 'drink_text', true );

// mood and device were ACF array-return fields; rebuild value + label so the
// rest of this template ($mood['value'], $mood['label']) is unchanged.
$device_value = get_post_meta( $diary_id, 'device', true );
$device = $device_value ? [ 'value' => $device_value, 'label' => diary_meta_label( 'device', $device_value ) ] : '';
$mood_value = get_post_meta( $diary_id, 'mood', true );
$mood = $mood_value ? [ 'value' => $mood_value, 'label' => diary_meta_label( 'mood', $mood_value ) ] : '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <?php if ( is_singular() ) : ?>
    <h1 class="prefix"><?php the_title(); ?></h1>
  <?php else : ?>
    <h2 class="prefix"><?php the_title(); ?></h2>
  <?php endif; ?>
  <p class="diary-title">
    <?php if ( ! is_singular( 'diary' ) ) echo '<a href="' . esc_url( get_the_permalink() ) . '">'; ?>
      <time datetime="<?php the_time( 'c' ); ?>"><?php echo esc_html( ucfirst( get_the_time( 'l' ) ) ); ?>na, <?php the_time( 'j.' ) ?> <?php the_time( 'F' ) ?>ta <?php the_time( 'Y' ) ?><br><span class="time">Kello on <?php the_time( 'H:i' ) ?></span></time>
      <?php if ( ! is_singular( 'diary' ) ) echo '</a>'; ?>
  </p>
  <div class="container container-article">
    <div class="entry-content article-content">
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

      <?php if ( is_singular() ) :
        $post_id = get_the_ID();
        $post_object = get_post( $post_id );
        $content = $post_object->post_content;
        $word_count = post_word_count( $content );
        $gratitude = get_post_meta( $diary_id, 'gratitude', true );
      ?>
        <p class="word-count">Tässä kirjoituksessa on <?php echo esc_html( $word_count ); ?> <?php echo ( $word_count === 1 ) ? 'sana' : 'sanaa'; ?>. Blogi on osa <a style="color: inherit; text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 3px;" href="https://jointhefediverse.net/?lang=fi">Fediversumia</a>, voit tykätä ja kommentoida seuraamalla blogia <a style="color: inherit; text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 3px;" href="https://joinmastodon.org/fi">Mastodonissa</a>.</p>
      <?php endif; ?>

      <?php if ( function_exists( 'do_blocks' ) ) {
        echo do_blocks('<!-- wp:activitypub/reactions /-->');
      } ?>

      <?php if ( is_singular() ) : ?>

      <?php if ( ! empty( $gratitude ) ) : ?>
        <div class="metadata metadata-gratitude">
          <p class="meta-prefix">Tänään olen kiitollinen</p>
          <div class="gratitude-reasons">
            <p><?php echo wp_kses_post( $gratitude ); ?></p>
          </div>

          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="700pt" height="700pt" viewBox="0 0 700 700"><path d="M402.1 145.4a4.18 4.18 0 0 1 .36-.277c1.761-1.368 3.363-2.727 4.757-4.059 4.332-4.133 5.531-9.973 7.64-20.266l.274-1.328c.012-.063.024-.125.04-.191 1.347-6.56 3.085-14.727 6.124-25.172a3.723 3.723 0 0 1 4.61-2.54 3.723 3.723 0 0 1 2.539 4.61c-2.27 7.8-3.801 14.336-4.989 19.863 8.332-1.035 9.676-4.02 12.871-11.11 1.332-2.96 2.934-6.51 5.266-10.815a3.724 3.724 0 0 1 5.047-1.516 3.724 3.724 0 0 1 1.516 5.047c-2.196 4.05-3.743 7.488-5.032 10.348-4.847 10.758-6.613 14.676-21.254 15.691-2.238 10.898-3.73 17.262-9.507 22.773a63.818 63.818 0 0 1-3.934 3.438l1.637 21.363c.054.156.105.316.14.48 9.43 42.54 43.07 56.336 78.887 71.02 6.41 2.629 12.895 5.285 19.621 8.277 54.758 24.324 73.543 109.67 87.781 174.36 2.973 13.508 5.746 26.11 8.477 36.59a3.722 3.722 0 0 1-7.203 1.868c-2.922-11.215-5.649-23.59-8.567-36.852-13.89-63.117-32.223-146.39-83.492-169.17-6.27-2.786-12.895-5.5-19.445-8.188-30.023-12.312-58.594-24.027-74.156-51.75l2.59 33.824a3.728 3.728 0 0 1-3.441 3.996 3.728 3.728 0 0 1-3.996-3.441l-5.914-77.18a145.755 145.755 0 0 1-9.2 5.672c-8.347 4.742-17.664 9.07-26.402 12.242-2.82 1.023-5.246 2.191-7.386 3.222-3.797 1.829-6.809 3.282-10.375 3.32-3.75.044-7.012-1.468-11.145-3.382-2.176-1.008-4.63-2.145-7.403-3.152-8.738-3.172-18.059-7.5-26.414-12.242a146.07 146.07 0 0 1-9.21-5.676l-5.915 77.18a3.728 3.728 0 0 1-3.996 3.441 3.728 3.728 0 0 1-3.441-3.996l2.59-33.824c-15.562 27.723-44.133 39.438-74.156 51.75-6.555 2.688-13.18 5.402-19.445 8.188-49.45 21.965-65.27 100.5-77.797 162.7-3.215 15.965-6.22 30.875-9.516 43.527a3.722 3.722 0 0 1-7.203-1.868c3.273-12.562 6.246-27.32 9.43-43.12 12.863-63.868 29.105-144.5 82.086-168.03 6.73-2.989 13.21-5.649 19.62-8.278 35.813-14.684 69.458-28.48 78.888-71.02a3.39 3.39 0 0 1 .14-.48l1.637-21.363a63.818 63.818 0 0 1-3.934-3.438c-5.777-5.512-7.27-11.875-9.508-22.773-14.64-1.012-16.406-4.93-21.254-15.69-1.289-2.864-2.835-6.298-5.03-10.349a3.728 3.728 0 0 1 1.515-5.047 3.728 3.728 0 0 1 5.047 1.516c2.332 4.305 3.933 7.855 5.265 10.816 3.196 7.09 4.54 10.074 12.871 11.109-1.187-5.531-2.718-12.062-4.988-19.863a3.722 3.722 0 0 1 7.149-2.07c3.039 10.449 4.773 18.613 6.125 25.172.015.062.027.125.039.191l.273 1.328c2.11 10.297 3.305 16.133 7.64 20.266 1.399 1.332 2.997 2.691 4.758 4.059.125.086.247.18.36.277 3.902 2.992 8.582 6.016 13.648 8.895 8 4.543 16.922 8.687 25.273 11.719 3.14 1.14 5.707 2.328 7.984 3.386 3.301 1.531 5.906 2.735 7.965 2.715 1.86-.02 4.223-1.156 7.195-2.59 2.286-1.101 4.883-2.351 8.086-3.515 8.352-3.036 17.266-7.176 25.266-11.72 5.063-2.874 9.739-5.902 13.637-8.89zm52.539 186.85c.602-.512 1.09-.918 1.547-1.3 4.539-3.794 5.77-4.821 3.215-11.392-.742-1.918.21-4.07 2.129-4.812s4.07.21 4.812 2.129c4.492 11.555 2.39 13.309-5.371 19.793-.441.367-.906.754-1.344 1.125 24.691 27.934 44.086 57.379 64.867 88.938 7.215 10.957 14.605 22.18 22.88 34.3a3.724 3.724 0 1 1-6.153 4.2c-7.746-11.348-15.434-23.02-22.938-34.418-25.074-38.07-48.102-73.04-80.848-105.77-10.625-10.621-18.79-19.754-24.812-29.887-6.086-10.238-9.926-21.383-11.812-35.914a1.468 1.468 0 0 0-.309-.742c-.27-.336-.718-.594-1.273-.77a5.514 5.514 0 0 0-2.356-.187c-.945.12-1.859.468-2.57 1.043-9.84 7.906-.723 25.57 4.602 35.887.433.843.84 1.629 1.543 3.03a3.716 3.716 0 0 1-1.664 4.99 3.716 3.716 0 0 1-4.989-1.665c-.21-.422-.84-1.64-1.511-2.945l-.23-.446c-15.45 6.98-20.18 4.067-31.466-2.894-2.5-1.543-5.375-3.316-9.055-5.332l-55.754-30.555c-1.867-1.023-3.464-1.461-4.734-1.45-.906.009-1.559.262-1.902.68a.974.974 0 0 0-.09.121c-.121.18-.188.38-.149.57.106.516.59 1.2 1.657 2.079.3.25.644.504 1.03.766l31.563 21.312a3.732 3.732 0 0 1 1.008 5.175 3.732 3.732 0 0 1-5.176 1.008l-31.562-21.312c-.59-.398-1.125-.8-1.617-1.203-2.508-2.07-3.766-4.254-4.192-6.336-.492-2.406.113-4.535 1.258-6.23a9.16 9.16 0 0 1 .527-.704c1.72-2.078 4.348-3.328 7.59-3.363 2.48-.027 5.34.688 8.383 2.352l55.754 30.555c3.59 1.965 6.684 3.875 9.379 5.535 8.805 5.43 12.55 7.738 24.219 2.55-5.801-12.233-11.13-28.3.918-37.983 1.808-1.453 4.047-2.324 6.305-2.617 1.89-.247 3.816-.094 5.535.453 1.894.597 3.59 1.672 4.832 3.219.984 1.226 1.656 2.718 1.883 4.476 1.07 8.258 2.82 15.32 5.312 21.723l11.273-6.18c3.04-1.664 5.898-2.378 8.383-2.351 3.242.035 5.875 1.285 7.59 3.363.187.227.363.461.527.703 1.144 1.696 1.754 3.824 1.258 6.23-.426 2.083-1.684 4.266-4.192 6.337-.488.406-1.027.808-1.617 1.203l-13.156 8.883c1.68 2.316 3.5 4.617 5.465 6.94l25.688-18.27c2-1.421 2.863-2.726 2.883-3.714a1.063 1.063 0 0 0-.188-.617c-.176-.258-.465-.492-.855-.672-1.543-.727-4.067-.605-7.16.973a3.73 3.73 0 0 1-3.383-6.649c5.36-2.73 10.3-2.668 13.723-1.058 1.629.765 2.941 1.883 3.86 3.242a8.442 8.442 0 0 1 1.44 4.934c-.062 3.19-1.867 6.695-6.003 9.636l-25.04 17.81c2.512 2.726 5.215 5.515 8.118 8.425l6.621-5.691c1.074-.922 1.809-1.536 2.48-2.098 4.348-3.629 5.66-4.727 3.61-10.344a3.725 3.725 0 0 1 2.23-4.77 3.725 3.725 0 0 1 4.77 2.23c3.848 10.52 1.601 12.396-5.824 18.599-.828.691-1.73 1.445-2.422 2.039l-6.223 5.352a382.663 382.663 0 0 1 7.285 7.742zm-26.277-51.867-11.809 6.473a76.612 76.612 0 0 0 2.484 4.507c.13.22.262.434.391.653l13.121-8.863c.387-.262.73-.52 1.031-.766 1.063-.879 1.547-1.563 1.656-2.078.04-.192-.03-.39-.148-.57a1.387 1.387 0 0 0-.09-.122c-.344-.418-1-.671-1.902-.68-1.27-.011-2.871.427-4.735 1.45zM330.51 296.43a3.722 3.722 0 1 1 3.82-6.39l9.88 5.925a3.722 3.722 0 1 1-3.821 6.39zm2.586 14.023a3.722 3.722 0 1 1-4.317 6.066l-63.68-45.293c-4.136-2.941-5.94-6.445-6.003-9.636a8.422 8.422 0 0 1 1.441-4.934c.914-1.36 2.23-2.473 3.86-3.242 3.421-1.61 8.363-1.672 13.722 1.058a3.73 3.73 0 0 1-3.382 6.649c-3.094-1.578-5.618-1.7-7.16-.973-.391.184-.68.414-.856.672a1.055 1.055 0 0 0-.188.617c.02.992.887 2.293 2.883 3.715l63.68 45.293zm-15.047 17.984a3.725 3.725 0 0 1-4.84 5.66l-4.437-3.816c-7.29 1.957-9.704 1.43-16.254.004l-2.086-.453-17.2-3.649c-2.753-.586-4.39-.344-5.077.32a.655.655 0 0 0-.192.352c-.043.25 0 .57.137.941.601 1.66 2.582 3.407 5.953 4.313l-.004.012.121.035 33.04 10.109.132.031c8.484 2.285 16.781 6.313 24.84 11.121 7.922 4.723 15.555 10.16 22.922 15.418 7.8 5.566 13.75 9.102 20.023 11.012 6.238 1.902 13.074 2.273 22.707 1.531a3.728 3.728 0 0 1 3.996 3.442 3.728 3.728 0 0 1-3.441 3.996c-.746.058-1.477.11-2.196.152 8.54 8.457 16.801 18.941 24.57 30.223 9.782 14.203 18.79 29.7 26.574 43.984a3.72 3.72 0 1 1-6.535 3.559c-7.672-14.078-16.547-29.344-26.164-43.316-5.75-8.352-11.762-16.234-17.93-23.098-2.98 1.847-5.965 3.82-8.93 5.843-3.828 2.614-7.464 5.207-11.03 7.754-8.422 6.012-14.97 9.868-22.18 12.062-3.887 1.184-7.887 1.864-12.384 2.114-5.367 4.62-10.656 10.188-15.797 16.375-5.496 6.617-10.859 13.977-16.008 21.672a3.723 3.723 0 0 1-6.183-4.145c5.258-7.86 10.77-15.418 16.473-22.285 3.504-4.215 7.066-8.16 10.672-11.73a117.9 117.9 0 0 1-2.195-.152 3.728 3.728 0 0 1-3.442-3.996 3.728 3.728 0 0 1 3.996-3.442c9.637.742 16.47.371 22.707-1.53 6.274-1.911 12.223-5.45 20.023-11.013 3.7-2.64 7.461-5.32 11.148-7.84 2.594-1.773 5.23-3.52 7.914-5.207a110.22 110.22 0 0 0-6.14-5.722c-4.5-.25-8.496-.93-12.383-2.114-7.211-2.199-13.758-6.054-22.18-12.062-7.262-5.18-14.785-10.539-22.426-15.098-7.504-4.472-15.18-8.21-22.945-10.305a3.513 3.513 0 0 1-.39-.129l-.93-.285a188.987 188.987 0 0 1-6.34 7.086l-.008-.008a331.325 331.325 0 0 1-7.656 7.89c-15.824 15.82-29.371 32.146-41.97 49.087-12.687 17.062-24.378 34.676-36.456 53.008a3.721 3.721 0 0 1-5.149 1.066 3.721 3.721 0 0 1-1.066-5.148c12.188-18.496 23.977-36.262 36.688-53.355 12.805-17.215 26.578-33.82 42.695-49.93 2.762-2.762 5.273-5.328 7.488-7.692l.008-.007-.008-.008a188.296 188.296 0 0 0 3.973-4.367l-24.176-7.399-.004.008c-5.969-1.605-9.695-5.293-11.027-8.98-.578-1.602-.734-3.243-.457-4.793a7.98 7.98 0 0 1 2.348-4.407c2.336-2.254 6.297-3.406 11.785-2.242l17.199 3.649c1.343.285 1.738.37 2.113.453 3.14.683 5.105 1.11 7.39 1.02l-30.921-26.587a250.12 250.12 0 0 0-2.422-2.039c-7.094-5.926-9.453-7.894-6.215-17.488a3.722 3.722 0 0 1 7.058 2.363c-1.613 4.774-.226 5.93 3.938 9.406.672.563 1.406 1.172 2.48 2.098l42.645 36.668z"/></svg>
        </div>
      <?php endif; ?>

      <?php if ( $habits || $habits_percent ) : ?>

        <h2 class="title-with-icon screen-reader-text">
          <span class="icon" aria-hidden="true">
            <?php require get_theme_file_path( '/svg/trophy.svg' ); ?>
          </span>

          <span class="sub-title">
            Päivän saavutukset kirjoittamishetkeen (<?php the_time( 'H:i' ) ?>) mennessä
          </span>
        </h2>

        <?php
        // Habits
        if ( $habits_percent ) {
          $habits_completion_percent = $habits_percent;
        } elseif ( is_array( $habits ) && count( $habits ) ) {
          $habits_checked = count( $habits );
          $habits_total = 10;
          $habits_completion_percent = ( $habits_checked / $habits_total ) * 100;
        } else {
          $habits_completion_percent = 0;
        }

        // Progress bars
        if ( $habits_completion_percent >= 0 && $habits_completion_percent <= 40 ) {
          $habit_progress_class = 'bad';
        } elseif ( $habits_completion_percent >= 41 && $habits_completion_percent <= 60 ) {
          $habit_progress_class = 'okay';
        } else {
          $habit_progress_class = 'good';
        }

        // Mood
        $total = 100;
        $mood_scale = get_post_meta( $diary_id, 'mood_scale', true );

        if ( $mood_scale >= 0 && $mood_scale <= 40 ) {
          $mood_class = 'bad';
        } elseif ( $mood_scale >= 41 && $mood_scale <= 60 ) {
          $mood_class = 'okay';
        } else {
          $mood_class = 'good';
        }

        // Productivity
        $total = 100;
        $productivity_scale = get_post_meta( $diary_id, 'productivity_scale', true );

        if ( $productivity_scale >= 0 && $productivity_scale <= 40 ) {
          $productivity_class = 'bad';
        } elseif ( $productivity_scale >= 41 && $productivity_scale <= 60 ) {
          $productivity_class = 'okay';
        } else {
          $productivity_class = 'good';
        }

        // Energy
        $energy_scale = get_post_meta( $diary_id, 'energy_scale', true );

        if ( $energy_scale >= 0 && $energy_scale <= 40 ) {
          $energy_class = 'bad';
        } elseif ( $energy_scale >= 41 && $energy_scale <= 60 ) {
          $energy_class = 'okay';
        } else {
          $energy_class = 'good';
        }

        // Anxiety
        $anxiety_scale = get_post_meta( $diary_id, 'anxiety_scale', true );

        if ( $anxiety_scale >= 0 && $anxiety_scale <= 40 ) {
          $anxiety_class = 'good';
        } elseif ( $anxiety_scale >= 41 && $anxiety_scale <= 60 ) {
          $anxiety_class = 'okay';
        } else {
          $anxiety_class = 'bad';
        }
        ?>

        <ul class="metadata metadata-progress">
          <?php if ( ! empty( $habits_completion_percent ) ) : ?>
            <li class="progress">
              <div class="progress-bar is-<?php echo esc_html( $habit_progress_class ); ?>" style="width: <?php echo esc_html( $habits_completion_percent ); ?>%;">
                <span>Päivätavoitteet: <?php echo esc_html( $habits_completion_percent ); ?>%</span>
              </div>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $productivity_scale ) ) : ?>
            <li class="progress">
              <div class="progress-bar is-<?php echo esc_html( $productivity_class ); ?>" style="width: <?php echo esc_html( $productivity_scale ); ?>%;">
                <span>Tuottavuusprosentti: <?php echo esc_html( $productivity_scale ); ?>%</span>
              </div>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $mood_scale ) ) : ?>
            <li class="progress">
              <div class="progress-bar is-<?php echo esc_html( $mood_class ); ?>" style="width: <?php echo esc_html( $mood_scale ); ?>%;">
                <span>Mieliala: <?php echo esc_html( $mood_scale ); ?>%</span>
              </div>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $energy_scale ) ) : ?>
            <li class="progress">
              <div class="progress-bar is-<?php echo esc_html( $energy_class ); ?>" style="width: <?php echo esc_html( $energy_scale ); ?>%;">
                <span>Energia: <?php echo esc_html( $energy_scale ); ?>%</span>
              </div>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $anxiety_scale ) ) : ?>
            <li class="progress">
              <div class="progress-bar is-<?php echo esc_html( $anxiety_class ); ?>" style="width: <?php echo esc_html( $anxiety_scale ); ?>%;">
                <span>Ahdistuksen taso: <?php echo esc_html( $anxiety_scale ); ?>%</span>
              </div>
            </li>
          <?php endif; ?>
        </ul>
      <?php endif; ?>

      <?php if ( ! empty( $location ) || ! empty( $highlight ) || ! empty( $temperature ) || ! empty( $weather_text ) || ! empty( $device ) || ! empty( $drink_text ) ) : ?>
        <ul class="metadata">
          <?php if ( ! empty( $mood ) && ! empty( $mood['label'] ) ) : ?>
            <li class="mood">
              <?php include get_theme_file_path( "/svg/{$mood['value']}.svg" ); ?> <?php echo esc_html( $mood['label'] ); ?>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $np ) ) : ?>
            <li>
              <?php include get_theme_file_path( '/svg/np.svg' ); ?>
              <?php if ( ! empty( $np_link ) ) : ?>
                <a href="<?php echo esc_url( $np_link ); ?>">
              <?php endif; ?>
                Nyt soi <?php echo esc_html( $np ); ?>
              <?php if ( ! empty( $np_link ) ) : ?>
                </a>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $device ) ) : ?>
            <li>
              <?php include get_theme_file_path( "/svg/{$device['value']}.svg" ); ?>
              <?php echo esc_html( $device['label'] ); ?>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $weather_text ) ) : ?>
            <li>
              <?php include get_theme_file_path( "/svg/{$weather_icon}.svg" ); ?>
              <?php echo esc_html( $temperature ); ?> &deg; C, <?php echo esc_html( $weather_text ); ?>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $location ) ) : ?>
            <li>
              <?php include get_theme_file_path( '/svg/location.svg' ); ?>
              <?php echo esc_html( $location ); ?>
            </li>
          <?php endif; ?>
          <?php if ( ! empty( $highlight ) ) : ?>
            <li>
              <?php include get_theme_file_path( '/svg/highlight.svg' ); ?>
              Päivän kohokohta: <?php echo esc_html( $highlight ); ?>
            </li>
          <?php endif; ?>

          <?php if ( ! empty( $drink_text ) ) : ?>
            <li>
              <?php include get_theme_file_path( "/svg/{$drink_icon}.svg" ); ?>
              <?php echo esc_html( $drink_text ); ?>
            </li>
          <?php endif; ?>

        </ul>
      <?php endif; ?>
      <?php endif; ?>

      <?php if ( is_singular( 'diary' ) ) : ?>
        <?php get_template_part( 'template-parts/year-since-now' ); ?>
        <div class="author-info">
          <div class="author-col author-col-avatar">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/author-info.jpg" alt="Kuva Rolle Laukkarisesta" />
          </div>

          <div class="author-col author-col-info">
            <h3>Rolle Laukkarinen</h3>
            <p class="description">Kirjoittaja on <?php echo esc_attr( khonsu_calculate_age( '1988/11/01' ) ); ?>-vuotias elämäntapanörtti, ammatiltaan yrittäjä ja teknologiajohtaja perustamassaan <a href="https://www.dude.fi" class="author-link no-external-link-indicator">digitoimistossa</a>, verkkosivujen tekijä, koukussa kirjoittamiseen 5-vuotiaasta. Päivät kuluu <a href="https://twitter.com/streetlazer" class="author-link no-external-link-indicator">monipuolisen</a> <a href="https://www.last.fm/user/rolle-" class="author-link no-external-link-indicator">musiikkiharrastuksen</a>, retropelien ja koodaamisen parissa, mutta arkea piristyttää myös vaimo ja kaksi lasta. <a rel="me" href="https://mementomori.social/@rolle" class="author-link no-external-link-indicator">Mastodon</a> ja <a href="https://www.rollekino.fi" class="author-link no-external-link-indicator">leffat</a> lähellä sydäntä.</p>
            <p class="button-paragraph"><a class="button" href="<?php echo esc_url( get_page_link( 2768 ) ); ?>">Lue Rollesta lisää</a></p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ( get_edit_post_link() ) {
        wp_reset_postdata();
        ?>
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
    </div><!-- .entry-content -->
  </div><!-- .container-article -->
</article><!-- #post-## -->

<?php if ( is_singular( 'diary' ) ) : ?>
  <nav class="post-prev-next-navigation">
    <ul>
      <li class="prev hide-label"><?php previous_post_link( '%link' ); ?></li>
      <li class="next hide-label"><?php next_post_link( '%link' ); ?></li>
    </ul>
  </nav>
<?php endif;
