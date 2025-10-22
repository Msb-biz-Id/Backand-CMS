<?php

/**
 * Shipping Helper
 * Integration dengan Raja Ongkir API
 */
class ShippingHelper {
    private $apiKey;
    private $baseUrl = 'https://api.rajaongkir.com/starter';
    
    public function __construct() {
        global $config;
        $this->apiKey = $config['shipping']['rajaongkir_api_key'] ?? '';
    }
    
    /**
     * Get provinces
     */
    public function getProvinces() {
        $url = $this->baseUrl . '/province';
        
        $response = $this->makeRequest($url);
        
        if ($response && isset($response['rajaongkir']['results'])) {
            return $response['rajaongkir']['results'];
        }
        
        return [];
    }
    
    /**
     * Get cities by province
     */
    public function getCities($provinceId = null) {
        $url = $this->baseUrl . '/city';
        
        if ($provinceId) {
            $url .= '?province=' . $provinceId;
        }
        
        $response = $this->makeRequest($url);
        
        if ($response && isset($response['rajaongkir']['results'])) {
            return $response['rajaongkir']['results'];
        }
        
        return [];
    }
    
    /**
     * Calculate shipping cost
     */
    public function calculateShipping($origin, $destination, $weight, $courier = 'jne') {
        $url = $this->baseUrl . '/cost';
        
        $data = [
            'origin' => $origin,        // City ID
            'destination' => $destination, // City ID
            'weight' => $weight,        // in grams
            'courier' => $courier       // jne, pos, tiki, etc
        ];
        
        $response = $this->makeRequest($url, 'POST', $data);
        
        if ($response && isset($response['rajaongkir']['results'])) {
            return $response['rajaongkir']['results'];
        }
        
        return [];
    }
    
    /**
     * Get available couriers
     */
    public function getAvailableCouriers() {
        return [
            'jne' => 'JNE',
            'pos' => 'POS Indonesia',
            'tiki' => 'TIKI',
        ];
    }
    
    /**
     * Make HTTP request to Raja Ongkir API
     */
    private function makeRequest($url, $method = 'GET', $data = []) {
        if (!$this->apiKey) {
            return null;
        }
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'key: ' . $this->apiKey
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
}
