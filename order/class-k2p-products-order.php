<?php

class K2P_Products_Order {

        private $plugin_name;
        private $version;

        /**
         *
         * @var K2P_Products_Price_Calculator
         */
        private $price_calculator;

        /**
         *
         * @var K2P_Products_Option_Resolver
         */
        private $option_resolver;

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        function __construct($plugin_name, $version) {
                $this->plugin_name = $plugin_name;
                $this->version = $version;
        }

        public function set_price_calculator(K2P_Products_Price_Calculator $price_calculator) {
                $this->price_calculator = $price_calculator;
        }

        public function set_option_resolver(K2P_Products_Option_Resolver $option_resolver) {
                $this->option_resolver = $option_resolver;
        }

        public function set_api_connector(K2P_Products_Api_Connector $api_connector) {
                $this->api_connector = $api_connector;
        }

        public function is_k2p_product_purchasable($previousResult, $product) {
                if ($product->get_type() !== 'k2p_product') {
                        return $previousResult;
                }

                return true;
        }

        public function fetch_k2p_product_api_data($cart_item_data, $product_id) {
                if (empty($cart_item_data['k2p_product_setup'])) {
                        return $cart_item_data;
                }

                if (empty($cart_item_data['k2p_product_setup']['price'])) {
                        $price_tier = $this->price_calculator->get_price_tier($product_id, $cart_item_data['k2p_product_setup']);
                        $cart_item_data['k2p_product_setup']['price'] = $price_tier['price'];
                        $cart_item_data['k2p_product_setup']['product-delivery-days'] = $price_tier['days'];
                }

                if (empty($cart_item_data['k2p_product_setup']['options']) || empty($cart_item_data['k2p_product_setup']['file_components'])) {
                        $resolvedData = $this->option_resolver->resolve($cart_item_data['k2p_product_setup']);
                        $cart_item_data['k2p_product_setup']['options'] = $resolvedData['options'];
                        $cart_item_data['k2p_product_setup']['file_components'] = $resolvedData['file_components'];
                }

                return $cart_item_data;
        }

        public function calculate_k2p_product_prices($cart) {

                // This is necessary for WC 3.0+
                if (is_admin() && !defined('DOING_AJAX')) {
                        return;
                }

                // Avoiding hook repetition (when using price calculations for example)
                if (did_action('woocommerce_before_calculate_totals') >= 2) {
                        return;
                }

                foreach ($cart->get_cart() as $item) {
                        $product = $item['data'];
                        if ($product->get_type() != 'k2p_product') {
                                continue;
                        }

                        $k2p_product_setup = $item['k2p_product_setup'];
                        $item['data']->set_price(floatval($k2p_product_setup['price']));
                }
        }

        public function display_k2p_product_cart_item_data($item_data, $cart_item) {
                if (empty($cart_item['k2p_product_setup'])) {
                        return $item_data;
                }

                foreach ($cart_item['k2p_product_setup']['options'] as $option) {
                        $item_data[] = array(
                            'key' => $option['name'],
                            'value' => wc_clean($option['value']['name']),
                            'display' => '',
                        );
                }
                $item_data[] = array(
                    'key' => __('Run size', 'k2p-products'),
                    'value' => $cart_item['k2p_product_setup']['product-run-size'],
                    'display' => '',
                );
                $item_data[] = array(
                    'key' => __('Delivery time', 'k2p-products'),
                    'value' => $cart_item['k2p_product_setup']['product-delivery-time'],
                    'display' => '',
                );
                $item_data[] = array(
                    'key' => __('Delivery days', 'k2p-products'),
                    'value' => $cart_item['k2p_product_setup']['product-delivery-days'],
                    'display' => '',
                );


                return $item_data;
        }

        public function create_k2p_product_order_line_item($item, $cart_item_key, $values, $order) {
                if (empty($values['k2p_product_setup'])) {
                        return;
                }

                $item->add_meta_data('k2p_product_setup', json_encode($values['k2p_product_setup']));

                foreach ($values['k2p_product_setup']['options'] as $option) {
                        $valueName = wc_clean($option['value']['name']);
                        $item->add_meta_data($option['name'], $valueName);
                }

                $item->add_meta_data(__('Run size', 'k2p-products'), $values['k2p_product_setup']['product-run-size']);
                $item->add_meta_data(__('Delivery time', 'k2p-products'), $values['k2p_product_setup']['product-delivery-time']);
                $item->add_meta_data(__('Delivery days', 'k2p-products'), $values['k2p_product_setup']['product-delivery-days']);
                $item->add_meta_data(__('Production status', 'k2p-products'), __('Not sent to backend yet.', 'k2p-products'));

                foreach ($values['k2p_product_setup']['file_components'] as $file_component) {
                        $component_key = $file_component['name'];
                        $component_link = "<a href='" . $file_component['url'] . "'>" . $file_component['filename'] . "</a>";

                        $item->add_meta_data($component_key, $component_link);
                }
        }

        public function hide_k2p_product_setup_meta_data($formatted_meta, $item) {
                if ($item instanceof WC_Order_Item_Product) {
                        if ($item->get_product()->get_type() !== 'k2p_product') {
                                return $formatted_meta;
                        }

                        foreach ($formatted_meta as $index => $meta_item) {
                                if ($meta_item->key == 'k2p_product_setup') {
                                        unset($formatted_meta[$index]);
                                }
                                if ($meta_item->key == 'k2p_order_status') {
                                        unset($formatted_meta[$index]);
                                }
                        }
                }

                return $formatted_meta;
        }

        public function prevent_checkout_without_files() {
                $cart = WC()->cart->get_cart();

                foreach ($cart as $itemData) {
                        if ($itemData['data']->get_type() != 'k2p_product') {
                                continue;
                        }

                        foreach ($itemData['k2p_product_setup']['file_components'] as $fileComponent) {
                                if (strlen($fileComponent['url']) == 0) {
                                        wc_add_notice(__('All artwork files must be uploaded before checkout.', 'k2p-products'), 'error');
                                        return;
                                }
                        }
                }
        }

        public function send_to_k2p($order_id) {
                $this->api_connector->submit_order($order_id);
        }

        public function update_from_k2p($order_id) {
                $this->api_connector->update_order_status($order_id);
        }

}
