<?php
/**
 * Site frame: skip link and the .site wrapper around the whole page.
 * $content is the rendered inner blocks (header, content, footer).
 *
 * @package minimalistmadness
 */

$frame_classes = 'site';
if ( ! is_singular() || has_tag( 'raha' ) ) {
  $frame_classes .= ' disable-google-ads';
}
?>
<a class="skip-link screen-reader-text" href="#content"><?php echo esc_html( \Air_Light\get_default_localization( 'Skip to content' ) ); ?></a>
<div class="<?php echo esc_attr( $frame_classes ); ?>">
<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
