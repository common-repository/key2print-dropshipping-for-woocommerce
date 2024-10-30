<div class="wrap">
    <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#k2p_product_demo_enabled').on('change', function () {
                    updateFields();
                });
                jQuery('#k2p_product_api_intance').on('change', function () {
                    updateBackendLink();
                });
                updateFields();
                updateBackendLink();

                function updateFields() {
                    if (jQuery('#k2p_product_demo_enabled').is(':checked')) {
                        jQuery('#k2p_product_api_key').attr('disabled', 'disabled');
                        jQuery('#k2p_product_api_secret').attr('disabled', 'disabled');
                    } else {
                        jQuery('#k2p_product_api_key').removeAttr('disabled');
                        jQuery('#k2p_product_api_secret').removeAttr('disabled');
                    }
                }

                function updateBackendLink() {
                    var instance_code = jQuery('#k2p_product_api_intance').val();

                    jQuery('.backend_link').hide();
                    jQuery('#backend_link_' + instance_code).show();
                }
            });
    </script>
    <script>
            (function () {
                var script = document.createElement('script');
                script.src = "https://paperform.co/__embed";
                document.body.appendChild(script);
            })()
    </script>


    <form method="post" name="k2p_products_api_settings" action="options.php">
        <div class="ib-row k2p_logo">
            <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../../images/k2p_dropshipping.png'; ?>"/>
        </div>
        <div class="ib-row" style="margin:60px 0;">
            <div class="ib-70 ibs-100">
                <div class="k2p_leftside">
                    <div class="ib-row k2p_card">
                        <div class="ib-100">
                            <span><?php echo __('My account', 'k2p-products') ?></span>
                            <div class="k2p_h1">
                                <?php echo __('Welcome to KEY2PRINT Dropshipping plugin for WooCommerce!', 'k2p-products') ?>
                            </div>
                            <div>
                                <?php echo __('A WooCommerce plugin for commercial printing that allows you to sell high quality printed products for variety of businesses.', 'k2p-products') ?>
                            </div>
                            <hr>
                            <div class="k2p_h1"><?php echo __('Your admin panel', 'k2p-products') ?></div>
                            <div>
                                <?php echo __('Here you will find link to SaxoPrint backend page, where you can forward your clients’ order and his files (artwork) to the print house, as well as look up all of the forwarded files', 'k2p-products') ?>
                            </div>
                            <div>
                                <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../../images/saxoprint_logo.png'; ?>"/><br><br>
                                <?php foreach ($backend_urls as $instance_code => $backend_url): ?>
                                        <a class="backend_link k2p_button k2p_button_orange" id="backend_link_<?php echo $instance_code ?>" href="<?php echo $backend_url ?>" target="_blank" style="display:none;" ><?php echo __('Login to Admin Panel', 'k2p-products') ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                    <br> <br><br> <br>
                    <div class="ib-row k2p_card">
                        <div class="ib-100">
                            <span><?php echo __('API configuration', 'k2p-products') ?></span>
                            <div class="ib-row">
                                <div class="k2p_h1">
                                    <?php echo __('Demo mode', 'k2p-products') ?>
                                </div>
                                <div class="k2p_product_demo_enabled_field">
                                    <?php settings_fields('k2p_products_settings'); ?>
                                    <?php
                                    woocommerce_wp_checkbox(array(
                                        'id' => 'k2p_product_demo_enabled',
                                        'name' => 'k2p_products_settings[demo_enabled]',
                                        'label' => __('Demo mode', 'k2p-products'),
                                        'value' => $set_options['demo_enabled'],
                                        'cbvalue' => '1'
                                    ));
                                    ?>
                                </div>
                                <div>
                                    <?php echo __('If the DEMO checkbox is checked, it means you are in a demo mode. You can configure all of the products there and see prices specific to each country. However, in demo mode you cannot send your order for production. Therefore, in order to switch to a production mode, click on Demo checkbox after you finish the configuration and enter your API key and API secret obtained from our support team.', 'k2p-products') ?>
                                </div>
                            </div>
                            <div class="ib-row k2p_greybox">
                                <div class="ib-33 ibs-100">
                                    <div class="k2p_input_group">
                                        <?php
                                        woocommerce_wp_text_input(
                                                array(
                                                    'id' => 'k2p_product_api_key',
                                                    'name' => 'k2p_products_settings[api_key]',
                                                    'label' => __('API key', 'k2p-products'),
                                                    'placeholder' => __('Enter your API key', 'k2p-products'),
                                                    'value' => isset($set_options['api_key']) ? $set_options['api_key'] : null,
                                                    'type' => 'text'
                                                )
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="ib-33 ibs-100">
                                    <div class="k2p_input_group">
                                        <?php
                                        woocommerce_wp_text_input(
                                                array(
                                                    'id' => 'k2p_product_api_secret',
                                                    'name' => 'k2p_products_settings[api_secret]',
                                                    'label' => __('API secret', 'k2p-products'),
                                                    'placeholder' => __('Enter your API secret', 'k2p-products'),
                                                    'value' => isset($set_options['api_secret']) ? $set_options['api_secret'] : null,
                                                    'type' => 'text'
                                                )
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="ib-33 ibs-100" style="text-align: right;">
                                    <input class="k2p_button k2p_button_blue" type="submit" name="submit" id="submit_2" value="<?php echo __('Save all changes', 'k2p-products') ?>">
                                </div>
                            </div>
                            <hr>
                            <div class="k2p_h1">
                                <?php echo __('System instance', 'k2p-products') ?>
                            </div>
                            <div>
                                <?php echo __('Using our plugin you can sell your products to only one country. Before configuring the plugin, please select the appropriate country in which you will be selling, otherwise you will have to re-assign each product.  Prices of products are sent in the main currency of each country: Eurozone - EURO, United Kingdom - GBP Poland - PLN Switzerland - CHF', 'k2p-products') ?>
                            </div>
                            <div class="ib-row k2p_greybox">
                                <div class="ib-33 ibs-100 k2p_input_group">
                                    <?php
                                    woocommerce_wp_select(array(
                                        'id' => 'k2p_product_api_intance',
                                        'name' => 'k2p_products_settings[api_instance]',
                                        'label' => __('Select system instance', 'k2p-products'),
                                        'value' => isset($set_options['api_instance']) ? $set_options['api_instance'] : null,
                                        'options' => $api_instances,
                                    ));
                                    ?>
                                </div>
                                <div class="ib-33 ibs-100"></div>
                                <div class="ib-33 ibs-100" style="text-align: right;">
                                    <input class="k2p_button k2p_button_blue" type="submit" name="submit" id="submit" value="<?php echo __('Save all changes', 'k2p-products') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ib-30 ibs-100 k2p_technicaldocumentation">
                <div class="k2p_card">
                    <div class="k2p_h1"><?php echo __('Technical documentation', 'k2p-products') ?></div>
                    <div>
                        <?php echo __('To learn more about the process of plugin configuration check out the Installation guide here', 'k2p-products') ?>
                    </div>
                    <div><br>
                        <a class="k2p_button k2p_button_blue" href="<?php echo $installation_guide_link ?>" target="_blank"><?php echo __('Installation guide page', 'k2p-products') ?></a>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
