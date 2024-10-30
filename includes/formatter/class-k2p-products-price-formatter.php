<?php

class K2P_Products_Price_Formatter {

        public function round_price($unrounded_price) {
                return round($unrounded_price, wc_get_price_decimals());
        }

        public function format_price($unformatted_price) {
                return wc_price($unformatted_price);
        }

        public function get_decimals() {
                return wc_get_price_decimals();
        }

        public function get_thousand_separator() {
                return wc_get_price_thousand_separator();
        }

        public function get_decimal_separator() {
                return wc_get_price_decimal_separator();
        }

        public function get_price_format() {
                return get_woocommerce_price_format();
        }

        public function get_currency_symbol() {
                return get_woocommerce_currency_symbol();
        }

}
