<?php

/**
 * Builds and manages plugin services
 *
 * @package    K2P_Products
 * @subpackage K2P_Products/includes
 * @author     key2print <support@key2print.com>
 */
class K2P_Products_Service_Container {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        public function __construct($plugin_name, $version) {

                $this->plugin_name = $plugin_name;
                $this->version = $version;
        }

        private $service_intances = array();

        public function get($service_name) {
                if (isset($this->service_intances[$service_name])) {
                        return $this->service_intances[$service_name];
                }

                $build_method_name = 'build_' . $service_name;

                if (!method_exists($this, $build_method_name)) {
                        throw new \Exception('Unknown service: ' . $service_name);
                }

                $service = $this->$build_method_name();

                $this->service_intances[$service_name] = $service;

                return $service;
        }

        private function build_loader() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-k2p-products-loader.php';

                return new K2P_Products_Loader();
        }

        private function build_plugin_admin() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-k2p-products-admin.php';

                $service = new K2P_Products_Admin($this->plugin_name, $this->version);

                $service->set_api_connector($this->get('api_connector'));
                $service->set_api_resolver($this->get('api_resolver'));
                $service->set_input_processor($this->get('input_processor'));

                return $service;
        }

        private function build_plugin_public() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-k2p-products-public.php';

                $service = new K2P_Products_Public($this->plugin_name, $this->version);

                $service->set_api_connector($this->get('api_connector'));
                $service->set_price_calculator($this->get('price_calculator'));
                $service->set_upload_helper($this->get('upload_helper'));
                $service->set_input_processor($this->get('input_processor'));

                return $service;
        }

        private function build_plugin_order() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'order/class-k2p-products-order.php';

                $service = new K2P_Products_Order($this->plugin_name, $this->version);

                $service->set_price_calculator($this->get('price_calculator'));
                $service->set_option_resolver($this->get('option_resolver'));
                $service->set_api_connector($this->get('api_connector'));

                return $service;
        }

        private function build_i18n() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-k2p-products-i18n.php';

                return new K2P_Products_i18n();
        }

        private function build_price_calculator() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/calculator/class-k2p-products-price-calculator.php';

                $service = new K2P_Products_Price_Calculator();

                $service->set_api_connector($this->get('api_connector'));
                $service->set_price_formatter($this->get('price_formatter'));

                return $service;
        }

        private function build_option_resolver() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/resolver/class-k2p-products-option-resolver.php';


                $service = new K2P_Products_Option_Resolver();

                $service->set_api_connector($this->get('api_connector'));
                return $service;
        }

        private function build_api_client() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-client-exception.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-client.php';


                /** @var K2P_Products_Api_Settings_Manager $api_settings_manager */
                $api_settings_manager = $this->get('api_settings_manager');

                $baseUrl = $api_settings_manager->getApiBaseUrl();
                $apiKey = $api_settings_manager->getApiKey();
                $apiSecret = $api_settings_manager->getApiSecret();

                $service = new K2P_Products_Api_Client($baseUrl, $apiKey, $apiSecret);

                return $service;
        }

        private function build_api_connector() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-connector.php';

                $service = new K2P_Products_Api_Connector(
                        $this->get('api_client'),
                        $this->get('api_payload_builder')
                );

                return $service;
        }

        private function build_api_resolver() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-resolver.php';

                $service = new K2P_Products_Api_Resolver();

                return $service;
        }

        private function build_api_payload_builder() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-payload-builder.php';

                /** @var K2P_Products_Api_Settings_Manager $api_settings_manager */
                $api_settings_manager = $this->get('api_settings_manager');

                $api_key = $api_settings_manager->getApiKey();

                $sign_generator = $this->get('api_sign_generator');

                $service = new K2P_Products_Api_Payload_Builder($api_key, $sign_generator);

                return $service;
        }

        private function build_api_sign_generator() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-sign-generator.php';

                /** @var K2P_Products_Api_Settings_Manager $api_settings_manager */
                $api_settings_manager = $this->get('api_settings_manager');

                $api_secret = $api_settings_manager->getApiSecret();

                $service = new K2P_Products_Api_Sign_Generator($api_secret);

                return $service;
        }

        private function build_api_settings_manager() {

                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class-k2p-products-api-settings-manager.php';

                $api_resolver = $this->get('api_resolver');

                $service = new K2P_Products_Api_Settings_Manager($api_resolver);

                return $service;
        }

        private function build_upload_helper() {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/upload/class-k2p-products-upload-helper.php';

                $service = new K2P_Products_Upload_Helper(
                        $this->get('api_connector')
                );

                return $service;
        }

        private function build_price_formatter() {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/formatter/class-k2p-products-price-formatter.php';

                $service = new K2P_Products_Price_Formatter( );

                return $service;
        }

        private function build_input_processor() {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/input/class-k2p-products-input-processor.php';

                $service = new K2P_Products_Input_Processor();

                return $service;
        }

}
