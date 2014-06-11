<?php

/*
Plugin Name: KP Woocommerce Custom Shipping Methods
Plugin URI: http://woothemes.com/woocommerce
Description: Adds custom shipping methods to WooCommerce
Version: 1.0.0
Author: Kevin Paul Prunty
Author URI: http://kevinpaulprunty.com
*/

/*
 * classes to create
 * Island_Of_Ireland_Shipping -> includes Republic of Ireland and Northern Ireland
 * UK_Mainland_Shipping -> The United Kingdom excluding Northern Ireland
 * Rest_Of_Europe_Shipping -> Europe excluding Ireland and UK
 * Rest_Of_The_World_Shipping -> All other countries 
 */

/*
 * Defining Settings/Options
 * You can then define options using the settings API. 
 * In each class has an init method including init_form_fields() and init_settings() 
 * These load up the settings API. 
 * To see how to add settings, see WooCommerce settings API. 
 * http://docs.woothemes.com/document/shipping-method-api/
 *
 * Copying much of this settings from classes defined in woocommerce core
 * /plugins/woocommerce/includes/shipping
 *
 */




/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
    function kp_custom_shipping_methods_init(){


    	//create the custom shipping method classes
    	
    	if ( ! class_exists( 'Island_Of_Ireland_Shipping' ) ) {

    		/**
	    	 * Island_Of_Ireland_Shipping class
	    	 *
	    	 * @author Kevin Prunty
	    	 **/ 
			class Island_Of_Ireland_Shipping extends WC_Shipping_Method {

				/**
				 * Constructor for UK_Mainland_Shipping
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 	= 'island_of_ireland_shipping';
					$this->title 				= __( 'Island of Ireland' );
					$this->method_description 	= __( 'Custom Shipping method for shipping to Republic of Ireland and Northern Ireland' ); // 
					$this->enabled            	= "yes"; // This can be added as an setting but for this example its forced enabled
					
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_flat_rates' ) );
					add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'save_default_costs' ) );

					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

					/* Copied from class-wc-shipping-flat-rate.php */
					// Define user set variables
					// $this->title 		  = $this->get_option( 'title' );
					// $this->availability   = $this->get_option( 'availability' );
					// $this->countries 	  = $this->get_option( 'countries' );
					// $this->type 		  = $this->get_option( 'type' );
					// $this->tax_status	  = $this->get_option( 'tax_status' );
					// $this->cost 		  = $this->get_option( 'cost' );
					// $this->cost_per_order = $this->get_option( 'cost_per_order' );
					// $this->fee 			  = $this->get_option( 'fee' );
					// $this->minimum_fee 	  = $this->get_option( 'minimum_fee' );
					// $this->options 		  = (array) explode( "\n", $this->get_option( 'options' ) );

					// Load Flat rates
					// $this->get_flat_rates();
					/* End of code copied from class-wc-shipping-flat-rate.php */
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					
					//create an array of values to be passed to the add_rate method
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '10.00', /* set cost */
						'calc_tax' => 'per_item'
					);
 
					// Register the rate
					$this->add_rate( $rate );
				}



				// function init_form_fields(){
					
					// $this->form_fields = array(
					// 				'enabled' => array(
					// 								'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
					// 								'type' 			=> 'checkbox',
					// 								'label' 		=> __( 'Enable this shipping method', 'woocommerce' ),
					// 								'default' 		=> 'no',
					// 							),
					// 				'title' => array(
					// 								'title' 		=> __( 'Method Title', 'woocommerce' ),
					// 								'type' 			=> 'text',
					// 								'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					// 								'default'		=> __( 'Flat Rate', 'woocommerce' ),
					// 								'desc_tip'		=> true
					// 							),
					// 				'availability' => array(
					// 								'title' 		=> __( 'Availability', 'woocommerce' ),
					// 								'type' 			=> 'select',
					// 								'default' 		=> 'all',
					// 								'class'			=> 'availability',
					// 								'options'		=> array(
					// 									'all' 		=> __( 'All allowed countries', 'woocommerce' ),
					// 									'specific' 	=> __( 'Specific Countries', 'woocommerce' ),
					// 								),
					// 							),
					// 				'countries' => array(
					// 								'title' 		=> __( 'Specific Countries', 'woocommerce' ),
					// 								'type' 			=> 'multiselect',
					// 								'class'			=> 'chosen_select',
					// 								'css'			=> 'width: 450px;',
					// 								'default' 		=> '',
					// 								'options'		=> WC()->countries->get_shipping_countries(),
					// 								'custom_attributes' => array(
					// 									'data-placeholder' => __( 'Select some countries', 'woocommerce' )
					// 								)
					// 							),
					// 				'tax_status' => array(
					// 								'title' 		=> __( 'Tax Status', 'woocommerce' ),
					// 								'type' 			=> 'select',
					// 								'default' 		=> 'taxable',
					// 								'options'		=> array(
					// 									'taxable' 	=> __( 'Taxable', 'woocommerce' ),
					// 									'none' 		=> __( 'None', 'woocommerce' ),
					// 								),
					// 							),
					// 				'cost_per_order' => array(
					// 								'title' 		=> __( 'Cost per order', 'woocommerce' ),
					// 								'type' 			=> 'price',
					// 								'placeholder'	=> wc_format_localized_price( 0 ),
					// 								'description'	=> __( 'Enter a cost (excluding tax) per order, e.g. 5.00. Default is 0.', 'woocommerce' ),
					// 								'default'		=> '',
					// 								'desc_tip'		=> true
					// 							),
					// 				'options' => array(
					// 								'title' 		=> __( 'Additional Rates', 'woocommerce' ),
					// 								'type' 			=> 'textarea',
					// 								'description'	=> __( 'Optional extra shipping options with additional costs (one per line): Option Name | Additional Cost [+- Percents] | Per Cost Type (order, class, or item) Example: <code>Priority Mail | 6.95 [+ 0.2%] | order</code>.', 'woocommerce' ),
					// 								'default'		=> '',
					// 								'desc_tip'		=> true,
					// 								'placeholder'	=> __( 'Option Name | Additional Cost [+- Percents%] | Per Cost Type (order, class, or item)', 'woocommerce' )
					// 							),
					// 				'additional_costs' => array(
					// 								'title'			=> __( 'Additional Costs', 'woocommerce' ),
					// 								'type'			=> 'title',
					// 								'description'   => __( 'Additional costs can be added below - these will all be added to the per-order cost above.', 'woocommerce' )
					// 							),
					// 				'type' => array(
					// 								'title' 		=> __( 'Costs Added...', 'woocommerce' ),
					// 								'type' 			=> 'select',
					// 								'default' 		=> 'order',
					// 								'options' 		=> array(
					// 									'order' 	=> __( 'Per Order - charge shipping for the entire order as a whole', 'woocommerce' ),
					// 									'item' 		=> __( 'Per Item - charge shipping for each item individually', 'woocommerce' ),
					// 									'class' 	=> __( 'Per Class - charge shipping for each shipping class in an order', 'woocommerce' ),
					// 								),
					// 							),
					// 				'additional_costs_table' => array(
					// 							'type'				=> 'additional_costs_table'
					// 							),
					// 				'minimum_fee' => array(
					// 								'title' 		=> __( 'Minimum Handling Fee', 'woocommerce' ),
					// 								'type' 			=> 'price',
					// 								'placeholder'	=> wc_format_localized_price( 0 ),
					// 								'description'	=> __( 'Enter a minimum fee amount. Fee\'s less than this will be increased. Leave blank to disable.', 'woocommerce' ),
					// 								'default'		=> '',
					// 								'desc_tip'		=> true
					// 							),
					// 				);

				// } //end init_form_fields function

			}//end Island_Of_Ireland_Shipping class

		}//end if (! class_exists( 'Island_Of_Ireland_Shipping' ))


		if ( ! class_exists( 'UK_Mainland_Shipping' ) ) {

			class UK_Mainland_Shipping extends WC_Shipping_Method {
			
				/**
				 * Constructor for UK_Mainland_Shipping
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'uk_mainland_shipping';
					$this->title       = __( 'UK Mainland' );
					$this->method_description = __( 'Custom Shipping method for shipping to UK excluding Northern Ireland' ); // 
					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					
					//create an array of values to be passed to the add_rate method
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '', /* set cost */
						'calc_tax' => 'per_item'
					);
 
					// Register the rate
					$this->add_rate( $rate );
				}
			
			}//end declaration of class UK_Mainland_Shipping

		}//end if (! class_exists( 'UK_Mainland_Shipping' ))

		if ( ! class_exists( 'Rest_Of_The_World_Shipping' ) ) {

			class Rest_Of_The_World_Shipping extends WC_Shipping_Method {
			
				/**
				 * Constructor for Rest_Of_The_World_Shipping
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'rest_of_the_world';
					$this->title       = __( 'Rest of the World' );
					$this->method_description = __( 'Custom Shipping method for shipping to countries outside Europe' ); // 
					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					
					//create an array of values to be passed to the add_rate method
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '10.75',
						'calc_tax' => 'per_item'
					);
 
					// Register the rate
					$this->add_rate( $rate );
				}
			
			}//end declaration of class Rest_Of_The_World_Shipping

		}//end if (! class_exists( 'Rest_Of_The_World_Shipping' ))

	    if ( ! class_exists( 'Rest_Of_Europe_Shipping' ) ) {

			class Rest_Of_Europe_Shipping extends WC_Shipping_Method {
			
				/**
				 * Constructor for Rest_Of_Europe_Shipping
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'rest_of_the_europe';
					$this->title       = __( 'Rest of the Europe' );
					$this->method_description = __( 'Custom Shipping method for shipping to European countries excluding Ireland and UK' ); // 
					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->init();
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					
					//create an array of values to be passed to the add_rate method
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '', /* set cost */
						'calc_tax' => 'per_item'
					);
 
					// Register the rate
					$this->add_rate( $rate );
				}
			
			}//end declaration of class Rest_Of_Europe_Shipping

		}//end if (! class_exists( 'Rest_Of_Europe_Shipping' ))

    }//end kp_custom_shipping_methods_init

    add_action( 'woocommerce_shipping_init', 'kp_custom_shipping_methods_init' );


	//let WooCommerce know the new shipping method exists
	function add_shipping_methods( $methods ) {
		$methods[] = 'Island_Of_Ireland_Shipping';
		$methods[] = 'UK_Mainland_Shipping'; 
		$methods[] = 'Rest_Of_Europe_Shipping'; 
		$methods[] = 'Rest_Of_The_World_Shipping'; 
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_shipping_methods' );

}













