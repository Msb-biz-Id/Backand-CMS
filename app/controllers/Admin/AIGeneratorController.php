<?php

/**
 * AI Article Generator Controller
 * Powered by Google Gemini AI
 */
class AIGeneratorController extends Controller {
    private $geminiAI;
    private $postModel;
    
    public function __construct() {
        parent::__construct();
        $this->geminiAI = new GeminiAI();
        $this->postModel = new Post();
    }
    
    /**
     * Show AI generator interface
     */
    public function index() {
        $this->requireAuth();
        
        // Get categories for selection
        $categoryModel = new Category();
        $categories = $categoryModel->getByType('post');
        
        $this->seo->setTitle('AI Article Generator');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/ai/generator', [
            'categories' => $categories
        ]);
    }
    
    /**
     * Generate article with AI
     */
    public function generate() {
        $this->requireAuth();
        
        try {
            $data = $this->validate([
                'keyword' => 'required|min:3',
                'category_id' => '',
                'style' => 'required|in:professional,academic,casual,journalistic,storytelling',
                'length' => 'required|in:short,medium,long,very_long'
            ]);
            
            // Get category name if provided
            $category = '';
            if (!empty($data['category_id'])) {
                $categoryModel = new Category();
                $cat = $categoryModel->find($data['category_id']);
                $category = $cat['name'] ?? '';
            }
            
            // Generate article
            $result = $this->geminiAI->generateArticle(
                $data['keyword'],
                $category,
                $data['style'],
                $data['length']
            );
            
            // Log generation
            $this->logActivity('generate', 'ai_article', null, 'Generated article: ' . $result['title']);
            
            $this->json([
                'success' => true,
                'data' => $result,
                'message' => 'Article generated successfully!'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Save generated article as post
     */
    public function saveAsPost() {
        $this->requireAuth();
        
        try {
            $data = $this->validate([
                'title' => 'required',
                'content' => 'required',
                'meta_description' => '',
                'keyword' => '',
                'category_id' => '',
                'status' => 'required|in:draft,published'
            ]);
            
            // Generate slug
            $data['slug'] = $this->generateSlug($data['title']);
            $data['user_id'] = $_SESSION['user_id'];
            $data['excerpt'] = $data['meta_description'] ?? substr(strip_tags($data['content']), 0, 160);
            
            // Create post
            $postId = $this->postModel->create($data);
            
            if (!$postId) {
                throw new Exception('Failed to create post');
            }
            
            // Add category if provided
            if (!empty($data['category_id'])) {
                $this->db->insert('post_categories', [
                    'post_id' => $postId,
                    'category_id' => $data['category_id']
                ]);
            }
            
            // Create SEO data
            if (!empty($data['meta_description'])) {
                $this->db->insert('post_seo', [
                    'post_id' => $postId,
                    'meta_title' => $data['title'],
                    'meta_description' => $data['meta_description'],
                    'focus_keyword' => $data['keyword'] ?? '',
                    'canonical_url' => '',
                    'robots' => 'index, follow'
                ]);
            }
            
            $this->logActivity('create', 'post', $postId, 'Created post from AI: ' . $data['title']);
            
            $this->json([
                'success' => true,
                'post_id' => $postId,
                'message' => 'Post created successfully!',
                'redirect' => '/admin/posts/' . $postId . '/edit'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Generate slug
     */
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check uniqueness
        $original = $slug;
        $counter = 1;
        
        while ($this->postModel->findOne(['slug' => $slug])) {
            $slug = $original . '-' . $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $subjectType, $subjectId, $description) {
        $this->db->insert('activity_log', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'ip_address' => $this->security->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
