<?php

/**
 * Frontend Page Controller
 * Static pages display (dynamic like WordPress)
 */
class PageController extends Controller {
    private $pageModel;
    
    public function __construct() {
        parent::__construct();
        $this->pageModel = new Page();
    }
    
    /**
     * Display page by slug (dynamic)
     */
    public function show($slug) {
        $page = $this->pageModel->getBySlug($slug);
        
        if (!$page || $page['status'] !== 'published') {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        // Increment view count
        $this->pageModel->incrementViews($page['id']);
        
        // Get child pages if this is a parent
        $childPages = $this->pageModel->findAll(['parent_id' => $page['id'], 'status' => 'published'], 'order ASC');
        
        // SEO from page data
        $this->seo->setTitle($page['meta_title'] ?? $page['title']);
        $this->seo->setDescription($page['meta_description'] ?? substr(strip_tags($page['content']), 0, 160));
        $this->seo->setKeywords($page['meta_keywords'] ?? '');
        $this->seo->setCanonicalUrl('/page/' . $page['slug']);
        
        // Open Graph
        $this->seo->setOpenGraph([
            'type' => 'website',
            'title' => $page['title'],
            'description' => substr(strip_tags($page['content']), 0, 200),
            'url' => '/page/' . $page['slug'],
            'image' => $page['featured_image'] ?? ''
        ]);
        
        // Check if page has custom template
        $template = $page['template'] ?? 'default';
        
        // Load template-specific view if exists
        $viewPath = 'frontend/pages/' . $template;
        if (!file_exists(__DIR__ . '/../views/' . $viewPath . '.php')) {
            $viewPath = 'frontend/pages/show';
        }
        
        $this->view($viewPath, [
            'page' => $page,
            'childPages' => $childPages
        ], 'frontend');
    }
    
    /**
     * Contact form
     */
    public function contact() {
        $this->seo->setTitle('Contact Us');
        $this->seo->setDescription('Get in touch with us');
        $this->seo->setRobots('noindex, follow');
        
        $this->view('frontend/pages/contact', [], 'frontend');
    }
    
    /**
     * Process contact form
     */
    public function sendContact() {
        try {
            $this->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'subject' => 'required|min:5',
                'message' => 'required|min:10'
            ]);
            
            $data = $this->input();
            
            // Save to database (contact submissions)
            $this->db->insert('contact_submissions', [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'ip_address' => $this->security->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            // TODO: Send email notification
            
            $this->setFlash('success', 'Thank you! Your message has been sent.');
            $this->redirect('/contact');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
}
