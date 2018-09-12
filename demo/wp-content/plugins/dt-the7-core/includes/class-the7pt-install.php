<?php

// File Security Check.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7PT_Install {

	/**
	 * @var The7_Background_Updater
	 */
	private static $background_updater;

	/**
	 * @var array
	 */
	private static $update_callbacks = array(
		'1.11.0' => array(
			'the7_mass_regenerate_short_codes_inline_css',
			'the7pt_set_db_version_1_11_0',
		),
	);

	public static function init() {
		if ( ! defined( 'PRESSCORE_STYLESHEETS_VERSION' ) ) {
			return;
		}

		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 7 );
		add_action( 'init', array( __CLASS__, 'check_version' ), 7 );

		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) && ! wp_doing_ajax() && ! wp_doing_cron() ) {
			add_action( 'init', array( __CLASS__, 'install_actions' ), 11 );
		}
	}

	public static function check_version() {
		if ( self::get_db_version() === null ) {
			self::update_db_version( '1.10.0' );
		}
	}

	/**
	 * Init background updates
	 */
	public static function init_background_updater() {
		if ( ! class_exists( 'The7_Background_Updater' ) ) {
			include_once PRESSCORE_MODS_DIR . '/theme-update/class-the7-background-updater.php';
		}

		include_once dirname( __FILE__ ) . '/the7pt-update-functions.php';

		self::$background_updater = new The7_Background_Updater();
	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}

		if ( self::db_is_updating() ) {
			return;
		}

		if ( ! self::is_auto_update_db() ) {
			return;
		}

		self::update();
	}

	private static function get_update_callbacks() {
		return self::$update_callbacks;
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	public static function update() {
		$db_version = self::get_db_version();

		if ( version_compare( $db_version, The7PT_Core::PLUGIN_DB_VERSION, '>=' ) ) {
			return;
		}

		$update_queued = false;
		$db_update_callbacks = self::get_update_callbacks();

		// Update db.
		foreach ( $db_update_callbacks as $version => $update_callbacks ) {
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}

	public static function update_db_version( $version = null ) {
		delete_option( 'the7pt_db_version' );
		add_option( 'the7pt_db_version', $version === null ? The7PT_Core::PLUGIN_DB_VERSION : $version );
	}

	public static function is_auto_update_db() {
		return The7_Admin_Dashboard_Settings::get( 'db-auto-update' );
	}

	public static function get_db_version() {
		return get_option( 'the7pt_db_version', null );
	}

	public static function regenerate_stylesheets() {
		presscore_refresh_dynamic_css();
	}

	public static function db_is_updating() {
		return self::$background_updater->is_updating();
	}

	public static function db_update_is_needed() {
		return version_compare( self::get_db_version(), The7PT_Core::PLUGIN_DB_VERSION, '<' );
	}
}