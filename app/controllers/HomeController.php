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
     * Homepage (Dynamic like WordPress)
     */
    public function index() {
        // SEO: Set page title and meta
        $this->seo->setTitle('Home - Advanced CMS', false);
        $this->seo->setDescription('Modern CMS with advanced SEO features, e-commerce, and AI content generation');
        $this->seo->setKeywords('cms, seo, content management, e-commerce, ai, php');
        
        // Set Open Graph
        $this->seo->setOpenGraph([
            'type' => 'website',
            'title' => 'Advanced CMS - Modern Content Management',
            'description' => 'Built with PHP Native OOP/MVC - E-commerce Ready + AI Powered',
            'url' => $this->config['app']['url']
        ]);
        
        // Get dynamic content
        $postModel = new Post();
        $productModel = new Product();
        $jobModel = new Job();
        
        // Recent posts
        $recentPosts = $postModel->getPublished(6);
        
        // Featured products
        $featuredProducts = $productModel->getFeatured(8);
        
        // Latest jobs
        $latestJobs = $jobModel->getActive(3);
        
        // Statistics
        $stats = [
            'total_posts' => $postModel->count(['status' => 'published']),
            'total_products' => $productModel->count(['status' => 'published']),
            'total_jobs' => $jobModel->count(['status' => 'active'])
        ];
        
        $this->view('frontend/home', [
            'recentPosts' => $recentPosts,
            'featuredProducts' => $featuredProducts,
            'latestJobs' => $latestJobs,
            'stats' => $stats
        ], 'frontend');
    }
}
