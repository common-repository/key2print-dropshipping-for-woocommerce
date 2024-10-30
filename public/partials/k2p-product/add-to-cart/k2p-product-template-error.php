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

<?php echo __('This product is not properly configured. Please try again later.', 'k2p-products') ?>

<?php do_action('gift_card_after_add_to_cart_form'); ?>