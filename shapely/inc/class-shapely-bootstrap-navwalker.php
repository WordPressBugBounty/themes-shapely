<?php
/**
 * Bootstrap 3 nav walker for the primary menu.
 *
 * Derived from WP Bootstrap Navwalker.
 *
 * @package Shapely
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Shapely_Bootstrap_Navwalker' ) ) :
	/**
	 * Prints the primary menu as Bootstrap 3 dropdown markup.
	 *
	 * It used to be declared as Wp_Bootstrap_Navwalker, and only when no class
	 * of that name existed yet. That is also the name of the widely bundled
	 * Bootstrap 4/5 walker, so with any plugin that ships it loaded first, the
	 * theme's menu was built by the wrong walker in the wrong markup. The old
	 * name remains an alias below.
	 */
	class Shapely_Bootstrap_Navwalker extends Walker_Nav_Menu {

		/**
		 * @see   Walker::start_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of page. Used for padding.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			// No role="menu" here: that ARIA pattern promises arrow-key menu semantics
			// this navigation does not implement, which is worse than no role at all.
			$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
		}

		/**
		 * @see   Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output       Passed by reference. Used to append additional content.
		 * @param object $item         Menu item data object.
		 * @param int    $depth        Depth of menu item. Used for padding.
		 * @param int    $current_page Menu item ID.
		 * @param object $args
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core's own menu filters: this replaces Walker_Nav_Menu::start_el(), and plugins hook them.
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			$extra  = get_post_meta( $item->ID, '_menu_item_extra', true );
			$widget = get_post_meta( $item->ID, '_menu_item_widget', true );

			/*
			 * Nav menu items routinely carry a null attr_title/title. Casting to string
			 * keeps strcasecmp() from raising the PHP 8.1 "passing null to non-nullable
			 * parameter" deprecation once per menu item on every page load.
			 */
			$attr_title = (string) $item->attr_title;
			$item_title = (string) $item->title;

			// Core passes $args as an object; normalise so property reads never fatal.
			$args         = is_array( $args ) ? (object) $args : $args;
			$has_children = ! empty( $args->has_children );

			/**
			 * Dividers, Headers or Disabled
			 * =============================
			 * Determine whether the item is a Divider, Header, Disabled or regular
			 * menu item. To prevent errors we use the strcasecmp() function to so a
			 * comparison that is not case sensitive. The strcasecmp() function returns
			 * a 0 if the strings are equal.
			 */
			if ( 0 === strcasecmp( $attr_title, 'divider' ) && 1 === $depth ) {
				$output .= $indent . '<li role="presentation" class="divider">';
			} elseif ( 0 === strcasecmp( $item_title, 'divider' ) && 1 === $depth ) {
				$output .= $indent . '<li role="presentation" class="divider">';
			} elseif ( 0 === strcasecmp( $attr_title, 'dropdown-header' ) && 1 === $depth ) {
				$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_html( $item_title );
			} elseif ( 0 === strcasecmp( $attr_title, 'disabled' ) ) {
				$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_html( $item_title ) . '</a>';
			} else {

				$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
				$classes[] = 'menu-item-' . $item->ID;

				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

				if ( $has_children ) {
					$class_names .= ' dropdown';
				}

				if ( in_array( 'current-menu-item', $classes, true ) ) {
					$class_names .= ' active';
				}

				$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

				$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
				$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

				$output .= $indent . '<li' . $id . $class_names . '>';

				$atts = array();
				// Only a title the site owner entered: defaulting it to the link text
				// made screen readers announce every menu item twice.
				$atts['title']  = ! empty( $item->attr_title ) && ! shapely_navwalker_is_icon_class( $item->attr_title ) ? $item->attr_title : '';
				$atts['target'] = ! empty( $item->target ) ? $item->target : '';
				$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

				$atts['href'] = ! empty( $item->url ) ? $item->url : '';

				if ( 'shapely-section' === $extra ) {
					if ( ! is_front_page() ) {
						/*
						 * home_url(), not site_url(): this builds a link back to the front
						 * page plus an anchor. site_url() is where the WordPress files live,
						 * so on an install with WordPress in a subdirectory every one-page
						 * section link pointed at /wp/#section instead of /#section.
						 */
						$atts['href'] = home_url() . $item->url;
					}

					if ( '' !== $widget ) {
						$atts['data-scroll'] = $widget;
					}
				}

				$atts['aria-current'] = $item->current ? 'page' : '';

				$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

				$attributes = '';
				foreach ( $atts as $attr => $value ) {
					if ( ! empty( $value ) ) {
						$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}

				$item_output = isset( $args->before ) ? $args->before : '';

				/*
				 * Glyphicons
				 * ===========
				 * Since the the menu item is NOT a Divider or Header we check the see
				 * if there is a value in the attr_title property. If the attr_title
				 * property is NOT null we apply it as the class name for the glyphicon.
				 */
				if ( ! empty( $item->attr_title ) && shapely_navwalker_is_icon_class( $item->attr_title ) ) {
					$item_output .= '<a' . $attributes . '><span class="glyphicon ' . esc_attr( $item->attr_title ) . '" aria-hidden="true"></span>&nbsp;';
				} else {
					$item_output .= '<a' . $attributes . '>';
				}

				$title = apply_filters( 'the_title', $item->title, $item->ID );
				// Core's filter; menu icon and badge plugins hook it.
				$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

				$link_before  = isset( $args->link_before ) ? $args->link_before : '';
				$link_after   = isset( $args->link_after ) ? $args->link_after : '';
				$item_output .= $link_before . $title . $link_after;

				if ( $has_children ) {
					/*
					 * A real button: the <span> this used to be could not be reached
					 * from the keyboard, had no name and did not say whether the
					 * submenu was open -- and on narrow screens it is the only way in.
					 */
					$item_output .= sprintf(
						' </a><button type="button" class="dropdown-toggle shapely-dropdown" aria-expanded="false" aria-label="%s"><i class="fa-solid fa-angle-down" aria-hidden="true"></i></button>',
						/* translators: %s: menu item title */
						esc_attr( sprintf( __( 'Show submenu for %s', 'shapely' ), wp_strip_all_tags( $title ) ) )
					);
				} else {
					$item_output .= '</a>';
				}
				$item_output .= isset( $args->after ) ? $args->after : '';

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
			// phpcs:enable
		}

		/**
		 * Traverse elements to create list from elements.
		 *
		 * Display one element if the element doesn't have any children otherwise,
		 * display the element and its children. Will only traverse up to the max
		 * depth and no ignore elements under that depth.
		 *
		 * This method shouldn't be called directly, use the walk() method instead.
		 *
		 * @see   Walker::start_el()
		 * @since 2.5.0
		 *
		 * @param object $element           Data object
		 * @param array  $children_elements List of elements to continue traversing.
		 * @param int    $max_depth         Max depth to traverse.
		 * @param int    $depth             Depth of current element.
		 * @param array  $args
		 * @param string $output            Passed by reference. Used to append additional content.
		 *
		 * @return null Null on failure with no changes to parameters.
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( ! $element ) {
				return;
			}

			$id_field = $this->db_fields['id'];

			// Display this element.
			if ( is_array( $args ) && isset( $args[0] ) && is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * Menu Fallback
		 * =============
		 * If this function is assigned to the wp_nav_menu's fallback_cb variable
		 * and a manu has not been assigned to the theme location in the WordPress
		 * menu manager the function with display nothing to a non-logged in user,
		 * and will add a link to the WordPress menu manager if logged in as an admin.
		 *
		 * @param array $args passed from the wp_nav_menu function.
		 *
		 */
		public static function fallback( $args ) {
			if ( current_user_can( 'manage_options' ) ) {

				$fb_output = '';

				// wp_nav_menu() allows 'container' => false, which isset() happily passes.
				$container = ! empty( $args['container'] ) ? sanitize_key( $args['container'] ) : '';

				if ( '' !== $container ) {
					$fb_output = '<' . $container;

					if ( ! empty( $args['container_id'] ) ) {
						$fb_output .= ' id="' . esc_attr( $args['container_id'] ) . '"';
					}

					if ( ! empty( $args['container_class'] ) ) {
						$fb_output .= ' class="' . esc_attr( $args['container_class'] ) . '"';
					}

					$fb_output .= '>';
				}

				$fb_output .= '<ul';

				if ( ! empty( $args['menu_id'] ) ) {
					$fb_output .= ' id="' . esc_attr( $args['menu_id'] ) . '"';
				}

				if ( ! empty( $args['menu_class'] ) ) {
					$fb_output .= ' class="' . esc_attr( $args['menu_class'] ) . '"';
				}

				$fb_output .= '>';
				$fb_output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add a menu', 'shapely' ) . '</a></li>';
				$fb_output .= '</ul>';

				if ( '' !== $container ) {
					$fb_output .= '</' . $container . '>';
				}

				echo wp_kses_post( $fb_output );
			}
		}
	}
endif;

/*
 * The name the class had until 1.3.7, for child themes that pass
 * `new Wp_Bootstrap_Navwalker()` to wp_nav_menu(). Left alone if something
 * else -- such as a plugin bundling the Bootstrap 4/5 walker -- got there first.
 */
if ( ! class_exists( 'Wp_Bootstrap_Navwalker', false ) ) {
	class_alias( 'Shapely_Bootstrap_Navwalker', 'Wp_Bootstrap_Navwalker' );
}
