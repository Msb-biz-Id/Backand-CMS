<?php

/**
 * Google Gemini AI Helper
 * Generate articles with AI
 */
class GeminiAI {
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    
    public function __construct() {
        global $config;
        $this->apiKey = $config['ai']['gemini_api_key'] ?? '';
    }
    
    /**
     * Generate article
     * 
     * @param string $keyword Main keyword
     * @param string $category Category context
     * @param string $style Writing style (professional, academic, casual, etc)
     * @param int $length Word count target (short=300, medium=600, long=1000)
     * @return array Generated content with title, content, meta description
     */
    public function generateArticle($keyword, $category = '', $style = 'professional', $length = 'medium') {
        if (!$this->apiKey) {
            throw new Exception('Gemini API key not configured');
        }
        
        // Define word counts
        $wordCounts = [
            'short' => 300,
            'medium' => 600,
            'long' => 1000,
            'very_long' => 1500
        ];
        
        $targetWords = $wordCounts[$length] ?? 600;
        
        // Define writing styles
        $styles = [
            'professional' => 'gaya penulisan profesional dan bisnis',
            'academic' => 'gaya penulisan akademis dan formal dengan referensi',
            'casual' => 'gaya penulisan santai dan friendly',
            'journalistic' => 'gaya penulisan jurnalistik objektif',
            'storytelling' => 'gaya penulisan bercerita yang engaging'
        ];
        
        $styleDesc = $styles[$style] ?? $styles['professional'];
        
        // Build prompt
        $prompt = $this->buildPrompt($keyword, $category, $styleDesc, $targetWords);
        
        // Make API request
        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            throw new Exception('Failed to generate content from Gemini AI');
        }
        
        // Parse response
        return $this->parseResponse($response, $keyword);
    }
    
    /**
     * Build comprehensive prompt
     */
    private function buildPrompt($keyword, $category, $style, $wordCount) {
        $prompt = "Tulis artikel blog lengkap dalam Bahasa Indonesia dengan ketentuan berikut:\n\n";
        $prompt .= "KEYWORD UTAMA: {$keyword}\n";
        
        if ($category) {
            $prompt .= "KATEGORI: {$category}\n";
        }
        
        $prompt .= "GAYA PENULISAN: {$style}\n";
        $prompt .= "PANJANG TARGET: {$wordCount} kata\n\n";
        
        $prompt .= "STRUKTUR ARTIKEL:\n";
        $prompt .= "1. Judul yang menarik dan mengandung keyword\n";
        $prompt .= "2. Meta description (150-160 karakter) yang SEO-friendly\n";
        $prompt .= "3. Pembukaan yang engaging\n";
        $prompt .= "4. Beberapa sub-heading (H2) yang relevan\n";
        $prompt .= "5. Konten yang informatif dan valuable\n";
        $prompt .= "6. Kesimpulan yang kuat\n\n";
        
        $prompt .= "KETENTUAN SEO:\n";
        $prompt .= "- Gunakan keyword utama secara natural\n";
        $prompt .= "- Sisipkan LSI keywords (variasi keyword)\n";
        $prompt .= "- Optimasi untuk search intent\n";
        $prompt .= "- Gunakan heading hierarchy yang proper\n\n";
        
        $prompt .= "FORMAT OUTPUT:\n";
        $prompt .= "Berikan output dalam format JSON dengan struktur:\n";
        $prompt .= '{"title": "judul artikel", "meta_description": "meta desc", "content": "konten artikel dalam HTML"}';
        
        return $prompt;
    }
    
    /**
     * Make API request to Gemini
     */
    private function makeRequest($prompt) {
        $url = $this->baseUrl . '?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Parse Gemini response
     */
    private function parseResponse($response, $keyword) {
        if (!isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Invalid response format from Gemini AI');
        }
        
        $text = $response['candidates'][0]['content']['parts'][0]['text'];
        
        // Try to parse as JSON first
        $json = json_decode($text, true);
        
        if ($json && isset($json['title']) && isset($json['content'])) {
            return [
                'title' => $json['title'],
                'meta_description' => $json['meta_description'] ?? substr(strip_tags($json['content']), 0, 160),
                'content' => $json['content'],
                'keyword' => $keyword
            ];
        }
        
        // If not JSON, parse manually
        return [
            'title' => $this->extractTitle($text, $keyword),
            'meta_description' => substr(strip_tags($text), 0, 160),
            'content' => $this->formatContent($text),
            'keyword' => $keyword
        ];
    }
    
    /**
     * Extract title from text
     */
    private function extractTitle($text, $fallbackKeyword) {
        // Try to find first heading or first line
        if (preg_match('/#\s+(.+)/m', $text, $matches)) {
            return trim($matches[1]);
        }
        
        $lines = explode("\n", $text);
        if (!empty($lines[0])) {
            return trim($lines[0]);
        }
        
        return ucwords($fallbackKeyword);
    }
    
    /**
     * Format content to HTML
     */
    private function formatContent($text) {
        // Convert markdown-like syntax to HTML
        $html = $text;
        
        // Headers
        $html = preg_replace('/^##\s+(.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^###\s+(.+)$/m', '<h3>$1</h3>', $html);
        
        // Bold
        $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
        
        // Paragraphs
        $html = '<p>' . preg_replace('/\n\n+/', '</p><p>', $html) . '</p>';
        
        return $html;
    }
    
    /**
     * Generate SEO-optimized excerpt
     */
    public function generateExcerpt($content, $length = 150) {
        $stripped = strip_tags($content);
        
        if (strlen($stripped) <= $length) {
            return $stripped;
        }
        
        return substr($stripped, 0, $length) . '...';
    }
}
