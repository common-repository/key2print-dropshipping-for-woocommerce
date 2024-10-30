<?php

class K2P_Products_Price_Calculator {

        /**
         *
         * @var K2P_Products_Api_Connector
         */
        private $api_connector;

        /**
         *
         * @var K2P_Products_Price_Formatter
         */
        private $price_formatter;

        public function set_api_connector(K2P_Products_Api_Connector $api_connector) {
                $this->api_connector = $api_connector;
        }

        public function set_price_formatter(K2P_Products_Price_Formatter $price_formatter) {
                $this->price_formatter = $price_formatter;
        }

        public function calculate_pricing_grid($product_id, $k2p_product_setup) {
                $calculated_grid = $this->api_connector->get_product_pricing($k2p_product_setup['product-api-id'], $k2p_product_setup['product-setup']);

                $product_margin = get_post_meta($product_id, 'k2p_product_margin', true);


                foreach ($calculated_grid as $run_size_index => $runSize) {
                        foreach ($runSize as $delivery_time_index => $deliveryTime) {
                                $raw_price = $calculated_grid[$run_size_index][$delivery_time_index]['price'];
                                if (is_numeric($product_margin)) {
                                        $raw_price *= 1 + ($product_margin / 100);
                                }
                                $rounded_price = $this->price_formatter->round_price($raw_price);
                                $formatted_price = $this->price_formatter->format_price($raw_price);

                                $calculated_grid[$run_size_index][$delivery_time_index]['price'] = $rounded_price;
                                $calculated_grid[$run_size_index][$delivery_time_index]['formatted_price'] = $formatted_price;
                        }
                }



                return $calculated_grid;
        }

        public function get_price_tier($product_id, $k2p_product_setup) {
                $pricing = $this->calculate_pricing_grid($product_id, $k2p_product_setup);

                return $pricing[$k2p_product_setup['product-run-size']][$k2p_product_setup['product-delivery-time']];
        }

}
