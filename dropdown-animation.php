<?php

/**
* @package Custom_Dropdown_Animations
* @version 0.0.1
*/

/*
* Plugin Name: Divi 100 Dropdown Animation
* Plugin URI: https://elegantthemes.com/
* Description: This plugin gives you the option to choose between different dropdown animations.
* Author: Elegant Themes
* Version: 0.0.1
* Author URI: http://elegantthemes.com
* License: GPL3
*/

/**
 * Load Divi 100 Setup
 */
require_once( plugin_dir_path( __FILE__ ) . '/divi-100-setup/divi-100-setup.php' );

/**
 * Load Custom Dropdown Animations
 */
class ET_Divi_100_Custom_Dropdown_Animations {
	/**
	* Unique instance of plugin
	*/
	public static $instance;
	public $main_prefix;
	public $plugin_slug;
	public $plugin_id;
	public $plugin_prefix;
	protected $settings;
	protected $utils;

	/**
	* Gets the instance of the plugin
	*/
	public static function instance(){
		if ( null === self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	* Constructor
	*/
	private function __construct(){
		$this->main_prefix   = 'et_divi_100_';
		$this->plugin_slug   = 'custom_dropdown_animations';
		$this->plugin_id     = "{$this->main_prefix}{$this->plugin_slug}";
		$this->plugin_prefix = "{$this->plugin_id}-";
		$this->settings      = maybe_unserialize( get_option( $this->plugin_id ) );
		$this->utils         = new Divi_100_Utils( $this->settings );

		// Initialize if Divi is active
		if ( et_divi_100_is_active() ) {
			$this->init();
		}
	}

	/**
	* Hooking methods into WordPress actions and filters
	*
	* @return void
	*/
	private function init(){
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_frontend_scripts' ) );
		add_filter( 'body_class',            array( $this, 'body_class' ) );

		if ( is_admin() ) {
			$settings_args = array(
				'plugin_id'       => $this->plugin_id,
				'preview_dir_url' => plugin_dir_url( __FILE__ ) . 'preview/',
				'title'           => __( 'Custom Dropdown Animations' ),
				'description'     => __( 'Nullam quis risus eget urna mollis ornare vel eu leo.' ),
				'fields' => array(
					array(
						'type'                 => 'select',
						'preview_prefix'       => 'style-',
						'preview_height'       => 300,
						'id'                   => 'primary-style',
						'label'                => __( 'Primary Nav Style' ),
						'description'          => __( 'Proper description goes here' ),
						'options'              => $this->get_primary_styles(),
						'sanitize_callback'    => 'sanitize_text_field',
					),
					array(
						'type'                 => 'select',
						'preview_prefix'       => 'style-',
						'preview_height'       => 300,
						'id'                   => 'secondary-style',
						'label'                => __( 'Secondary Nav Style' ),
						'description'          => __( 'Proper description goes here' ),
						'options'              => $this->get_secondary_styles(),
						'sanitize_callback'    => 'sanitize_text_field',
					),
				),
				'button_save_text' => __( 'Save Changes' ),
			);

			new Divi_100_Settings( $settings_args );
		}
	}

	/**
	* List of valid primary nav styles
	*
	* @return array
	*/
	function get_primary_styles() {
		return apply_filters( $this->plugin_prefix . 'primary_styles', array(
			''             => __( 'Default' ),
			'fadeIn'       => __( 'Fade In' ),
			'fadeInTop'    => __( 'Fade In From Top' ),
			'fadeInRight'  => __( 'Fade In From Right' ),
			'fadeInBottom' => __( 'Fade In From Bottom' ),
			'fadeInLeft'   => __( 'Fade In From Left' ),
			'scaleIn'      => __( 'Scale In' ),
			'scaleInRight' => __( 'Scale In From Right' ),
			'scaleInLeft'  => __( 'Scale In From Left' ),
			'flipInY'      => _("Flip In Vertical"),
			'flipInX'      => _("Flip In Horizontal"),
		) );
	}

	/**
	* List of valid secondary nav styles
	*
	* @return array
	*/
	function get_secondary_styles() {
		return apply_filters( $this->plugin_prefix . 'secondary_styles', array(
			''             => __( 'Default' ),
			'fadeIn'       => __( 'Fade In' ),
			'fadeInTop'    => __( 'Fade In From Top' ),
			'fadeInRight'  => __( 'Fade In From Right' ),
			'fadeInBottom' => __( 'Fade In From Bottom' ),
			'fadeInLeft'   => __( 'Fade In From Left' ),
			'scaleIn'      => __( 'Scale In' ),
			'scaleInRight' => __( 'Scale In From Right' ),
			'scaleInLeft'  => __( 'Scale In From Left' ),
			'flipInY'      => _("Flip In Vertical"),
			'flipInX'      => _("Flip In Horizontal"),
		) );
	}

	/**
	* Add specific class to <body>
	*
	* @return array
	*/
	function body_class( $classes ) {
		// Get selected style
		$selected_primary_style   = $this->utils->get_value( 'primary-style' );
		$selected_secondary_style = $this->utils->get_value( 'secondary-style' );

		if ( '' !== $selected_primary_style ) {
			$classes[] = esc_attr(  $this->plugin_prefix . '-primary' );
			$classes[] = esc_attr( "et_primary_nav_dropdown_animation_{$selected_primary_style}" );
		}

		if ( '' !== $selected_secondary_style ) {
			$classes[] = esc_attr(  $this->plugin_prefix . '-secondary' );
			$classes[] = esc_attr( "et_secondary_nav_dropdown_animation_{$selected_secondary_style}" );
		}

		return $classes;
	}

	/**
	* Load front end scripts
	*
	* @return void
	*/
	function enqueue_frontend_scripts() {
		wp_enqueue_style( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'css/style.css' );
		wp_enqueue_script( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery', 'divi-custom-script' ), '0.0.1', true );
	}
}
ET_Divi_100_Custom_Dropdown_Animations::instance();