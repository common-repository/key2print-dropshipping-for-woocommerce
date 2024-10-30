<?php

class K2P_Products_Api_Connector {

        /**
         *
         * @var K2P_Products_Api_Client
         */
        private $api_client;

        /**
         *
         * @var K2P_Products_Api_Payload_Builder
         */
        private $api_payload_builder;

        public function __construct(K2P_Products_Api_Client $api_client, K2P_Products_Api_Payload_Builder $api_payload_builder) {
                $this->api_client = $api_client;
                $this->api_payload_builder = $api_payload_builder;
        }

        public function get_products() {
                $results = $this->api_client->get('get_products', array(
                ));

                $products = array();
                foreach ($results as $result) {
                        if ($result == null) {
                                continue;
                        }
                        $products[$result['id']] = $result;
                }

                return $products;
        }

        public function get_product($product_api_id) {
                $query = json_encode(array(
                    'product_id' => $product_api_id,
                    'lang' => $this->get_api_lang(),
                ));

                $results = $this->api_client->get('get_products', array(
                    'query' => base64_encode($query),
                ));

                return $results[0];
        }

        public function get_product_pricing($product_api_id, $product_setup) {

                $query = json_encode(array(
                    'product_id' => $product_api_id,
                    'configuration' => $product_setup
                ));

                $results = $this->api_client->get('get_prices', array(
                    'query' => base64_encode($query),
                ));


                return $results;
        }

        public function get_product_available_options($product_api_id, $product_setup = null) {

                $queryData = array(
                    'product_id' => $product_api_id,
                );

                if ($product_setup !== null) {
                        $queryData['configuration'] = $product_setup;
                }

                $query = json_encode($queryData);

                $results = $this->api_client->get('get_available_options', array(
                    'query' => base64_encode($query),
                ));


                return $results;
        }

        public function submit_order($order_id) {
                /** @var WC_Order_Refund|WC_Order $order */
                $order = wc_get_order($order_id);

                if ($this->is_order_already_sent($order)) {
                        return;
                }

                $orderPayload = $this->api_payload_builder->build_order_payload($order);

                $response = $this->api_client->post('submit_order', $orderPayload);

                if (isset($response['success']) && $response['success']) {
                        $this->update_order_items_statuses($order, $response['data']);
                }
        }

        public function update_order_status($order_id) {
                /** @var WC_Order_Refund|WC_Order $order */
                $order = wc_get_order($order_id);

                if (!$this->is_order_already_sent($order)) {
                        return;
                }

                $orderPayload = $this->api_payload_builder->build_order_status_payload($order);

                $response = $this->api_client->post('check_order_status', $orderPayload);

                if (isset($response['success']) && $response['success']) {
                        $this->update_order_items_statuses($order, $response['data']);
                }
        }

        public function create_upload($filename) {
                $payload = $this->api_payload_builder->build_start_upload_payload($filename);

                return $this->api_client->post('create_upload', $payload);
        }

        public function complete_upload($api_upload_id) {
                $payload = $this->api_payload_builder->build_complete_upload_payload($api_upload_id);

                return $this->api_client->post('complete_upload', $payload);
        }

        public function get_chunk_upload_url() {
                return $this->api_client->prepare_url('do_chunk_upload');
        }

        protected function update_order_items_statuses(WC_Order $order, $order_statuses) {
                $order_ext_id = $order_statuses['id'];
                $order_number = $order_statuses['number'];
                $order_url = $order_statuses['url'];

                /** @var WC_Order_Item $item */
                foreach ($order->get_items() as $item) {
                        foreach ($order_statuses['items'] as $itemStatus) {
                                if ($item->get_id() != $itemStatus['ext_id']) {
                                        continue;
                                }

                                $item->delete_meta_data('k2p_order_status');
                                $item->add_meta_data('k2p_order_status', json_encode(array(
                                    'order_ext_id' => $order_ext_id,
                                    'order_number' => $order_number,
                                    'order_url' => $order_url,
                                    'status' => $itemStatus['status'],
                                )));

                                $link = "<a href='" . $order_url . "' target='_blank'>" . $order_url . "</a>";

                                $item->delete_meta_data(__('Production status', 'k2p-products'));
                                $item->add_meta_data(__('Production status', 'k2p-products'), $itemStatus['status']);
                                $item->delete_meta_data(__('Order number', 'k2p-products'));
                                $item->add_meta_data(__('Order number', 'k2p-products'), $order_number);
                                $item->delete_meta_data(__('Order link', 'k2p-products'));
                                $item->add_meta_data(__('Order link', 'k2p-products'), $link);

                                $item->save_meta_data();
                        }
                }

                $order->delete_meta_data(__('Order number', 'k2p-products'));
                $order->add_meta_data(__('Order number', 'k2p-products'), $order_number);
                $order->delete_meta_data(__('Order link', 'k2p-products'));
                $order->add_meta_data(__('Order link', 'k2p-products'), $order_url);

                $order->save_meta_data();
        }

        protected function is_order_already_sent(WC_Order $order) {
                foreach ($order->get_items() as $item) {
                        if (strlen($item->get_meta('k2p_order_status')) > 0) {
                                return true;
                        }
                }
                return false;
        }

        protected function get_api_lang() {
                $locale = explode('_', get_locale());
                $lang = $locale[0];

                $available_langs = array(
                    'de',
                    'en',
                    'ch',
                    'fr',
                    'nl',
                    'it',
                    'es',
                );
                return in_array($lang, $available_langs) ? $lang : 'en';
        }

}
