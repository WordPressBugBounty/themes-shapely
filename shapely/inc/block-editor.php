<?php
/**
 * Block editor styles and patterns.
 *
 * Shapely is a classic theme, but posts and pages are still written in the
 * block editor, so the blocks people actually use should be able to take the
 * theme's own appearance rather than core's defaults.
 *
 * Everything here mirrors values that already exist in style.css (the .btn
 * rules around line 1392 and the theme.json palette) instead of introducing
 * a second, competing palette.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'shapely_register_block_styles' ) ) :
	/**
	 * Register block styles that reuse the theme's button and section looks.
	 */
	function shapely_register_block_styles() {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		/*
		 * The CSS is passed as inline_style so each style is self-contained and
		 * is only printed when the block is actually on the page, rather than
		 * growing style.css for markup most sites never use.
		 */
		register_block_style(
			'core/button',
			array(
				'name'         => 'shapely-filled',
				'label'        => esc_html__( 'Shapely Filled', 'shapely' ),
				'inline_style' => '
					.wp-block-button.is-style-shapely-filled .wp-block-button__link {
						background: var(--wp--preset--color--button, #745cf9);
						border: 2px solid var(--wp--preset--color--button, #745cf9);
						border-radius: 0;
						color: #fff;
						font-size: 12px;
						font-weight: 600;
						letter-spacing: 1px;
						padding: 12px 26px;
						text-transform: uppercase;
					}
					.wp-block-button.is-style-shapely-filled .wp-block-button__link:hover,
					.wp-block-button.is-style-shapely-filled .wp-block-button__link:focus {
						background: var(--wp--preset--color--button-hover, #5d47d7);
						border-color: var(--wp--preset--color--button-hover, #5d47d7);
						color: #fff;
					}',
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'         => 'shapely-outline',
				'label'        => esc_html__( 'Shapely Outline', 'shapely' ),
				'inline_style' => '
					.wp-block-button.is-style-shapely-outline .wp-block-button__link {
						background: transparent;
						border: 2px solid var(--wp--preset--color--button, #745cf9);
						border-radius: 0;
						color: var(--wp--preset--color--button, #745cf9);
						font-size: 12px;
						font-weight: 600;
						letter-spacing: 1px;
						padding: 12px 26px;
						text-transform: uppercase;
					}
					.wp-block-button.is-style-shapely-outline .wp-block-button__link:hover,
					.wp-block-button.is-style-shapely-outline .wp-block-button__link:focus {
						background: var(--wp--preset--color--button, #745cf9);
						color: #fff;
					}',
			)
		);

		register_block_style(
			'core/separator',
			array(
				'name'         => 'shapely-short',
				'label'        => esc_html__( 'Shapely Short Rule', 'shapely' ),
				'inline_style' => '
					.wp-block-separator.is-style-shapely-short {
						background: var(--wp--preset--color--button, #745cf9);
						border: 0;
						height: 3px;
						margin: 32px auto;
						max-width: 60px;
						opacity: 1;
					}',
			)
		);
	}
endif;
add_action( 'init', 'shapely_register_block_styles' );

if ( ! function_exists( 'shapely_register_block_patterns' ) ) :
	/**
	 * Register a small set of patterns built from core blocks.
	 */
	function shapely_register_block_patterns() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category(
				'shapely',
				array( 'label' => esc_html__( 'Shapely', 'shapely' ) )
			);
		}

		register_block_pattern(
			'shapely/call-to-action',
			array(
				'title'      => esc_html__( 'Centred call to action', 'shapely' ),
				'categories' => array( 'shapely', 'call-to-action' ),
				'content'    => '
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"64px","bottom":"64px"}}}} -->
<div class="wp-block-group alignwide" style="padding-top:64px;padding-bottom:64px">
<!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="wp-block-heading has-text-align-center">' . esc_html__( 'Ready to get started?', 'shapely' ) . '</h2>
<!-- /wp:heading -->
<!-- wp:separator {"className":"is-style-shapely-short"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-shapely-short"/>
<!-- /wp:separator -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">' . esc_html__( 'Say a little about what you offer and why someone should take the next step.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-shapely-filled"} -->
<div class="wp-block-button is-style-shapely-filled"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Get in touch', 'shapely' ) . '</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'shapely/two-column-feature',
			array(
				'title'      => esc_html__( 'Two column feature', 'shapely' ),
				'categories' => array( 'shapely', 'columns' ),
				'content'    => '
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'What we do', 'shapely' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'Describe the service in a sentence or two. Keep it short enough to read at a glance.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'How we do it', 'shapely' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'A second short paragraph, so the two columns balance rather than one running long.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
			)
		);
	}
endif;
add_action( 'init', 'shapely_register_block_patterns' );

if ( ! function_exists( 'shapely_editor_layout_sizes' ) ) :
	/**
	 * Size the block editor like the column the post will actually render in.
	 *
	 * theme.json says 1140px for content and wide blocks, which is the Full
	 * Width layout. Posts default to a sidebar layout, where the column is
	 * 750px and wide blocks stay inside it, so the editor showed lines half as
	 * long again as the published post. The front end is untouched: style.css
	 * already sizes each layout.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme's theme.json data.
	 *
	 * @return WP_Theme_JSON_Data
	 */
	function shapely_editor_layout_sizes( $theme_json ) {
		global $pagenow;

		if ( ! is_admin() || ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return $theme_json;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only: which post the editor is open on.
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
			$layout = shapely_get_post_layout_class( absint( $_GET['post'] ) );
		} else {
			$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'post';
			$layout    = shapely_get_post_layout_class(
				(object) array(
					'post_type' => $post_type,
					'ID'        => 0,
					'filter'    => 'raw',
				)
			);
		}
		// phpcs:enable

		$sizes = array(
			'full-width'    => array( '1140px', '1140px' ),
			'no-sidebar'    => array( '750px', '1140px' ),
			'sidebar-left'  => array( '750px', '750px' ),
			'sidebar-right' => array( '750px', '750px' ),
		);

		if ( ! isset( $sizes[ $layout ] ) ) {
			return $theme_json;
		}

		return $theme_json->update_with(
			array(
				'version'  => 2,
				'settings' => array(
					'layout' => array(
						'contentSize' => $sizes[ $layout ][0],
						'wideSize'    => $sizes[ $layout ][1],
					),
				),
			)
		);
	}
endif;
add_filter( 'wp_theme_json_data_theme', 'shapely_editor_layout_sizes' );
