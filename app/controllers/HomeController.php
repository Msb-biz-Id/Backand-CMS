<?php

/**
 * Home Controller
 * Frontend homepage
 */
class HomeController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Homepage
     */
    public function index() {
        // SEO: Set page title and meta
        $this->seo->setTitle('Home - Advanced CMS', false);
        $this->seo->setDescription('Modern CMS with advanced SEO features');
        $this->seo->setKeywords('cms, seo, content management, php');
        
        // Set Open Graph
        $this->seo->setOpenGraph([
            'type' => 'website',
            'title' => 'Advanced CMS - Modern Content Management',
            'description' => 'Built with PHP Native OOP/MVC',
            'url' => $this->config['app']['url']
        ]);
        
        // Get recent posts (if any)
        $postModel = new Post();
        $recentPosts = $postModel->getPublished(6);
        
        $this->view('frontend/home', [
            'recentPosts' => $recentPosts
        ], 'frontend');
    }
}
