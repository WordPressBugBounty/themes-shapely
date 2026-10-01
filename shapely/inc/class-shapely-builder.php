<?php
/**
 * Builder Page support: one widget area per page using the Builder Page template.
 *
 * @package Shapely
 */

if ( ! class_exists( 'Shapely_Builder' ) ) :
	/**
	 * Shapely Builder Class
	 */
	class Shapely_Builder {

		/**
		 * Cache key for the list of Builder pages.
		 *
		 * Persistent, with no expiry: it is rebuilt when a page is saved, trashed,
		 * restored or deleted, or its template changes. It replaced a query plus
		 * four option writes on every request, front end included.
		 */
		const CACHE_KEY = 'shapely_builder_pages';

		/**
		 * @var Shapely_Builder|null
		 */
		private static $instance = null;

		/**
		 * Builder pages keyed by slug, loaded on first use.
		 *
		 * @var array|null slug => array( 'id' => int, 'title' => string )
		 */
		private $pages = null;

		/**
		 * Hook up. Nothing is queried here; the page list is loaded on demand.
		 */
		public function __construct() {
			add_action( 'widgets_init', array( $this, 'register_sidebars' ), 20 );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_builder_js' ) );
			add_filter( 'sidebars_widgets', array( $this, 'remove_specific_widget' ) );

			foreach ( array( 'save_post_page', 'deleted_post', 'trashed_post', 'untrashed_post' ) as $hook ) {
				add_action( $hook, array( $this, 'flush_pages' ) );
			}

			foreach ( array( 'added_post_meta', 'updated_post_meta', 'deleted_post_meta' ) as $hook ) {
				add_action( $hook, array( $this, 'maybe_flush_pages' ), 10, 3 );
			}

			add_action( 'switch_theme', array( $this, 'flush_pages' ) );
		}

		/**
		 * @return Shapely_Builder
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Builder pages, keyed by slug.
		 *
		 * @return array
		 */
		public function get_all_pages() {
			if ( null !== $this->pages ) {
				return $this->pages;
			}

			$pages = get_transient( self::CACHE_KEY );

			if ( ! is_array( $pages ) ) {
				$pages = array();
				$query = new WP_Query(
					array(
						'post_type'              => 'page',
						'post_status'            => array( 'publish', 'private' ),
						'posts_per_page'         => -1,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'                 => 'page-templates/template-widget.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					)
				);

				foreach ( $query->posts as $post ) {
					// Stored raw and escaped where printed; escaping here showed
					// "A &amp; B" in the block widget editor.
					$pages[ $post->post_name ] = array(
						'id'    => absint( $post->ID ),
						'title' => $post->post_title,
					);
				}

				set_transient( self::CACHE_KEY, $pages );
			}

			$this->pages = $pages;

			return $this->pages;
		}

		/**
		 * Builder sidebar ids.
		 *
		 * @return array
		 */
		public function get_sidebar_ids() {
			$ids = array();
			foreach ( array_keys( $this->get_all_pages() ) as $slug ) {
				$ids[] = 'shapely-' . $slug;
			}
			return $ids;
		}

		/**
		 * Forget the cached page list.
		 */
		public function flush_pages() {
			delete_transient( self::CACHE_KEY );
			$this->pages = null;
		}

		/**
		 * Flush when a page template is assigned, changed or removed.
		 *
		 * @param int|array $meta_id  Meta id(s).
		 * @param int       $post_id  Post id.
		 * @param string    $meta_key Meta key.
		 */
		public function maybe_flush_pages( $meta_id, $post_id, $meta_key ) {
			if ( '_wp_page_template' === $meta_key ) {
				$this->flush_pages();
			}
		}

		public function register_sidebars() {
			foreach ( $this->get_all_pages() as $slug => $page ) {
				register_sidebar(
					array(
						/* translators: %s: page title */
						'name'          => sprintf( __( 'Page: %s', 'shapely' ), $page['title'] ),
						'id'            => 'shapely-' . $slug,
						/* translators: %s: page title */
						'description'   => sprintf( __( 'This widgets will appear in %s page', 'shapely' ), $page['title'] ),
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					)
				);
			}
		}

		public function enqueue_builder_js() {
			$pages = $this->get_all_pages();
			if ( empty( $pages ) ) {
				return;
			}

			$builder_settings = array(
				/*
				 * Compared in customizer-builder.js against api.settings.url.preview,
				 * which is a front-end URL -- so this has to be home_url(), not the
				 * WordPress files location. The trailing slash matters too: the preview
				 * URL carries one, so the unslashed site_url() could never match it.
				 */
				'siteURL' => esc_url( home_url( '/' ) ),
				'pages'   => $pages,
			);
			wp_enqueue_script( 'shapely_builder_customizer', get_template_directory_uri() . '/assets/js/customizer-builder.js', array( 'jquery', 'customize-controls' ), SHAPELY_VERSION, true );

			wp_localize_script( 'shapely_builder_customizer', 'ShapelyBuilder', $builder_settings );
		}

		/**
		 * Keep shapely-companion's Page Content / Page Title widgets out of every
		 * widget area except the Builder and Home ones.
		 *
		 * Front end only. The widgets screen, the block widget REST API and the
		 * Customizer read the filtered list and save it back, so filtering there
		 * permanently deleted any such widget parked in Inactive Widgets.
		 *
		 * @param array $sidebars_widgets Widget ids by area.
		 *
		 * @return array
		 */
		public function remove_specific_widget( $sidebars_widgets ) {
			if ( ! is_array( $sidebars_widgets ) || is_admin() || wp_is_json_request() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
				return $sidebars_widgets;
			}

			$allowed   = $this->get_sidebar_ids();
			$allowed[] = 'sidebar-home';

			foreach ( $sidebars_widgets as $widget_area => $widget_list ) {
				// 'array_version' is an int; inactive and orphaned widgets are never shown.
				if ( ! is_array( $widget_list ) || in_array( $widget_area, $allowed, true ) || 'wp_inactive_widgets' === $widget_area || 0 === strpos( $widget_area, 'orphaned_widgets' ) ) {
					continue;
				}

				$kept = array();
				foreach ( $widget_list as $widget_id ) {
					if ( is_string( $widget_id ) && ( false !== strpos( $widget_id, 'shapely-page-content' ) || false !== strpos( $widget_id, 'shapely-page-title' ) ) ) {
						continue;
					}
					$kept[] = $widget_id;
				}

				// Reindexed: a list with gaps is encoded as an object in JSON.
				$sidebars_widgets[ $widget_area ] = $kept;
			}

			return $sidebars_widgets;
		}
	}
endif;
