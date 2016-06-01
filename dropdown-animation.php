<?php

/**
* @package Custom_Dropdown_Animation
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
 * Register plugin to Divi 100 list
 */
class ET_Divi_100_Custom_Dropdown_Animation_Config {
	public static $instance;

	/**
	 * Hook the plugin info into Divi 100 list
	 */
	function __construct() {
		add_filter( 'et_divi_100_settings', array( $this, 'register' ) );
		add_action( 'plugins_loaded',       array( $this, 'init' ) );
	}

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
	 * Define plugin info
	 *
	 * @return array plugin info
	 */
	public static function info() {
		$main_prefix = 'et_divi_100_';
		$plugin_slug = 'custom_dropdown_animations';

		return array(
			'main_prefix'        => $main_prefix,
			'plugin_name'        => __( 'Custom Dropdown Animation' ),
			'plugin_description' => __( 'This plugin gives you the option to choose between different dropdown animations.' ),
			'plugin_slug'        => $plugin_slug,
			'plugin_id'          => "{$main_prefix}{$plugin_slug}",
			'plugin_prefix'      => "{$main_prefix}{$plugin_slug}-",
			'plugin_version'     => 20160601,
			'plugin_dir_path'    => plugin_dir_path( __FILE__ ),
		);
	}

	/**
	 * et_divi_100_settings callback
	 *
	 * @param array  settings
	 * @return array settings
	 */
	function register( $settings ) {
		$info = self::info();

		$settings[ $info['plugin_slug'] ] = $info;

		return $settings;
	}

	/**
	 * Init plugin after all plugins has been loaded
	 */
	function init() {
		// Load Divi 100 Setup
		require_once( plugin_dir_path( __FILE__ ) . 'divi-100-setup/divi-100-setup.php' );

		// Load Dropdown Animation
		ET_Divi_100_Custom_Dropdown_Animation::instance();
	}
}
ET_Divi_100_Custom_Dropdown_Animation_Config::instance();

/**
 * Load Custom Dropdown Animation
 * this requires Divi 100 setup to be loaded first
 */
class ET_Divi_100_Custom_Dropdown_Animation {
	/**
	* Unique instance of plugin
	*/
	public static $instance;
	public $config;
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
		$this->config   = ET_Divi_100_Custom_Dropdown_Animation_Config::info();
		$this->settings = maybe_unserialize( get_option( $this->config['plugin_id'] ) );
		$this->utils    = new Divi_100_Utils( $this->settings );

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_filter( 'body_class',         array( $this, 'body_class' ) );

		if ( is_admin() ) {
			$settings_args = array(
				'plugin_id'       => $this->config['plugin_id'],
				'preview_dir_url' => plugin_dir_url( __FILE__ ) . 'assets/preview/',
				'title'           => $this->config['plugin_name'],
				'description'     => $this->config['plugin_description'],
				'fields' => array(
					array(
						'type'              => 'select',
						'preview_prefix'    => 'style-',
						'preview_height'    => 300,
						'id'                => 'primary-style',
						'label'             => __( 'Primary Nav Style' ),
						'description'       => __( 'This style will be applied to your primary nav' ),
						'options'           => $this->get_primary_styles(),
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'type'              => 'select',
						'preview_prefix'    => 'style-',
						'preview_height'    => 300,
						'id'                => 'secondary-style',
						'label'             => __( 'Secondary Nav Style' ),
						'description'       => __( 'This style will be applied to your secondary nav' ),
						'options'           => $this->get_secondary_styles(),
						'sanitize_callback' => 'sanitize_text_field',
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
		return apply_filters( $this->config['plugin_prefix'] . 'primary_styles', array(
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
		return apply_filters( $this->config['plugin_prefix'] . 'secondary_styles', array(
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

		if ( '' !== $selected_primary_style && '' !== $selected_secondary_style ) {
			$classes[] = esc_attr(  $this->config['plugin_id'] );
		}

		if ( '' !== $selected_primary_style ) {
			$classes[] = esc_attr(  $this->config['plugin_prefix'] . '-primary' );
			$classes[] = esc_attr( "et_primary_nav_dropdown_animation_{$selected_primary_style}" );
		}

		if ( '' !== $selected_secondary_style ) {
			$classes[] = esc_attr(  $this->config['plugin_prefix'] . '-secondary' );
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
		wp_enqueue_style( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->config['plugin_version'] );
		wp_enqueue_script( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery', 'divi-custom-script' ), $this->config['plugin_version'], true );
	}
}