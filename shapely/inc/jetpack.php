<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.me/
 *
 * @package Shapely
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.me/support/infinite-scroll/
 * See: https://jetpack.me/support/responsive-videos/
 */
if ( ! function_exists( 'shapely_jetpack_setup' ) ) :
	function shapely_jetpack_setup() {
		/*
		 * Infinite Scroll. The container is #primary, the posts column: #main
		 * also holds the sidebar, so new posts were added below it at full width.
		 */
		add_theme_support(
			'infinite-scroll',
			array(
				'container' => 'primary',
				'render'    => 'shapely_infinite_scroll_render',
				'footer'    => 'page',
			)
		);

		// Add theme support for Responsive Videos.
		add_theme_support( 'jetpack-responsive-videos' );

		// Add theme support for Content Options. The selectors are the elements
		// the theme actually prints; the Underscores defaults matched nothing.
		add_theme_support(
			'jetpack-content-options',
			array(
				'post-details'    => array(
					'stylesheet' => 'shapely-style',
					'date'       => '.posted-on',
					'categories' => '.shapely-category',
					'tags'       => '.shapely-tags',
					'author'     => '.author-bio',
				),
				'featured-images' => array(
					'archive' => true,
					'post'    => true,
					'page'    => true,
				),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'shapely_jetpack_setup' );

if ( ! function_exists( 'shapely_infinite_scroll_render' ) ) :
	/**
	 * Custom render function for Infinite Scroll.
	 */
	function shapely_infinite_scroll_render() {
		if ( is_search() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', 'search' );
			}

			return;
		}

		// The same layout the first page used; content.php is the single post
		// template, so every loaded post came in as a full article.
		$layout_type = str_replace( '_', '-', get_theme_mod( 'blog_layout_view', 'grid' ) );
		get_template_part( 'template-parts/layouts/blog', $layout_type );
	}
endif;
