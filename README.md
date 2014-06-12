kp-woocommerce-shipping
=======================

A wordpress plugin to add custom shipping methods to woocommerce
Version 1.0.0 is a set of 5 shipping methods specific to their shipping destinations
These can be altered to match the requirements of different online stores

Should be abstracted so that multiple shipping methods can be created through a plugin UI 
Abstract classes can be based on the existing WooCommerce Shipping Methods but will offer greater customisation

The ability to customise locales through the backend (unrequiring post codes, altering labels from 'State' to 'County' etc.) could be useful


=======================
Classes
=======================

all of the classes extend WC_Shipping_Method

Republic_Of_Ireland_Shipping
Northern_Ireland_Shipping
UK_Mainland_Shipping
Rest_Of_Europe_Shipping
Rest_Of_The_World_Shipping

=======================
Functions
=======================

kp_custom_shipping_methods_init()

Each class contains the following functions
__construct()
	sets id, title, method_description, enabled properties
	calls the init() function


init()
	calls init_form_fields() function - this inherited method is overridden 
	calls init_settings() function - inherited 
	sets the following properties using get_option(), example: $this->enabled = $this->get_option('enabled');
		enabled 				
		title 				
		description 			
		availability 		
		countries 			
		tax_status 			
		cost_per_order 		
		options 				
		additional_costs 	
		type 				
		additional_costs_table
		minimum_fee 		

	save the settings with the following:
	add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );	


init_form_fields()
	initialises the Gateway Settings Form Fields - this is where the user can customise the shipping methods

calculate_shipping( $package )
	calculates the cost of shipping
	sets the objects rate
	$this->add_rate( $rate );

is_available( $package )
	allow or disallow the shipping method for an order based on factors such as
	- is the method enabled (all are enabled by default but can be disabled through the backend)
	- contents/number of items in the cart
	- country/destination 

admin_options()
	output the settings
	calls generate_settings_html() method wrapped in a little html

clean()
	a single line of code used to tidy up the postcode (could be used to do more?)
















