<?php

class K2P_Products_Api_Sign_Generator {

        protected $api_secret;

        public function __construct($api_secret) {
                $this->api_secret = $api_secret;
        }

        public function generate_sign($content_string) {
                return hash_hmac("sha256", $content_string, hash('sha256', $this->api_secret));
        }

}
