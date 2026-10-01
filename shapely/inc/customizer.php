<?php

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
if ( ! function_exists( 'shapely_customize_register' ) ) :
	function shapely_customize_register( $wp_customize ) {
		/*
		 * These are core settings, but a plugin is free to remove any of them --
		 * and get_setting() then returns null, so assigning ->transport straight
		 * onto the result is a fatal on PHP 8 rather than a notice.
		 */
		$transports = array(
			'blogname'         => 'postMessage',
			'blogdescription'  => 'postMessage',
			'header_textcolor' => 'postMessage',
			'custom_logo'      => 'refresh',
		);

		foreach ( $transports as $setting_id => $transport ) {
			$setting = $wp_customize->get_setting( $setting_id );

			if ( $setting instanceof WP_Customize_Setting ) {
				$setting->transport = $transport;
			}
		}

		// Abort if selective refresh is not available.
		if ( ! isset( $wp_customize->selective_refresh ) ) {
			return;
		}

		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title',
				'render_callback' => function () {
					bloginfo( 'name' );
				},
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'footer_callout_text',
			array(
				'selector'        => '.footer-callout',
				'render_callback' => function () {
					shapely_footer_callout();
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'footer_callout_btntext',
			array(
				'selector'        => '.footer-callout',
				'render_callback' => function () {
					shapely_footer_callout();
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'footer_callout_link',
			array(
				'selector'        => '.footer-callout',
				'render_callback' => function () {
					shapely_footer_callout();
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blog_name',
			array(
				'selector'        => '.header-callout',
				'render_callback' => function () {
					shapely_top_callout();
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'header_textcolor',
			array(
				'selector'        => '.header-callout',
				'render_callback' => function () {
					shapely_top_callout();
				},
			)
		);
	}
endif;

add_action( 'customize_register', 'shapely_customize_register' );

if ( ! function_exists( 'shapely_customizer' ) ) :
	/**
	 * Options for WordPress Theme Customizer.
	 */
	function shapely_customizer( $wp_customize ) {

		// Load custom controls
		require_once get_template_directory() . '/inc/custom-controls/class-shapely-logo-dimensions.php';
		require_once get_template_directory() . '/inc/custom-controls/class-shapely-custom-label.php';
		require_once get_template_directory() . '/inc/custom-controls/class-shapely-section-link.php';
		require_once get_template_directory() . '/inc/custom-controls/class-shapely-control-range.php';

		$wp_customize->register_section_type( 'Shapely_Section_Link' );

		/*
		 * Section id kept as 'epsilon-section-pro'. It is not a setting, so nothing
		 * is stored against it, but renaming it would drop any customizer state
		 * keyed on the id for no benefit.
		 */
		$wp_customize->add_section(
			new Shapely_Section_Link(
				$wp_customize,
				'epsilon-section-pro',
				array(
					'title'       => esc_html__( 'Theme documentation', 'shapely' ),
					'button_text' => esc_html__( 'Learn more', 'shapely' ),
					'button_url'  => 'https://colorlib.com/wp/support/shapely/',
					'priority'    => 1,
				)
			)
		);

		/* Main option Settings Panel */
		$wp_customize->add_panel(
			'shapely_main_options',
			array(
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => esc_html__( 'Theme Options', 'shapely' ),
				'description'    => esc_html__( 'Panel to update Shapely theme options', 'shapely' ),
				'priority'       => 10,
			)
		);

		$wp_customize->add_panel(
			'shapely_blog_options',
			array(
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => esc_html__( 'Blog Settings', 'shapely' ),
				'description'    => esc_html__( 'Panel to update Blog related options', 'shapely' ),
				'priority'       => 10,
			)
		);

		// Logo dimensions
		$wp_customize->add_setting(
			'shapely_logo_dimension',
			array(
				'sanitize_callback' => 'shapely_sanitize_logo_dimension',
			)
		);
		$wp_customize->add_control(
			new Shapely_Logo_Dimensions(
				$wp_customize,
				'shapely_logo_dimension',
				array(
					'section'  => 'title_tagline',
					'priority' => 9,
				)
			)
		);

		$title_tagline = $wp_customize->get_section( 'title_tagline' );
		if ( $title_tagline ) {
			$title_tagline->panel    = 'shapely_main_options';
			$title_tagline->priority = 1;
		}

		// add "Sidebar" section
		$color_section = $wp_customize->get_section( 'colors' );
		if ( $color_section ) {
			$color_section->panel    = 'shapely_main_options';
			$color_section->priority = 31;
		}

		$header_image = $wp_customize->get_control( 'header_image' );
		if ( $header_image ) {
			$header_image->section     = 'shapely_blog_section';
			$header_image->description = esc_html__( 'Blog Index Header Image', 'shapely' );
			$header_image->priority    = 31;
		}

		// Add a heading for Blog Hero Image
		$wp_customize->add_setting(
			'blog_hero_image_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			new Shapely_Custom_Label(
				$wp_customize,
				'blog_hero_image_title',
				array(
					'label'       => esc_html__( 'Blog Hero Image', 'shapely' ),
					'description' => esc_html__( 'The image displayed as a banner on blog pages', 'shapely' ),
					'section'     => 'shapely_blog_section',
					'priority'    => 30,
				)
			)
		);

		$wp_customize->add_section(
			'shapely_blog_section',
			array(
				'title'    => esc_html__( 'Blog Index Settings', 'shapely' ),
				'panel'    => 'shapely_blog_options',
				'priority' => 33,
			)
		);

		// Add placeholder image settings
		$wp_customize->add_setting(
			'shapely_placeholder_image_enabled',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'shapely_placeholder_image_enabled',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Placeholder Images', 'shapely' ),
				'description' => esc_html__( 'Show/hide placeholder images for posts without a featured image', 'shapely' ),
				'section'     => 'shapely_blog_section',
				'priority'    => 25,
			)
		);

		$wp_customize->add_setting(
			'shapely_placeholder_image',
			array(
				/*
				 * Empty means "the bundled image", which shapely_get_placeholder_image_url()
				 * resolves per layout. A URL default was saved as-is by the image
				 * control's Default button, which pinned wide layouts to the square
				 * image and broke on a domain or https move.
				 */
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'shapely_placeholder_image',
				array(
					'label'           => esc_html__( 'Custom Placeholder Image', 'shapely' ),
					'description'     => esc_html__( 'Upload a custom placeholder image to use instead of the default', 'shapely' ),
					'section'         => 'shapely_blog_section',
					'priority'        => 26,
					'active_callback' => function () {
						return get_theme_mod( 'shapely_placeholder_image_enabled', 1 );
					},
				)
			)
		);

		$wp_customize->add_section(
			'shapely_single_post_section',
			array(
				'title'    => esc_html__( 'Blog Single Settings', 'shapely' ),
				'panel'    => 'shapely_blog_options',
				'priority' => 35,
			)
		);

		$wp_customize->add_setting(
			'link_color',
			array(
				'default'           => '#745cf9',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'link_color',
				array(
					'label'   => esc_html__( 'Link Color', 'shapely' ),
					'section' => 'colors',
				)
			)
		);
		$wp_customize->add_setting(
			'link_hover_color',
			array(
				'default'           => '#5234f9',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'link_hover_color',
				array(
					'label'   => esc_html__( 'Link Hover Color', 'shapely' ),
					'section' => 'colors',
				)
			)
		);
		$wp_customize->add_setting(
			'button_color',
			array(
				'default'           => '#745cf9',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'button_color',
				array(
					'label'   => esc_html__( 'Button Color', 'shapely' ),
					'section' => 'colors',
				)
			)
		);
		$wp_customize->add_setting(
			'button_hover_color',
			array(
				// What .btn:hover has always rendered; theme.json's button-hover matches.
				'default'           => '#5d47d7',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'button_hover_color',
				array(
					'label'   => esc_html__( 'Button Hover Color', 'shapely' ),
					'section' => 'colors',
				)
			)
		);

		// add "Sidebar" section
		$wp_customize->add_section(
			'shapely_main_section',
			array(
				'title'    => esc_html__( 'Main Options', 'shapely' ),
				'priority' => 11,
				'panel'    => 'shapely_main_options',
			)
		);

		$wp_customize->add_setting(
			'top_callout',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'top_callout',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Blog Title', 'shapely' ),
				'description' => esc_html__( 'Show/hide the title from the Blog Page', 'shapely' ),
				'section'     => 'shapely_blog_section',
				'priority'    => 20,
			)
		);

		$wp_customize->add_setting(
			'hide_post_title',
			array(
				// Read with a default of true in shapely_top_callout(); at 0 the box
				// showed unticked while the title was displayed.
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		/*
		 * Only read when Yoast SEO or Rank Math breadcrumbs are on. It lived in
		 * Yoast's own section alone, so Rank Math users could never reach it.
		 */
		$wp_customize->add_control(
			'hide_post_title',
			array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Title in Blog Post', 'shapely' ),
				'section'         => function_exists( 'yoast_breadcrumb' ) ? 'wpseo_breadcrumbs_customizer_section' : 'shapely_single_post_section',
				'active_callback' => function () {
					return function_exists( 'yoast_breadcrumb' ) || function_exists( 'rank_math_the_breadcrumbs' );
				},
			)
		);

		$wp_customize->add_setting(
			'blog_name',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'blog_name',
			array(
				'label'       => esc_html__( 'Blog Name in Top Callout', 'shapely' ),
				'description' => esc_html__( 'Heading for the Blog page', 'shapely' ),
				'section'     => 'shapely_blog_section',
			)
		);

		$wp_customize->add_setting(
			'mobile_menu_on_desktop',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'mobile_menu_on_desktop',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Mobile Menu on Desktop', 'shapely' ),
				'description' => esc_html__( 'Use the collapsed mobile menu on every screen size', 'shapely' ),
				'section'     => 'shapely_main_section',
			)
		);

		$wp_customize->add_setting(
			'footer_callout_text',
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'footer_callout_text',
			array(
				'label'       => esc_html__( 'Text for Footer Callout', 'shapely' ),
				'description' => esc_html__( 'The title of the call to action section from footer', 'shapely' ),
				'section'     => 'shapely_main_section',
			)
		);

		$wp_customize->add_setting(
			'footer_callout_btntext',
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'footer_callout_btntext',
			array(
				'label'       => esc_html__( 'Text for Footer Callout Button', 'shapely' ),
				'description' => esc_html__( 'The label of the call to action section\'s button from the footer', 'shapely' ),
				'section'     => 'shapely_main_section',
			)
		);
		$wp_customize->add_setting(
			'footer_callout_link',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'footer_callout_link',
			array(
				'label'       => esc_html__( 'Footer Callout Button Link', 'shapely' ),
				'section'     => 'shapely_main_section',
				'description' => esc_html__( 'The URL of the call to action section\'s button from footer', 'shapely' ),
				'type'        => 'url',
			)
		);

		/**
		 *
		 * @since 1.2.2
		 *
		 */

		// transparent header
		$wp_customize->add_setting(
			'shapely_transparent_header',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'shapely_transparent_header',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Transparent header', 'shapely' ),
				'description' => esc_html__( 'Make the header background see-through. Set how much with the opacity slider below.', 'shapely' ),
				'section'     => 'shapely_main_section',
			)
		);

		// transparent header: opacity range slider
		$wp_customize->add_setting(
			'shapely_sticky_header_transparency',
			array(
				'default'           => 100,
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new Shapely_Control_Range(
				$wp_customize,
				'shapely_sticky_header_transparency',
				array(
					'label'           => esc_html__( 'Header background opacity', 'shapely' ),
					'description'     => esc_html__( 'At 100 the header is fully opaque, so lower it to see the transparent header.', 'shapely' ),
					'section'         => 'shapely_main_section',
					'input_attrs'     => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 5,
					),
					'active_callback' => 'shapely_active_callback_transparent_header',
				)
			)
		);

		// sticky header
		$wp_customize->add_setting(
			'shapely_sticky_header',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'shapely_sticky_header',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Sticky header', 'shapely' ),
				'description' => esc_html__( 'Keep the header fixed to the top of the window while scrolling', 'shapely' ),
				'section'     => 'shapely_main_section',
			)
		);

		/**
		 *
		 * END
		 *
		 * @since 1.2.2
		 *
		 */

		// add "Footer" section
		$wp_customize->add_section(
			'shapely_footer_section',
			array(
				'title'    => esc_html__( 'Footer', 'shapely' ),
				'priority' => 90,
			)
		);

		$wp_customize->add_setting(
			'shapely_footer_copyright',
			array(
				'default'           => '',
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'shapely_footer_copyright',
			array(
				'type'    => 'textarea',
				'label'   => esc_html__( 'Copyright Text', 'shapely' ),
				'section' => 'shapely_footer_section',
			)
		);

		$wp_customize->add_setting(
			'title_in_header',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'title_above_post',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_date',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_category',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_author',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_setting(
			'first_letter_caps',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'tags_post_meta',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'related_posts_area',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_author_area',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_author_left_side',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'post_author_email',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		// Single Post Settings
		$wp_customize->add_control(
			'title_in_header',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show title in header', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the post title from callout', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);

		$wp_customize->add_control(
			'title_above_post',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show title above post', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the post title above post content', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);

		$wp_customize->add_control(
			'post_date',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the date', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the date when post was published', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);

		$wp_customize->add_control(
			'post_author',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the author', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author who wrote the post under the post title', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);

		$wp_customize->add_control(
			'post_category',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the category', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the categories of post', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);

		$wp_customize->add_control(
			'first_letter_caps',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'First Letter Caps', 'shapely' ),
				'description' => esc_html__( 'This will transform your first letter from a post into uppercase', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_control(
			'tags_post_meta',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Tags Post Meta', 'shapely' ),
				'description' => esc_html__( 'This will show/hide tags from the end of post', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_control(
			'related_posts_area',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Related Posts Area', 'shapely' ),
				'description' => esc_html__( 'This will enable/disable the related posts', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_control(
			'post_author_area',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Post Author Area', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author box', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_control(
			'post_author_left_side',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Post Author Left Side', 'shapely' ),
				'description' => esc_html__( 'This will move the author box from the bottom of the post on top on the left side', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_control(
			'post_author_email',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Author Email', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author\'s email from the author box', 'shapely' ),
				'section'     => 'shapely_single_post_section',
			)
		);
		$wp_customize->add_setting(
			'single_post_layout_template',
			array(
				'default'           => 'sidebar-right',
				'sanitize_callback' => 'shapely_sanitize_blog_layout',
			)
		);

		$wp_customize->add_control(
			'single_post_layout_template',
			array(
				'label'       => esc_html__( 'Single Post Template', 'shapely' ),
				'description' => esc_html__( 'Set the default template for single posts', 'shapely' ),
				'section'     => 'shapely_single_post_section',
				'type'        => 'select',
				'choices'     => array(
					'full-width'    => esc_html__( 'Full Width', 'shapely' ),
					'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
					'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
					'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
				),
			)
		);

		$wp_customize->add_setting(
			'blog_layout_view',
			array(
				'default'           => 'grid',
				'sanitize_callback' => 'shapely_sanitize_choice',
			)
		);

		$wp_customize->add_control(
			'blog_layout_view',
			array(
				'label'       => esc_html__( 'Blog Layout', 'shapely' ),
				'description' => esc_html__( 'Choose how you want to display posts in grid', 'shapely' ),
				'section'     => 'shapely_blog_section',
				'type'        => 'select',
				'choices'     => array(
					'grid'             => esc_html__( 'Grid only', 'shapely' ),
					'large_image_grid' => esc_html__( 'Large Image and Grid', 'shapely' ),
					'large_image'      => esc_html__( 'Large Images', 'shapely' ),
				),
			)
		);

		$wp_customize->add_setting(
			'blog_layout_template',
			array(
				'default'           => 'sidebar-right',
				'sanitize_callback' => 'shapely_sanitize_blog_layout',
			)
		);

		$wp_customize->add_control(
			'blog_layout_template',
			array(
				'label'       => esc_html__( 'Blog Template', 'shapely' ),
				'description' => esc_html__( 'Choose the template for your posts page', 'shapely' ),
				'section'     => 'shapely_blog_section',
				'type'        => 'select',
				'choices'     => array(
					'full-width'    => esc_html__( 'Full Width', 'shapely' ),
					'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
					'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
					'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
				),
			)
		);

		// shapely_category_page_section
		$wp_customize->add_setting(
			'show_category_on_category_page',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'show_category_on_category_page',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Category on Posts', 'shapely' ),
				'description' => esc_html__( 'Show/hide posts\' categories from the Category Page', 'shapely' ),
				'section'     => 'shapely_blog_section',
			)
		);

		// Global category display setting
		$wp_customize->add_setting(
			'show_categories_globally',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'show_categories_globally',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Categories Globally', 'shapely' ),
				'description' => esc_html__( 'Show/hide categories on all blog pages and single posts', 'shapely' ),
				'section'     => 'shapely_blog_section',
			)
		);

		if ( post_type_exists( 'jetpack-portfolio' ) ) {

			// Add Projects Settings
			$wp_customize->add_panel(
				'shapely_projects_options',
				array(
					'capability'     => 'edit_theme_options',
					'theme_supports' => '',
					'title'          => esc_html__( 'Projects Settings', 'shapely' ),
					'description'    => esc_html__( 'Panel to update projects related options', 'shapely' ),
					'priority'       => 10,
				)
			);
			$wp_customize->add_section(
				'shapely_projects_section',
				array(
					'title'    => esc_html__( 'Projects Page Settings', 'shapely' ),
					'panel'    => 'shapely_projects_options',
					'priority' => 33,
				)
			);

			$wp_customize->add_section(
				'shapely_single_project_section',
				array(
					'title'    => esc_html__( 'Project Single Settings', 'shapely' ),
					'panel'    => 'shapely_projects_options',
					'priority' => 35,
				)
			);

			// Projects Archive Page
			$wp_customize->add_setting(
				'portfolio_archive_title',
				array(
					'default'           => 1,
					'sanitize_callback' => 'shapely_sanitize_checkbox',
				)
			);
			$wp_customize->add_control(
				'portfolio_archive_title',
				array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Show Portfolio Archive Title', 'shapely' ),
					'description' => esc_html__( 'Show/hide the title from the Portfolio Archive Page', 'shapely' ),
					'section'     => 'shapely_projects_section',
				)
			);
			$wp_customize->add_setting(
				'portfolio_name',
				array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'portfolio_name',
				array(
					'label'       => esc_html__( 'Portfolio Archive Title', 'shapely' ),
					'description' => esc_html__( 'Add a title on the Portfolio Archive Page.', 'shapely' ),
					'section'     => 'shapely_projects_section',
				)
			);

			$wp_customize->add_setting(
				'portfolio_description',
				array(
					'default'           => '',
					'sanitize_callback' => 'wp_kses_post',
				)
			);
			$wp_customize->add_control(
				'portfolio_description',
				array(
					'type'        => 'textarea',
					'label'       => esc_html__( 'Portfolio Archive Description', 'shapely' ),
					'description' => esc_html__( 'Add a description on the Portfolio Archive Page.', 'shapely' ),
					'section'     => 'shapely_projects_section',
				)
			);

			$wp_customize->add_setting(
				'projects_layout_view',
				array(
					'default'           => 'mansonry',
					'sanitize_callback' => 'shapely_sanitize_choice',
				)
			);

			$wp_customize->add_control(
				'projects_layout_view',
				array(
					'label'       => esc_html__( 'Projects Layout', 'shapely' ),
					'description' => esc_html__( 'Choose how you want to display projects', 'shapely' ),
					'section'     => 'shapely_projects_section',
					'type'        => 'select',
					'choices'     => array(
						'mansonry' => esc_html__( 'Masonry', 'shapely' ),
						'grid'     => esc_html__( 'Grid', 'shapely' ),
					),
				)
			);

			$wp_customize->add_setting(
				'projects_layout_template',
				array(
					'default'           => 'full-width',
					'sanitize_callback' => 'shapely_sanitize_blog_layout',
				)
			);

			$wp_customize->add_control(
				'projects_layout_template',
				array(
					'label'       => esc_html__( 'Projects Template', 'shapely' ),
					'description' => esc_html__( 'Choose the template for your projects archive page', 'shapely' ),
					'section'     => 'shapely_projects_section',
					'type'        => 'select',
					'choices'     => array(
						'full-width'    => esc_html__( 'Full Width', 'shapely' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
						'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
						'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
					),
				)
			);
		}

		// Single Project
		$wp_customize->add_setting(
			'project_title_in_header',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'title_above_project',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_date',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_category',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_author',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		$wp_customize->add_setting(
			'project_first_letter_caps',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_tags_post_meta',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'related_projects_area',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_author_area',
			array(
				'default'           => 1,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_author_left_side',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);
		$wp_customize->add_setting(
			'project_author_email',
			array(
				'default'           => 0,
				'sanitize_callback' => 'shapely_sanitize_checkbox',
			)
		);

		// Single Project Settings
		$wp_customize->add_control(
			'project_title_in_header',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show title in header', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the project title from callout', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);

		$wp_customize->add_control(
			'title_above_project',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show title above project', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the project title above project content', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);

		$wp_customize->add_control(
			'project_date',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the date', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the date when project was published', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);

		$wp_customize->add_control(
			'project_author',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the author', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author who written the project under the project title', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);

		$wp_customize->add_control(
			'project_category',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show the project type', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the type of project', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);

		$wp_customize->add_control(
			'project_first_letter_caps',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'First Letter Caps', 'shapely' ),
				'description' => esc_html__( 'This will transform your first letter from a project into uppercase', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_control(
			'project_tags_post_meta',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Tags Project Meta', 'shapely' ),
				'description' => esc_html__( 'This will show/hide tags from the end of project', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_control(
			'related_projects_area',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Related Projects Area', 'shapely' ),
				'description' => esc_html__( 'This will enable/disable the related projects', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_control(
			'project_author_area',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Project Author Area', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author box', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_control(
			'project_author_left_side',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Project Author Left Side', 'shapely' ),
				'description' => esc_html__( 'This will move the author box from the bottom of the project on top on the left side', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_control(
			'project_author_email',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Author Email', 'shapely' ),
				'description' => esc_html__( 'This will show/hide the author\'s email from the author box', 'shapely' ),
				'section'     => 'shapely_single_project_section',
			)
		);
		$wp_customize->add_setting(
			'single_project_layout_template',
			array(
				'default'           => 'sidebar-right',
				'sanitize_callback' => 'shapely_sanitize_blog_layout',
			)
		);

		$wp_customize->add_control(
			'single_project_layout_template',
			array(
				'label'       => esc_html__( 'Single Project Template', 'shapely' ),
				'description' => esc_html__( 'Set the default template for single project', 'shapely' ),
				'section'     => 'shapely_single_project_section',
				'type'        => 'select',
				'choices'     => array(
					'full-width'    => esc_html__( 'Full Width', 'shapely' ),
					'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
					'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
					'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
				),
			)
		);
	}
endif;

add_action( 'customize_register', 'shapely_customizer' );

if ( ! function_exists( 'shapely_sanitize_logo_dimension' ) ) :
	/**
	 * Sanitize logo dimension setting.
	 */
	function shapely_sanitize_logo_dimension( $dimensions ) {
		if ( ! is_array( $dimensions ) ) {
			return array();
		}

		$width  = isset( $dimensions['width'] ) ? absint( $dimensions['width'] ) : 0;
		$height = isset( $dimensions['height'] ) ? absint( $dimensions['height'] ) : 0;

		// Cleared fields saved 0 x 0, which was then requested as the image size.
		if ( ! $width || ! $height ) {
			return array();
		}

		return array(
			'width'  => $width,
			'height' => $height,
		);
	}
endif;

if ( ! function_exists( 'shapely_active_callback_transparent_header' ) ) :
	/**
	 * Show the opacity slider only while the transparent header is on.
	 *
	 * @param WP_Customize_Control $control The slider control.
	 *
	 * @return bool
	 */
	function shapely_active_callback_transparent_header( $control ) {
		return shapely_sanitize_checkbox( $control->manager->get_setting( 'shapely_transparent_header' )->value() );
	}
endif;

if ( ! function_exists( 'shapely_sanitize_checkbox' ) ) :
	/**
	 * Sanitize checkbox for WordPress customizer.
	 */
	function shapely_sanitize_checkbox( $input ) {
		if ( in_array( $input, array( true, 1, '1' ), true ) ) {
			return true;
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'shapely_sanitize_blog_layout' ) ) :
	/**
	 * Sanitize layout control.
	 */
	function shapely_sanitize_blog_layout( $input, $setting = null ) {
		if ( in_array( $input, array( 'full-width', 'no-sidebar', 'sidebar-left', 'sidebar-right' ), true ) ) {
			return $input;
		}

		// Fall back to the setting's own default: projects default to full-width.
		return $setting instanceof WP_Customize_Setting ? $setting->default : 'sidebar-right';
	}
endif;

if ( ! function_exists( 'shapely_sanitize_choice' ) ) :
	/**
	 * Sanitize a select against the choices its control offers.
	 *
	 * The layout selects were saved through wp_kses_stripslashes(), which checks
	 * nothing, and an unknown value made get_template_part() find no template --
	 * the blog index then rendered no posts at all.
	 *
	 * @param string               $input   Submitted value.
	 * @param WP_Customize_Setting $setting Setting being saved.
	 *
	 * @return string
	 */
	function shapely_sanitize_choice( $input, $setting ) {
		$control = $setting->manager->get_control( $setting->id );
		$choices = $control ? $control->choices : array();
		$input   = (string) $input;

		// Demo content stores the blog layout hyphenated; the select uses underscores.
		if ( ! isset( $choices[ $input ] ) && isset( $choices[ str_replace( '-', '_', $input ) ] ) ) {
			$input = str_replace( '-', '_', $input );
		}

		return isset( $choices[ $input ] ) ? $input : $setting->default;
	}
endif;

if ( ! function_exists( 'shapely_sanitize_layout' ) ) :
	/**
	 * Adds sanitization callback function: Sidebar Layout.
	 */
	function shapely_sanitize_layout( $input ) {
		$shapely_site_layout = array(
			'pull-right' => esc_html__( 'Left Sidebar', 'shapely' ),
			'side-right' => esc_html__( 'Right Sidebar', 'shapely' ),
			'no-sidebar' => esc_html__( 'No Sidebar', 'shapely' ),
			'full-width' => esc_html__( 'Full Width', 'shapely' ),
		);

		if ( array_key_exists( $input, $shapely_site_layout ) ) {
			return $input;
		} else {
			return '';
		}
	}
endif;

if ( ! function_exists( 'shapely_customizer_custom_control_css' ) ) :
	/**
	 * Add CSS for custom controls.
	 */
	function shapely_customizer_custom_control_css() {
		?>
	<style>
		#customize-control-shapely-main_body_typography-size select, #customize-control-shapely-main_body_typography-face select, #customize-control-shapely-main_body_typography-style select {
			width: 60%;
		}

		.shapely-logo-dimension .half {
			width: 49%;
			float: left;
		}

		.shapely-logo-dimension .half:nth-child(2) {
			margin-left: 2%;
		}

		.shapely-logo-dimension .ratio {
			clear: both;
		}

		.widget-content .iris-picker .iris-strip .ui-slider-handle {
			top: auto;
			transform: translateX(0);
		}

		.widget-content .iris-picker .iris-slider-offset {
			margin: 0;
		}
	</style>
		<?php
	}
endif;

add_action( 'customize_controls_print_styles', 'shapely_customizer_custom_control_css' );

if ( ! function_exists( 'shapely_customizer_custom_label_css' ) ) :
	/**
	 * Add CSS styles for the custom label control
	 */
	function shapely_customizer_custom_label_css() {
		?>
	<style>
		.shapely-customizer-heading {
			margin-top: 15px;
			margin-bottom: 5px;
			padding: 10px 0;
			border-bottom: 1px solid #ddd;
			color: #333;
			font-size: 14px;
			font-weight: 600;
		}
		.shapely-customizer-description {
			margin-top: 5px;
			color: #555;
			font-style: italic;
		}

		/* Shapely_Control_Range: slider with its value alongside. */
		.shapely-range {
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.shapely-range input[type="range"] {
			flex: 1 1 auto;
			min-width: 0;
		}
		.shapely-range__value {
			flex: 0 0 auto;
			min-width: 3ch;
			text-align: right;
			font-variant-numeric: tabular-nums;
			color: #50575e;
		}

		/* Shapely_Section_Link: a section that is just an outbound button. */
		.shapely-link-section {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 10px;
			padding: 12px 15px;
			border-bottom: 1px solid #ddd;
			background: #fff;
		}
		.shapely-link-section__title {
			margin: 0;
			font-size: 14px;
			font-weight: 600;
			color: #50575e;
		}
		.shapely-link-section__button {
			flex: 0 0 auto;
		}
	</style>
		<?php
	}
endif;

add_action( 'customize_controls_print_styles', 'shapely_customizer_custom_label_css' );

/*
 * customizer-preview.js is gone: it listened for an 'update-inline-css'
 * message that only the retired Epsilon framework sent, via a WPUrls global
 * nothing defines.
 */

if ( ! function_exists( 'shapely_customize_preview' ) ) :
	/**
	 * Scripts for the Customizer controls pane.
	 *
	 * This depended on 'customize-preview', which boots the preview runtime in the
	 * controls frame too: it added the unsaved Customizer values to every jQuery
	 * AJAX request the pane made and intercepted its links and forms.
	 */
	function shapely_customize_preview() {
		wp_enqueue_script( 'shapely_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'jquery', 'customize-controls' ), SHAPELY_VERSION, true );
	}
endif;

add_action( 'customize_controls_enqueue_scripts', 'shapely_customize_preview' );
