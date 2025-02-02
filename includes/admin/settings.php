<?php

/**
 * OurPass Plugin Settings
 *
 * Adds config UI for wp-admin.
 *
 * @package OurPass
 */

// Load admin notices.
require_once OURPASSWC_PATH . 'includes/admin/notices.php';
// Load admin fields.
require_once OURPASSWC_PATH . 'includes/admin/fields.php';


/**
 * Add timestamp when an option is updated.
 *
 * @param string $option    Name of the updated option.
 * @param mixed  $old_value The old option value.
 * @param mixed  $value     The new option value.
 */
function ourpasswc_updated_option($option, $old_value, $value)
{
    if ($old_value === $value) {
        return;
    }

    $stampable_options = array(
        OURPASSWC_SETTING_TEST_PUBLIC_KEY,
        OURPASSWC_SETTING_TEST_SECRET_KEY,
        OURPASSWC_SETTING_LIVE_PUBLIC_KEY,
        OURPASSWC_SETTING_LIVE_SECRET_KEY,
        OURPASSWC_SETTING_SUB_ACCOUNT_KEY,
        //=====
        OURPASSWC_SETTING_PDP_BUTTON_HOOK,
        OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS,
        OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE,
        //====
        OURPASSWC_SETTING_PDP_BUTTON_STYLES,
        OURPASSWC_SETTING_CART_BUTTON_STYLES,
        OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES,
        OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES
    );

    if (in_array($option, $stampable_options, true)) {
        $ourpasswc_settings_timestamps = get_option(OURPASSWC_SETTINGS_TIMESTAMPS, array());

        $ourpasswc_settings_timestamps[$option] = time();

        update_option(OURPASSWC_SETTINGS_TIMESTAMPS, $ourpasswc_settings_timestamps);
    }
}
add_action('updated_option', 'ourpasswc_updated_option', 10, 3);

add_action('admin_menu', 'ourpasswc_admin_create_menu');
add_action('admin_init', 'ourpasswc_maybe_redirect_after_activation', 1);
add_action('admin_init', 'ourpasswc_admin_setup_sections');
add_action('admin_init', 'ourpasswc_admin_setup_fields');

/**
 * Add plugin action links to the OurPass plugin on the plugins page.
 *
 * @param array  $plugin_meta The list of links for the plugin.
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data.
 * @param string $status      Status filter currently applied to the plugin list. Possible
 *                            values are: 'all', 'active', 'inactive', 'recently_activated',
 *                            'upgrade', 'mustuse', 'dropins', 'search', 'paused',
 *                            'auto-update-enabled', 'auto-update-disabled'.
 *
 * @return array
 */
function ourpasswc_admin_plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
{
    if (plugin_basename(OURPASSWC_PATH . 'woo-ourpass.php') !== $plugin_file) {
        return $plugin_meta;
    }

    // Add "Become a Merchant!" CTA if the secret & public has not yet been set.
    if (function_exists('ourpasswc_get_public_key') && function_exists('ourpasswc_get_secret_key')) {
        $publicKey = ourpasswc_get_public_key();
        $secretKey = ourpasswc_get_secret_key();

        if (empty($publicKey) && empty($secretKey)) {
            $ourpasswc_setting_ourpass_onboarding_url = OURPASSWC_ONBOARDING_URL;

            $plugin_meta[] = sprintf(
                '<a href="%1$s" target="_blank" rel="noopener"><strong>%2$s</strong></a>',
                esc_url($ourpasswc_setting_ourpass_onboarding_url),
                esc_html__('Become a Merchant!')
            );
        }
    }

    $plugin_meta[] = sprintf(
        '<a href="%1$s">%2$s</a>',
        esc_url(admin_url('admin.php?page=ourpass')),
        esc_html__('Settings')
    );

    return $plugin_meta;
}
add_action('plugin_row_meta', 'ourpasswc_admin_plugin_row_meta', 10, 4);

/**
 * Registers the OurPass menu within wp-admin.
 */
function ourpasswc_admin_create_menu()
{
    // Add the menu item and page.
    $page_title = 'OurPass Settings';
    $menu_title = 'OurPass';
    $capability = 'manage_options';
    $slug       = 'ourpass';
    $callback   = 'ourpasswc_settings_page_content';
    $icon       = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTY3IiBoZWlnaHQ9IjE2NyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cGF0aCBvcGFjaXR5PSIuNSIgZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Im01NS4zNTIgODkuMTQgMzMuOTEtMzMuODh2MzMuODhoLTMzLjkxWiIgZmlsbD0iI2ZmZiIvPgogIDxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJtNjMuODMyIDEyMy4wNTQgNTYuNTA4LTU2LjUwOHY1Ni41MDhINjMuODMyWk00Ni44NzkgNjYuNTQ1bDIyLjU5Ny0yMi41OTd2MjIuNTk3SDQ2Ljg3OVoiIGZpbGw9IiNmZmYiLz4KPC9zdmc+Cg==';
    $position   = 101;

    add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
}

/**
 * Maybe redirect to the OurPass settings page after activation.
 */
function ourpasswc_maybe_redirect_after_activation()
{
    $activated = get_option(OURPASSWC_PLUGIN_ACTIVATED, false);

    if ($activated) {
        // Delete the flag to prevent an endless redirect loop.
        delete_option(OURPASSWC_PLUGIN_ACTIVATED);

        // Redirect to the OurPass settings page.
        wp_safe_redirect(
            esc_url(
                admin_url('admin.php?page=ourpass')
            )
        );
        exit;
    }
}

/**
 * Get the list of tabs for the OurPass settings page.
 *
 * @return array
 */
function ourpasswc_get_settings_tabs()
{
    /**
     * Filter the list of settings tabs.
     *
     * @param array $settings_tabs The settings tabs.
     *
     * @return array
     */
    return apply_filters(
        'ourpasswc_settings_tabs',
        array(
            'ourpass_app_info'  => __('App Info'),
            'ourpass_options'   => __('Options'),
            'ourpass_styles'   => __('Styles')
        )
    );
}

/**
 * Get the active tab in the OurPass settings page.
 *
 * @return string
 */
function ourpasswc_get_active_tab()
{
    return isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'ourpass_app_info'; // phpcs:ignore
}

/**
 * Renders content of OurPass settings page.
 */
function ourpasswc_settings_page_content()
{
    ourpasswc_load_template('admin/ourpass-settings');
}

/**
 * Sets up sections for OurPass settings page.
 */
function ourpasswc_admin_setup_sections()
{

    $section_name = 'ourpass_app_info';
    add_settings_section($section_name, '', false, $section_name);
    register_setting($section_name, OURPASSWC_SETTING_DEBUG_MODE);
    register_setting($section_name, OURPASSWC_SETTING_TEST_MODE);
    register_setting($section_name, OURPASSWC_SETTING_TEST_PUBLIC_KEY);
    register_setting($section_name, OURPASSWC_SETTING_TEST_SECRET_KEY);
    register_setting($section_name, OURPASSWC_SETTING_LIVE_PUBLIC_KEY);
    register_setting($section_name, OURPASSWC_SETTING_LIVE_SECRET_KEY);
    register_setting($section_name, OURPASSWC_SETTING_SUB_ACCOUNT_KEY);

    $section_name = 'ourpass_options';
    add_settings_section($section_name, '', false, $section_name);
    register_setting($section_name, OURPASSWC_SETTING_PDP_BUTTON_HOOK);
    register_setting($section_name, OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS);
    register_setting($section_name, OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE);

    $section_name = 'ourpass_styles';
    add_settings_section($section_name, '', false, $section_name);
    register_setting($section_name, OURPASSWC_SETTING_BUTTON_SIZE);
    register_setting($section_name, OURPASSWC_SETTING_LOAD_BUTTON_STYLES);
    register_setting($section_name, OURPASSWC_SETTING_PDP_BUTTON_STYLES);
    register_setting($section_name, OURPASSWC_SETTING_CART_BUTTON_STYLES);
    register_setting($section_name, OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES);
    register_setting($section_name, OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES);
}

/**
 * Sets up fields for OurPass settings page.
 */
function ourpasswc_admin_setup_fields()
{
    // App Info settings.
    $settings_section = 'ourpass_app_info';
    add_settings_field(OURPASSWC_SETTING_TEST_MODE, __('Test Mode'), 'ourpasswc_test_mode_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_DEBUG_MODE, __('Debug Mode'), 'ourpasswc_debug_mode_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_TEST_PUBLIC_KEY, __('Public test key'), 'ourpasswc_test_public_key_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_TEST_SECRET_KEY, __('Secret test key'), 'ourpasswc_test_secret_key_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_LIVE_PUBLIC_KEY, __('Public live key'), 'ourpasswc_live_public_key_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_LIVE_SECRET_KEY, __('Secret live key'), 'ourpasswc_live_secret_key_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_SUB_ACCOUNT_KEY, __('Sub Account key'), 'ourpasswc_sub_account_key_content', $settings_section, $settings_section);

    // Button options settings.
    $settings_section = 'ourpass_options';
    add_settings_field(OURPASSWC_SETTING_PDP_BUTTON_HOOK, __('Select Product Button Location'), 'ourpasswc_pdp_button_hook', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS, __('Hide Buttons for these Products'), 'ourpasswc_hide_button_products', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE, __('Checkout Redirect Page'), 'ourpasswc_checkout_redirect_page', $settings_section, $settings_section);

    // Button style settings.
    $settings_section = 'ourpass_styles';
    add_settings_field(OURPASSWC_SETTING_BUTTON_SIZE, __('Select Button size'), 'ourpasswc_button_size', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_LOAD_BUTTON_STYLES, __('Load Button Styles'), 'ourpasswc_load_button_styles', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_PDP_BUTTON_STYLES, __('Product page button styles'), 'ourpasswc_pdp_button_styles_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_CART_BUTTON_STYLES, __('Cart page button styles'), 'ourpasswc_cart_button_styles_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES, __('Mini cart widget button styles'), 'ourpasswc_mini_cart_button_styles_content', $settings_section, $settings_section);
    add_settings_field(OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES, __('Checkout page button styles'), 'ourpasswc_checkout_button_styles_content', $settings_section, $settings_section);

}


/**
 * Renders the Test Mode field.
 */
function ourpasswc_test_mode_content()
{
    $ourpasswc_test_mode = get_option(OURPASSWC_SETTING_TEST_MODE, OURPASSWC_SETTING_TEST_MODE_NOT_SET);

    if (OURPASSWC_SETTING_TEST_MODE_NOT_SET === $ourpasswc_test_mode) {
        // If the option is OURPASSWC_SETTING_TEST_MODE_NOT_SET, then it hasn't yet been set. In this case, we
        // want to configure test mode to be on.
        $ourpasswc_test_mode = '1';
        update_option(OURPASSWC_SETTING_TEST_MODE, '1');
    }

    ourpasswc_settings_field_checkbox(
        array(
            'name'        => OURPASSWC_SETTING_TEST_MODE,
            'current'     => $ourpasswc_test_mode,
            'label'       => __('Enable test mode'),
            'description' => __('At the point when test mode is ticked, just signed in administrator will see the OurPass Checkout button.')
        )
    );
}

/**
 * Renders the Debug Mode field.
 */
function ourpasswc_debug_mode_content()
{
    $ourpasswc_debug_mode = get_option(OURPASSWC_SETTING_DEBUG_MODE, OURPASSWC_SETTING_DEBUG_MODE_NOT_SET);

    if (OURPASSWC_SETTING_DEBUG_MODE_NOT_SET === $ourpasswc_debug_mode) {
        // If the option is OURPASSWC_SETTING_DEBUG_MODE_NOT_SET, then it hasn't yet been set. In this case, we
        // want to configure debug mode to be off.
        $ourpasswc_debug_mode = 0;
        update_option(OURPASSWC_SETTING_DEBUG_MODE, $ourpasswc_debug_mode);
    }

    ourpasswc_settings_field_checkbox(
        array(
            'name'        => OURPASSWC_SETTING_DEBUG_MODE,
            'current'     => $ourpasswc_debug_mode,
            'label'       => __('Enable debug mode'),
            'description' => __('The OurPass plugin will keep an error log when debug mode is ticked.')
        )
    );
}

/**
 * Renders the test public key field.
 */
function ourpasswc_test_public_key_content()
{
    ourpasswc_settings_field_input(
        array(
            'name'        => OURPASSWC_SETTING_TEST_PUBLIC_KEY,
            'value'       => get_option(OURPASSWC_SETTING_TEST_PUBLIC_KEY)
        )
    );
}

/**
 * Renders the test secret key field.
 */
function ourpasswc_test_secret_key_content()
{
    ourpasswc_settings_field_input(
        array(
            'name'        => OURPASSWC_SETTING_TEST_SECRET_KEY,
            'value'       => get_option(OURPASSWC_SETTING_TEST_SECRET_KEY)
        )
    );
}

/**
 * Renders the live public key field.
 */
function ourpasswc_live_public_key_content()
{
    ourpasswc_settings_field_input(
        array(
            'name'        => OURPASSWC_SETTING_LIVE_PUBLIC_KEY,
            'value'       => get_option(OURPASSWC_SETTING_LIVE_PUBLIC_KEY)
        )
    );
}

/**
 * Renders the live secret key field.
 */
function ourpasswc_live_secret_key_content()
{
    ourpasswc_settings_field_input(
        array(
            'name'        => OURPASSWC_SETTING_LIVE_SECRET_KEY,
            'value'       => get_option(OURPASSWC_SETTING_LIVE_SECRET_KEY)
        )
    );
}

/**
 * Renders the sub account key field.
 */
function ourpasswc_sub_account_key_content()
{
    ourpasswc_settings_field_input(
        array(
            'name'        => OURPASSWC_SETTING_SUB_ACCOUNT_KEY,
            'value'       => get_option(OURPASSWC_SETTING_SUB_ACCOUNT_KEY)
        )
    );
}



/**
 * Renders the PDP Button Hook field.
 */
function ourpasswc_pdp_button_hook()
{
    $ourpasswc_setting_pdp_button_hook = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_PDP_BUTTON_HOOK, OURPASSWC_DEFAULT_PDP_BUTTON_HOOK);

    ourpasswc_settings_field_select(
        array(
            'name'        => OURPASSWC_SETTING_PDP_BUTTON_HOOK,
            'description' => __('Select an area inside the Add to Cart form to show the OurPass Checkout button.'),
            'value'       => $ourpasswc_setting_pdp_button_hook,
            'options'     => array(
                'woocommerce_before_add_to_cart_quantity' => __('Before Quantity Selection'),
                'woocommerce_after_add_to_cart_quantity'  => __('After Quantity Selection'),
                'woocommerce_after_add_to_cart_button'    => __('After Add to Cart Button')
            )
        )
    );
}

/**
 * Renders the Hide Buttons for Products field.
 */
function ourpasswc_hide_button_products()
{
    $ourpasswc_setting_hide_button_products = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS, array());

    $selected = array();
    if (!empty($ourpasswc_setting_hide_button_products)) {
        if (!is_array($ourpasswc_setting_hide_button_products)) {
            $ourpasswc_setting_hide_button_products = array($ourpasswc_setting_hide_button_products);
        }

        $ourpasswc_hide_products = wc_get_products(
            array(
                'include' => $ourpasswc_setting_hide_button_products
            )
        );

        foreach ($ourpasswc_hide_products as $ourpasswc_hide_product) {
            $selected[$ourpasswc_hide_product->get_id()] = $ourpasswc_hide_product->get_name();
        }
    }

    ourpasswc_settings_field_ajax_select(
        array(
            'name'        => OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS,
            'selected'    => $selected,
            'class'       => 'ourpass-select ourpass-select--hide-button-products',
            'description' => __('Select items for which the OurPass checkout button ought not to show for.'),
            'nonce'       => 'search-products'
        )
    );
}

/**
 * Renders the Checkout Redirect Page field.
 */
function ourpasswc_checkout_redirect_page()
{
    $ourpasswc_setting_checkout_redirect_page = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE, 0);

    $selected = array();
    if (!empty($ourpasswc_setting_checkout_redirect_page)) {
        $ourpasswc_checkout_redirect_page = get_post($ourpasswc_setting_checkout_redirect_page);

        if (!empty($ourpasswc_checkout_redirect_page)) {
            $selected[$ourpasswc_checkout_redirect_page->ID] = $ourpasswc_checkout_redirect_page->post_title;
        }
    }

    ourpasswc_settings_field_ajax_select(
        array(
            'name'        => OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE,
            'selected'    => $selected,
            'class'       => 'ourpass-select ourpass-select--checkout-redirect-page',
            'description' => __('Select a page to divert to once checkout is completed.Leave it blank if you want it to redirect to the cart.'),
            'nonce'       => 'search-pages',
            'multiple'    => false
        )
    );
}


/**
 * Renders the dropdown for button size.
 */
function ourpasswc_button_size()
{
    $ourpasswc_setting_size = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_BUTTON_SIZE, OURPASSWC_DEFAULT_BUTTON_SIZE);

    ourpasswc_settings_field_select(
        array(
            'name'        => OURPASSWC_SETTING_BUTTON_SIZE,
            'description' => __('Select the size you want the OurPass checkout buttons to assume to your customers on your site.'),
            'value'       => $ourpasswc_setting_size,
            'options'     => array(
                'default' => __('Default'),
                'compact'  => __('Compact'),
                'large'    => __('Large')
            )
        )
    );
}

/**
 * Renders a checkbox to set whether or not to load the button styles.
 */
function ourpasswc_load_button_styles()
{
    $ourpasswc_load_button_styles = get_option(OURPASSWC_SETTING_LOAD_BUTTON_STYLES, OURPASSWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET);

    if (OURPASSWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET === $ourpasswc_load_button_styles) {
        // If the option is OURPASSWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET, then it hasn't yet been set. In this case, we
        // want to configure it to true.
        $ourpasswc_load_button_styles = '1';
        update_option(OURPASSWC_SETTING_LOAD_BUTTON_STYLES, $ourpasswc_load_button_styles);
    }

    ourpasswc_settings_field_checkbox(
        array(
            'name'        => OURPASSWC_SETTING_LOAD_BUTTON_STYLES,
            'current'     => $ourpasswc_load_button_styles,
            'label'       => __('Load the button styles as configured in the settings.'),
            'description' => __('Upon ticking this box, the styles arranged beneath will be stacked to give extra styling to the OurPass checkout buttons.')
        )
    );
}

/**
 * Renders the PDP button styles field.
 */
function ourpasswc_pdp_button_styles_content()
{
    $ourpasswc_setting_pdp_button_styles = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_PDP_BUTTON_STYLES, OURPASSWC_SETTING_PDP_BUTTON_STYLES_DEFAULT);

    ourpasswc_settings_field_textarea(
        array(
            'name'  => OURPASSWC_SETTING_PDP_BUTTON_STYLES,
            'value' => $ourpasswc_setting_pdp_button_styles
        )
    );
}

/**
 * Renders the cart button styles field.
 */
function ourpasswc_cart_button_styles_content()
{
    $ourpasswc_setting_cart_button_styles = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_CART_BUTTON_STYLES, OURPASSWC_SETTING_CART_BUTTON_STYLES_DEFAULT);

    ourpasswc_settings_field_textarea(
        array(
            'name'  => OURPASSWC_SETTING_CART_BUTTON_STYLES,
            'value' => $ourpasswc_setting_cart_button_styles
        )
    );
}

/**
 * Renders the mini-cart button styles field.
 */
function ourpasswc_mini_cart_button_styles_content()
{
    $ourpasswc_setting_mini_cart_button_styles = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES, OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT);

    ourpasswc_settings_field_textarea(
        array(
            'name'  => OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES,
            'value' => $ourpasswc_setting_mini_cart_button_styles
        )
    );
}

/**
 * Renders the checkout button styles field.
 */
function ourpasswc_checkout_button_styles_content()
{
    $ourpasswc_setting_checkout_button_styles = ourpasswc_get_option_or_set_default(OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES, OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT);

    ourpasswc_settings_field_textarea(
        array(
            'name'  => OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES,
            'value' => $ourpasswc_setting_checkout_button_styles
        )
    );
}


/**
 * Get the OurPass Test Mode.
 *
 * @return bool
 */
function ourpasswc_get_is_test_mode()
{
    $ourpasswc_test_mode = get_option(OURPASSWC_SETTING_TEST_MODE, OURPASSWC_SETTING_TEST_MODE_NOT_SET);

    if (OURPASSWC_SETTING_TEST_MODE_NOT_SET === $ourpasswc_test_mode) {
        $ourpasswc_test_mode = '1';
        update_option(OURPASSWC_SETTING_TEST_MODE, '1');
    }

    return ($ourpasswc_test_mode === '1')? true: false;
}

/**
 * Get the OurPass Public Key.
 *
 * @return string
 */
function ourpasswc_get_public_key()
{
    if(ourpasswc_get_is_test_mode()){
        return get_option(OURPASSWC_SETTING_TEST_PUBLIC_KEY);
    }
    return get_option(OURPASSWC_SETTING_LIVE_PUBLIC_KEY);
}

/**
 * Get the OurPass Secret Key.
 *
 * @return string
 */
function ourpasswc_get_secret_key()
{
    if(ourpasswc_get_is_test_mode()){
        return get_option(OURPASSWC_SETTING_TEST_SECRET_KEY);
    }
    return get_option(OURPASSWC_SETTING_LIVE_SECRET_KEY);
}

/**
 * Get the OurPass Environment.
 *
 * @return string
 */
function ourpasswc_get_environment()
{
    return ourpasswc_get_is_test_mode() ? 'sandbox' : 'live';
}

/**
 * Get the OurPass Environment.
 *
 * @return string
 */
function ourpasswc_get_sub_account_key()
{
    return get_option(OURPASSWC_SETTING_SUB_ACCOUNT_KEY);
}

/**
 * Helper that returns the value of an option if it is set, and sets and returns a default if the option was not set.
 * This is similar to get_option($option, $default), except that it *sets* the option if it is not set instead of just returning a default.
 *
 * @see https://developer.wordpress.org/reference/functions/get_option/
 *
 * @param string $option Name of the option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Default value to set option to and return if the return value of get_option is falsey.
 * @return mixed The value of the option if it is truthy, or the default if the option's value is falsey.
 */
function ourpasswc_get_option_or_set_default($option, $default)
{
    $val = get_option($option);
    if (false !== $val) {
        return $val;
    }
    update_option($option, $default);
    return $default;
}

/**
 * Search pages to return for the page select Ajax.
 */
function ourpasswc_ajax_search_pages()
{
    check_ajax_referer('search-pages', 'security');

    $return = array();

    if (isset($_GET['term'])) {
        $q_term = (string) sanitize_text_field(wp_unslash($_GET['term']));
    }

    if (empty($q_term)) {
        wp_die();
    }

    $search_results = new WP_Query(
        array(
            's'              => $q_term,
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'posts_per_page' => -1
        )
    );

    if ($search_results->have_posts()) {
        while ($search_results->have_posts()) {
            $search_results->the_post();

            $return[get_the_ID()] = get_the_title();
        }
        wp_reset_postdata();
    }

    wp_send_json($return);
}
add_action('wp_ajax_ourpasswc_search_pages', 'ourpasswc_ajax_search_pages');
