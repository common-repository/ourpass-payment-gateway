<?php
/**
 * Common utility functions for the OurPass plugin.
 *
 * @package OurPass
 */

/**
 * Load a OurPass template.
 *
 * @param string $template_name The name of the template to load.
 * @param array  $args          Optional. Args to pass to the template. Requires WP 5.5+.
 *
 * @uses load_template
 */
function ourpasswc_load_template( $template_name, $args = array() ) {
	$locations = array(
		// Child theme directory.
		get_stylesheet_directory() . '/templates/' . $template_name . '.php',

		// Parent theme directory.
		get_template_directory() . '/templates/' . $template_name . '.php',

		// Plugin directory.
		OURPASSWC_PATH . 'templates/' . $template_name . '.php'
	);

	// Check each file location and load the first one that exists.
	foreach ( $locations as $location ) {
		if ( file_exists( $location ) ) {
			/**
			 * WordPress load_template function to load the located template.
			 *
			 * @param string $location     Location of the template to load.
			 * @param bool   $require_once Flag to use require_once instead of require.
			 * @param array  $args         Array of args to pass to the tepmlate. Requires WP 5.5+.
			 */
			load_template( $location, false, $args );
			ourpasswc_log_info( 'Loaded template: ' . $location );
			return;
		}
	}
}

/**
 * Get the selected hook/location to render the PDP button.
 *
 * @return string
 */
function ourpasswc_get_pdp_button_hook() {
	$ourpasswc_pdp_button_hook = get_option( OURPASSWC_SETTING_PDP_BUTTON_HOOK, OURPASSWC_DEFAULT_PDP_BUTTON_HOOK );

	return ! empty( $ourpasswc_pdp_button_hook ) ? $ourpasswc_pdp_button_hook : OURPASSWC_DEFAULT_PDP_BUTTON_HOOK;
}

/**
 * Get the list of products for which the button should be hidden.
 *
 * @return array
 */
function ourpasswc_get_products_to_hide_buttons() {
	$ourpasswc_hidden_products = get_option( OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS );

	if ( ! empty( $ourpasswc_hidden_products ) ) {
		$ourpasswc_count_products = count( $ourpasswc_hidden_products );

		for ( $i = 0; $i < $ourpasswc_count_products; $i++ ) {
			$ourpasswc_hidden_products[ $i ] = (int) $ourpasswc_hidden_products[ $i ];
		}

		ourpasswc_log_info( 'Products fetched to hide buttons: ' . print_r( $ourpasswc_hidden_products, true ) ); // phpcs:ignore
	}

	return $ourpasswc_hidden_products;
}

/**
 * Determine if a product is supported.
 *
 * @param int $product_id The product ID to check.
 *
 * @return bool
 */
function ourpasswc_product_is_supported( $product_id ) {
	/**
	 * Filter to determine if a product is supported by OurPass Checkout. Returns true by default.
	 *
	 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
	 * @param int  $product_id   The ID of the product to check.
	 */
	$is_supported = apply_filters( 'ourpasswc_product_is_supported', true, $product_id );

	ourpasswc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported: ' . $product_id );

	return $is_supported;
}

/**
 * Check if a product is supported based on if it has addons.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function ourpasswc_product_is_supported_if_no_addons( $is_supported, $product_id ) {
	if ( ourpasswc_product_has_addons( $product_id ) ) {
		$is_supported = false;
	}

	ourpasswc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after addon check: ' . $product_id );

	return $is_supported;
}
add_filter( 'ourpasswc_product_is_supported', 'ourpasswc_product_is_supported_if_no_addons', 10, 2 );

/**
 * Check if a product is supported based on if it is not a grouped product.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function ourpasswc_product_is_supported_if_not_grouped( $is_supported, $product_id ) {
	if ( ourpasswc_product_is_grouped( $product_id ) ) {
		$is_supported = false;
	}

	ourpasswc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after grouped check: ' . $product_id );

	return $is_supported;
}
add_filter( 'ourpasswc_product_is_supported', 'ourpasswc_product_is_supported_if_not_grouped', 10, 2 );

/**
 * Check if a product is supported based on if it is not a subscription product.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function ourpasswc_product_is_supported_if_not_subscription( $is_supported, $product_id ) {
	if ( ourpasswc_product_is_subscription( $product_id ) ) {
		$is_supported = false;
	}

	ourpasswc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after subscription check: ' . $product_id );

	return $is_supported;
}
add_filter( 'ourpasswc_product_is_supported', 'ourpasswc_product_is_supported_if_not_subscription', 10, 2 );

/**
 * Detect if the product has any addons (OurPass Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function ourpasswc_product_has_addons( $product_id ) {
	$has_addons = false;

	if ( class_exists( WC_Product_Addons_Helper::class ) ) {
		// If the store has the addons plugin installed, then we can use its static function to see if this product has any
		// addons.
		$addons = WC_Product_Addons_Helper::get_product_addons( $product_id );
		if ( ! empty( $addons ) ) {
			// If this product has any addons (not just the one in the cart, but the product as a whole), hide the button.
			$has_addons = true;
		}
	}

	ourpasswc_log_info( 'Product does' . ( $has_addons ? '' : ' not' ) . ' have addons: ' . $product_id );

	return $has_addons;
}

/**
 * Detect if the product is a grouped product (OurPass Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function ourpasswc_product_is_grouped( $product_id ) {
	$is_grouped = false;

	$product = wc_get_product( $product_id );

	if (
		method_exists( $product, 'get_type' ) &&
		'grouped' === $product->get_type()
	) {
		$is_grouped = true;
	}

	ourpasswc_log_info( 'Product is' . ( $is_grouped ? '' : ' not' ) . ' grouped: ' . $product_id );

	return $is_grouped;
}

/**
 * Detect if the product is a subscription product (OurPass Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function ourpasswc_product_is_subscription( $product_id ) {
	$product = wc_get_product( $product_id );

	$is_subscription = false;

	if (
		is_a( $product, WC_Product_Subscription::class ) ||
		is_a( $product, WC_Product_Variable_Subscription::class )
	) {
		$is_subscription = true;
	}

	ourpasswc_log_info( 'Product is' . ( $is_subscription ? '' : ' not' ) . ' a subscription: ' . $product_id );

	return false;
}

/**
 * Get OurPass button styles.
 *
 * @param mixed|string|array $button_type Type of styles to get. Default to empty string for all.
 *
 * @return string
 */
function ourpasswc_get_button_styles( $button_type = '' ) {
	$types = array(
		'pdp'       => OURPASSWC_SETTING_PDP_BUTTON_STYLES,
		'mini_cart' => OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES,
		'checkout'  => OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES,
		'cart'      => OURPASSWC_SETTING_CART_BUTTON_STYLES
	);

	// If $button_type is empty, use all button types.
	$button_type = '' === $button_type ? array_keys( $types ) : $button_type;
	$button_type = is_array( $button_type ) ? $button_type : array( $button_type );

	$button_styles = array();
	foreach ( $button_type as $type ) {
		if ( in_array( $type, array_keys( $types ), true ) ) {
			$button_styles[] = get_option( $types[ $type ], '' );
		}
	}

	return implode( "\n", $button_styles );
}

/**
 * Check if a string is valid JSON.
 *
 * @param string $string The string to check.
 *
 * @return bool
 */
function ourpasswc_is_json( $string ) {
	if ( ! defined( 'JSON_ERROR_NONE' ) ) {
		define( 'JSON_ERROR_NONE', 0 );
	}

	json_decode( $string );
	return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get the OurPass product options string.
 *
 * @param mixed|string|array $product_options The product options value.
 *
 * @return string
 */
function ourpasswc_get_normalized_product_options( $product_options ) {
	if ( is_array( $product_options ) ) {
		$product_options = json_encode( $product_options );
	}

	return ourpasswc_is_json( $product_options ) ? $product_options : '';
}

/**
 * Check if reference already exist
 *
 * @param array $reference
 * @return bool
 */
function ourpasswc_reference_is_unique($reference)
{
	global $wpdb;

	$data = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s", array(OURPASSWC_ORDER_REFERENCE_META_KEY, $reference)));
	
	return count($data) < 1;
}

/**
 * Check if reference already exist
 *
 * @param array $reference
 * @return int
 */
function ourpasswc_get_last_sale_order_post_id()
{
	global $wpdb;

	$data = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key=%s ORDER BY `post_id` DESC LIMIT 1 OFFSET 0", array(OURPASSWC_ORDER_REFERENCE_META_KEY)));

	if(count($data) < 1) {
        return 0;
	}

	return intval($data[0]->post_id);
}

function ourpasswc_get_button_size()
{
    return get_option(OURPASSWC_SETTING_BUTTON_SIZE, 'default');
}