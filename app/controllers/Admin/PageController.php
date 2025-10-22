<?php

/**
 * Admin Page Controller
 * CRUD untuk static pages
 */
class PageController extends Controller {
    private $pageModel;
    
    public function __construct() {
        parent::__construct();
        $this->pageModel = new Page();
    }
    
    /**
     * List all pages
     */
    public function index() {
        $this->requireAuth();
        
        $page = $this->input('page', 1);
        $search = $this->input('search');
        
        if ($search) {
            $pages = $this->pageModel->search($search);
            $pagination = null;
        } else {
            $result = $this->pageModel->paginate($page, 15, [], 'created_at DESC');
            $pages = $result['data'];
            $pagination = $result['pagination'];
        }
        
        $this->seo->setTitle('All Pages');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/pages/index', [
            'pages' => $pages,
            'pagination' => $pagination,
            'search' => $search
        ]);
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        // Get all pages for parent selection
        $allPages = $this->pageModel->getPublished();
        
        $this->seo->setTitle('Add New Page');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/pages/create', [
            'allPages' => $allPages,
            'extraJs' => ['https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js']
        ]);
    }
    
    /**
     * Store new page
     */
    public function store() {
        $this->requireAuth();
        
        try {
            $data = $this->validate([
                'title' => 'required|min:3|max:255',
                'slug' => 'required|slug|unique:pages,slug',
                'content' => 'required|min:10',
                'status' => 'required|in:draft,published,private',
                'template' => '',
                'parent_id' => '',
                'featured_image' => '',
                'order' => 'numeric',
                'show_in_menu' => '',
                'menu_order' => 'numeric'
            ]);
            
            $data['user_id'] = $_SESSION['user_id'];
            $data['show_in_menu'] = isset($data['show_in_menu']) ? 1 : 0;
            $data['order'] = $data['order'] ?? 0;
            $data['menu_order'] = $data['menu_order'] ?? 0;
            
            $this->pageModel->beginTransaction();
            
            $pageId = $this->pageModel->create($data);
            
            if (!$pageId) {
                throw new Exception('Failed to create page');
            }
            
            // Handle SEO data
            if ($this->input('seo_enabled')) {
                $this->saveSEOData($pageId);
            }
            
            $this->pageModel->commit();
            
            $this->logActivity('create', 'page', $pageId, 'Created page: ' . $data['title']);
            
            $this->cache->forget('pages_list');
            $this->cache->forget('menu_pages');
            
            $this->setFlash('success', 'Page created successfully!');
            $this->redirect('/admin/pages');
            
        } catch (Exception $e) {
            $this->pageModel->rollback();
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $this->requireAuth();
        
        $page = $this->pageModel->getWithSEO($id);
        
        if (!$page) {
            $this->setFlash('error', 'Page not found');
            $this->redirect('/admin/pages');
        }
        
        $allPages = $this->pageModel->getPublished();
        
        $this->seo->setTitle('Edit Page: ' . $page['title']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/pages/edit', [
            'page' => $page,
            'allPages' => $allPages,
            'extraJs' => ['https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js']
        ]);
    }
    
    /**
     * Update page
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $page = $this->pageModel->find($id);
            
            if (!$page) {
                throw new Exception('Page not found');
            }
            
            $data = $this->validate([
                'title' => 'required|min:3|max:255',
                'slug' => 'required|slug',
                'content' => 'required|min:10',
                'status' => 'required|in:draft,published,private',
                'template' => '',
                'parent_id' => '',
                'featured_image' => '',
                'order' => 'numeric',
                'show_in_menu' => '',
                'menu_order' => 'numeric'
            ]);
            
            $data['show_in_menu'] = isset($data['show_in_menu']) ? 1 : 0;
            
            $this->pageModel->beginTransaction();
            
            $this->pageModel->update($id, $data);
            
            if ($this->input('seo_enabled')) {
                $this->saveSEOData($id);
            }
            
            $this->pageModel->commit();
            
            $this->logActivity('update', 'page', $id, 'Updated page: ' . $data['title']);
            
            $this->cache->forget('pages_list');
            $this->cache->forget('page_' . $id);
            
            $this->setFlash('success', 'Page updated successfully!');
            $this->redirect('/admin/pages');
            
        } catch (Exception $e) {
            $this->pageModel->rollback();
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Delete page
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $page = $this->pageModel->find($id);
            
            if (!$page) {
                throw new Exception('Page not found');
            }
            
            $this->pageModel->delete($id);
            
            $this->logActivity('delete', 'page', $id, 'Deleted page: ' . $page['title']);
            
            $this->cache->forget('pages_list');
            
            $this->setFlash('success', 'Page deleted successfully!');
            $this->redirect('/admin/pages');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Save SEO data
     */
    private function saveSEOData($pageId) {
        $seoData = [
            'page_id' => $pageId,
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'meta_keywords' => $this->input('meta_keywords'),
            'canonical_url' => $this->input('canonical_url'),
            'robots' => $this->input('robots', 'index, follow')
        ];
        
        $existingSEO = $this->db->selectOne("SELECT id FROM cms_page_seo WHERE page_id = ?", [$pageId]);
        
        if ($existingSEO) {
            $this->db->update('page_seo', $seoData, ['page_id' => $pageId]);
        } else {
            $this->db->insert('page_seo', $seoData);
        }
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
