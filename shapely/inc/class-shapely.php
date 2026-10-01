<?php

if ( ! class_exists( 'Shapely' ) ) :
	/**
	 * Shapely Theme Class
	 */
	class Shapely {

		public $recommended_plugins = array(
			'kali-forms'                       => array(
				'recommended' => true,
			),
			'colorlib-login-customizer'        => array(
				'recommended' => true,
			),
			'colorlib-404-customizer'          => array(
				'recommended' => true,
			),
			'colorlib-coming-soon-maintenance' => array(
				'recommended' => true,
			),
			'simple-custom-post-order'         => array(
				'recommended' => true,
			),
			'fancybox-for-wordpress'           => array(
				'recommended' => true,
			),
			'modula-best-grid-gallery'         => array(
				'recommended' => true,
			),
			'rsvp'                             => array(
				'recommended' => true,
			),
		);

		public $recommended_actions;

		public $theme_slug = 'shapely';

		/**
		 * @var Shapely|null Single shared instance.
		 */
		private static $instance = null;

		/**
		 * Return the one shared instance.
		 *
		 * The class used to be instantiated three separate times (file scope, an
		 * `init` callback, and shapely_init_plugins()), which registered every
		 * admin hook — and the whole Epsilon framework — three times over.
		 *
		 * @return Shapely
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			if ( ! is_admin() && ! is_customize_preview() ) {
				return;
			}

			$this->load_class();

			/*
			 * This constructor runs on init at priority 0, so a callback added for
			 * init:0 here never fires; init_welcome_screen() builds the actions.
			 */
			add_action( 'init', array( $this, 'init_welcome_screen' ), 1 );

			// Hooks
			add_action( 'customize_register', array( $this, 'init_customizer' ) );
		}

		public function load_class() {

			if ( ! is_admin() && ! is_customize_preview() ) {
				return;
			}

			require_once get_template_directory() . '/inc/class-shapely-notify-system.php';
			require_once get_template_directory() . '/inc/admin/class-shapely-welcome.php';
		}

		public function init_welcome_screen() {

			if ( empty( $this->recommended_actions ) ) {
				$this->setup_recommended_actions();
			}

			Shapely_Welcome::get_instance(
				array(
					'actions' => $this->recommended_actions,
					'plugins' => $this->recommended_plugins,
				)
			);
		}

		public function init_customizer( $wp_customize ) {
			require_once get_template_directory() . '/inc/custom-controls/class-shapely-section-link.php';

			$wp_customize->register_section_type( 'Shapely_Section_Link' );

			/*
			 * Epsilon_Section_Recommended_Actions rendered the recommended actions,
			 * the recommended plugins and a set of social links inside the
			 * customizer -- a second copy of what the welcome screen already shows.
			 * Rather than maintain two implementations of the same list that can
			 * drift apart, the customizer now links to the one that is complete.
			 *
			 * Section id preserved so any stored customizer state keyed on it, and
			 * the deep link from the old admin notice, keep resolving.
			 */
			$wp_customize->add_section(
				new Shapely_Section_Link(
					$wp_customize,
					'epsilon_recomended_section',
					array(
						'title'       => esc_html__( 'Recommended Actions', 'shapely' ),
						'button_text' => esc_html__( 'View', 'shapely' ),
						'button_url'  => admin_url( 'themes.php?page=shapely-welcome&tab=recommended-actions' ),
						'priority'    => 0,
					)
				)
			);
		}

		public function setup_recommended_actions() {
			/*
			 * load_class() only runs for admin and customizer requests, so anything
			 * calling this directly outside those would hit an undefined class.
			 * Cheap to make that impossible rather than rely on call order.
			 */
			if ( ! class_exists( 'Shapely_Notify_System' ) ) {
				require_once get_template_directory() . '/inc/class-shapely-notify-system.php';
			}

			$this->recommended_actions = apply_filters(
				'shapely_required_actions',
				array(
					array(
						'id'          => 'shapely-req-import-content',
						'title'       => esc_html__( 'Import Demo Content', 'shapely' ),
						'description' => esc_html__( 'Clicking the button below will add the demo homepage widgets and set a static front page. Click Advanced to choose what is imported.', 'shapely' ),
						'help'        => $this->generate_action_html(),
						'check'       => Shapely_Notify_System::shapely_has_content(),
					),
					array(
						'id'          => 'shapely-req-ac-install-companion-plugin',
						'title'       => Shapely_Notify_System::shapely_companion_title(),
						'description' => Shapely_Notify_System::shapely_companion_description(),
						'check'       => Shapely_Notify_System::shapely_has_plugin( 'shapely-companion' ),
						'plugin_slug' => 'shapely-companion',
					),
					array(
						'id'          => 'shapely-req-ac-install-wp-jetpack-plugin',
						'title'       => Shapely_Notify_System::shapely_jetpack_title(),
						'description' => Shapely_Notify_System::shapely_jetpack_description(),
						'check'       => Shapely_Notify_System::shapely_has_plugin( 'jetpack' ),
						'plugin_slug' => 'jetpack',
					),
					array(
						'id'          => 'shapely-req-ac-install-kali-forms',
						'title'       => Shapely_Notify_System::shapely_kaliforms_title(),
						'description' => Shapely_Notify_System::shapely_kaliforms_description(),
						'check'       => Shapely_Notify_System::shapely_has_plugin( 'kali-forms' ),
						'plugin_slug' => 'kali-forms',
					),
				)
			);
		}

		private function generate_action_html() {

			$import_actions = array(
				'set-frontpage'  => esc_html__( 'Set Static FrontPage', 'shapely' ),
				'import-widgets' => esc_html__( 'Import HomePage Widgets', 'shapely' ),
			);

			/*
			 * The import runs in shapely-companion; without it active the request
			 * returned "0" and the screen only said "There was an error".
			 */
			if ( ! Shapely_Notify_System::shapely_has_plugin( 'shapely-companion' ) ) {
				return '<p class="description">' . esc_html__( 'The demo content is imported by the Shapely Companion plugin. Install and activate it first; it is listed below.', 'shapely' ) . '</p>';
			}

			// The import sets the front page, so the companion checks manage_options;
			// an editor with only edit_theme_options got "There was an error".
			if ( ! current_user_can( 'manage_options' ) ) {
				return '<p class="description">' . esc_html__( 'Importing the demo content changes the site\'s front page, so it needs an administrator.', 'shapely' ) . '</p>';
			}

			if ( is_customize_preview() ) {
				$url  = 'themes.php?page=%1$s-welcome&tab=%2$s';
				$html = '<a class="button button-primary" id="" href="' . esc_url( admin_url( sprintf( $url, 'shapely', 'recommended-actions' ) ) ) . '">' . __( 'Import Demo Content', 'shapely' ) . '</a>';
			} else {
				$html  = '<p><a class="button button-primary cpo-import-button epsilon-ajax-button" data-action="import_demo" id="add_default_sections" href="#">' . __( 'Import Demo Content', 'shapely' ) . '</a>';
				$html .= '<a class="button epsilon-hidden-content-toggler" href="#welcome-hidden-content">' . __( 'Advanced', 'shapely' ) . '</a></p>';
				$html .= '<div class="import-content-container" id="welcome-hidden-content">';

				/*
				 * There used to be a 'Plugins' group of checkboxes here. Nothing
				 * read them -- the import only ever sent the options below -- so
				 * ticking them installed nothing. The plugins are separate actions
				 * on this screen, each with its own install button.
				 */

				$html .= '<div class="demo-content-container">';
				$html .= '<h4>' . __( 'Demo Content', 'shapely' ) . '</h4>';
				$html .= '<div class="checkbox-group">';
				foreach ( $import_actions as $id => $label ) {
					$html .= $this->generate_checkbox( $id, $label );
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}

			return $html;
		}

		private function generate_checkbox( $id, $label, $name = 'options', $block = false ) {
			$string = '<label><input checked type="checkbox" name="%1$s" class="demo-checkboxes"' . ( $block ? ' disabled ' : ' ' ) . 'value="%2$s">%3$s</label>';

			return sprintf( $string, $name, $id, $label );
		}
	}
endif;
