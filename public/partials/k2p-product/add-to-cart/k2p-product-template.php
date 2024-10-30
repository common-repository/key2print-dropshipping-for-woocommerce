<?php
/**
 * Simple custom product
 */
if (!defined('ABSPATH')) {
        exit;
}
global $product;
do_action('gift_card_before_add_to_cart_form');
?>

<form class="k2p_product_cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
    <table cellspacing="0">
        <tbody>
            <?php foreach ($options as $option): ?>
                    <tr <?php echo in_array($option['id'], $hidden_options) ? 'style="display: none;"' : '' ?>>
                        <td>
                            <?php echo $option['name'] ?>
                        </td>
                        <td>
                            <select class="product-option-selector" name="option_<?php echo $option['id'] ?>" id="option_<?php echo $option['id'] ?>" data-option-id="<?php echo $option['id'] ?>" >
                                <?php foreach ($option['values'] as $value): ?>
                                        <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
            <?php endforeach ?>
            <tr>
                <td>
                    <?php echo __('Runsize', 'k2p-products') ?>
                </td>
                <td>
                    <select class="product-run-size-selector" id="product-run-size-selector" name="product-run-size"  >
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Delivery Time', 'k2p-products') ?>
                </td>
                <td>
                    <select class="product-delivery-time-selector" id="product-delivery-time-selector" name="product-delivery-time"  >
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="price"><?php echo __("Net price", 'k2p-products'); ?></label>
                </td>
                <td>
                    <div id="k2p-price">

                    </div>
                </td>
            </tr>
            <tr>
                <td >
                    <label for="delivery_days"><?php echo __("Delivery days", 'k2p-products'); ?></label>
                </td>
                <td>
                    <div id="k2p-delivery-days">

                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="k2p_loading" style="text-align: center;">
        <svg xmlns="http://www.w3.org/2000/svg" height="80px" viewBox="0 0 100 100"><path class="k2p_loading_color" d="M47.47092389 27.13947127C34.84793629 28.53596112 25.74298141 39.9060885 27.13947127 52.5290761S39.9060885 74.25701859 52.5290761 72.86052873m-.42884334-3.87635052c-10.43632834 1.15457822-19.91883677-6.3482235-21.08441098-16.88394544S37.46343889 32.1704 47.89976723 31.01582179" transform="rotate(251.638 50 50)"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path></svg>
    </div>
    <button type="submit" disabled="disabled" id="add-to-cart-button" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
    <input type="hidden" name="product_id" value="<?php echo $product->get_id() ?>" />
    <input type="hidden" name="product_api_id" value="<?php echo $product_api_id ?>" />
    <input type="hidden" name="product_setup" value="" />
</form>

<?php do_action('gift_card_after_add_to_cart_form'); ?>