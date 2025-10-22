<?php

/**
 * SEO Helper Class
 * Best Practice: Comprehensive SEO management similar to RankMath
 * Features: Meta tags, Open Graph, Twitter Cards, Schema.org JSON-LD, Sitemap
 */
class SEO {
    private static $instance = null;
    private $config;
    private $data = [];
    
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->initializeDefaults();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize default SEO data
     */
    private function initializeDefaults() {
        $defaults = $this->config['seo']['meta'];
        
        $this->data = [
            'title' => $defaults['default_title'],
            'description' => $defaults['default_description'],
            'keywords' => $defaults['default_keywords'],
            'canonical' => null,
            'robots' => $defaults['robots'],
            'og_type' => $defaults['og_type'],
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'og_url' => null,
            'twitter_card' => 'summary_large_image',
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_image' => null,
            'schema' => [],
            'breadcrumbs' => [],
        ];
    }
    
    /**
     * Set page title
     * @param string $title
     * @param bool $append
     */
    public function setTitle($title, $append = true) {
        if ($append) {
            $separator = $this->config['seo']['meta']['title_separator'];
            $this->data['title'] = $title . $separator . $this->config['seo']['meta']['default_title'];
        } else {
            $this->data['title'] = $title;
        }
        
        // Auto-set OG title if not set
        if (!$this->data['og_title']) {
            $this->data['og_title'] = $title;
        }
        
        // Auto-set Twitter title if not set
        if (!$this->data['twitter_title']) {
            $this->data['twitter_title'] = $title;
        }
    }
    
    /**
     * Set meta description
     * @param string $description
     */
    public function setDescription($description) {
        // Limit to recommended length
        $maxLength = $this->config['seo']['meta_description_length'];
        $description = substr($description, 0, $maxLength);
        
        $this->data['description'] = $description;
        
        // Auto-set OG description if not set
        if (!$this->data['og_description']) {
            $this->data['og_description'] = $description;
        }
        
        // Auto-set Twitter description if not set
        if (!$this->data['twitter_description']) {
            $this->data['twitter_description'] = $description;
        }
    }
    
    /**
     * Set meta keywords
     * @param string|array $keywords
     */
    public function setKeywords($keywords) {
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }
        $this->data['keywords'] = $keywords;
    }
    
    /**
     * Set canonical URL
     * @param string $url
     */
    public function setCanonical($url) {
        $this->data['canonical'] = $url;
    }
    
    /**
     * Set robots meta tag
     * @param string $robots
     */
    public function setRobots($robots) {
        $this->data['robots'] = $robots;
    }
    
    /**
     * Set Open Graph data
     * @param array $data
     */
    public function setOpenGraph($data) {
        if (isset($data['type'])) {
            $this->data['og_type'] = $data['type'];
        }
        if (isset($data['title'])) {
            $this->data['og_title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $this->data['og_description'] = $data['description'];
        }
        if (isset($data['image'])) {
            $this->data['og_image'] = $data['image'];
        }
        if (isset($data['url'])) {
            $this->data['og_url'] = $data['url'];
        }
    }
    
    /**
     * Set Twitter Card data
     * @param array $data
     */
    public function setTwitterCard($data) {
        if (isset($data['card'])) {
            $this->data['twitter_card'] = $data['card'];
        }
        if (isset($data['title'])) {
            $this->data['twitter_title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $this->data['twitter_description'] = $data['description'];
        }
        if (isset($data['image'])) {
            $this->data['twitter_image'] = $data['image'];
        }
    }
    
    /**
     * Add schema markup
     * @param array $schema
     */
    public function addSchema($schema) {
        $this->data['schema'][] = $schema;
    }
    
    /**
     * Set Article schema for posts
     * @param array $data
     */
    public function setArticleSchema($data) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'],
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? 'Admin'
            ],
            'publisher' => $this->config['seo']['schema']['organization'],
            'datePublished' => $data['published_at'] ?? date('c'),
            'dateModified' => $data['updated_at'] ?? date('c'),
        ];
        
        $this->addSchema($schema);
    }
    
    /**
     * Set Product schema for products
     * @param array $data
     */
    public function setProductSchema($data) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'sku' => $data['sku'] ?? '',
            'brand' => [
                '@type' => 'Brand',
                'name' => $data['brand'] ?? $this->config['app']['name']
            ],
        ];
        
        if (isset($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'IDR',
                'availability' => 'https://schema.org/' . ($data['in_stock'] ? 'InStock' : 'OutOfStock'),
                'url' => $data['url'] ?? ''
            ];
        }
        
        $this->addSchema($schema);
    }
    
    /**
     * Set Job Posting schema for job listings
     * @param array $data
     */
    public function setJobSchema($data) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'datePosted' => $data['posted_at'] ?? date('c'),
            'validThrough' => $data['valid_through'] ?? '',
            'employmentType' => $data['employment_type'] ?? 'FULL_TIME',
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $data['company'] ?? $this->config['app']['name'],
                'sameAs' => $this->config['app']['url'],
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $data['location'] ?? '',
                    'addressCountry' => $data['country'] ?? 'ID'
                ]
            ]
        ];
        
        if (isset($data['salary'])) {
            $schema['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $data['currency'] ?? 'IDR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => $data['salary'],
                    'unitText' => $data['salary_unit'] ?? 'MONTH'
                ]
            ];
        }
        
        $this->addSchema($schema);
    }
    
    /**
     * Set Breadcrumb schema
     * @param array $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs) {
        $this->data['breadcrumbs'] = $breadcrumbs;
        
        $itemList = [];
        $position = 1;
        
        foreach ($breadcrumbs as $crumb) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $crumb['name'],
                'item' => $crumb['url'] ?? ''
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList
        ];
        
        $this->addSchema($schema);
    }
    
    /**
     * Generate auto description from content
     * @param string $content
     * @param int $length
     * @return string
     */
    public function generateDescription($content, $length = 160) {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim to length
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length);
            $text = substr($text, 0, strrpos($text, ' ')) . '...';
        }
        
        return trim($text);
    }
    
    /**
     * Generate slug from title
     * @param string $title
     * @return string
     */
    public function generateSlug($title) {
        // Convert to lowercase
        $slug = strtolower($title);
        
        // Replace spaces with hyphens
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // Remove special characters
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from ends
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Render all meta tags
     * @return string
     */
    public function render() {
        $html = '';
        
        // Title
        $html .= '<title>' . htmlspecialchars($this->data['title']) . '</title>' . "\n";
        
        // Meta description
        if ($this->data['description']) {
            $html .= '<meta name="description" content="' . htmlspecialchars($this->data['description']) . '">' . "\n";
        }
        
        // Meta keywords
        if ($this->data['keywords']) {
            $html .= '<meta name="keywords" content="' . htmlspecialchars($this->data['keywords']) . '">' . "\n";
        }
        
        // Canonical URL
        if ($this->data['canonical']) {
            $html .= '<link rel="canonical" href="' . htmlspecialchars($this->data['canonical']) . '">' . "\n";
        }
        
        // Robots
        $html .= '<meta name="robots" content="' . htmlspecialchars($this->data['robots']) . '">' . "\n";
        
        // Open Graph
        $html .= '<meta property="og:type" content="' . htmlspecialchars($this->data['og_type']) . '">' . "\n";
        $html .= '<meta property="og:title" content="' . htmlspecialchars($this->data['og_title'] ?? $this->data['title']) . '">' . "\n";
        
        if ($this->data['og_description'] ?? $this->data['description']) {
            $html .= '<meta property="og:description" content="' . htmlspecialchars($this->data['og_description'] ?? $this->data['description']) . '">' . "\n";
        }
        
        if ($this->data['og_image']) {
            $html .= '<meta property="og:image" content="' . htmlspecialchars($this->data['og_image']) . '">' . "\n";
        }
        
        if ($this->data['og_url']) {
            $html .= '<meta property="og:url" content="' . htmlspecialchars($this->data['og_url']) . '">' . "\n";
        }
        
        // Twitter Card
        $html .= '<meta name="twitter:card" content="' . htmlspecialchars($this->data['twitter_card']) . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($this->data['twitter_title'] ?? $this->data['title']) . '">' . "\n";
        
        if ($this->data['twitter_description'] ?? $this->data['description']) {
            $html .= '<meta name="twitter:description" content="' . htmlspecialchars($this->data['twitter_description'] ?? $this->data['description']) . '">' . "\n";
        }
        
        if ($this->data['twitter_image']) {
            $html .= '<meta name="twitter:image" content="' . htmlspecialchars($this->data['twitter_image']) . '">' . "\n";
        }
        
        // Schema.org JSON-LD
        if (!empty($this->data['schema'])) {
            foreach ($this->data['schema'] as $schema) {
                $html .= '<script type="application/ld+json">' . "\n";
                $html .= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
                $html .= '</script>' . "\n";
            }
        }
        
        return $html;
    }
    
    /**
     * Get SEO data
     * @return array
     */
    public function getSEOData() {
        return $this->data;
    }
    
    /**
     * Calculate SEO score for content
     * @param array $data
     * @return array
     */
    public function calculateSEOScore($data) {
        $score = 0;
        $recommendations = [];
        
        // Check title
        if (!empty($data['title'])) {
            $titleLength = strlen($data['title']);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 15;
            } else {
                $recommendations[] = 'Title should be between 30-60 characters';
            }
        } else {
            $recommendations[] = 'Title is required';
        }
        
        // Check description
        if (!empty($data['description'])) {
            $descLength = strlen($data['description']);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 15;
            } else {
                $recommendations[] = 'Meta description should be between 120-160 characters';
            }
        } else {
            $recommendations[] = 'Meta description is required';
        }
        
        // Check slug
        if (!empty($data['slug'])) {
            if (preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
                $score += 10;
            } else {
                $recommendations[] = 'URL slug should only contain lowercase letters, numbers, and hyphens';
            }
        }
        
        // Check keywords
        if (!empty($data['keywords'])) {
            $score += 10;
        } else {
            $recommendations[] = 'Add focus keywords';
        }
        
        // Check content length
        if (!empty($data['content'])) {
            $contentLength = str_word_count(strip_tags($data['content']));
            if ($contentLength >= 300) {
                $score += 20;
            } else {
                $recommendations[] = 'Content should be at least 300 words';
            }
        }
        
        // Check image alt text
        if (!empty($data['images'])) {
            $score += 10;
        }
        
        // Check internal links
        if (!empty($data['internal_links'])) {
            $score += 10;
        } else {
            $recommendations[] = 'Add internal links to related content';
        }
        
        // Check external links
        if (!empty($data['external_links'])) {
            $score += 10;
        }
        
        return [
            'score' => $score,
            'grade' => $this->getScoreGrade($score),
            'recommendations' => $recommendations
        ];
    }
    
    /**
     * Get score grade
     * @param int $score
     * @return string
     */
    private function getScoreGrade($score) {
        if ($score >= 80) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        if ($score >= 20) return 'D';
        return 'F';
    }
}
