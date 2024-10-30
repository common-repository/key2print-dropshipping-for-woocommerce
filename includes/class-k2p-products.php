<?php

class K2P_Products {

        /**
         *
         * @var K2P_Products_Loader
         */
        protected $loader;

        /**
         *
         * @var K2P_Products_Service_Container
         */
        protected $container;

        /**
         *
         * @var string
         */
        protected $plugin_name;

        /**
         *
         * @var version
         */
        protected $version;

        public function __construct() {
                if (defined('K2P_PRODUCTS_VERSION')) {
                        $this->version = K2P_PRODUCTS_VERSION;
                } else {
                        $this->version = '1.0.1';
                }
                $this->plugin_name = 'K2P product plugin';

                $this->load_dependencies();
                $this->set_locale();
                $this->define_admin_hooks();
                $this->define_public_hooks();
                $this->define_order_hooks();
        }

        private function load_dependencies() {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-k2p-products-service-container.php';

                $this->container = new K2P_Products_Service_Container($this->plugin_name, $this->version);
                $this->loader = $this->container->get('loader');
        }

        private function set_locale() {

                $plugin_i18n = $this->container->get('i18n');

                $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        }

        private function define_admin_hooks() {

                $plugin_admin = $this->container->get('plugin_admin');

                $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
                $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

                //K2P product registration
                $this->loader->add_filter('woocommerce_product_class', $plugin_admin, 'register_k2p_product_class', 10, 2);

                //K2P product edit
                $this->loader->add_filter('product_type_selector', $plugin_admin, 'add_k2p_product_type');
                $this->loader->add_action('woocommerce_product_data_tabs', $plugin_admin, 'add_k2p_product_tab');
                $this->loader->add_action('woocommerce_product_data_panels', $plugin_admin, 'add_k2p_product_panel', 10, 3);
                $this->loader->add_action('woocommerce_process_product_meta', $plugin_admin, 'save_k2p_product_panel');

                //K2P products plugin config
                $this->loader->add_action('admin_menu', $plugin_admin, 'add_k2p_products_admin_menu');
                $this->loader->add_action('admin_init', $plugin_admin, 'update_k2p_products_setting_page');

                //K2P order send to K2P API action
                $this->loader->add_action('woocommerce_order_actions', $plugin_admin, 'add_send_order_to_k2p_action');
        }

        private function define_public_hooks() {

                $plugin_public = $this->container->get('plugin_public');

                $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
                $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');


                //Product display
                $this->loader->add_action('woocommerce_single_product_summary', $plugin_public, 'render_k2p_product_template');
                //File upload display
                $this->loader->add_action('woocommerce_after_cart_contents', $plugin_public, 'render_order_file_upload_template');
                //Price calculation AJAX
                $this->loader->add_action('wp_ajax_get_k2p_pricing', $plugin_public, 'get_k2p_pricing_ajax_action');
                $this->loader->add_action('wp_ajax_nopriv_get_k2p_pricing', $plugin_public, 'get_k2p_pricing_ajax_action');
                //Available options AJAX
                $this->loader->add_action('wp_ajax_get_k2p_available_options', $plugin_public, 'get_k2p_available_options_ajax_action');
                $this->loader->add_action('wp_ajax_nopriv_get_k2p_available_options', $plugin_public, 'get_k2p_available_options_ajax_action');
                //Upload actions AJAX
                $this->loader->add_action('wp_ajax_start_k2p_product_upload', $plugin_public, 'start_k2p_product_upload_ajax_action');
                $this->loader->add_action('wp_ajax_nopriv_start_k2p_product_upload', $plugin_public, 'start_k2p_product_upload_ajax_action');
                $this->loader->add_action('wp_ajax_complete_k2p_product_upload', $plugin_public, 'complete_k2p_product_upload_ajax_action');
                $this->loader->add_action('wp_ajax_nopriv_complete_k2p_product_upload', $plugin_public, 'complete_k2p_product_upload_ajax_action');
                $this->loader->add_action('wp_ajax_remove_k2p_product_upload', $plugin_public, 'remove_k2p_product_upload_ajax_action');
                $this->loader->add_action('wp_ajax_nopriv_remove_k2p_product_upload', $plugin_public, 'remove_k2p_product_upload_ajax_action');

                //Store cart form data
                $this->loader->add_action('woocommerce_add_cart_item_data', $plugin_public, 'store_k2p_product_cart_item_data', 10, 2);
        }

        private function define_order_hooks() {
                $plugin_order = $this->container->get('plugin_order');

                $this->loader->add_action('', $plugin_order, 'fetch_k2p_product_api_data', 11, 2);

                //Check if K2P Product is purchasable
                $this->loader->add_action('woocommerce_is_purchasable', $plugin_order, 'is_k2p_product_purchasable', 10, 2);
                //Add cart item data from "add to cart" form and store it in session
                $this->loader->add_action('woocommerce_add_cart_item_data', $plugin_order, 'fetch_k2p_product_api_data', 20, 2);
                //Display cart item data in cart
                $this->loader->add_filter('woocommerce_get_item_data', $plugin_order, 'display_k2p_product_cart_item_data', 10, 2);
                //Price calculation on server side
                $this->loader->add_action('woocommerce_before_calculate_totals', $plugin_order, 'calculate_k2p_product_prices', 20, 1);
                //Save order item data when rewriting order items from session to db (during purchase)
                $this->loader->add_action('woocommerce_checkout_create_order_line_item', $plugin_order, 'create_k2p_product_order_line_item', 10, 4);
                //Meta data filter
                $this->loader->add_filter('woocommerce_order_item_get_formatted_meta_data', $plugin_order, 'hide_k2p_product_setup_meta_data', 10, 2);


                //K2P order send to K2P API action
                $this->loader->add_action('woocommerce_order_action_send_to_k2p', $plugin_order, 'send_to_k2p');
                //K2P order status update from  K2P API action
                $this->loader->add_action('woocommerce_order_action_update_from_k2p', $plugin_order, 'update_from_k2p');
                //K2P order prevent checkout without files
                $this->loader->add_action( 'woocommerce_check_cart_items', $plugin_order, 'prevent_checkout_without_files');
        }

        public function run() {
                $this->loader->run();
        }

        public function get_plugin_name() {
                return $this->plugin_name;
        }

        public function get_loader() {
                return $this->loader;
        }

        public function get_version() {
                return $this->version;
        }

}
