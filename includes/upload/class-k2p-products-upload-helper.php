<?php

class K2P_Products_Upload_Helper {

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        public function __construct(K2P_Products_Api_Connector $api_connector) {
                $this->api_connector = $api_connector;
        }

        public function get_cart_items_for_active_cart() {
                $cart = WC()->cart->get_cart();

                $cartItems = array();

                foreach ($cart as $itemData) {
                        if ($itemData['data']->get_type() != 'k2p_product') {
                                continue;
                        }

                        /** @var K2P_Products_Product $product */
                        $product = $itemData['data'];

                        $cartItem = array(
                            'key' => $itemData['key'],
                            'name' => $product->get_name(),
                            'quantity' => $itemData['quantity'],
                            'options' => $itemData['k2p_product_setup']['options'],
                            'run_size' => $itemData['k2p_product_setup']['product-run-size'],
                            'delivery_time' => $itemData['k2p_product_setup']['product-delivery-time'],
                            'file_components' => $itemData['k2p_product_setup']['file_components'],
                        );


                        $cartItems[] = $cartItem;
                }



                return $cartItems;
        }

        public function create_upload_for_file_component($cart_item_key, $file_component_id, $filename) {
                $cart = WC()->cart->get_cart();

                $cart_item = $cart[$cart_item_key];

                foreach ($cart_item['k2p_product_setup']['file_components'] as $file_component_index => $file_component) {
                        if ($file_component['id'] == $file_component_id) {

                                $result = $this->api_connector->create_upload($filename);

                                $file_component['api_upload_id'] = $result['data']['id'];
                                $file_component['filename'] = $filename;

                                $cart_item['k2p_product_setup']['file_components'][$file_component_index] = $file_component;
                                WC()->cart->cart_contents[$cart_item_key] = $cart_item;
                                WC()->cart->set_session();


                                return;
                        }
                }
        }

        public function complete_upload_for_file_component($cart_item_key, $file_component_id) {
                $cart = WC()->cart->get_cart();

                $cart_item = $cart[$cart_item_key];

                foreach ($cart_item['k2p_product_setup']['file_components'] as $file_component_index => $file_component) {
                        if ($file_component['id'] == $file_component_id) {

                                $result = $this->api_connector->complete_upload($file_component['api_upload_id']);

                                $file_component['url'] = $result['data']['url'];

                                $cart_item['k2p_product_setup']['file_components'][$file_component_index] = $file_component;
                                WC()->cart->cart_contents[$cart_item_key] = $cart_item;
                                WC()->cart->set_session();

                                return;
                        }
                }
        }

        public function remove_upload_for_file_component($cart_item_key, $file_component_id) {
                $cart = WC()->cart->get_cart();

                $cart_item = $cart[$cart_item_key];

                foreach ($cart_item['k2p_product_setup']['file_components'] as $file_component_index => $file_component) {
                        if ($file_component['id'] == $file_component_id) {

                                $file_component['api_upload_id'] = null;
                                $file_component['filename'] = null;
                                $file_component['url'] = null;

                                $cart_item['k2p_product_setup']['file_components'][$file_component_index] = $file_component;
                                WC()->cart->cart_contents[$cart_item_key] = $cart_item;
                                WC()->cart->set_session();

                                return;
                        }
                }
        }

        public function get_file_component_data($cart_item_key, $file_component_id) {
                $cart = WC()->cart->get_cart();
                $cart_item = $cart[$cart_item_key];

                foreach ($cart_item['k2p_product_setup']['file_components'] as $file_component) {
                        if ($file_component['id'] == $file_component_id) {
                                return $file_component;
                        }
                }
                return null;
        }

}
