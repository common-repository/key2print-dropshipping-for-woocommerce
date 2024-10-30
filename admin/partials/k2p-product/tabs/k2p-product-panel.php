<div id='k2p_product_options' class='panel woocommerce_options_panel'>
    <div class='options_group'>
        <p>
            <?php echo __("Originally, the shop displays standard sales prices, from which you have a discount that represents your earnings.", 'k2p-products') ?>
        </p>
        <p>
            <?php echo __("For example, let's take a product for 100 Euros. ", 'k2p-products') ?>
            <br/><?php echo __("With a 12% discount, when the standard price is 100 Euro, you’ll buy it for 88 Euro.", 'k2p-products') ?>
        </p>
        <p>
            <?php echo __("You can also modify the prices globally by using the Product Margin parameter.", 'k2p-products') ?>
            <br/><?php echo __("By setting a margin at 10% you will increase the prices by 10% from the standard prices - then you can offer the product for 110 Euros.", 'k2p-products') ?>
            <br/><?php echo __("By setting a margin of -10% you can reduce prices by 10% from standard prices and offer it for 90 Euros.", 'k2p-products') ?>
        </p>
        <p>
            <b><?php echo __("Remember that shipping is always included in the price of the product!") ?></b>
        </p>
        <?php
        woocommerce_wp_select(array(
            'id' => 'k2p_product_api_id',
            'label' => __('Select product', 'k2p-products'),
            'options' => $options,
            'value' => $productApiId
        ));
        ?>
        <?php
        woocommerce_wp_text_input(array(
            'id' => 'k2p_product_margin',
            'label' => __('Product margin [%]', 'k2p-products'),
            'productMargin' => $productMargin,
        ));
        ?>
    </div>
</div>