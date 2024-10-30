<?php

class K2P_Products_Api_Client {

        private $baseUrl;
        private $apiKey;
        private $apiSecret;
        private $methods = array(
            'get_products' => '/v1/getProducts',
            'get_prices' => '/v1/getPrices',
            'get_available_options' => '/v1/getAvailableOptions',
            'submit_order' => '/v1/submitOrder',
            'check_order_status' => '/v1/checkOrderStatus',
            'create_upload' => '/v1/createUpload',
            'do_chunk_upload' => '/v1/doChunkUpload',
            'complete_upload' => '/v1/completeUpload',
        );

        public function __construct($baseUrl, $apiKey, $apiSecret) {
                $this->baseUrl = $baseUrl;
                $this->apiKey = $apiKey;
                $this->apiSecret = $apiSecret;
        }

        public function getApiKey() {
                return $this->apiKey;
        }

        public function get($method, $params = array()) {
                $url = $this->prepare_url($method, $params);

                $args = array(
                    'timeout' => 5000,
                    'headers' => $this->prepare_headers(),
                );

                $response = wp_remote_get($url, $args);

                if ($response instanceof WP_Error || !isset($response['body'])) {
                        throw new K2P_Products_Api_Client_Exception('Could not connect');
                }

                $data = json_decode($response['body'], true);

                if (!is_array($data)) {
                        throw new K2P_Products_Api_Client_Exception('Malformed response');
                }


                return $data;
        }

        public function post($method, $body) {
                $url = $this->prepare_url($method);

                $headers = $this->prepare_headers();
                $headers['Content-Type'] = 'application/json';

                $args = array(
                    'timeout' => 5000,
                    'body' => is_array($body) ? json_encode($body) : $body,
                    'headers' => $headers,
                );

                $response = wp_remote_post($url, $args);

                if ($response instanceof WP_Error || !isset($response['body'])) {
                        throw new K2P_Products_Api_Client_Exception('Could not connect');
                }

                $data = json_decode($response['body'], true);

                if (!is_array($data)) {
                        throw new K2P_Products_Api_Client_Exception('Malformed response');
                }


                return $data;
        }

        public function prepare_url($method, $params = array()) {
                if (!isset($this->methods[$method])) {
                        throw new K2P_Products_Api_Client_Exception('Unknown method name: ' . $method);
                }

                $url = $this->baseUrl . '' . $this->methods[$method];

                if (count($params) == 0) {
                        return $url;
                }

                $paramsChunks = array();
                foreach ($params as $name => $value) {
                        $paramsChunks[] = $name . "=" . $value;
                }
                $url .= '?' . implode('&', $paramsChunks);

                return $url;
        }

        private function prepare_headers() {
                $timestamp = time();
                $stringToHash = $timestamp;
                $hashedSecret = hash('sha256', $this->apiSecret);
                $requestSign = hash_hmac("sha256", $stringToHash, $hashedSecret);

                return array(
                    "x-api-key" => $this->apiKey,
                    "x-api-timestamp" => $timestamp,
                    "x-api-sign" => $requestSign
                );
        }

}
