jQuery(document).ready(function () {

    jQuery('.product-option-selector').on('change', function () {
        var currentSetup = {};
        jQuery('.product-option-selector').each(function () {
            currentSetup[jQuery(this).data('option-id')] = jQuery(this).val();
        });
        loadOptions(currentSetup);
    });
    jQuery('#product-run-size-selector').on('change', function () {
        loadDeliveryTime();
    });
    jQuery('#product-delivery-time-selector').on('change', function () {
        loadPrice();
    });
    loadOptions();
});

var pricingTable = {};

function loadOptions(currentSetup) {
    productMask();
    var data = {
        'action': 'get_k2p_available_options',
        'product_id': jQuery('input[name = "product_id"]').val(),
        'product_api_id': jQuery('input[name = "product_api_id"]').val(),
    };

    if (currentSetup !== undefined) {
        data['current_setup'] = currentSetup;
    }

    jQuery.post(ajax_object.ajax_url, data, function (response) {
        var response = JSON.parse(response);

        if (response.success == true) {

            jQuery('.product-option-selector').each(function () {
                var optionId = jQuery(this).data('option-id');

                var validOptionValues = response.data.valid_configuration[optionId];

                jQuery(this).find('option').each(function () {
                    if (jQuery.inArray(this.value, validOptionValues, 0) >= 0) {
                        jQuery(this).removeAttr('disabled');
                    } else {
                        jQuery(this).attr('disabled', 'disabled');
                    }
                });

                jQuery(this).val(response.data.valid_setup[optionId]);
            });
            loadSetup(response.data.valid_setup);
        }
    })
            .fail(function () {
                productUnmask();
            });

}

function loadSetup(currentSetup) {
    var data = {
        'action': 'get_k2p_pricing',
        'product_id': jQuery('input[name = "product_id"]').val(),
        'product_api_id': jQuery('input[name = "product_api_id"]').val(),
        'current_setup': currentSetup
    };


    jQuery.post(ajax_object.ajax_url, data, function (response) {
        var response = JSON.parse(response);

        if (response.success == true) {
            pricingTable = response.data;
            jQuery('input[name="product_setup"]').val(JSON.stringify(currentSetup));
            loadRunSize();
        } else {
            jQuery("#k2p-price").html(messages.config_not_available);
            jQuery("#k2p-delivery-days").html(messages.config_not_available);
            productUnmask();
            jQuery('#add-to-cart-button').attr('disabled', 'disabled');
        }
    })
            .fail(function () {
                productUnmask();
            })
            ;

}

function loadRunSize() {
    var runSizeOptions = [];

    jQuery.each(pricingTable, function (key, item) {
        runSizeOptions.push('<option value="' + key + '">' + key + '</option>');
    });

    jQuery('#product-run-size-selector').html(runSizeOptions.join(''));

    loadDeliveryTime();
}

function loadDeliveryTime() {
    var runSizeValue = jQuery('#product-run-size-selector').val();
    var deliveryTimeOptions = [];

    jQuery.each(pricingTable[runSizeValue], function (key, item) {
        deliveryTimeOptions.push('<option value="' + key + '">' + key + '</option>');
    });

    jQuery('#product-delivery-time-selector').html(deliveryTimeOptions.join(''));

    loadPrice();
}

function loadPrice() {
    var runSizeValue = jQuery('#product-run-size-selector').val();
    var deliveryTimeValue = jQuery('#product-delivery-time-selector').val();

    var price = pricingTable[runSizeValue][deliveryTimeValue]['formatted_price'];
    var days = pricingTable[runSizeValue][deliveryTimeValue]['days'];
    var isOutOfStock = pricingTable[runSizeValue][deliveryTimeValue]['out_of_stock'];

    jQuery("#k2p-price").html(price);
    jQuery("#k2p-delivery-days").html(days);
    productUnmask();
}

function productMask() {
    jQuery('.k2p_product_cart').find('select').each(function () {
        jQuery(this).attr('disabled', 'disabled');
    });
    jQuery('#add-to-cart-button').attr('disabled', 'disabled');
    jQuery('#add-to-cart-button').hide();
    jQuery('.k2p_loading').show();
}

function productUnmask() {
    jQuery('.k2p_product_cart').find('select').each(function () {
        jQuery(this).removeAttr('disabled');
    });
    jQuery('#add-to-cart-button').removeAttr('disabled');
    jQuery('#add-to-cart-button').show();
    jQuery('.k2p_loading').hide();

}