<?php

/**
 * Order Model - E-Commerce
 * Order management with WhatsApp integration
 */
class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_method',
        'payment_status', 'shipping_method', 'shipping_cost',
        'subtotal', 'total', 'customer_name', 'customer_email',
        'customer_phone', 'shipping_address', 'billing_address',
        'notes', 'tracking_number', 'paid_at', 'shipped_at',
        'completed_at', 'cancelled_at'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Generate order number
     */
    public function generateOrderNumber() {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $random;
    }
    
    /**
     * Create order from cart
     */
    public function createOrderFromCart($cartItems, $customerData, $shippingData) {
        $this->db->beginTransaction();
        
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $shippingCost = $shippingData['cost'] ?? 0;
            $total = $subtotal + $shippingCost;
            
            // Create order
            $orderData = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $customerData['payment_method'],
                'payment_status' => 'pending',
                'shipping_method' => $shippingData['courier'] ?? 'JNE',
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $total,
                'customer_name' => $customerData['name'],
                'customer_email' => $customerData['email'],
                'customer_phone' => $customerData['phone'],
                'shipping_address' => json_encode($customerData['shipping_address']),
                'billing_address' => json_encode($customerData['billing_address'] ?? $customerData['shipping_address']),
                'notes' => $customerData['notes'] ?? null
            ];
            
            $orderId = $this->create($orderData);
            
            // Create order items
            foreach ($cartItems as $item) {
                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);
                
                // Update stock
                $productModel = new Product();
                $productModel->updateStock($item['product_id'], $item['quantity']);
            }
            
            $this->db->commit();
            
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Get order with items
     */
    public function getOrderWithItems($orderId) {
        $order = $this->find($orderId);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $sql = "SELECT * FROM cms_order_items WHERE order_id = ?";
        $order['items'] = $this->db->select($sql, [$orderId]);
        
        return $order;
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $data = ['status' => $status];
        
        // Set timestamps based on status
        switch ($status) {
            case 'paid':
                $data['paid_at'] = date('Y-m-d H:i:s');
                $data['payment_status'] = 'paid';
                break;
            case 'shipped':
                $data['shipped_at'] = date('Y-m-d H:i:s');
                break;
            case 'completed':
                $data['completed_at'] = date('Y-m-d H:i:s');
                break;
            case 'cancelled':
                $data['cancelled_at'] = date('Y-m-d H:i:s');
                break;
        }
        
        return $this->update($orderId, $data);
    }
    
    /**
     * Get WhatsApp message for order
     */
    public function getWhatsAppMessage($orderId) {
        $order = $this->getOrderWithItems($orderId);
        
        if (!$order) {
            return '';
        }
        
        $message = "*PESANAN BARU*\n\n";
        $message .= "Order: *{$order['order_number']}*\n";
        $message .= "Tanggal: " . date('d/m/Y H:i', strtotime($order['created_at'])) . "\n\n";
        
        $message .= "*CUSTOMER*\n";
        $message .= "Nama: {$order['customer_name']}\n";
        $message .= "Email: {$order['customer_email']}\n";
        $message .= "Phone: {$order['customer_phone']}\n\n";
        
        $message .= "*PRODUK*\n";
        foreach ($order['items'] as $item) {
            $message .= "- {$item['product_name']} x{$item['quantity']} @ Rp " . number_format($item['price']) . "\n";
        }
        
        $message .= "\n*TOTAL*\n";
        $message .= "Subtotal: Rp " . number_format($order['subtotal']) . "\n";
        $message .= "Ongkir: Rp " . number_format($order['shipping_cost']) . "\n";
        $message .= "TOTAL: *Rp " . number_format($order['total']) . "*\n\n";
        
        $message .= "*Pembayaran*: " . strtoupper($order['payment_method']) . "\n";
        
        return urlencode($message);
    }
    
    /**
     * Get orders by user
     */
    public function getByUser($userId) {
        return $this->findAll(['user_id' => $userId], 'created_at DESC');
    }
    
    /**
     * Get order statistics
     */
    public function getStatistics() {
        $stats = [];
        
        $stats['total'] = $this->count([]);
        $stats['pending'] = $this->count(['status' => 'pending']);
        $stats['paid'] = $this->count(['payment_status' => 'paid']);
        $stats['shipped'] = $this->count(['status' => 'shipped']);
        $stats['completed'] = $this->count(['status' => 'completed']);
        
        // Total revenue
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} 
                WHERE payment_status = 'paid' AND deleted_at IS NULL";
        $result = $this->db->selectOne($sql);
        $stats['revenue'] = $result['revenue'] ?? 0;
        
        return $stats;
    }
}
