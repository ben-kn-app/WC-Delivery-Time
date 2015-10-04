<?php
/**
 * Plugin Name: WooCommerce Delivery Time
 * Plugin URI: http://rawlab.nl/plugin
 * Description: Set an estimated delivery time for all products
 * Author: Ben Chini
 * Author URI: http://facebook.com/benchini
 * Version: 0.9
 * Text Domain: wc-delivery-time
 * Domain Path: languages
 *
 * Copyright: © 2015 Ben Chini
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

class WC_Delivery_Time_Init {
	/**
	* Construct the plugin.
	*/
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
	/**
	* Initialize the plugin.
	*/
	public function init() {
		if ( ! class_exists( 'WC_Integration' ) )
			return;
	
		load_plugin_textdomain( 'wc-delivery-time', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	
		global $woocommerce;

		$settings_url = admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=deliverytime' );
	
		if ( $woocommerce->version >= '2.1' ) {
			$settings_url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=deliverytime' );
		}
	
		if ( ! defined( 'WOOCOMMERCE_DELIVERY_TIME_SETTINGS_URL' ) ) {
			define( 'WOOCOMMERCE_DELIVERY_TIME_SETTINGS_URL', $settings_url );
		}
	
		include_once 'classes/class-wc-delivery-time.php';
		add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		
		// Add the "Settings" links on the Plugins administration screen
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'action_links' );
	}
	/**
	 * Add a new integration to WooCommerce.
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'WC_Delivery_Time';
		return $integrations;
	}
	/**
	 * Add Settings link to plugins list
	 *
	 * @param  array $links Plugin links
	 * @return array        Modified plugin links
	 */
	function action_links( $links ) {
		$plugin_links = array(
			'<a href="' . WOOCOMMERCE_DELIVERY_TIME_SETTINGS_URL . '">' . __( 'Settings', 'wc-delivery-time' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}
}
$WC_Delivery_Time_Init = new WC_Delivery_Time_Init( __FILE__ );
