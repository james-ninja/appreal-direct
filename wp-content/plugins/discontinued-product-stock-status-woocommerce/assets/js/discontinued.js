jQuery(document).ready(function () {
    const { __ } = wp.i18n;

    // To show/hide 'Apply Grayscale effect based on "Show in catalog".
    jQuery('#discontinued_show_in_catalog').change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("#discontinued_greyscale_effect").parents('tr').show();
        } else {
            jQuery("#discontinued_greyscale_effect").parents('tr').hide();
        }
    }).trigger('change');

    // To show/hide 'Enter the Global Message' based on "Set Custom Global Message".
    jQuery('#discontinued_enable_custom_message').change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("#discontinued_global_message").parents('tr').show();
        } else {
            jQuery("#discontinued_global_message").parents('tr').hide();
        }
    }).trigger('change');

    jQuery('select#product-type').change(function () {
        productType = jQuery(this).val();

        if ('variable' == productType || 'grouped' == productType) {
            jQuery('input#_discontinued_product').parent().show();
        } else {
            jQuery('input#_discontinued_product').parent().hide();
        }
    });
    jQuery('select#product-type').trigger('change');

    // To hide and show message box on variable product level. i.e when checked on 'Discontinued Product'.
    jQuery('input#_discontinued_product').on('change', function () {

        var is_discontinued_product = jQuery('input#_discontinued_product:checked').size();
        variElements = jQuery(this).parent().siblings();
        productType = jQuery('select#product-type').val();


        if (is_discontinued_product) {
            // if (is_discontinued_product) {

            jQuery(this).parent().siblings('p.form-field.show_specific_messsage_field').show();
            jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').show();

            let prodType = variElements.find('select#show_specific_messsage').val();

            if ('product_specific_message' == prodType) {

                jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').show();
            } else {
                jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').hide();
            }
        } else if ('simple' != productType) {

            jQuery(this).parent().siblings('p.form-field.show_specific_messsage_field').hide();
            jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').hide();

        }

    }).trigger('change');

    // To hide and show message box on variable product level. i.e when checked on 'Discontinued Product'.
    jQuery('div#discontinued_tab_container select#show_specific_messsage').on('change', function () {

        msgType = jQuery(this).val();
        productType = jQuery(this).val();

        if ('product_specific_message' == msgType) {
            jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').show();
        } else {
            jQuery(this).parent().siblings('div#wp-custom_editor_box-wrap').hide();
        }

    });

    if ('simple' == jQuery('select#product-type').val()) {
        jQuery('div#discontinued_tab_container select#show_specific_messsage').trigger('change');
    }

    // ------------------------------------------Backorder---------------------------------.

    // Get the current WooCommerce version.
    let wc_version = parseFloat( dpssw_custom_data.wc_version );

    if ( wc_version < 7.6 ) {

        // gets the backorder option value.
        let backOrderValue = jQuery('#_backorders').find(":selected").val();

        // chceking back order value and disabling checkbox.
        if ('no' !== backOrderValue) {
            jQuery("#_stock_discontinued_product").attr('disabled', true);
        } else {
            jQuery("#_stock_discontinued_product").attr('disabled', false);
        }

        // on changing backorder changing value of checkbox.
        jQuery("#_backorders").on('change', function () {

            if ('no' !== this.value) {
                jQuery("#_stock_discontinued_product").attr('disabled', true);
                jQuery("#_stock_discontinued_product").prop('checked', false);
            } else {
                jQuery("#_stock_discontinued_product").attr('disabled', false);
            }

        });

    } else {

        // gets the backorder option value.
        let backOrderValue = jQuery("input[name=_backorders]:checked").val();

        console.log( backOrderValue );
        // checking back order value and disabling checkbox.
        if ('no' !== backOrderValue) {
            jQuery("#_stock_discontinued_product").attr('disabled', true);
        } else {
            jQuery("#_stock_discontinued_product").attr('disabled', false);
        }

        // on changing backorder changing value of checkbox.
        jQuery("input[name=_backorders]").on('change', function () {

            if ('no' !== this.value) {
                jQuery("#_stock_discontinued_product").attr('disabled', true);
                jQuery("#_stock_discontinued_product").prop('checked', false);
            } else {
                jQuery("#_stock_discontinued_product").attr('disabled', false);
            }

        });

    }

    // ---------------------------------------------END-------------------------------------------------.

    // Hide rating notice on click.
    jQuery(".dpssw_hide_rate").click(function (event) {
        event.preventDefault();
        jQuery.ajax({
            method: 'POST',
            url: dpssw_custom_data.url,
            data: {
                action: 'dpssw_update',
                nonce: dpssw_custom_data.nonce,
            },
            success: (res) => {
                window.location.href = window.location.href
            }
        });
    });

});

// Reference: https://wordpress.stackexchange.com/questions/217518/woocommerce-hook-after-loading-variation-in-admin-edit-page. 
jQuery(document).on('woocommerce_variations_loaded', function (event) {
    /**
     * Check if variation manage stock is checked and then show/hide elements
     */
    jQuery('#variable_product_options').on('change', 'input.variable_manage_stock', function () {

        stockstatus = jQuery(this).closest('.woocommerce_variation').find('.variable_stock_status select').val();

        if (jQuery(this).is(':checked')) {
            jQuery(this).closest('.woocommerce_variation').find('.variation-discontinued-div').hide();
        } else if ('discontinued' == stockstatus) {
            jQuery(this).closest('.woocommerce_variation').find('.variation-discontinued-div').show();
        }

        // Parent level.
        if (jQuery('input#_manage_stock:checked').length) {
            jQuery(this).closest('.woocommerce_variation').find('.variation-discontinued-div').hide();
        }
    });

    // cheks the back order and update the discontinued checkbox.
    jQuery('#variable_product_options').on('change', 'select.short', function () {

        // gets the back order status
        backOrderStatus = jQuery(this).closest('.woocommerce_variation').find('.show_if_variation_manage_stock .select').val();

        disconCheckbox = jQuery(this).closest('.woocommerce_variation').find('.show_if_variation_manage_stock input[type="checkbox"]');

        // on back order allowed change product checkbox status.
        if ('no' !== backOrderStatus) {
            disconCheckbox.attr('disabled', true);
            disconCheckbox.prop('checked', false);
        } else {
            disconCheckbox.attr('disabled', false);
        }
    });

    /**
     * Check if message type of variation is 'Product specific' if yes then show message box else hide it.
     * This function is initially trigger when clicked on 'Variations'.
     */
    var _messageType = function (element) {

        msgType = element.val();

        if ('variations_specific_message' == msgType) {
            element.parent().siblings('.dpssw-message').show();
        } else {
            element.parent().siblings('.dpssw-message').hide();
        }

    };

    /**
     * Check if variation stock status is discontinued is selected and then show/hide elements.
     */
    jQuery('.variable_stock_status select').on('change', function () {

        var stockStatus, discontinuedDiv = null;

        stockStatus = jQuery(this).val();
        discontinuedDiv = jQuery(this).parent().siblings('.variation-discontinued-div');

        if ('discontinued' == stockStatus) {
            discontinuedDiv.show();
            _messageType(discontinuedDiv.find('select.dpssw-select'));
        } else {
            discontinuedDiv.hide();
        }

    }).trigger('change');

    /**
     * Check if message type of variation is 'Product specific' if yes then show message box else hide it.
     * This function is initially trigger when clicked on 'Variations'.
    */
    jQuery('.variation-discontinued-div select.select.dpssw-select').on('change', function () {

        msgType = jQuery(this).val();

        if ('variations_specific_message' == msgType) {
            jQuery(this).parent().siblings('.dpssw-message').show();
        } else {
            jQuery(this).parent().siblings('.dpssw-message').hide();
        }
    });

});
