<?php
/**
 * Plugin Name: WooCommerce Slack
 * Plugin URI: https://woocommerce.com/products/woocommerce-slack/
 * Description: Easily send notifications to your different Slack channels and groups whenever a WooCommerce event happens!
 * Version: 1.1.10
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL-2.0+
 * Domain: woocommerce-slack
 * WC tested up to: 3.5
 * WC requires at least: 2.6
 * Tested up to: 5.0
 * Woo: 609199:5d6bda97bdd686290db0d68143723878
 *
 * Copyright: © 2019 WooCommerce.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Required Functions (Woo Updater)
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '5d6bda97bdd686290db0d68143723878', '609199' );

/**
 * WC_Slack Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.0.0
 */

if ( ! class_exists( 'WC_Slack' ) ) {

	define( 'WC_SLACK_VERSION', '1.1.10' );

	class WC_Slack {

		/**
		 * Construct the plugin
		 **/

		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'init' ) );

		}


		/**
		 * Initialize the plugin
		 **/

		public function init() {

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || isset( get_site_option( 'active_sitewide_plugins')['woocommerce/woocommerce.php'] ) ) {

				// Brace Yourself
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-settings.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-privacy.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-slack.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-events.php' );

				// Vroom.. Vroom..
				add_action( 'plugins_loaded', array( 'WC_Slack_Init', 'get_instance' ) );
				add_action( 'init', array( 'WC_Slack_Events', 'get_instance' ) );
				add_action( 'init', array( 'WC_Slack_API', 'get_instance' ) );

				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

			} else {

				add_action( 'admin_notices', array( $this, 'woocoommerce_deactivated' ) );

			}


		}


		/**
		 * Add Integration Settings
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function add_integration( $integrations ) {

			$integrations[] = 'WC_Slack_Settings';
			return $integrations;

		}


		/**
		 * WooCommerce Deactivated Notice
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function woocoommerce_deactivated() {

			echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Slack requires %s to be installed and active.', 'woocommerce-slack' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';

		}

	}

}

$WC_Slack = new WC_Slack( __FILE__ );


/**
 * Plugin Settings Links etc.
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.0.0
 */

$plugin = plugin_basename( __FILE__ );
add_filter( 'plugin_action_links_' . $plugin, 'wcslack_plugin_links' );

// Add settings link on plugin page
if ( ! function_exists( 'wcslack_plugin_links' ) ) {
	function wcslack_plugin_links( $links ) {

		$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=wcslack' ) . '">' . __( 'Settings', 'woocommerce-slack' ) . '</a>';
		$settings_link .= ' | <a href="http://docs.woocommerce.com/document/woocommerce-slack" target="_blank">' . __( 'Docs', 'woocommerce-slack' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;

	}
}
