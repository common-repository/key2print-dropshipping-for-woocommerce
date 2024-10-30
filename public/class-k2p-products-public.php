<?php

class K2P_Products_Public {

        private $plugin_name;
        private $version;

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        /**
         *
         * @var K2P_Products_Price_Calculator
         */
        private $price_calculator;

        /**
         *
         * @var K2P_Products_Upload_Helper
         */
        private $upload_helper;

        /**
         *
         * @var K2P_Products_Input_Processor
         */
        private $input_processor;

        public function __construct($plugin_name, $version) {

                $this->plugin_name = $plugin_name;
                $this->version = $version;
        }

        public function set_api_connector(K2P_Products_Api_Connector $api_connector) {
                $this->api_connector = $api_connector;
        }

        public function set_price_calculator(K2P_Products_Price_Calculator $price_calculator) {
                $this->price_calculator = $price_calculator;
        }

        public function set_upload_helper(K2P_Products_Upload_Helper $upload_helper) {
                $this->upload_helper = $upload_helper;
        }

        public function set_input_processor(K2P_Products_Input_Processor $input_processor) {
                $this->input_processor = $input_processor;
        }

        public function enqueue_styles() {

                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/k2p-products-public.css', array(), $this->version, 'all');
        }

        public function enqueue_scripts() {

        }

        public function render_k2p_product_template() {

                global $product;

                if ($product->get_type() != 'k2p_product') {
                        return;
                }

                $productApiId = get_post_meta($product->get_id(), 'k2p_product_api_id', true);

                if (!$productApiId) {
                        $this->render_template('/k2p-product/add-to-cart/k2p-product-template-error.php');
                        return;
                }

                $apiProduct = $this->api_connector->get_product($productApiId);

                if (!$apiProduct) {
                        $this->render_template('/k2p-product/add-to-cart/k2p-product-template-error.php');
                        return;
                }

                wp_enqueue_script('k2p-product-config', plugin_dir_url(__FILE__) . 'js/k2p-product/add-to-cart/k2p-product-config.js', array('jquery'), $this->version, false);
                wp_localize_script('k2p-product-config', 'ajax_object',
                        array('ajax_url' => admin_url('admin-ajax.php')));
                wp_localize_script('k2p-product-config', 'messages',
                        array(
                            'config_not_available' => __('This configuration is not valid.', 'k2p-products'),
                            'config_out_of_stock' => __('This configuration is out of stock.', 'k2p-products')
                ));

                $this->render_template('/k2p-product/add-to-cart/k2p-product-template.php', array(
                    'product' => $product,
                    'product_api_id' => $productApiId,
                    'options' => $apiProduct['options'],
                    'hidden_options' => array(
                        6 //Product group option id
                    ),
                ));
        }

        public function render_order_file_upload_template() {
                $cartItems = $this->upload_helper->get_cart_items_for_active_cart();
                wp_enqueue_script('k2p-product-jquery-ui-widget', plugin_dir_url(__FILE__) . 'js/k2p-product/order-file-upload/jquery.ui.widget.js', array('jquery'), $this->version, false);
                wp_enqueue_script('k2p-product-jquery-fileupload', plugin_dir_url(__FILE__) . 'js/k2p-product/order-file-upload/jquery.fileupload.js', array('jquery'), $this->version, false);
                wp_enqueue_script('k2p-product-files-upload', plugin_dir_url(__FILE__) . 'js/k2p-product/order-file-upload/k2p-product-files-upload.js', array('jquery'), $this->version, false);
                wp_localize_script('k2p-product-files-upload', 'ajax_object',
                        array(
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'chunk_upload_url' => $this->api_connector->get_chunk_upload_url(),
                ));

                $this->render_template('/k2p-product/order-file-upload/k2p-product-files-template.php', array(
                    'cartItems' => $cartItems,
                ));
        }

        public function get_k2p_pricing_ajax_action() {
                $product_id = $this->input_processor->process_product_id($_POST['product_id']);
                $product_api_id = $this->input_processor->process_product_api_id($_POST['product_api_id']);
                $current_setup = $this->input_processor->process_product_setup_array($_POST['current_setup']);

                $k2p_product_setup = array(
                    'product-api-id' => $product_api_id,
                    'product-setup' => $current_setup,
                );

                $result = $this->price_calculator->calculate_pricing_grid($product_id, $k2p_product_setup);


                if (isset($result['success']) && $result['success'] == false) {
                        echo json_encode($result);
                        wp_die();
                }

                echo json_encode(array(
                    'success' => true,
                    'data' => $result,
                ));

                wp_die();
        }

        public function get_k2p_available_options_ajax_action() {
                $product_api_id = $this->input_processor->process_product_api_id($_POST['product_api_id']);
                $current_setup = isset($_POST['current_setup']) ? $this->input_processor->process_product_setup_array($_POST['current_setup']) : null;

                $result = $this->api_connector->get_product_available_options($product_api_id, $current_setup);

                if (isset($result['success']) && $result['success'] == false) {
                        echo json_encode($result);
                        wp_die();
                }

                echo json_encode(array(
                    'success' => true,
                    'data' => $result,
                ));

                wp_die();
        }

        public function start_k2p_product_upload_ajax_action() {
                $cart_item_key = $this->input_processor->process_cart_item_key($_POST['cart_item_key']);
                $file_component_id = $this->input_processor->process_file_component_id($_POST['file_component_id']);
                $filename = $this->input_processor->process_filename($_POST['filename']);

                $result = $this->upload_helper->create_upload_for_file_component($cart_item_key, $file_component_id, $filename);

                echo json_encode(array(
                    'success' => true,
                    'verification_result' => $result,
                    'data' => $this->upload_helper->get_file_component_data($cart_item_key, $file_component_id),
                ));

                wp_die();
        }

        public function complete_k2p_product_upload_ajax_action() {
                $cart_item_key = $this->input_processor->process_cart_item_key($_POST['cart_item_key']);
                $file_component_id = $this->input_processor->process_file_component_id($_POST['file_component_id']);


                $this->upload_helper->complete_upload_for_file_component($cart_item_key, $file_component_id);

                echo json_encode(array(
                    'success' => true,
                    'data' => $this->upload_helper->get_file_component_data($cart_item_key, $file_component_id),
                ));

                wp_die();
        }

        public function remove_k2p_product_upload_ajax_action() {
                $cart_item_key = $this->input_processor->process_cart_item_key($_POST['cart_item_key']);
                $file_component_id = $this->input_processor->process_file_component_id($_POST['file_component_id']);


                $this->upload_helper->remove_upload_for_file_component($cart_item_key, $file_component_id);

                echo json_encode(array(
                    'success' => true,
                ));

                wp_die();
        }

        public function store_k2p_product_cart_item_data($cart_item_data, $product_id) {
                $factory = new WC_Product_Factory();
                $product = $factory->get_product($product_id);

                if ($product->get_type() != 'k2p_product') {
                        return;
                }

                $product_api_id = $this->input_processor->process_product_api_id($_POST['product_api_id']);
                $product_run_size = $this->input_processor->process_runsize($_POST['product-run-size']);
                $product_delivery_time = $this->input_processor->process_delivery_time($_POST['product-delivery-time']);
                $product_setup = $this->input_processor->process_product_setup_json($_POST['product_setup']);

                $cart_item_data['k2p_product_setup'] = array(
                    'product-api-id' => $product_api_id,
                    'product-run-size' => $product_run_size,
                    'product-delivery-time' => $product_delivery_time,
                    'product-setup' => $product_setup,
                );

                return $cart_item_data;
        }

        private function render_template($path, $args = array()) {
                wc_get_template($path,
                        $args,
                        '',
                        trailingslashit(plugin_dir_path(__FILE__) . '/partials'));
        }

}
