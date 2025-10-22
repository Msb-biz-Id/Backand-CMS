<?php

/**
 * Admin Post Controller
 * CRUD operations untuk Posts dengan SEO, Scheduling, dan Auto-archive
 */
class PostController extends Controller {
    private $postModel;
    private $categoryModel;
    private $tagModel;
    
    public function __construct() {
        parent::__construct();
        $this->postModel = new Post();
    }
    
    /**
     * List all posts
     */
    public function index() {
        $this->requireAuth();
        
        // Get pagination parameters
        $page = $this->input('page', 1);
        $perPage = 15;
        
        // Get filters
        $status = $this->input('status');
        $search = $this->input('search');
        
        // Build conditions
        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }
        
        // Get posts with pagination
        if ($search) {
            $posts = $this->postModel->search($search);
        } else {
            $result = $this->postModel->paginate($page, $perPage, $conditions, 'created_at DESC');
            $posts = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // SEO
        $this->seo->setTitle('All Posts');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/posts/index', [
            'posts' => $posts,
            'pagination' => $pagination ?? null,
            'status' => $status,
            'search' => $search
        ]);
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        // SEO
        $this->seo->setTitle('Add New Post');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/posts/create', [
            'extraCss' => ['/assets/libs/select2/css/select2.min.css'],
            'extraJs' => [
                'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js',
                '/assets/libs/select2/js/select2.min.js'
            ]
        ]);
    }
    
    /**
     * Store new post
     */
    public function store() {
        $this->requireAuth();
        
        try {
            // Validate input
            $data = $this->validate([
                'title' => 'required|min:3|max:255',
                'slug' => 'required|slug|unique:posts,slug',
                'content' => 'required|min:10',
                'excerpt' => 'max:500',
                'status' => 'required|in:draft,published,scheduled',
                'featured_image' => '',
                'published_at' => '',
                'scheduled_at' => '',
                'auto_archive_at' => '',
                'allow_comments' => '',
                'is_featured' => '',
            ]);
            
            // Set user_id
            $data['user_id'] = $_SESSION['user_id'];
            
            // Handle checkboxes
            $data['allow_comments'] = isset($data['allow_comments']) ? 1 : 0;
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            
            // Calculate reading time
            $wordCount = str_word_count(strip_tags($data['content']));
            $data['reading_time'] = ceil($wordCount / 200); // 200 words per minute
            
            // Auto-generate excerpt if not provided
            if (empty($data['excerpt'])) {
                $data['excerpt'] = $this->seo->generateDescription($data['content'], 160);
            }
            
            // Handle dates
            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
            
            if ($data['status'] === 'scheduled' && !empty($data['scheduled_at'])) {
                $data['published_at'] = $data['scheduled_at'];
            }
            
            // Begin transaction
            $this->postModel->beginTransaction();
            
            // Create post
            $postId = $this->postModel->create($data);
            
            if (!$postId) {
                throw new Exception('Failed to create post');
            }
            
            // Handle SEO data
            if ($this->input('seo_enabled')) {
                $this->saveSEOData($postId);
            }
            
            // Handle categories
            if ($categories = $this->input('categories')) {
                $this->attachCategories($postId, $categories);
            }
            
            // Handle tags
            if ($tags = $this->input('tags')) {
                $this->attachTags($postId, $tags);
            }
            
            // Commit transaction
            $this->postModel->commit();
            
            // Log activity
            $this->logActivity('create', 'post', $postId, 'Created post: ' . $data['title']);
            
            // Clear cache
            $this->cache->forget('posts_list');
            $this->cache->forget('home_posts');
            
            if ($this->security->isAjax()) {
                $this->success(['id' => $postId], 'Post created successfully');
            } else {
                $this->setFlash('success', 'Post created successfully!');
                $this->redirect('/admin/posts');
            }
            
        } catch (Exception $e) {
            $this->postModel->rollback();
            
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 400);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $this->requireAuth();
        
        $post = $this->postModel->getWithRelations($id);
        
        if (!$post) {
            $this->setFlash('error', 'Post not found');
            $this->redirect('/admin/posts');
        }
        
        // Check permission (only owner or admin/editor can edit)
        if (!$this->canEditPost($post)) {
            $this->setFlash('error', 'You do not have permission to edit this post');
            $this->redirect('/admin/posts');
        }
        
        // SEO
        $this->seo->setTitle('Edit Post: ' . $post['title']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/posts/edit', [
            'post' => $post,
            'extraCss' => ['/assets/libs/select2/css/select2.min.css'],
            'extraJs' => [
                'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js',
                '/assets/libs/select2/js/select2.min.js'
            ]
        ]);
    }
    
    /**
     * Update post
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $post = $this->postModel->find($id);
            
            if (!$post) {
                throw new Exception('Post not found');
            }
            
            // Check permission
            if (!$this->canEditPost($post)) {
                throw new Exception('You do not have permission to edit this post');
            }
            
            // Validate input
            $data = $this->validate([
                'title' => 'required|min:3|max:255',
                'slug' => 'required|slug',
                'content' => 'required|min:10',
                'excerpt' => 'max:500',
                'status' => 'required|in:draft,published,scheduled,archived',
                'featured_image' => '',
                'published_at' => '',
                'scheduled_at' => '',
                'auto_archive_at' => '',
                'allow_comments' => '',
                'is_featured' => '',
            ]);
            
            // Handle checkboxes
            $data['allow_comments'] = isset($data['allow_comments']) ? 1 : 0;
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            
            // Calculate reading time
            $wordCount = str_word_count(strip_tags($data['content']));
            $data['reading_time'] = ceil($wordCount / 200);
            
            // Auto-generate excerpt if not provided
            if (empty($data['excerpt'])) {
                $data['excerpt'] = $this->seo->generateDescription($data['content'], 160);
            }
            
            // Begin transaction
            $this->postModel->beginTransaction();
            
            // Update post
            $this->postModel->update($id, $data);
            
            // Handle SEO data
            if ($this->input('seo_enabled')) {
                $this->saveSEOData($id);
            }
            
            // Handle categories
            if ($categories = $this->input('categories')) {
                $this->syncCategories($id, $categories);
            }
            
            // Handle tags
            if ($tags = $this->input('tags')) {
                $this->syncTags($id, $tags);
            }
            
            // Save revision
            $this->saveRevision($id, $post, $data);
            
            // Commit transaction
            $this->postModel->commit();
            
            // Log activity
            $this->logActivity('update', 'post', $id, 'Updated post: ' . $data['title']);
            
            // Clear cache
            $this->cache->forget('posts_list');
            $this->cache->forget('post_' . $id);
            $this->cache->forget('post_' . $data['slug']);
            
            if ($this->security->isAjax()) {
                $this->success(null, 'Post updated successfully');
            } else {
                $this->setFlash('success', 'Post updated successfully!');
                $this->redirect('/admin/posts');
            }
            
        } catch (Exception $e) {
            $this->postModel->rollback();
            
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 400);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Delete post
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $post = $this->postModel->find($id);
            
            if (!$post) {
                throw new Exception('Post not found');
            }
            
            // Check permission (only admin or owner can delete)
            if (!$this->hasRole(['admin', 'editor']) && $post['user_id'] != $_SESSION['user_id']) {
                throw new Exception('You do not have permission to delete this post');
            }
            
            // Soft delete
            $this->postModel->delete($id);
            
            // Log activity
            $this->logActivity('delete', 'post', $id, 'Deleted post: ' . $post['title']);
            
            // Clear cache
            $this->cache->forget('posts_list');
            $this->cache->forget('post_' . $id);
            
            if ($this->security->isAjax()) {
                $this->success(null, 'Post deleted successfully');
            } else {
                $this->setFlash('success', 'Post deleted successfully!');
                $this->redirect('/admin/posts');
            }
            
        } catch (Exception $e) {
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 400);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Save SEO data
     */
    private function saveSEOData($postId) {
        $seoData = [
            'post_id' => $postId,
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'meta_keywords' => $this->input('meta_keywords'),
            'focus_keyword' => $this->input('focus_keyword'),
            'canonical_url' => $this->input('canonical_url'),
            'robots' => $this->input('robots', 'index, follow'),
            'og_title' => $this->input('og_title'),
            'og_description' => $this->input('og_description'),
            'og_image' => $this->input('og_image'),
        ];
        
        // Check if SEO data exists
        $existingSEO = $this->db->selectOne("SELECT id FROM cms_post_seo WHERE post_id = ?", [$postId]);
        
        if ($existingSEO) {
            $this->db->update('post_seo', $seoData, ['post_id' => $postId]);
        } else {
            $this->db->insert('post_seo', $seoData);
        }
    }
    
    /**
     * Attach categories to post
     */
    private function attachCategories($postId, $categories) {
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        
        foreach ($categories as $categoryId) {
            $this->db->insert('post_categories', [
                'post_id' => $postId,
                'category_id' => $categoryId
            ]);
        }
    }
    
    /**
     * Sync categories (for update)
     */
    private function syncCategories($postId, $categories) {
        // Delete existing
        $this->db->delete('post_categories', ['post_id' => $postId]);
        
        // Attach new
        $this->attachCategories($postId, $categories);
    }
    
    /**
     * Attach tags to post
     */
    private function attachTags($postId, $tags) {
        if (!is_array($tags)) {
            $tags = [$tags];
        }
        
        foreach ($tags as $tagId) {
            $this->db->insert('post_tags', [
                'post_id' => $postId,
                'tag_id' => $tagId
            ]);
        }
    }
    
    /**
     * Sync tags (for update)
     */
    private function syncTags($postId, $tags) {
        // Delete existing
        $this->db->delete('post_tags', ['post_id' => $postId]);
        
        // Attach new
        $this->attachTags($postId, $tags);
    }
    
    /**
     * Save revision
     */
    private function saveRevision($postId, $oldData, $newData) {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }
        
        if (!empty($changes)) {
            $this->db->insert('revisions', [
                'revisionable_type' => 'post',
                'revisionable_id' => $postId,
                'user_id' => $_SESSION['user_id'],
                'content_data' => json_encode($newData),
                'changes' => json_encode($changes),
                'ip_address' => $this->security->getClientIP()
            ]);
        }
    }
    
    /**
     * Check if user can edit post
     */
    private function canEditPost($post) {
        // Admin and editor can edit all posts
        if ($this->hasRole(['admin', 'editor'])) {
            return true;
        }
        
        // Author can edit own posts
        if ($post['user_id'] == $_SESSION['user_id']) {
            return true;
        }
        
        return false;
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
