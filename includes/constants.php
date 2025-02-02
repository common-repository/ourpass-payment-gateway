<?php

/**
 * OurPass Plugin Settings Constants
 *
 * @package OurPass
 */

// APP INFO SETTINGS
define('OURPASSWC_SETTINGS_TIMESTAMPS', 'ourpasswc_settings_timestamps');
define('OURPASSWC_SETTING_DEBUG_MODE', 'ourpasswc_debug_mode' );
define('OURPASSWC_SETTING_DEBUG_MODE_NOT_SET', 'ourpass debug mode not set' );
define('OURPASSWC_SETTING_TEST_MODE', 'ourpasswc_test_mode' );
define('OURPASSWC_SETTING_TEST_MODE_NOT_SET', 'ourpass test mode not set' );
define('OURPASSWC_SETTING_TEST_PUBLIC_KEY', 'ourpasswc_test_public_key');
define('OURPASSWC_SETTING_TEST_SECRET_KEY', 'ourpasswc_test_secret_key');
define('OURPASSWC_SETTING_LIVE_PUBLIC_KEY', 'ourpasswc_live_public_key');
define('OURPASSWC_SETTING_LIVE_SECRET_KEY', 'ourpasswc_live_secret_key');
define('OURPASSWC_SETTING_SUB_ACCOUNT_KEY', 'ourpasswc_sub_account_key');
//APP OPTION SETTING
define('OURPASSWC_SETTING_PDP_BUTTON_HOOK', 'ourpass_pdp_button_hook');
define('OURPASSWC_DEFAULT_PDP_BUTTON_HOOK', 'woocommerce_after_add_to_cart_quantity');
define('OURPASSWC_SETTING_HIDE_BUTTON_PRODUCTS', 'ourpass_hide_button_products');
define('OURPASSWC_SETTING_CHECKOUT_REDIRECT_PAGE', 'ourpasswc_checkout_redirect_page');
// STYLES
define('OURPASSWC_SETTING_BUTTON_SIZE', 'ourpasswc_button_size');
define('OURPASSWC_DEFAULT_BUTTON_SIZE', 'default');
define('OURPASSWC_SETTING_LOAD_BUTTON_STYLES', 'ourpasswc_load_button_styles');
define('OURPASSWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET', 'ourpass load button styles not set');
define('OURPASSWC_SETTING_PDP_BUTTON_STYLES', 'ourpass_pdp_button_styles');
define('OURPASSWC_SETTING_CART_BUTTON_STYLES', 'ourpass_cart_button_styles');
define('OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES', 'ourpass_mini_cart_button_styles');
define('OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES', 'ourpass_checkout_button_styles');
// OTHER SETTINGS
define('OURPASSWC_ONBOARDING_URL', 'https://merchant.ourpass.co');
define('OURPASSWC_SUPPORTED_CURRENCY', 'NGN');
define('OURPASSWC_PRODUCTION_LIVE_BASE_URL', 'https://beta-api.ourpass.co');
define('OURPASSWC_PRODUCTION_SANDBOX_BASE_URL', 'https://api-pass-sandbox.ourpass.co');
define('OURPASSWC_CDN_URL', 'https://cdn.jsdelivr.net/gh/Cheetah-Speed-Technology/core-cdn@1.0.5/cdn.js');

define('OURPASSWC_ORDER_REFERENCE_META_KEY', '_ourpasswc_checkout_reference');
define('OURPASSWC_ORDER_PREFIX', 'OURPASS_ORDER_');
// Define the API route base path.
define('OURPASSWC_ROUTES_BASE', 'wc/ourpass/v1');

define(
	'OURPASSWC_SETTING_PDP_BUTTON_STYLES_DEFAULT',
	<<<CSS
.ourpass-pdp-wrapper {
  padding: 21px 0 20px 0;
  margin: 20px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.ourpass-pdp-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

.woocommerce_after_add_to_cart_button .ourpass-pdp-or {
  top: -22px;
}

@media only screen and (max-width: 767px) {
  .ourpass-pdp-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .ourpass-pdp-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);

define(
	'OURPASSWC_SETTING_CART_BUTTON_STYLES_DEFAULT',
	<<<CSS
.ourpass-cart-wrapper {
  padding: 21px 0 20px 0;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.ourpass-cart-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

@media only screen and (max-width: 767px) {
  .ourpass-cart-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .ourpass-cart-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);

define(
	'OURPASSWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT',
	<<<CSS
.ourpass-mini-cart-wrapper {
  height: 68px;
  clear: both;
  border-bottom: 1px solid #dfdfdf;
  padding-bottom: 0px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.ourpass-mini-cart-or {
  position: relative;
  background: inherit;
  width: 40px;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #dfdfdf;
}
CSS
);

define(
	'OURPASSWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT',
	<<<CSS
.ourpass-checkout-wrapper {
  padding: 21px 0 20px 0;
  margin-bottom: 20px;
}

.ourpass-checkout-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

@media only screen and (max-width: 767px) {
  .ourpass-checkout-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .ourpass-checkout-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);
