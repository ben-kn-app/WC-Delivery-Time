<?php
//todo submenu under woocommerce settings products, settings plugin to this page, clean up plugin, publish. github?
/**
 * Integration WC Delivery Time Integration.
 *
 * @package  WC_Delivery_Time
 * @category Integration
 * @author   Ben Chini
 * @source: https://docs.woothemes.com/document/implementing-wc-integration/
 */
 

 
if ( ! class_exists( 'WC_Delivery_Time' ) ) :
class WC_Delivery_Time extends WC_Integration {
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		//global $woocommerce;
		$this->id                 = 'deliverytime';
		$this->method_title       = __( 'Delivery Time', 'wc-delivery-time' );
		$this->method_description = __( 'Display the delivery time on  EVERY product page e.g. Estimated Delivery: 2 - 3 weeks. Format: "Title:" "minimum time" - "maximum time" "time Unit".', 'wc-delivery-time' );
		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		
		// Define user set variables.
		$this->show_delivery_time  					= $this->get_option( 'show_delivery_time' );
		$this->estimated_delivery_time  			= $this->get_option( 'estimated_delivery_time' );
		$this->estimated_delivery_time_min_value  	= $this->get_option( 'estimated_delivery_time_min_value' );
		$this->estimated_delivery_time_max_value  	= $this->get_option( 'estimated_delivery_time_max_value' );
		$this->estimated_delivery_time_unit_code  	= $this->get_option( 'estimated_delivery_time_unit_code' );
		$this->estimated_delivery_display  			= $this->get_option( 'estimated_delivery_display' );

		// Actions.
		//Add action to save the options
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		
		if ('yes' == $this->show_delivery_time) {
			add_action( 'woocommerce_single_product_summary', array($this,'show_delivery_time_on_product'), $this->estimated_delivery_display );
		}
	}
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters( 'woocommerce_delivery_time_settings', array(
			'show_delivery_time' => array(
				'title'             => __( 'Display Delivery Time', 'wc-delivery-time' ),
				'type'              => 'checkbox',
				'label'             => __( 'Show delivery time on product page (after price).', 'wc-delivery-time' ),
				'default'           => 'no',
				'description'       => __( '', 'wc-delivery-time' ),
			),
			'estimated_delivery_time' => array(
				'title'             => __( 'Title', 'wc-delivery-time' ),
				'type'              => 'text',
				'description'       => __( 'Enter the title, these will be displayed before the times on all products.', 'wc-delivery-time' ),
				'desc_tip'          => true,
				'default'           => __('Estimated Delivery:', 'wc-delivery-time' ),
			),
			'estimated_delivery_time_min_value' => array(
				'title'             => __( 'Minimum Delivery Time', 'wc-delivery-time' ),
				'type'              => 'decimal',
				'description'       => __( '', 'wc-delivery-time' ),
				'desc_tip'          => false,
				'default'           => __('', 'wc-delivery-time' ),
			),
			'estimated_delivery_time_max_value' => array(
				'title'             => __( 'Maximum Delivery Time', 'wc-delivery-time' ),
				'type'              => 'decimal',
				'description'       => __( '', 'wc-delivery-time' ),
				'desc_tip'          => false,
				'default'           => __('', 'wc-delivery-time' ),
			),
			'estimated_delivery_time_unit_code' => array(
				'title'             => __( 'Estimated Delivery Time Unit', 'wc-delivery-time' ),
				'type'              => 'select',
				'options'			=> array("working days" => "Working days", "weeks" => "Weeks"),
				'description'       => __( '', 'wc-delivery-time' ),
				'desc_tip'          => false,
				'default'           => __('', 'wc-delivery-time' ),
			),
			'estimated_delivery_display' => array(
				'title'             => __( 'Where to display delivery time', 'wc-delivery-time' ),
				'type'              => 'select',
				'options'			=> array(13 => "After price", 33 => "After Variations"),
				'description'       => __( '', 'wc-delivery-time' ),
				'desc_tip'          => false,
				'default'           => __('', 'wc-delivery-time' ),
			),
		));
	}
	function show_delivery_time_on_product() {
		/** Display delivery time **/
		echo '<div itemprop="deliveryLeadTime" itemscope itemtype="http://schema.org/QuantitativeValue">';
		echo $this->estimated_delivery_time;
		echo '	<span itemprop="minValue">'.$this->estimated_delivery_time_min_value.'</span> -';
		echo '	<span itemprop="maxValue">'.$this->estimated_delivery_time_max_value.'</span> ';
		echo '	<span>'.$this->estimated_delivery_time_unit_code.'</span>';
		echo '	<meta itemprop="unitCode" content="'.$this->convert_estimated_delivery_time_unit_code($this->estimated_delivery_time_unit_code).'" />';
		echo '</div>';
	}
	function convert_estimated_delivery_time_unit_code($time) {
		// Convert text to UN/CEFACT Common Codes for Units of Measurement
		// source: http://www.unece.org/fileadmin/DAM/cefact/recommendations/rec20/rec20_Rev9e_2014.xls
		switch (apply_filters( 'woocommerce_delivery_time_unit',$time)) {
			case 'working days': 
				return 'E49';
			case 'weeks':
				return 'WEE';
			default: 
				return $time;
		}	
	}
	function log_me($message) {
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log(print_r($message, true));
			} else {
				error_log($message);
			}
		}
	}
}
endif;