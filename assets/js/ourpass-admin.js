jQuery(function ($) {
    'use strict';

    /**
     * Object to handle Ourpass admin functions.
     */
    var wc_ourpass_admin = {
        /**
         * Initialize.
         */
        init: function () {

            // Toggle api key settings.
            $(document.body).on('change', '#woocommerce_ourpass_testmode', function () {
                var test_secret_key = $('#woocommerce_ourpass_test_secret_key').parents('tr').eq(0),
                    test_public_key = $('#woocommerce_ourpass_test_public_key').parents('tr').eq(0),
                    live_secret_key = $('#woocommerce_ourpass_live_secret_key').parents('tr').eq(0),
                    live_public_key = $('#woocommerce_ourpass_live_public_key').parents('tr').eq(0);

                if ($(this).is(':checked')) {
                    test_secret_key.show();
                    test_public_key.show();
                    live_secret_key.hide();
                    live_public_key.hide();
                } else {
                    test_secret_key.hide();
                    test_public_key.hide();
                    live_secret_key.show();
                    live_public_key.show();
                }
            });

            $('#woocommerce_ourpass_testmode').change();
        }
    };

    wc_ourpass_admin.init();

});
