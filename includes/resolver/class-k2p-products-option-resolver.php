<?php

class K2P_Products_Option_Resolver {

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        public function set_api_connector(K2P_Products_Api_Connector $api_connector) {
                $this->api_connector = $api_connector;
        }

        public function resolve($k2p_product_setup) {
                $api_product = $this->api_connector->get_product($k2p_product_setup['product-api-id']);

                return array(
                    'options' => $this->resolve_options($k2p_product_setup, $api_product),
                    'file_components' => $this->resolve_file_components($api_product['file_components']),
                );
        }

        private function resolve_file_components($api_product_file_components) {
                $resolved_file_components = array();

                foreach ($api_product_file_components as $file_component) {
                        $resolved_file_components[] = array(
                            'id' => $file_component['id'],
                            'name' => $file_component['name'],
                            'type' => $file_component['type'],
                            'api_upload_id' => null,
                            'filename' => null,
                            'url' => null,
                        );
                }

                return $resolved_file_components;
        }

        private function resolve_options($k2p_product_setup, $api_product) {
                $resolved_options = array();
                foreach ($k2p_product_setup['product-setup'] as $optionId => $optionValueId) {

                        foreach ($api_product['options'] as $index => $option) {
                                if ($option['id'] == $optionId) {
                                        foreach ($api_product['options'][$index]['values'] as $optionValue) {
                                                if ($optionValue['id'] == $optionValueId) {
                                                        $resolved_options[] = array(
                                                            'id' => $option['id'],
                                                            'name' => $option['name'],
                                                            'value' => array(
                                                                'id' => $optionValue['id'],
                                                                'name' => $optionValue['name'],
                                                            )
                                                        );
                                                        break;
                                                }
                                        }
                                        break;
                                }
                        }
                }

                return $resolved_options;
        }

}
