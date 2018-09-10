<?php
/*
  Plugin Name: Smart Variations Images
  Plugin URI: http://www.rosendo.pt
  Description: This is a WooCommerce extension plugin, that allows the user to add any number of images to the product images gallery and be used as variable product variations images in a very simple and quick way, without having to insert images p/variation.
  Author: David Rosendo
  Version: 3.2.20
  WC requires at least: 3.0
  WC tested up to: 3.4.0
  Author URI: http://www.rosendo.pt
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('woocommerce_svi')) {

    class woocommerce_svi {

        /**
         * init
         *
         * @access public
         * @since 1.0.0
         * @return bool
         */
        function __construct() {

            define('SL_VERSION', '3.2.20');

            require ('lib/persist-admin-notices-dismissal.php');
            add_action('admin_init', array('PAnD', 'init'));

            add_action('init', array($this, 'load_plugin_textdomain'));

            if (function_exists('WC')) {
                $woosvi_options = get_option('woosvi_options');
                if (is_admin()) {
                    include_once( 'lib/class-svi-admin.php' );
                } elseif ($woosvi_options['default']) {
                    include_once( 'lib/class-svi-frontend.php' );
                }
            } else {
                add_action('admin_notices', array($this, 'svi_missing_notice'));
            }

            //add_action('admin_notices', array($this, 'svi2_missing_notice'));

            return true;
        }

        /**
         * load the plugin text domain for translation.
         *
         * @since 1.0.0
         * @return bool
         */
        public function load_plugin_textdomain() {
            apply_filters('svi_locale', get_locale(), 'woocommerce-svi');

            load_plugin_textdomain('wc-svi', false, dirname(plugin_basename(__FILE__)) . '/languages');

            return true;
        }

        function svi2_missing_notice() {

            if (!PAnD::is_admin_notice_active('disablesvipro_notice-notice')) {
                return;
            }
            ?>

            <div data-dismissible="disablesvipro_notice-notice" class="notice notice-info is-dismissible">
                <p><?php _e('Take advantage of WooCommerce 3.0 new ligthbox features with <strong>Smart Variations Images PRO</strong> for just <small><del>€25</del></small>€22 until <b>end of April</b>.<br> SVI PRO for WooCommerce makes adding custom images to variations a breeze! Give your customers the most amazing experience while navigating your products! ', 'woocommerce'); ?></p>
                <p class="submit"><a class="button-primary" href="https://www.rosendo.pt/smart-variations-images-pro/" target="_blank"><?php _e('View the features', 'woocommerce'); ?></a></p>
            </div>
            <?php
        }

        /**
         * SVI fallback notice.
         *
         * @return string
         */
        public function svi_missing_notice() {
            ?>
            <div class="error">
                <p><?php _e('Smart Variations Images Pro is enabled but not effective. It requires WooCommerce in order to work.', 'wc_svi'); ?></p>
            </div>
            <?php
        }

    }

    add_action('plugins_loaded', 'svi_init', 0);

    /**
     * init function
     *
     * @package  woocommerce_svi
     * @since 1.0.0
     * @return bool
     */
    function svi_init() {
        new woocommerce_svi();

        return true;
    }

    /**
     * print array
     */
    function svipre($arg) {
        echo "<pre>" . print_r($arg, true) . "</pre>";
    }

}