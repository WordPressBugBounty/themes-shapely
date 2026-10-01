<?php
/**
 * Plugin and content state checks for the welcome screen.
 *
 * @package Shapely
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Shapely_Notify_System' ) ) {
	/**
	 * Plugin / content state checks for the welcome screen.
	 *
	 * Previously extended Epsilon_Notify_System. Nothing was actually inherited:
	 * every self:: call in this class resolves to a method defined here, and the
	 * two methods that looked inherited (check_plugin_is_installed,
	 * check_plugin_is_active) were already overridden because the parent
	 * hardcoded ABSPATH . 'wp-content/plugins/' and broke on relocated content
	 * directories.
	 *
	 * shapely-companion calls shapely_has_plugin(); keep its name and signature.
	 */
	class Shapely_Notify_System {

		/**
		 * Whether the demo content has been imported.
		 *
		 * @return bool
		 */
		public static function shapely_has_content() {
			return (bool) get_option( 'shapely_imported_demo', false );
		}

		/**
		 * Resolve a plugin slug to its "dir/file.php" basename.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return string Plugin basename, or an empty string when not installed.
		 */
		protected static function shapely_locate_plugin( $slug ) {
			$slug = trim( (string) $slug, '/' );
			if ( '' === $slug || false !== strpos( $slug, '.' ) ) {
				return '';
			}

			$candidates = array( $slug . '/' . $slug . '.php' );
			if ( 'wordpress-seo' === $slug ) {
				$candidates[] = $slug . '/wp-seo.php';
			}

			foreach ( $candidates as $candidate ) {
				if ( file_exists( WP_PLUGIN_DIR . '/' . $candidate ) ) {
					return $candidate;
				}
			}

			// Plugins whose main file does not match the directory name.
			$dir = WP_PLUGIN_DIR . '/' . $slug;
			if ( is_dir( $dir ) ) {
				foreach ( (array) glob( $dir . '/*.php' ) as $file ) {
					$data = get_file_data( $file, array( 'Name' => 'Plugin Name' ) );
					if ( ! empty( $data['Name'] ) ) {
						return $slug . '/' . basename( $file );
					}
				}
			}

			return '';
		}

		/**
		 * Whether the plugin is present on disk.
		 *
		 * WP_PLUGIN_DIR rather than ABSPATH . 'wp-content/plugins/', which never
		 * finds anything on installs with a relocated content directory.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return bool
		 */
		public static function check_plugin_is_installed( $slug ) {
			if ( '' !== self::shapely_locate_plugin( $slug ) ) {
				return true;
			}

			// Must-use plugins are installed and permanently active.
			return defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR . '/' . trim( (string) $slug, '/' ) );
		}

		/**
		 * Whether the plugin is active for this site or across the network.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return bool
		 */
		public static function check_plugin_is_active( $slug ) {
			$basename = self::shapely_locate_plugin( $slug );

			if ( '' === $basename ) {
				// mu-plugins cannot be deactivated, so presence implies active.
				return defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR . '/' . trim( (string) $slug, '/' ) );
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( $basename ) || is_plugin_active_for_network( $basename );
		}

		/**
		 * Whether the plugin is installed and active.
		 *
		 * @param string|null $slug Plugin directory slug.
		 *
		 * @return bool
		 */
		public static function shapely_has_plugin( $slug = null ) {
			return self::check_plugin_is_installed( $slug ) && self::check_plugin_is_active( $slug );
		}

		/**
		 * Whether the plugin is on disk but switched off, so the action is "Activate".
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return bool
		 */
		private static function shapely_is_installed_inactive( $slug ) {
			return self::check_plugin_is_installed( $slug ) && ! self::check_plugin_is_active( $slug );
		}

		/**
		 * @return string
		 */
		public static function shapely_companion_title() {
			return self::shapely_is_installed_inactive( 'shapely-companion' )
				? esc_html__( 'Activate: Shapely Companion Plugin', 'shapely' )
				: esc_html__( 'Install: Shapely Companion Plugin', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_jetpack_title() {
			return self::shapely_is_installed_inactive( 'jetpack' )
				? esc_html__( 'Activate: Jetpack by WordPress', 'shapely' )
				: esc_html__( 'Install: Jetpack by WordPress', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_kaliforms_title() {
			return self::shapely_is_installed_inactive( 'kali-forms' )
				? esc_html__( 'Activate: Kali Forms', 'shapely' )
				: esc_html__( 'Install: Kali Forms', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_companion_description() {
			return self::shapely_is_installed_inactive( 'shapely-companion' )
				? esc_html__( 'Please activate Shapely Companion plugin.', 'shapely' )
				: esc_html__( 'Please install Shapely Companion plugin.', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_jetpack_description() {
			return self::shapely_is_installed_inactive( 'jetpack' )
				? esc_html__( 'Please activate Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' )
				: esc_html__( 'Please install Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_kaliforms_description() {
			return self::shapely_is_installed_inactive( 'kali-forms' )
				? esc_html__( 'Please activate Kali Forms. Note that you won\'t be able to use Contact widget without it.', 'shapely' )
				: esc_html__( 'Please install Kali Forms. Note that you won\'t be able to use Contact widget without it.', 'shapely' );
		}
	}
}
