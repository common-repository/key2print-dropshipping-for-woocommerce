<?php

class K2P_Products_Api_Payload_Builder {

        protected $api_key;

        /**
         *
         * @var K2P_Products_Api_Sign_Generator
         */
        protected $api_sign_generator;

        public function __construct($api_key, K2P_Products_Api_Sign_Generator $api_sign_generator) {
                $this->api_key = $api_key;
                $this->api_sign_generator = $api_sign_generator;
        }

        public function build_order_payload(WC_Order $order) {

                $orderPayload = array(
                    'id' => $order->get_id(),
                    'completed_at' => $order->get_date_created(),
                    'items' => $this->build_order_items_payload($order),
                    'shipping_address' => $this->build_address_payload($order, 'shipping'),
                    'billing_address' => $this->build_address_payload($order, 'billing'),
                    'email' => $order->get_billing_email(),
                );

                $payload = array(
                    'endpoint_key' => $this->api_key,
                    'endpoint_sign' => $this->api_sign_generator->generate_sign($this->api_key),
                    'order_payload' => $orderPayload,
                );

                return $payload;
        }

        public function build_start_upload_payload($filename) {
                return array(
                    'endpoint_key' => $this->api_key,
                    'endpoint_sign' => $this->api_sign_generator->generate_sign($this->api_key),
                    'upload_payload' => array(
                        'filename' => $filename,
                        'context' => 'order'
                ));
        }

        public function build_complete_upload_payload($api_upload_id) {
                return array(
                    'endpoint_key' => $this->api_key,
                    'endpoint_sign' => $this->api_sign_generator->generate_sign($this->api_key),
                    'upload_payload' => array(
                        'upload_id' => $api_upload_id,
                    )
                );
        }

        public function build_order_status_payload(WC_Order $order) {
                $orderExtId = null;
                foreach ($order->get_items() as $item) {
                        $meta = $item->get_meta('k2p_order_status');
                        if (strlen($meta) == 0) {
                                continue;
                        }
                        $data = json_decode($meta, true);
                        if (!$data || !is_array($data)) {
                                continue;
                        }
                        $orderExtId = $data['order_ext_id'];
                }

                $payload = array(
                    'endpoint_key' => $this->api_key,
                    'endpoint_sign' => $this->api_sign_generator->generate_sign($this->api_key),
                    'order_id' => $orderExtId,
                );


                return $payload;
        }

        protected function build_order_items_payload(WC_Order $order) {
                $itemsPayload = array();

                /** @var WC_Order_Item_Product $item */
                foreach ($order->get_items() as $item) {
                        if ($item->get_product()->get_type() !== 'k2p_product') {
                                continue;
                        }

                        $k2p_product_setup = json_decode($item->get_meta('k2p_product_setup'), true);

                        $itemPayload = array(
                            'id' => $item->get_id(),
                            'product_id' => $k2p_product_setup['product-api-id'],
                            'configuration' => $k2p_product_setup['product-setup'],
                            'run_size' => $k2p_product_setup['product-run-size'],
                            'delivery_time' => $k2p_product_setup['product-delivery-time'],
                            'quantity' => $item->get_quantity(),
                            'file_components' => $k2p_product_setup['file_components'],
                        );

                        $itemsPayload[] = $itemPayload;
                }

                return $itemsPayload;
        }

        protected function build_address_payload(WC_Order $order, $type) {
                $order_address = $order->get_address($type);

                $address = $order_address['address_1'];
                if (isset($address['address_2'])) {
                        $address .= ' ' . $address['address_2'];
                }


                return array(
                    'first_name' => $order_address['first_name'],
                    'last_name' => $order_address['last_name'],
                    'address' => $address,
                    'company_name' => $order_address['company'],
                    'city' => $order_address['city'],
                    'postcode' => $order_address['postcode'],
                    'phone' => '',
                    'email' => $order->get_billing_email(),
                    'tax_number' => '',
                    'country' => $order->get_billing_country(),
                );
        }

}
