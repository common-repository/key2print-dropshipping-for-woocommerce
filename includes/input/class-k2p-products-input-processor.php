<?php

class K2P_Products_Input_Processor {

        public function process_product_id($rawData) {
                $productId = sanitize_key($rawData);

                /** @var WC_Product $product */
                $product = wc_get_product($productId);

                if (!$product) {
                        return null;
                }
                if ($product->get_type() != 'k2p_product') {
                        return null;
                }


                return $productId;
        }

        public function process_product_api_id($rawData) {

                return sanitize_key($rawData);
        }

        public function process_product_setup_array($rawData) {

                if (!is_array($rawData)) {
                        return array();
                }

                $setup = array();
                foreach ($rawData as $key => $value) {
                        $setup[sanitize_key($key)] = sanitize_key($value);
                }

                return $rawData;
        }

        public function process_product_setup_json($rawData) {

                $decodedJson = json_decode(stripslashes($rawData), true);

                if (!is_array($decodedJson)) {
                        return array();
                }

                return $this->process_product_setup_array($decodedJson);
        }

        public function process_file_component_id($rawData) {
                return sanitize_key($rawData);
        }

        public function process_cart_item_key($rawData) {
                return sanitize_key($rawData);
        }

        public function process_filename($rawData) {
                return sanitize_file_name($rawData);
        }

        public function process_delivery_time($rawData) {
                return sanitize_text_field($rawData);
        }

        public function process_runsize($rawData) {
                $data = sanitize_text_field($rawData);

                if (!is_numeric($data)) {
                        return 0;
                }

                return $data;
        }

        public function process_margin($rawData) {

                $margin = sanitize_text_field($rawData);

                if (!is_numeric($margin)) {
                        return 0;
                } else {
                        return floatval($margin);
                }
        }

}
