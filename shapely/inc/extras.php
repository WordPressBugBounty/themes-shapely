<?php

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
if ( ! function_exists( 'shapely_body_classes' ) ) :
	function shapely_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		// Switch on the rules in style.css's "Customizer colours" section for
		// each colour the site owner has set.
		foreach ( array( 'link_color', 'link_hover_color', 'button_color', 'button_hover_color' ) as $mod ) {
			if ( get_theme_mod( $mod ) ) {
				$classes[] = 'shapely-' . str_replace( '_', '-', $mod );
			}
		}

		/*
		 * Read from the layout actually in use. This used to read a
		 * shapely_sidebar_position setting that was removed in 2016, so every
		 * page claimed has-sidebar-right whatever its layout.
		 */
		switch ( shapely_get_layout_class() ) {
			case 'sidebar-left':
				$classes[] = 'has-sidebar-left';
				break;
			case 'no-sidebar':
				$classes[] = 'has-no-sidebar';
				break;
			case 'full-width':
				$classes[] = 'has-full-width';
				break;
			default:
				$classes[] = 'has-sidebar-right';
		}

		return $classes;
	}
endif;

add_filter( 'body_class', 'shapely_body_classes' );

if ( ! function_exists( 'shapely_page_menu_args' ) ) :
	/**
	 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
	 *
	 * @param array $args Configuration arguments.
	 *
	 * @return array
	 */
	function shapely_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}
endif;

add_filter( 'wp_page_menu_args', 'shapely_page_menu_args' );

// Mark Posts/Pages as Untiled when no title is used
add_filter( 'the_title', 'shapely_title' );

if ( ! function_exists( 'shapely_title' ) ) :
	function shapely_title( $title ) {
		if ( '' === (string) $title ) {
			return esc_html__( 'Untitled', 'shapely' );
		} else {
			return $title;
		}
	}
endif;

/**
 * Password protected post form using Bootstrap classes
 */
add_filter( 'the_password_form', 'shapely_custom_password_form', 10, 3 );

if ( ! function_exists( 'shapely_custom_password_form' ) ) :
	/**
	 * @param string       $output           Core's form, replaced.
	 * @param WP_Post|null $post             Post being unlocked (WordPress 5.8+).
	 * @param string       $invalid_password Error for a wrong password (WordPress 6.8+).
	 *
	 * @return string
	 */
	function shapely_custom_password_form( $output = '', $post = null, $invalid_password = '' ) {
		$post  = get_post( $post );
		$label = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );
		$error = '';
		$aria  = '';

		if ( '' !== $invalid_password ) {
			$error = '<div class="post-password-form-invalid-password" role="alert"><p id="error-' . esc_attr( $label ) . '">' . esc_html( $invalid_password ) . '</p></div>';
			$aria  = ' aria-describedby="error-' . esc_attr( $label ) . '"';
		}

		// Core sends the visitor back here after a wrong password; without it
		// they landed on the referring page instead.
		$redirect = empty( $post->ID ) ? '' : '<input type="hidden" name="redirect_to" value="' . esc_attr( get_permalink( $post->ID ) ) . '" />';

		$o = '<form class="protected-post-form post-password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">' . $redirect . $error . '
  <div class="row">
    <div class="col-lg-10">
        <p>' . esc_html__( 'This post is password protected. To view it please enter your password below:', 'shapely' ) . '</p>
        <label for="' . esc_attr( $label ) . '">' . esc_html__( 'Password:', 'shapely' ) . ' </label>
      <div class="input-group">
        <input class="form-control" name="post_password" id="' . esc_attr( $label ) . '" type="password" spellcheck="false" required' . $aria . '>
        <span class="input-group-btn"><button type="submit" class="btn btn-default" name="Submit">' . esc_html__( 'Submit', 'shapely' ) . '</button>
        </span>
      </div>
    </div>
  </div>
</form>';

		return $o;
	}
endif;

// Add Bootstrap classes for table
add_filter( 'the_content', 'shapely_add_custom_table_class' );

if ( ! function_exists( 'shapely_add_custom_table_class' ) ) :
	function shapely_add_custom_table_class( $content ) {
		return preg_replace( '/(<table) ?(([^>]*)class="([^"]*)")?/', '$1 $3 class="$4 table table-hover" ', $content );
	}
endif;

if ( ! function_exists( 'shapely_navwalker_is_icon_class' ) ) :
	/**
	 * Whether a menu item's Title Attribute is an icon class list.
	 *
	 * The walker has always printed the Title Attribute as the class of an icon
	 * span, which is how some sites add glyphicon or Font Awesome icons to menu
	 * items. Anything else someone typed there is a tooltip, and used to come
	 * out as a meaningless class plus a stray space.
	 *
	 * @param string $value Title Attribute.
	 *
	 * @return bool
	 */
	function shapely_navwalker_is_icon_class( $value ) {
		$tokens = preg_split( '/\s+/', trim( (string) $value ) );

		foreach ( $tokens as $token ) {
			if ( ! preg_match( '/^(glyphicon(-[\w-]+)?|fa[srbl]?|fa-[\w-]+)$/', $token ) ) {
				return false;
			}
		}

		return '' !== $tokens[0];
	}
endif;

if ( ! function_exists( 'shapely_header_menu' ) ) :
	/**
	 * Header menu (should you choose to use one)
	 */
	function shapely_header_menu() {
		// display the WordPress Custom Menu if available
		wp_nav_menu(
			array(
				'menu_id'         => 'menu',
				'theme_location'  => 'primary',
				'depth'           => 0,
				'container'       => 'div',
				'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
				'menu_class'      => 'menu',
				'fallback_cb'     => 'Shapely_Bootstrap_Navwalker::fallback',
				'walker'          => new Shapely_Bootstrap_Navwalker(),
			)
		);
	}
endif;

if ( ! function_exists( 'shapely_footer_info' ) ) :
	/**
	 * function to show the footer info, copyright information
	 */
	function shapely_footer_info() {
		/* translators: 1: Colorlib link, 2: WordPress link */
		printf( esc_html__( 'Theme by %1$s Powered by %2$s', 'shapely' ), '<a href="https://colorlib.com/" target="_blank" rel="nofollow noopener" title="Colorlib">Colorlib</a>', '<a href="https://wordpress.org/" target="_blank" title="WordPress.org">WordPress</a>' );
	}
endif;

if ( ! function_exists( 'shapely_sanitize_color_theme_mod' ) ) :
	/**
	 * Validate a colour theme mod on read.
	 *
	 * The colours are written into a stylesheet. The Customizer sanitizes them on
	 * save, but a value set through set_theme_mod() -- a demo import, WP-CLI, a
	 * child theme -- never passes through it, and esc_attr() is not CSS escaping:
	 * "red}body{display:none" went straight into the page.
	 *
	 * @param mixed $value Stored value.
	 *
	 * @return string A #hex colour, or '' for unset or invalid.
	 */
	function shapely_sanitize_color_theme_mod( $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			return '';
		}

		return (string) sanitize_hex_color( $value );
	}
endif;

foreach ( array( 'link_color', 'link_hover_color', 'button_color', 'button_hover_color' ) as $shapely_color_mod ) {
	add_filter( 'theme_mod_' . $shapely_color_mod, 'shapely_sanitize_color_theme_mod' );
}
unset( $shapely_color_mod );

if ( ! function_exists( 'shapely_get_theme_options' ) ) :
	/**
	 * Print the CSS that depends on theme options.
	 *
	 * The colour rules that used to be built here live in style.css now, under
	 * "Customizer colours", switched on by body classes; the colours themselves
	 * reach them through the theme.json palette, which
	 * shapely_theme_json_customizer_colors() overrides.
	 */
	function shapely_get_theme_options() {
		echo '<style>';

		// Add header text color styling
		if ( shapely_header_text_color() ) {
			echo '.page-title-section .page-title {color:#' . esc_attr( shapely_header_text_color() ) . ' !important; }';
		}

		echo '</style>';
	}
endif;

if ( ! function_exists( 'shapely_theme_json_customizer_colors' ) ) :
	/**
	 * Put the Customizer colours into the theme.json palette.
	 *
	 * theme.json is the one source for the palette: style.css, the block
	 * styles and the editor all read its --wp--preset--color--* properties.
	 * Overriding the palette here, rather than printing a second :root block
	 * on the front end as 1.3.0-1.3.6 did, means the block editor and its
	 * colour pickers show the site owner's colours too.
	 *
	 * One theme mod per preset slug: 1.3.0 mapped link and button colour both
	 * onto 'primary', and whichever was saved last won.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme's theme.json data.
	 *
	 * @return WP_Theme_JSON_Data
	 */
	function shapely_theme_json_customizer_colors( $theme_json ) {
		$presets = array(
			'link_color'         => 'primary',
			'link_hover_color'   => 'primary-hover',
			'button_color'       => 'button',
			'button_hover_color' => 'button-hover',
		);

		$overrides = array();
		foreach ( $presets as $mod => $slug ) {
			$value = get_theme_mod( $mod );
			if ( $value ) {
				$overrides[ $slug ] = $value;
			}
		}

		if ( empty( $overrides ) ) {
			return $theme_json;
		}

		$data    = $theme_json->get_data();
		$palette = isset( $data['settings']['color']['palette'] ) ? $data['settings']['color']['palette'] : array();
		// The resolver keys presets by origin; a bare list is the file's own shape.
		if ( isset( $palette['theme'] ) ) {
			$palette = $palette['theme'];
		}

		foreach ( $palette as $i => $entry ) {
			if ( isset( $entry['slug'], $overrides[ $entry['slug'] ] ) ) {
				$palette[ $i ]['color'] = $overrides[ $entry['slug'] ];
			}
		}

		return $theme_json->update_with(
			array(
				'version'  => 2,
				'settings' => array(
					'color' => array(
						'palette' => array_values( $palette ),
					),
				),
			)
		);
	}
endif;

add_filter( 'wp_theme_json_data_theme', 'shapely_theme_json_customizer_colors' );

if ( ! function_exists( 'shapely_enqueue_theme_options_css' ) ) :
	/**
	 * Attach the customizer-driven CSS to the theme stylesheet.
	 *
	 * Previously this was echoed straight into wp_head as its own <style> block,
	 * which bypassed the dependency graph and any CSS concatenation. Buffering
	 * shapely_get_theme_options() keeps that function overridable by child themes
	 * while routing its output through wp_add_inline_style().
	 */
	function shapely_enqueue_theme_options_css() {
		ob_start();
		shapely_get_theme_options();
		$css = (string) ob_get_clean();

		// Drop the wrapping <style> tags the function emits.
		$css = trim( preg_replace( '#</?style[^>]*>#i', '', $css ) );

		if ( '' !== $css ) {
			wp_add_inline_style( 'shapely-style', $css );
		}
	}
endif;

add_action( 'wp_enqueue_scripts', 'shapely_enqueue_theme_options_css', 20 );

if ( ! function_exists( 'shapely_caption' ) ) :
	/**
	 * Customize the caption
	 *
	 * @param string $output
	 * @param string $attr
	 * @param string $content
	 *
	 * @return string
	 */
	function shapely_caption( $output, $attr, $content ) {
		if ( is_feed() ) {
			return $output;
		}

		$defaults = array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => '',
		);

		$attr = shortcode_atts( $defaults, $attr );

		// If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
		if ( 1 > $attr['width'] || empty( $attr['caption'] ) ) {
			return $content;
		}

		// Set up the attributes for the caption <div>
		$attributes = ' class="figure ' . esc_attr( $attr['align'] ) . '"';

		$output  = '<figure' . $attributes . '>';
		$output .= do_shortcode( $content );
		$output .= '<figcaption class="figure-caption text-center">' . $attr['caption'] . '</figcaption>';
		$output .= '</figure>';

		return $output;
	}
endif;

add_filter( 'img_caption_shortcode', 'shapely_caption', 10, 3 );

/*
 * shapely_add_top_level_menu_url() and shapely_make_top_level_menu_clickable()
 * were removed in 1.3.7. A menu item whose URL is "#" -- the usual way to add
 * a dropdown parent -- was rewritten to the permalink of the menu item post
 * itself, which is a 404, and lost its dropdown toggle, so the submenu could
 * not be opened on touch screens.
 */


if ( ! function_exists( 'shapely_excerpt_more' ) ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
	 *
	 * @return string 'Continue reading' link prepended with an ellipsis.
	 */
	function shapely_excerpt_more() {
		$link = sprintf(
			'<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Name of current post */
			sprintf( esc_html__( 'Continue reading %s', 'shapely' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
		return ' &hellip; ' . $link;
	}
endif;

add_filter( 'excerpt_more', 'shapely_excerpt_more' );

/*
 * Pagination
 */
if ( ! function_exists( 'shapely_pagination' ) ) {

	function shapely_pagination() {
		?>
		<div class="text-center">
			<?php // the_posts_pagination() emits its own <nav>; a second one here would nest navigation landmarks. ?>
			<div class="pagination">
				<?php
				/*
				 * <icon> is not an HTML element -- browsers parse it as an unknown
				 * inline element, so the arrows rendered but the links had no
				 * accessible name at all. Use <i> plus screen-reader text.
				 */
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '<i class="fa-solid fa-angle-left" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Previous page', 'shapely' ) . '</span>',
						'next_text' => '<i class="fa-solid fa-angle-right" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Next page', 'shapely' ) . '</span>',
					)
				);
				?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'shapely_search_form' ) ) :
	/**
	 * Search form
	 *
	 * @param string $form
	 *
	 * @return string
	 */
	function shapely_search_form() {
		/*
		 * The form is rebuilt from scratch rather than rewritten with a regex: the old
		 * approach spliced the incoming <form> attributes back in verbatim, which both
		 * emitted duplicate method/class/action attributes and reflected whatever any
		 * other plugin had put there straight into the markup.
		 *
		 * A unique id per instance keeps the label association valid when more than one
		 * search form is rendered on a page (header widget + sidebar widget).
		 */
		$field_id = wp_unique_id( 'shapely-search-field-' );

		return sprintf(
			'<form role="search" method="get" class="search-form" action="%1$s">
		<div class="search-form-wrapper">
			<label class="screen-reader-text" for="%2$s">%3$s</label>
			<input type="search" id="%2$s" class="search-field" placeholder="%4$s" value="%5$s" name="s" />
			<button type="submit" class="search-submit">
				<span class="screen-reader-text">%6$s</span>
				<i class="fa-solid fa-search" aria-hidden="true"></i>
			</button>
		</div>
	</form>',
			esc_url( home_url( '/' ) ),
			esc_attr( $field_id ),
			esc_html_x( 'Search for:', 'label', 'shapely' ),
			esc_attr_x( 'Search &hellip;', 'placeholder', 'shapely' ),
			esc_attr( get_search_query() ),
			esc_html_x( 'Search', 'submit button', 'shapely' )
		);
	}
endif;

add_filter( 'get_search_form', 'shapely_search_form' );

/*
 * Author bio on single page
 */
if ( ! function_exists( 'shapely_author_bio' ) ) {

	function shapely_author_bio() {

		if ( ! get_the_ID() ) {
			return;
		}

		$author_displayname       = get_the_author_meta( 'display_name' );
		$author_nickname          = get_the_author_meta( 'nickname' );
		$author_fullname          = ( '' !== get_the_author_meta( 'first_name' ) && '' !== get_the_author_meta( 'last_name' ) ) ? get_the_author_meta( 'first_name' ) . ' ' . get_the_author_meta( 'last_name' ) : '';
		$author_email             = get_the_author_meta( 'email' );
		$author_description       = get_the_author_meta( 'description' );
		$author_name              = '' !== trim( $author_nickname ) ? $author_nickname : ( '' !== trim( $author_displayname ) ? $author_displayname : $author_fullname );
		$show_athor_email         = get_theme_mod( 'post_author_email', false );
		$show_project_athor_email = get_theme_mod( 'project_author_email', false );
		?>

		<div class="author-bio">
			<div class="row">
				<div class="col-sm-2">
					<div class="avatar">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
					</div>
				</div>
				<div class="col-sm-10">
					<span class="vcard author"><b class="fn"><?php echo esc_html( $author_name ); ?></b></span>
					<div>
						<?php
						if ( '' !== trim( $author_description ) ) {
							echo wp_kses_post( $author_description );
						}
						?>
					</div>
					<?php if ( ( $show_athor_email && ! is_singular( 'jetpack-portfolio' ) ) || ( is_singular( 'jetpack-portfolio' ) && $show_project_athor_email ) ) : ?>
						<a class="author-email" href="mailto:<?php echo esc_attr( antispambot( $author_email ) ); ?>"><?php echo esc_html( antispambot( $author_email ) ); ?></a>
					<?php endif ?>
					<ul class="list-inline social-list author-social">
						<?php
						$twitter_profile = get_the_author_meta( 'twitter' );
						if ( $twitter_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $twitter_profile ); ?>">
									<i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
								</a>
							</li>
							<?php
						}

						$fb_profile = get_the_author_meta( 'facebook' );
						if ( $fb_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $fb_profile ); ?>">
									<i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
								</a>
							</li>
							<?php
						}

						$dribble_profile = get_the_author_meta( 'dribble' );
						if ( $dribble_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $dribble_profile ); ?>">
									<i class="fa-brands fa-dribbble" aria-hidden="true"></i>
								</a>
							</li>
							<?php
						}

						$github_profile = get_the_author_meta( 'github' );
						if ( $github_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $github_profile ); ?>">
									<i class="fa-brands fa-github" aria-hidden="true"></i>
								</a>
							</li>
							<?php
						}

						$vimeo_profile = get_the_author_meta( 'vimeo' );
						if ( $vimeo_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $vimeo_profile ); ?>">
									<i class="fa-brands fa-vimeo-v" aria-hidden="true"></i>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<!--end of author-bio-->
		<?php
	}
}

if ( ! function_exists( 'shapely_cb_comment' ) ) :
	/**
	 * Custom comment template
	 */
	function shapely_cb_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the comment template tags read it.

		$add_below = 'div' === $args['style'] ? 'comment' : 'div-comment';
		?>
	<li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
		<?php if ( 'div' !== $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<?php endif; ?>
			<div class="avatar">
				<?php
				if ( 0 !== (int) $args['avatar_size'] ) {
					echo get_avatar( $comment, $args['avatar_size'] );
				}
				?>
			</div>
			<div class="comment">
				<b class="fn"><?php echo esc_html( get_comment_author() ); ?></b>
				<div class="comment-date">
					<time datetime="<?php echo esc_attr( get_comment_time( 'c' ) ); ?>">
						<?php
						/* translators: 1: date, 2: time */
						printf( esc_html__( '%1$s at %2$s', 'shapely' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) );
						?>
					</time>
						<?php
						edit_comment_link( esc_html__( 'Edit', 'shapely' ), '  ', '' );
						?>
				</div>
					<?php
					$comment_reply_args = array(
						'add_below' => $add_below,
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
					);

					comment_reply_link( array_merge( $args, $comment_reply_args ) );
					?>

					<?php if ( '0' === $comment->comment_approved ) : ?>
					<p>
						<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'shapely' ); ?></em>
						<br />
					</p>
				<?php endif; ?>

				<?php comment_text(); ?>

			</div>
				<?php if ( 'div' !== $args['style'] ) : ?>
		</div>
	<?php endif; ?>
		<?php
		// No closing </li>: Walker_Comment::end_el() prints it after any replies,
		// so closing it here nested every reply list outside its parent comment.
	}
endif;

/*
 * Filter to replace
 * Reply button class
 */
if ( ! function_exists( 'shapely_reply_link_class' ) ) :
	function shapely_reply_link_class( $link ) {
		/*
		 * Adds the button classes and keeps comment-reply-link, which core's
		 * comment-reply.js looks for to move the form under the comment. The
		 * old version swapped that class out and matched only single quotes,
		 * which core stopped printing, so it had silently done nothing.
		 */
		return preg_replace( '/class=([\'"])comment-reply-link/', 'class=$1comment-reply-link btn btn-xs comment-reply', $link, 1 );
	}
endif;

/*
 * Comment form template
 */
if ( ! function_exists( 'shapely_custom_comment_form' ) ) :
	function shapely_custom_comment_form() {
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		// Name and email were always required, even with "Comment author must
		// fill out name and email" switched off. The website field stays
		// type="text": the theme's input styles do not cover type="url".
		$required = $req ? ' required' : '';
		$fields   = array(
			'author' => '<label for="author" class="screen-reader-text">' . esc_html__( 'Your Name', 'shapely' ) . '</label><input id="author" placeholder="' . esc_attr__( 'Your Name', 'shapely' ) . ( $req ? '*' : '' ) . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" autocomplete="name"' . $required . ' />',
			'email'  => '<label for="email" class="screen-reader-text">' . esc_html__( 'Email Address', 'shapely' ) . '</label><input id="email" name="email" type="email" placeholder="' . esc_attr__( 'Email Address', 'shapely' ) . ( $req ? '*' : '' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" autocomplete="email"' . $required . ' />',
			'url'    => '<label for="url" class="screen-reader-text">' . esc_html__( 'Your Website (optional)', 'shapely' ) . '</label><input placeholder="' . esc_attr__( 'Your Website (optional)', 'shapely' ) . '" id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" autocomplete="url" />',
		);

		$comments_args = array(
			'label_submit'  => esc_html__( 'Leave Comment', 'shapely' ),
			'comment_field' => '<label for="comment" class="screen-reader-text">' . esc_html_x( 'Comment', 'noun', 'shapely' ) . '</label><textarea placeholder="' . esc_attr_x( 'Comment', 'noun', 'shapely' ) . '" id="comment" name="comment" cols="45" rows="8" required></textarea>',
			// Core's own filter: these fields replace core's defaults, so plugins
			// that extend the comment form still get to add theirs.
			'fields'        => apply_filters( 'comment_form_default_fields', $fields ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		);

		return $comments_args;
	}
endif;

/*
 * Header Logo
 */
if ( ! function_exists( 'shapely_get_header_logo' ) ) :
	function shapely_get_header_logo() {
		$logo_dimensions = get_theme_mod( 'shapely_logo_dimension', array() );
		if ( is_array( $logo_dimensions ) && ! empty( $logo_dimensions['width'] ) && ! empty( $logo_dimensions['height'] ) ) {
			$dimension = array( $logo_dimensions['width'], $logo_dimensions['height'] );
		} else {
			$dimension = 'full';
		}

		$custom_logo_id = get_theme_mod( 'custom_logo' );
		// We have a logo. Logo is go.
		if ( $custom_logo_id ) {
			$custom_logo_attr = array(
				'class'    => 'custom-logo logo',
				'itemprop' => 'logo',
			);
			/*
			 * If the logo alt attribute is empty, get the site title and explicitly
			 * pass it to the attributes used by wp_get_attachment_image().
			 */
			$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
			}
			/*
			 * If the alt attribute is not empty, there's no need to explicitly pass
			 * it because wp_get_attachment_image() already adds the alt attribute.
			 */
			$html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>', esc_url( home_url( '/' ) ), wp_get_attachment_image( $custom_logo_id, $dimension, false, $custom_logo_attr ) );
		} elseif ( is_customize_preview() ) {
			// No logo, but in the Customizer: leave a placeholder for the live preview.
			$html = sprintf( '<a href="%1$s" class="custom-logo-link"><img class="custom-logo" style="display:none;" alt="" /><span class="site-title">%2$s</span></a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
		} else {
			$html = sprintf( '<a href="%1$s" class="custom-logo-link"><span class="site-title">%2$s</span></a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
		}

		/*
		 * Every branch above is built from escaped parts; kses guards against
		 * filtered attachment markup. wp_kses_post() alone stripped srcset/sizes,
		 * so the logo was blurry on high-density screens, and the itemprop
		 * attributes added above.
		 */
		$allowed                     = wp_kses_allowed_html( 'post' );
		$allowed['img']              = array_merge( $allowed['img'], shapely_image_allowed_html()['img'], array( 'itemprop' => true ) );
		$allowed['a']['itemprop']    = true;
		$allowed['span']['itemprop'] = true;
		echo wp_kses( $html, $allowed );
	}
endif;

/*
 * Get layout class from single page
 * then from themeoptions
 */
if ( ! function_exists( 'shapely_get_layout_class' ) ) :
	function shapely_get_layout_class() {
		if ( is_singular( 'jetpack-portfolio' ) ) {
			$layout_class = get_theme_mod( 'single_project_layout_template', 'sidebar-right' );
		} elseif ( is_single() ) {
			$template     = get_page_template_slug();
			$layout_class = '';
			switch ( $template ) {
				case 'page-templates/full-width.php':
					$layout_class = 'full-width';
					break;
				case 'page-templates/no-sidebar.php':
					$layout_class = 'no-sidebar';
					break;
				case 'page-templates/sidebar-left.php':
					$layout_class = 'sidebar-left';
					break;
				case 'page-templates/sidebar-right.php':
					$layout_class = 'sidebar-right';
					break;
				default:
					$layout_class = get_theme_mod( 'single_post_layout_template', 'sidebar-right' );
					break;
			}
		} elseif ( is_singular() ) {
			$template     = get_page_template_slug();
			$layout_class = '';
			switch ( $template ) {
				case 'page-templates/full-width.php':
				case 'page-templates/template-blocks.php':
					$layout_class = 'full-width';
					break;
				case 'page-templates/no-sidebar.php':
					$layout_class = 'no-sidebar';
					break;
				case 'page-templates/sidebar-left.php':
					$layout_class = 'sidebar-left';
					break;
				case 'page-templates/sidebar-right.php':
					$layout_class = 'sidebar-right';
					break;
				default:
					$layout_class = get_theme_mod( 'blog_layout_template', 'sidebar-right' );
					break;
			}
		} elseif ( is_archive() && is_post_type_archive( 'jetpack-portfolio' ) ) {
			$layout_class = get_theme_mod( 'projects_layout_template', 'full-width' );
		} else {
			$layout_class = get_theme_mod( 'blog_layout_template', 'sidebar-right' );
		}

		return $layout_class;
	}
endif;

if ( ! function_exists( 'shapely_get_post_layout_class' ) ) :
	/**
	 * The layout a given post renders with, outside the main query.
	 *
	 * Mirrors the singular branches of shapely_get_layout_class(), which relies on
	 * the query conditionals and so cannot answer for the post open in the editor.
	 *
	 * @param int|WP_Post|null $post Post, or null for the current one.
	 *
	 * @return string full-width | no-sidebar | sidebar-left | sidebar-right
	 */
	function shapely_get_post_layout_class( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return get_theme_mod( 'blog_layout_template', 'sidebar-right' );
		}

		if ( 'jetpack-portfolio' === $post->post_type ) {
			return get_theme_mod( 'single_project_layout_template', 'sidebar-right' );
		}

		$templates = array(
			'page-templates/full-width.php'      => 'full-width',
			'page-templates/template-blocks.php' => 'full-width',
			'page-templates/no-sidebar.php'      => 'no-sidebar',
			'page-templates/sidebar-left.php'    => 'sidebar-left',
			'page-templates/sidebar-right.php'   => 'sidebar-right',
		);
		$template  = get_page_template_slug( $post );

		if ( isset( $templates[ $template ] ) ) {
			return $templates[ $template ];
		}

		// is_single() excludes pages and attachments; those follow the blog setting.
		if ( in_array( $post->post_type, array( 'page', 'attachment' ), true ) ) {
			return get_theme_mod( 'blog_layout_template', 'sidebar-right' );
		}

		return get_theme_mod( 'single_post_layout_template', 'sidebar-right' );
	}
endif;

/*
 * Show Sidebar or not
 */
if ( ! function_exists( 'shapely_show_sidebar' ) ) :
	function shapely_show_sidebar() {
		global $post;
		$show_sidebar = true;

		// The site_layout post meta was written by early releases. The
		// shapely_sidebar_position theme mod this also read was removed in 2016.
		if ( is_singular() && $post && in_array( get_post_meta( $post->ID, 'site_layout', true ), array( 'no-sidebar', 'full-width' ), true ) ) {
			$show_sidebar = false;
		}

		return $show_sidebar;
	}
endif;

/*
 * Top Callout
 */
if ( ! function_exists( 'shapely_header_text_color' ) ) :
	/**
	 * The header text colour as a hex value without the hash, or ''.
	 *
	 * Core stores 'blank' when "Display Site Title and Tagline" is unticked,
	 * which the theme printed as color:#blank.
	 *
	 * @return string
	 */
	function shapely_header_text_color() {
		$color = sanitize_hex_color_no_hash( (string) get_theme_mod( 'header_textcolor', '' ) );

		return $color ? $color : '';
	}
endif;

if ( ! function_exists( 'shapely_top_callout' ) ) :
	function shapely_top_callout() {
		if ( ( get_theme_mod( 'portfolio_archive_title', true ) && is_post_type_archive( 'jetpack-portfolio' ) ) || ( get_theme_mod( 'top_callout', true ) && ! is_single() && ! is_post_type_archive( 'jetpack-portfolio' ) ) || ( is_single() && get_theme_mod( 'title_in_header', true ) && ! is_singular( 'jetpack-portfolio' ) ) || ( get_theme_mod( 'project_title_in_header', true ) && is_singular( 'jetpack-portfolio' ) ) ) {
			$header = get_header_image();
			?>
		<section class="page-title-section bg-secondary <?php echo $header ? 'header-image-bg' : ''; ?>" <?php echo $header ? 'style="background-image:url(' . esc_url( $header ) . ')"' : ''; ?> aria-labelledby="shapely-page-title">
			<div class="container">
				<div class="row">
					<?php
					$breadcrumbs_enabled = false;
					$title_in_post       = true;

					if ( function_exists( 'yoast_breadcrumb' ) ) {
						/*
						 * Yoast SEO moved `breadcrumbs-enable` from the `wpseo_internallinks`
						 * option into `wpseo_titles` in version 7.0. Read the modern location
						 * first and fall back to the legacy one, guarding both against the
						 * `false` that get_option() returns when the option does not exist.
						 */
						$options = get_option( 'wpseo_titles' );
						if ( ! is_array( $options ) || ! array_key_exists( 'breadcrumbs-enable', $options ) ) {
							$options = get_option( 'wpseo_internallinks' );
						}

						$breadcrumbs_enabled = is_array( $options ) && ! empty( $options['breadcrumbs-enable'] );
						$title_in_post       = get_theme_mod( 'hide_post_title', true );
					}

					if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
						// Rank Math only defines this helper when breadcrumbs are switched on.
						$breadcrumbs_enabled = true;
						$title_in_post       = get_theme_mod( 'hide_post_title', true );
					}
					$header_color = shapely_header_text_color();
					?>
					<?php if ( $title_in_post ) : ?>
						<div class="<?php echo $breadcrumbs_enabled ? 'col-md-6 col-sm-6 col-xs-12' : 'col-xs-12'; ?>">
							<h3 class="page-title" id="shapely-page-title" <?php echo $header_color ? 'style="color:#' . esc_attr( $header_color ) . '"' : ''; ?>>
								<?php
								if ( is_home() ) {
									echo esc_html( get_theme_mod( 'blog_name' ) ? get_theme_mod( 'blog_name' ) : __( 'Blog', 'shapely' ) );
								} elseif ( is_search() ) {
									echo esc_html__( 'Search', 'shapely' );
								} elseif ( is_archive() ) {
									if ( is_post_type_archive( 'jetpack-portfolio' ) ) {
										// A cleared field saves '', which printed an empty heading.
										$portfolio_title = get_theme_mod( 'portfolio_name' ) ? get_theme_mod( 'portfolio_name' ) : __( 'Portfolio', 'shapely' );
										echo esc_html( $portfolio_title );
									} else {
										// Core wraps the archive title in a <span> since WP 6.7.
										echo wp_kses_post( get_the_archive_title() );
									}
								} elseif ( is_singular() ) {
									// single_post_title() echoes on its own; echoing its void return is a no-op.
									single_post_title();
								} else {
									echo esc_html( get_the_title() );
								}
								?>
							</h3>
							<?php

							if ( is_archive() && is_post_type_archive( 'jetpack-portfolio' ) ) {
								$portfolio_description = get_theme_mod( 'portfolio_description' );
								if ( $portfolio_description ) {
									echo '<p>' . wp_kses_post( nl2br( $portfolio_description ) ) . '</p>';
								}
							}

							?>
						</div>
					<?php endif; ?>
						<?php if ( $breadcrumbs_enabled ) { ?>
							<?php if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
							<div class="<?php echo $title_in_post ? 'col-md-6 col-sm-6' : ''; ?> col-xs-12 text-right">
								<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
							</div>
						<?php } ?>
						<!-- Rank Math SEO's Breadcrumb Function -->
							<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { ?>
							<div class="<?php echo $title_in_post ? 'col-md-6 col-sm-6' : ''; ?> col-xs-12 text-right">
								<?php rank_math_the_breadcrumbs(); ?>
							</div>
						<?php } ?>
					<?php } ?>

				</div>
				<!--end of row-->
			</div>
			<!--end of container-->
		</section>
			<?php
		} else {
			?>
			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
			<div class="container mt20">
				<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
			</div>
		<?php } ?>

		<!-- Rank Math SEO's Breadcrumb Function -->
			<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { ?>
			<div class="container mt20">
				<?php rank_math_the_breadcrumbs(); ?>
			</div>
		<?php } ?>
			<?php
		}
	}
endif;

/*
 * Footer Callout
 */
if ( ! function_exists( 'shapely_footer_callout' ) ) :
	function shapely_footer_callout() {
		if ( '' !== (string) get_theme_mod( 'footer_callout_text' ) ) {
			?>
		<section class="cfa-section bg-secondary">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center p0">
						<div class="overflow-hidden">
							<div class="col-sm-9">
								<h3 class="cfa-text"><?php echo wp_kses_post( nl2br( get_theme_mod( 'footer_callout_text' ) ) ); ?></h3>
							</div>
							<div class="col-sm-3">
								<a href="<?php echo esc_url( get_theme_mod( 'footer_callout_link' ) ); ?>" class="mb0 btn btn-lg btn-filled cfa-button">
									<?php echo wp_kses_post( get_theme_mod( 'footer_callout_btntext' ) ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
			<?php
		}
	}
endif;

// Check WooCommerce
if ( ! function_exists( 'shapely_is_woocommerce_activated' ) ) {
	function shapely_is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * Add container to Rank Math breadcrumbs.
 */
add_action(
	'rank_math/frontend/breadcrumb/args',
	function ( $args ) {
		$args['wrap_before'] = '<p id="breadcrumbs">';
		$args['wrap_after']  = '</p>';
		return $args;
	}
);

if ( ! function_exists( 'shapely_custom_excerpt_length' ) ) :
	/**
	 * Custom excerpt length
	 *
	 * @param int $length
	 *
	 * @return int
	 */
	function shapely_custom_excerpt_length() {
		return 20;
	}
endif;

add_filter( 'excerpt_length', 'shapely_custom_excerpt_length', 999 );

if ( ! function_exists( 'shapely_excerpt' ) ) :
	/**
	 * Custom excerpt
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	function shapely_excerpt( $length ) {
		$post = get_post();
		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		// Strip shortcodes before tags, otherwise shortcode attributes survive as text.
		$content = strip_shortcodes( $post->post_content );
		$content = wp_strip_all_tags( $content );
		$content = trim( $content );

		$length = absint( $length );
		if ( '' === $content || $length < 1 || mb_strlen( $content ) <= $length ) {
			return $content;
		}

		// mb_substr keeps multibyte characters intact where the old substr() split them.
		$content = mb_substr( $content, 0, $length );

		// Trim back to the last whole word.
		$trimmed = preg_replace( '/\s+\S*$/u', '', $content );
		if ( null !== $trimmed && '' !== $trimmed ) {
			$content = $trimmed;
		}

		return $content . '...';
	}
endif;

if ( ! function_exists( 'shapely_get_thumbnail_url' ) ) :
	/**
	 * Get thumbnail URL
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	function shapely_get_thumbnail_url( $size = 'full' ) {
		$post_id = get_the_ID();

		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
			// wp_get_attachment_image_src() returns false when the attachment is gone.
			if ( is_array( $image ) && ! empty( $image[0] ) ) {
				return $image[0];
			}
		}

		return shapely_get_placeholder_image_url();
	}
endif;

if ( ! function_exists( 'shapely_image_allowed_html' ) ) :
	/**
	 * kses allowlist for thumbnail markup.
	 *
	 * The per-template allowlists this replaces omitted `loading` and `decoding`,
	 * so kses stripped them back off every listing image and silently disabled
	 * WordPress' native lazy loading. `<picture>`/`<source>` are permitted so
	 * WebP/AVIF plugins survive, and the data-* attributes keep lazy-load plugins
	 * working.
	 *
	 * @return array
	 */
	function shapely_image_allowed_html() {
		return apply_filters(
			'shapely_image_allowed_html',
			array(
				'img'      => array(
					'src'           => true,
					'srcset'        => true,
					'sizes'         => true,
					'alt'           => true,
					'class'         => true,
					'id'            => true,
					'style'         => true,
					'title'         => true,
					'width'         => true,
					'height'        => true,
					'loading'       => true,
					'decoding'      => true,
					'fetchpriority' => true,
					'data-src'      => true,
					'data-srcset'   => true,
					'data-sizes'    => true,
				),
				'picture'  => array( 'class' => true ),
				'source'   => array(
					'srcset' => true,
					'sizes'  => true,
					'type'   => true,
					'media'  => true,
				),
				'noscript' => array(),
			)
		);
	}
endif;

if ( ! function_exists( 'shapely_get_placeholder_image_url' ) ) :
	/**
	 * Resolve the placeholder image URL honouring the customizer settings.
	 *
	 * Single source of truth for every "post has no featured image" fallback.
	 *
	 * @param string $default_file Bundled image to fall back to.
	 *
	 * @return string Placeholder URL, or an empty string when placeholders are disabled.
	 */
	function shapely_get_placeholder_image_url( $default_file = 'placeholder.jpg' ) {
		if ( ! get_theme_mod( 'shapely_placeholder_image_enabled', 1 ) ) {
			return '';
		}

		$custom_placeholder = get_theme_mod( 'shapely_placeholder_image', '' );
		if ( is_string( $custom_placeholder ) && '' !== $custom_placeholder ) {
			return $custom_placeholder;
		}

		return get_template_directory_uri() . '/assets/images/' . ltrim( $default_file, '/' );
	}
endif;

if ( ! function_exists( 'shapely_get_thumbnail' ) ) :
	/**
	 * Get thumbnail
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	function shapely_get_thumbnail( $size = 'full', $default_file = 'placeholder.jpg' ) {
		$post_id = get_the_ID();

		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail( $post_id, $size );
		}

		$placeholder = shapely_get_placeholder_image_url( $default_file );
		if ( '' === $placeholder ) {
			return '';
		}

		return sprintf(
			'<img class="wp-post-image" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
			esc_url( $placeholder ),
			esc_attr( get_the_title() )
		);
	}
endif;

if ( ! function_exists( 'shapely_legacy_asset_handles' ) ) :
	/**
	 * The generic asset handles this theme used before 1.3.0.
	 *
	 * @return array old handle => current handle
	 */
	function shapely_legacy_asset_handles() {
		return array(
			'bootstrap'          => 'shapely-bootstrap',
			'flexslider'         => 'shapely-flexslider',
			'owl.carousel'       => 'shapely-owl-carousel',
			'owl.carousel.theme' => 'shapely-owl-carousel-theme',
		);
	}
endif;

if ( ! function_exists( 'shapely_register_legacy_handle_aliases' ) ) :
	/**
	 * Keep pre-1.3.0 handle names working for child themes.
	 *
	 * 1.3.0 renamed four generic handles so a plugin claiming 'bootstrap' could
	 * no longer suppress the theme's own stylesheet. That fixed a real fault,
	 * but it silently broke the most common child-theme pattern there is:
	 *
	 *     wp_dequeue_style( 'bootstrap' );   // swap in my own build
	 *
	 * After the rename that call names a handle nobody registers, so it does
	 * nothing, the parent's Bootstrap loads again, and it lands on top of
	 * whatever the child theme had replaced it with. Nothing errors -- the
	 * layout just changes under them on update.
	 *
	 * So each old name is registered as an empty placeholder and enqueued. If a
	 * child theme dequeues it, shapely_mirror_legacy_handle_dequeues() sees that
	 * and drops the real stylesheet too, which is what the old code did.
	 *
	 * The placeholder is only registered when nothing else has claimed the
	 * handle, so this does not put the theme back in the business of squatting
	 * on generic names.
	 */
	function shapely_register_legacy_handle_aliases() {
		foreach ( shapely_legacy_asset_handles() as $legacy => $current ) {
			if ( wp_style_is( $legacy, 'registered' ) ) {
				// Someone else owns this handle; leave it alone.
				continue;
			}

			// src of false registers the handle without ever requesting a file.
			wp_register_style( $legacy, false, array(), SHAPELY_VERSION );
			wp_enqueue_style( $legacy );
			shapely_owned_legacy_handles( $legacy );
		}
	}
endif;

if ( ! function_exists( 'shapely_owned_legacy_handles' ) ) :
	/**
	 * The legacy handles this request registered as placeholders.
	 *
	 * @param string|null $handle Handle to record, or null to read the list.
	 *
	 * @return array
	 */
	function shapely_owned_legacy_handles( $handle = null ) {
		static $owned = array();

		if ( null !== $handle ) {
			$owned[ $handle ] = true;
		}

		return $owned;
	}
endif;

add_action( 'wp_enqueue_scripts', 'shapely_register_legacy_handle_aliases', 11 );

if ( ! function_exists( 'shapely_mirror_legacy_handle_dequeues' ) ) :
	/**
	 * Apply a dequeue of an old handle to the stylesheet it used to name.
	 *
	 * Runs late so child themes and plugins -- which conventionally hook
	 * wp_enqueue_scripts at 10 or 20 -- have already had their say.
	 */
	function shapely_mirror_legacy_handle_dequeues() {
		$owned = shapely_owned_legacy_handles();

		foreach ( shapely_legacy_asset_handles() as $legacy => $current ) {
			/*
			 * Only mirror a placeholder the theme registered itself. A plugin
			 * that registers its own 'bootstrap' without enqueueing it on this
			 * page would otherwise read as a dequeue, and the theme's grid
			 * stylesheet went with it.
			 */
			if ( ! isset( $owned[ $legacy ] ) ) {
				continue;
			}

			if ( ! wp_style_is( $legacy, 'registered' ) ) {
				// Deregistered outright, which the old code treated as removal.
				wp_dequeue_style( $current );
				continue;
			}

			if ( ! wp_style_is( $legacy, 'enqueued' ) ) {
				wp_dequeue_style( $current );
			}
		}
	}
endif;

add_action( 'wp_enqueue_scripts', 'shapely_mirror_legacy_handle_dequeues', 100 );

if ( ! function_exists( 'shapely_call_related_posts_class' ) ) :
	/**
	 * Start the related posts carousel when "Related Posts Area" is switched on.
	 */
	function shapely_call_related_posts_class() {
		if ( get_theme_mod( 'related_posts_area', true ) ) {
			Shapely_Related_Posts::get_instance();
		}
	}
endif;
add_action( 'wp_loaded', 'shapely_call_related_posts_class' );
