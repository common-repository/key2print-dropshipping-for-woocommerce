<?php

class K2P_Products_Admin {

        /**
         *
         * @var string
         */
        private $plugin_name;

        /**
         *
         * @var string
         */
        private $version;

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        /**
         *
         * @var K2P_Products_Api_Resolver
         */
        private $api_resolver;

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

        public function set_api_resolver(K2P_Products_Api_Resolver $api_resolver) {
                $this->api_resolver = $api_resolver;
        }

        public function set_input_processor(K2P_Products_Input_Processor $input_processor) {
                $this->input_processor = $input_processor;
        }

        public function enqueue_styles() {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/k2p-products-admin.css', array(), $this->version, 'all');
        }

        public function enqueue_scripts() {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/k2p-products-admin.js', array(
                    'jquery'), $this->version, false);
        }

        public function register_k2p_product_class($classname, $product_type) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/product/class-k2p-products-product.php';

                if ($product_type == 'k2p_product') {
                        $classname = 'K2P_Products_Product';
                }
                return $classname;
        }

        public function add_k2p_product_type($types) {
                $types['k2p_product'] = __('K2P Product', 'k2p-products');

                return $types;
        }

        public function add_k2p_product_tab($tabs) {

                $tabs['k2p_product'] = array(
                    'label' => __('K2P Product', 'k2p-products'),
                    'target' => 'k2p_product_options',
                    'class' => 'show_if_k2p_product',
                );
                return $tabs;
        }

        public function add_k2p_product_panel() {
                $product_id = get_the_ID();
                try {

                        $product_api_id = get_post_meta($product_id, 'k2p_product_api_id', true);
                        $product_margin = get_post_meta($product_id, 'k2p_product_margin', true);
                        $product_list = $this->get_k2p_remote_product_list();

                        if (count($product_list) == 0) {
                                $this->render_template('/k2p-product/tabs/k2p-product-panel-error.php');
                                return;
                        }

                        $args = array(
                            'options' => $product_list,
                            'productApiId' => $product_api_id,
                            'productMargin' => $product_margin
                        );

                        $this->render_template('/k2p-product/tabs/k2p-product-panel.php', $args);
                } catch (K2P_Products_Api_Client_Exception $ex) {
                        $this->render_template('/k2p-product/tabs/k2p-product-panel-error.php');
                }
        }

        public function save_k2p_product_panel($post_id) {
                if (!empty($_POST['k2p_product_api_id'])) {
                        $k2p_product_api_id = $this->input_processor->process_product_api_id($_POST['k2p_product_api_id']);
                        $k2p_product_margin = $this->input_processor->process_margin($_POST['k2p_product_margin']);

                        update_post_meta($post_id, 'k2p_product_api_id', esc_attr($k2p_product_api_id));
                        update_post_meta($post_id, 'k2p_product_margin', esc_attr($k2p_product_margin));
                }
        }

        public function add_k2p_products_admin_menu() {
                add_menu_page(
                        __('K2P products API settings', 'k2p-products'),
                        __('Key2Print API', 'k2p-products'),
                        'manage_woocommerce',
                        $this->plugin_name,
                        array($this, 'display_plugin_setup_page'),
                        null,
                        '55.6');
        }

        public function display_plugin_setup_page() {

                $set_options = get_option('k2p_products_settings');

                $api_instances = array();
                $backend_urls = array();

                foreach ($this->api_resolver->get_available_instances() as $instance_code) {
                        $api_instances[$instance_code] = $this->api_resolver->get_api_name($instance_code);
                        $backend_urls[$instance_code] = $this->api_resolver->get_backend_url($instance_code);
                }

                if (!isset($set_options['demo_enabled'])) {
                        $set_options['demo_enabled'] = 1;
                }
                if (!isset($set_options['api_instance'])) {
                        $set_options['api_instance'] = 'ch';
                }

                $this->render_template('k2p-product/configuration/k2p-product-configuration-panel.php', array(
                    'api_instances' => $api_instances,
                    'backend_urls' => $backend_urls,
                    'set_options' => $set_options,
                    'installation_guide_link' => 'http://dropshipping.key2print.com/technical-documentation/',
                    'contact_form_button' => '<button data-paperform-id=“8fmrcebq” data-popup-button=“1">Click me to show the form!</button>'
                ));
        }

        public function validate_k2p_settings($input) {
                $already_set_options = get_option('k2p_products_settings');

                $option_keys = array(
                    'api_instance',
                    'api_key',
                    'api_secret',
                );

                $valid = array();
                foreach ($option_keys as $option_key) {
                        if (isset($input[$option_key])) {
                                $valid[$option_key] = esc_attr(sanitize_text_field($input[$option_key]));
                                continue;
                        }
                        if (is_array($already_set_options) && isset($already_set_options[$option_key])) {
                                $valid[$option_key] = $already_set_options[$option_key];
                                continue;
                        }

                        $valid[$option_key] = '';
                }

                $valid['demo_enabled'] = isset($input['demo_enabled']) ? 1 : 0;

                return $valid;
        }

        public function update_k2p_products_setting_page() {
                register_setting('k2p_products_settings', 'k2p_products_settings', array($this, 'validate_k2p_settings'));
        }

        public function add_send_order_to_k2p_action($actions) {
                $actions['send_to_k2p'] = __('Send to K2P backend', 'k2p-products');
                $actions['update_from_k2p'] = __('Update status K2P backend', 'k2p-products');

                return $actions;
        }

        private function get_k2p_remote_product_list() {
                $products = $this->api_connector->get_products();

                $options = array();
                foreach ($products as $product) {
                        $options[$product['id']] = $product['name'];
                }

                return $options;
        }

        private function render_template($path, $args = array()) {
                wc_get_template($path,
                        $args,
                        '',
                        trailingslashit(plugin_dir_path(__FILE__) . '/partials'));
        }

}
