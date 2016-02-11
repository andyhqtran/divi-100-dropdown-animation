<?php

/**
* @package Custom_Dropdown_Animations
* @version 0.0.1
*/

/*
* Plugin Name: Custom Dropdown Animations
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
	public $plugin_prefix;

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
		$this->plugin_prefix = "{$this->main_prefix}{$this->plugin_slug}-";

		$this->init();
	}

	/**
	* Hooking methods into WordPress actions and filters
	*
	* @return void
	*/
	private function init(){
		add_action( 'admin_menu',            array( $this, 'add_submenu' ), 30 ); // Make sure the priority is higher than Divi 100's add_menu()
		add_action( 'admin_enqueue_scripts', array( $this, 'add_submenu_scripts' ) );
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_frontend_scripts' ) );
		add_filter( 'body_class',            array( $this, 'body_class' ) );
	}

	/**
	* Add submenu
	* @return void
	*/
	function add_submenu() {
		add_submenu_page(
			$this->main_prefix . 'options',
			__( 'Custom Dropdown Animations' ),
			__( 'Custom Dropdown Animations' ),
			'switch_themes',
			$this->plugin_prefix . 'options',
			array( $this, 'render_options_page' )
		);
	}

	/**
	* Add dashboard scripts
	* @return void
	*/
	function add_submenu_scripts() {
		if ( isset( $_GET['page'] ) && $this->plugin_prefix . 'options' === $_GET['page'] ) {
			wp_enqueue_script( $this->plugin_prefix . 'dashboard-scripts', plugin_dir_url( __FILE__ ) . 'js/dashboard-scripts.js', array( 'jquery' ), '0.0.1', true );
			wp_localize_script( $this->plugin_prefix . 'dashboard-scripts', $this->main_prefix, array( 'preview_dir_url' => plugin_dir_url( __FILE__ ) . 'preview/', ) );
		}
	}

	/**
	* Render options page
	* @return void
	*/
	function render_options_page() {
		$is_option_updated         = false;
		$is_option_updated_success = false;
		$is_option_updated_message = '';
		$primary_style_name        = 'primary-style';
		$secondary_style_name      = 'secondary-style';
		$nonce_action              = $this->plugin_prefix . 'options';
		$nonce                     = $this->plugin_prefix . 'options_nonce';

		// Verify whether an update has been occured
		if ( isset( $_POST[ $primary_style_name ] ) && isset( $_POST[ $secondary_style_name ] ) && isset( $_POST[ $nonce ] ) ) {
			$is_option_updated = true;

			// Verify nonce. Thou shalt use correct nonce
			if ( wp_verify_nonce( $_POST[ $nonce ], $nonce_action ) ) {

				// Verify input
				if ( in_array( $_POST[ $primary_style_name ], array_keys( $this->get_primary_styles() ) ) && in_array( $_POST[ $secondary_style_name ], array_keys( $this->get_secondary_styles() ) ) ) {
					// Update option
					update_option( $this->plugin_prefix . 'primary_styles', sanitize_text_field( $_POST[ $primary_style_name ] ) );

					update_option( $this->plugin_prefix . 'secondary_styles', sanitize_text_field( $_POST[ $secondary_style_name ] ) );

					// Update submission status & message
					$is_option_updated_message = __( 'Your setting has been updated.' );
					$is_option_updated_success = true;
				} else {
					$is_option_updated_message = __( 'Invalid submission. Please try again.' );
				}
			} else {
				$is_option_updated_message = __( 'Error authenticating request. Please try again.' );
			}
		}

		?>
		<div class="wrap">
			<h1><?php _e( 'Custom Dropdown Animations' ); ?></h1>

			<?php if ( $is_option_updated ) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible <?php echo $is_option_updated_success ? '' : 'error' ?>">
				<p>
					<strong><?php echo esc_html( $is_option_updated_message ); ?></strong>
				</p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php _e( 'Dismiss this notice.' ); ?></span>
				</button>
			</div>
			<?php } ?>

			<form action="" method="POST">
				<p><?php _e( 'Proper description goes here Nullam id dolor id nibh ultricies vehicula ut id elit. Vestibulum id ligula porta felis euismod semper. Nullam id dolor id nibh ultricies vehicula ut id elit. Vestibulum id ligula porta felis euismod semper.' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="primary-style"><?php _e( 'Primary Nav Style' ); ?></label>
							</th>
							<td>
								<select name="primary-style" id="primary-style" data-preview-prefix="style-">
									<?php
									// Get saved style
									$primary_style = $this->get_selected_primary_style();

									// Render options
									foreach ( $this->get_primary_styles() as $primary_style_id => $primary_style_label ) {
										printf(
											'<option value="%1$s" %3$s>%2$s</option>',
											esc_attr( $primary_style_id ),
											esc_html( $primary_style_label ),
											"{$primary_style}" === "{$primary_style_id}" ? 'selected="selected"' : ''
										);
									}
									?>
								</select>
								<p class="description"><?php _e( 'Proper description goes here' ); ?></p>

								<div class="option-preview" style="margin-top: 20px; <?php echo ( '' !== $primary_style ) ? 'min-height: 305px; ' : ''; ?>">
									<?php if ( '' !== $primary_style ) { ?>
										<img src="<?php echo plugin_dir_url( __FILE__ ) . 'preview/style-' . $primary_style . '.gif'; ?>">
									<?php } ?>
								</div>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="secondary-style"><?php _e( 'Secondary Nav Style' ); ?></label>
							</th>
							<td>
								<select name="secondary-style" id="secondary-style" data-preview-prefix="style-">
									<?php
									// Get saved style
									$secondary_style = $this->get_selected_secondary_style();

									// Render options
									foreach ( $this->get_secondary_styles() as $secondary_style_id => $secondary_style_label ) {
										printf(
											'<option value="%1$s" %3$s>%2$s</option>',
											esc_attr( $secondary_style_id ),
											esc_html( $secondary_style_label ),
											"{$secondary_style}" === "{$secondary_style_id}" ? 'selected="selected"' : ''
										);
									}
									?>
								</select>
								<p class="description"><?php _e( 'Proper description goes here' ); ?></p>

								<div class="option-preview" style="margin-top: 20px; <?php echo ( '' !== $secondary_style ) ? 'min-height: 305px; ' : ''; ?>">
									<?php if ( '' !== $secondary_style ) { ?>
										<img src="<?php echo plugin_dir_url( __FILE__ ) . 'preview/style-' . $secondary_style . '.gif'; ?>">
									<?php } ?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- /.form-table -->

				<?php wp_nonce_field( $nonce_action, $nonce ); ?>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
				</p>
				<!-- /.submit -->

			</form>
		</div>
		<!-- /.wrap -->
		<?php
	}

	/**
	* List of valid primary nav styles
	* @return array
	*/
	function get_primary_styles() {
		return apply_filters( $this->plugin_prefix . 'primary_styles', array(
			''    => __( 'Default' ),
			'fadeIn'   => __( 'Fade In' ),
			'fadeInTop'   => __( 'Fade In From Top' ),
			'fadeInRight'   => __( 'Fade In From Right' ),
			'fadeInBottom'   => __( 'Fade In From Bottom' ),
			'fadeInLeft'   => __( 'Fade In From Left' ),
			'scaleIn'   => __( 'Scale In' ),
			'scaleInRight'   => __( 'Scale In From Right' ),
			'scaleInLeft'   => __( 'Scale In From Left' ),
			'flipInY' => _("Flip In Vertical"),
			'flipInX' => _("Flip In Horizontal"),
		) );
	}

	/**
	* List of valid secondary nav styles
	* @return array
	*/
	function get_secondary_styles() {
		return apply_filters( $this->plugin_prefix . 'secondary_styles', array(
			''    => __( 'Default' ),
			'fadeIn'   => __( 'Fade In' ),
			'fadeInTop'   => __( 'Fade In From Top' ),
			'fadeInRight'   => __( 'Fade In From Right' ),
			'fadeInBottom'   => __( 'Fade In From Bottom' ),
			'fadeInLeft'   => __( 'Fade In From Left' ),
			'scaleIn'   => __( 'Scale In' ),
			'scaleInRight'   => __( 'Scale In From Right' ),
			'scaleInLeft'   => __( 'Scale In From Left' ),
			'flipInY' => _("Flip In Vertical"),
			'flipInX' => _("Flip In Horizontal"),
		) );
	}

	/**
	* Get selected primary style
	* @return string
	*/
	function get_selected_primary_style() {
		$style = get_option( $this->plugin_prefix . 'primary_styles', '' );

		return apply_filters( $this->plugin_prefix . 'get_selected_primary_style', $style );
	}

	/**
	* Get selected secondary style
	* @return string
	*/
	function get_selected_secondary_style() {
		$style = get_option( $this->plugin_prefix . 'secondary_styles', '' );

		return apply_filters( $this->plugin_prefix . 'get_selected_secondary_style', $style );
	}

	/**
	* Add specific class to <body>
	* @return array
	*/
	function body_class( $classes ) {
		// Get selected style
		$selected_primary_style = $this->get_selected_primary_style();
		$selected_secondary_style = $this->get_selected_secondary_style();

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
	* @return void
	*/
	function enqueue_frontend_scripts() {
		wp_enqueue_style( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'css/style.css' );
		wp_enqueue_script( 'custom-dropdown-animations', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery', 'divi-custom-script' ), '0.0.1', true );
	}
}
ET_Divi_100_Custom_Dropdown_Animations::instance();