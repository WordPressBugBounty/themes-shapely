<?php
/**
 * Title: Hero
 * Slug: shapely/hero
 * Categories: shapely, banner
 * Keywords: hero, header, cover, intro
 * Viewport Width: 1400
 * Description: A full-width opening section with a headline, a line of text and two buttons. Add a background image in the block settings.
 *
 * @package Shapely
 */

?>
<!-- wp:cover {"overlayColor":"dark","minHeight":620,"align":"full","style":{"spacing":{"padding":{"top":"96px","bottom":"96px"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:96px;padding-bottom:96px;min-height:620px"><span aria-hidden="true" class="wp-block-cover__background has-dark-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"background"} -->
<h1 class="wp-block-heading has-text-align-center has-background-color has-text-color"><?php esc_html_e( 'We build things people like to use', 'shapely' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"background","fontSize":"large"} -->
<p class="has-text-align-center has-background-color has-text-color has-large-font-size"><?php esc_html_e( 'One sentence on what you do and who it is for. Keep it short enough to take in at a glance.', 'shapely' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-shapely-filled"} -->
<div class="wp-block-button is-style-shapely-filled"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'See our work', 'shapely' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"textColor":"background","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-background-color has-text-color wp-element-button"><?php esc_html_e( 'Get in touch', 'shapely' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
