<?php

class K2P_Products_Api_Settings_Manager {

        /**
         *
         * @var K2P_Products_Api_Resolver
         */
        private $api_resolver;
        private $demoCredentials = array(
            'uk' => array(
                'api_key' => 'd37481573ccf33c817b9d9b641fc9656',
                'api_secret' => 'JDJ5JDEwJHZEWXpEOE1oVTZBMVp1QmxNSWM2U2U0NjlaZS9US083VVo1S2RTeUlQMWxPd1pIVUQ2emU2',
            ),
            'de' => array(
                'api_key' => '92ab125b689695e9298ca5c012e8bed2',
                'api_secret' => 'JDJ5JDEwJGdQUUdiYTMxYzRlVU5GTHpSU2F6cHVJT1U2cEpxalFDaWVoOU5TLnk0RG9ZRGRTTGNJc1dl',
            ),
            'at' => array(
                'api_key' => '93127b61ada18786fa8c422aa60ec72f',
                'api_secret' => 'JDJ5JDEwJEJ0L2JjVzVQNTJXL3BUQ3dtbXBHN2UvaGF6MDN0aXlIY3VsNlRWRlhYSThHRzZjcWdzN1JD',
            ),
            'fr' => array(
                'api_key' => 'ef3151d412f95361648801ea31e82312',
                'api_secret' => 'JDJ5JDEwJFJ1clg0dUo2YUdTbm5YZUZ4aTZ3V3VKT21LVVF3YXRaYXZjeTZiSUN3dHpPWUFlOUhReEtX',
            ),
            'be' => array(
                'api_key' => '3d66b9a0eee9862657cee51fc680a504',
                'api_secret' => 'JDJ5JDEwJDlaT2Q2TXZuS3hUd0hPTGVybnlRWHV2cTFwZjFnT01tZmZTRENHNk9ISEsybGFyOUdBbzhD',
            ),
            'nl' => array(
                'api_key' => 'a324635986f0e2b404d7b1bc61df5bb4',
                'api_secret' => 'JDJ5JDEwJDJtRnAyZ1MzUXZ3dGNqR2xPLmtDR3VwUzhRSVFLQ1pTelNSOWhVbkhQRUNsLk5sdEN2aGNp',
            ),
            'es' => array(
                'api_key' => 'c51b704f58dccdeca802795234ab8428',
                'api_secret' => 'JDJ5JDEwJElXdS5jOC5DL285SE1EdlRaemwydE9HU2xpOEtoUkRaVnFCZXpYaG0vWGNrQVVZUDdFZ0NX',
            ),
            'it' => array(
                'api_key' => '6eb6a7e695b79297dd56da4878be464c',
                'api_secret' => 'JDJ5JDEwJEJGdG5oMlF4NEl0NDFTOGhIZW5Cb2VidzhRNkQ1NlphVmVKNE52MUVicUowZFRnSlJwbEhT',
            ),
            'ch' => array(
                'api_key' => 'fad9b526b70978cdc4908d92f40f4459',
                'api_secret' => 'JDJ5JDEwJFdYTnZnTVhDVWp4M0hYYThoTjVwcmVrLzNLUGI2a01jR24vUy52bWN1SUpXQ0ZrdW5mbktl',
            ),
        );

        public function __construct(K2P_Products_Api_Resolver $api_resolver) {
                $this->api_resolver = $api_resolver;
        }

        public function getApiBaseUrl() {
                return $this->api_resolver->get_api_url($this->getSettingsValue('api_instance', 'uk'));
        }

        public function getApiKey() {
                if ($this->isDemoMode()) {
                        return $this->getDemoSettingsValue($this->getSettingsValue('api_instance', 'uk'), 'api_key');
                }
                return $this->getSettingsValue('api_key');
        }

        public function getApiSecret() {
                if ($this->isDemoMode()) {
                        return $this->getDemoSettingsValue($this->getSettingsValue('api_instance', 'uk'), 'api_secret');
                }
                return $this->getSettingsValue('api_secret');
        }

        private function getDemoSettingsValue($instance, $key) {

                if (!isset($this->demoCredentials[$instance])) {
                        return null;
                }
                if (!isset($this->demoCredentials[$instance][$key])) {
                        return null;
                }

                return $this->demoCredentials[$instance][$key];
        }

        private function getSettingsValue($key, $defaultValue = '') {
                $settings = get_option('k2p_products_settings');

                return isset($settings[$key]) ? $settings[$key] : $defaultValue;
        }

        private function isDemoMode() {
                return $this->getSettingsValue('demo_enabled', 1) == 1;
        }

}
