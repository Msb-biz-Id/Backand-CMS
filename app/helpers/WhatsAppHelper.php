<?php

/**
 * WhatsApp Helper
 * Generate WhatsApp links & messages
 */
class WhatsAppHelper {
    
    /**
     * Generate WhatsApp link
     */
    public static function generateLink($phone, $message = '') {
        // Clean phone number (remove spaces, dashes, etc)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        $baseUrl = 'https://wa.me/';
        $url = $baseUrl . $phone;
        
        if ($message) {
            $url .= '?text=' . urlencode($message);
        }
        
        return $url;
    }
    
    /**
     * Send order notification
     */
    public static function sendOrderNotification($order, $adminPhone) {
        $message = "*PESANAN BARU*\n\n";
        $message .= "Order: *{$order['order_number']}*\n";
        $message .= "Customer: {$order['customer_name']}\n";
        $message .= "Phone: {$order['customer_phone']}\n";
        $message .= "Total: Rp " . number_format($order['total']) . "\n\n";
        $message .= "Klik link untuk lihat detail order di admin panel.";
        
        return self::generateLink($adminPhone, $message);
    }
    
    /**
     * Generate order confirmation message
     */
    public static function orderConfirmationMessage($order) {
        $message = "*TERIMA KASIH ATAS PESANAN ANDA!*\n\n";
        $message .= "Order Number: *{$order['order_number']}*\n";
        $message .= "Tanggal: " . date('d M Y H:i', strtotime($order['created_at'])) . "\n\n";
        
        $message .= "*DETAIL PESANAN*\n";
        foreach ($order['items'] as $item) {
            $message .= "• {$item['product_name']} x{$item['quantity']}\n";
            $message .= "  Rp " . number_format($item['price']) . "\n";
        }
        
        $message .= "\n*TOTAL*\n";
        $message .= "Subtotal: Rp " . number_format($order['subtotal']) . "\n";
        $message .= "Ongkir: Rp " . number_format($order['shipping_cost']) . "\n";
        $message .= "Total: *Rp " . number_format($order['total']) . "*\n\n";
        
        $message .= "*PEMBAYARAN*\n";
        $message .= "Method: " . strtoupper($order['payment_method']) . "\n\n";
        
        $message .= "Kami akan segera memproses pesanan Anda.\n";
        $message .= "Terima kasih!";
        
        return $message;
    }
    
    /**
     * Generate simple contact link
     */
    public static function contactLink($phone, $productName = null) {
        $message = "Halo, saya tertarik dengan";
        
        if ($productName) {
            $message .= " produk: *{$productName}*";
        } else {
            $message .= " produk Anda";
        }
        
        $message .= ". Mohon info lebih lanjut. Terima kasih!";
        
        return self::generateLink($phone, $message);
    }
}
