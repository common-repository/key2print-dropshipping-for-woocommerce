<?php

class K2P_Products_Api_Resolver {

        private $fallback_instance_code = 'ch';
        private $api_urls = array(
            'uk' => 'https://gb.api.saxoprint.partners',
            'de' => 'https://de.api.saxoprint.partners',
            'at' => 'https://at.api.saxoprint.partners',
            'fr' => 'https://fr.api.saxoprint.partners',
            'be' => 'https://be.api.saxoprint.partners',
            'nl' => 'https://nl.api.saxoprint.partners',
            'es' => 'https://es.api.saxoprint.partners',
            'it' => 'https://it.api.saxoprint.partners',
            'ch' => 'https://ch.api.saxoprint.partners',
        );
        private $backend_urls = array(
            'uk' => 'https://backend.saxoprint.partners',
            'de' => 'https://de.backend.saxoprint.partners',
            'at' => 'https://at.backend.saxoprint.partners',
            'fr' => 'https://fr.backend.saxoprint.partners',
            'be' => 'https://backend.be.saxoprint.partners',
            'nl' => 'https://nl.backend.saxoprint.partners',
            'es' => 'https://es.backend.saxoprint.partners',
            'it' => 'https://it.backend.saxoprint.partners',
            'ch' => 'https://ch.backend.aws.saxoprint.partners',
        );
        private $api_names = array();

        public function __construct() {
                $this->api_names = array(
                    'uk' => __('United Kingdom - prices in GBP', 'k2p-products'),
                    'de' => __('Germany - prices in EURO', 'k2p-products'),
                    'at' => __('Austria - prices in EURO', 'k2p-products'),
                    'fr' => __('France - prices in EURO', 'k2p-products'),
                    'be' => __('Belgium - prices in EURO', 'k2p-products'),
                    'nl' => __('Netherlands - prices in EURO', 'k2p-products'),
                    'es' => __('Spain - prices in EURO', 'k2p-products'),
                    'it' => __('Italy - prices in EURO', 'k2p-products'),
                    'ch' => __('Swiss - prices in CHF', 'k2p-products'),
                );
        }

        public function get_api_name($instance_code) {
                return $this->get_api_value($instance_code, $this->api_names);
        }

        public function get_api_url($instance_code) {
                return $this->get_api_value($instance_code, $this->api_urls);
        }

        public function get_backend_url($instance_code) {
                return $this->get_api_value($instance_code, $this->backend_urls);
        }

        public function get_available_instances() {
                return array(
                    'uk',
                    'de',
                    'at',
                    'fr',
                    'be',
                    'nl',
                    'es',
                    'it',
                    'ch'
                );
        }

        private function get_api_value($instance_code, $list) {
                return isset($list[$instance_code]) ? $list[$instance_code] : $list[$this->fallback_instance_code];
        }

}
