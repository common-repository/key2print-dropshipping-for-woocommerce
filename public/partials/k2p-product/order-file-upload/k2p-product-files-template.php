<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
    <thead>
        <tr>
            <th colspan="2"><h1><?php echo __("File upload", 'k2p-products'); ?></h1></th>
        </tr>
        <tr>
            <th class="product-name"><?php echo __("Product", 'k2p-products'); ?></th>
            <th><?php echo __("Files", 'k2p-products'); ?></th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($cartItems as $cartItem): ?>
                <tr class="woocommerce-cart-form__cart-item cart_item">
                    <td class="product-name" data-title="Product">
                        <?php echo $cartItem['name'] ?>
                        <dl class="variation">
                            <?php foreach ($cartItem['options'] as $option): ?>
                                    <dt class="variation-Productgroup">
                                        <?php echo $option['name'] ?>:
                                    </dt>
                                    <dd class="variation-Productgroup">
                                        <p><?php echo $option['value']['name'] ?></p>
                                    </dd>
                            <?php endforeach ?>
                            <dt class="variation-Productgroup">
                                <?php echo __('Run size', 'k2p-products') ?>:
                            </dt>
                            <dd class="variation-Productgroup">
                                <p><?php echo $cartItem['run_size'] ?></p>
                            </dd>
                            <dt class="variation-Productgroup">
                                <?php echo __('Delivery time', 'k2p-products') ?>:
                            </dt>
                            <dd class="variation-Productgroup">
                                <p><?php echo $cartItem['delivery_time'] ?></p>
                            </dd>
                        </dl>
                    </td>
                    <td class="k2p_upload_table">
                        <dl class="variation">
                            <?php foreach ($cartItem['file_components'] as $fileComponent): ?>
                                    <dt class="variation-Productgroup">
                                        <?php echo $fileComponent['name'] ?>:
                                    </dt>
                                    <dd class="upload_container" id="upload_container_<?php echo $cartItem['key'] ?>_<?php echo $fileComponent['id'] ?>" data-is-uploaded="<?php echo (strlen($fileComponent['url']) > 0) ? "1" : "0" ?>">
                                        <div class="upload_container_status">
                                            <a class="file_upload_link" href="<?php echo $fileComponent['url'] ?>"><?php echo $fileComponent['filename'] ?></a>
                                            <a class="file_upload_remove" data-cart-item-key="<?php echo $cartItem['key'] ?>" data-file-component-id="<?php echo $fileComponent['id'] ?>"><?php echo __('Remove', 'k2p-products') ?></a>
                                        </div>
                                        <div class="upload_container_button">
                                            <input class="cart_item_upload" data-cart-item-key="<?php echo $cartItem['key'] ?>" data-file-component-id="<?php echo $fileComponent['id'] ?>" type="file" name="file"  multiple>
                                            <div class='percent_value'>
                                                <span class="percent_bar"></span>
                                            </div>
                                        </div>
                                    </dd>
                            <?php endforeach ?>
                        </dl>
                    </td>
                </tr>
        <?php endforeach ?>

    </tbody>
</table>