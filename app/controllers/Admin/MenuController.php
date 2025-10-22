<?php

/**
 * Admin Menu Controller
 * Menu & Menu Item CRUD
 */
class MenuController extends Controller {
    private $menuModel;
    private $menuItemModel;
    
    public function __construct() {
        parent::__construct();
        $this->menuModel = new Menu();
        $this->menuItemModel = new MenuItem();
    }
    
    /**
     * List all menus
     */
    public function index() {
        $this->requireAuth();
        
        $menus = $this->menuModel->findAll([], 'created_at DESC');
        $locations = $this->menuModel->getLocations();
        
        $this->seo->setTitle('Menus');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/menus/index', [
            'menus' => $menus,
            'locations' => $locations
        ]);
    }
    
    /**
     * Create new menu
     */
    public function create() {
        $this->requireAuth();
        
        $locations = $this->menuModel->getLocations();
        
        $this->seo->setTitle('Create Menu');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/menus/create', [
            'locations' => $locations
        ]);
    }
    
    /**
     * Store new menu
     */
    public function store() {
        $this->requireAuth();
        
        try {
            $data = $this->validate([
                'name' => 'required|min:3|max:255',
                'slug' => 'required|slug|unique:menus,slug',
                'location' => '',
                'description' => ''
            ]);
            
            $menuId = $this->menuModel->create($data);
            
            if (!$menuId) {
                throw new Exception('Failed to create menu');
            }
            
            $this->logActivity('create', 'menu', $menuId, 'Created menu: ' . $data['name']);
            
            $this->setFlash('success', 'Menu created successfully!');
            $this->redirect('/admin/menus/' . $menuId . '/builder');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Menu builder (drag & drop interface)
     */
    public function builder($id) {
        $this->requireAuth();
        
        $menu = $this->menuModel->getMenuWithItems($id);
        
        if (!$menu) {
            $this->setFlash('error', 'Menu not found');
            $this->redirect('/admin/menus');
        }
        
        // Get available pages
        $pageModel = new Page();
        $pages = $pageModel->getPublished();
        
        // Get available posts (recent)
        $postModel = new Post();
        $posts = $postModel->getPublished(20);
        
        // Get categories
        $categoryModel = new Category();
        $categories = $categoryModel->getByType('post');
        
        $this->seo->setTitle('Menu Builder: ' . $menu['name']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/menus/builder', [
            'menu' => $menu,
            'pages' => $pages,
            'posts' => $posts,
            'categories' => $categories,
            'extraJs' => ['https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js']
        ]);
    }
    
    /**
     * Add menu item
     */
    public function addItem($menuId) {
        $this->requireAuth();
        
        try {
            $menu = $this->menuModel->find($menuId);
            
            if (!$menu) {
                throw new Exception('Menu not found');
            }
            
            $data = $this->validate([
                'type' => 'required|in:custom,page,post,category',
                'title' => 'required|min:1|max:255',
                'url' => '',
                'target' => '',
                'icon' => '',
                'css_class' => '',
                'parent_id' => '',
                'object_id' => ''
            ]);
            
            $data['menu_id'] = $menuId;
            
            // Build URL based on type
            if ($data['type'] !== 'custom') {
                $data['url'] = $this->buildMenuItemUrl($data['type'], $data['object_id']);
            }
            
            $itemId = $this->menuItemModel->createItem($data);
            
            if (!$itemId) {
                throw new Exception('Failed to add menu item');
            }
            
            $this->logActivity('create', 'menu_item', $itemId, 'Added menu item: ' . $data['title']);
            
            $this->json([
                'success' => true,
                'message' => 'Menu item added successfully',
                'item_id' => $itemId
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Update menu item
     */
    public function updateItem($id) {
        $this->requireAuth();
        
        try {
            $item = $this->menuItemModel->find($id);
            
            if (!$item) {
                throw new Exception('Menu item not found');
            }
            
            $data = $this->validate([
                'title' => 'required|min:1|max:255',
                'url' => '',
                'target' => '',
                'icon' => '',
                'css_class' => '',
                'parent_id' => ''
            ]);
            
            $this->menuItemModel->update($id, $data);
            
            $this->logActivity('update', 'menu_item', $id, 'Updated menu item: ' . $data['title']);
            
            $this->json([
                'success' => true,
                'message' => 'Menu item updated successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Delete menu item
     */
    public function deleteItem($id) {
        $this->requireAuth();
        
        try {
            $item = $this->menuItemModel->find($id);
            
            if (!$item) {
                throw new Exception('Menu item not found');
            }
            
            $this->menuItemModel->delete($id);
            
            $this->logActivity('delete', 'menu_item', $id, 'Deleted menu item: ' . $item['title']);
            
            $this->json([
                'success' => true,
                'message' => 'Menu item deleted successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Reorder menu items
     */
    public function reorder($menuId) {
        $this->requireAuth();
        
        try {
            $items = $this->input('items');
            
            if (!is_array($items)) {
                throw new Exception('Invalid items data');
            }
            
            $this->menuItemModel->reorder($menuId, $items);
            
            $this->json([
                'success' => true,
                'message' => 'Menu order updated successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Edit menu
     */
    public function edit($id) {
        $this->requireAuth();
        
        $menu = $this->menuModel->find($id);
        
        if (!$menu) {
            $this->setFlash('error', 'Menu not found');
            $this->redirect('/admin/menus');
        }
        
        $locations = $this->menuModel->getLocations();
        
        $this->seo->setTitle('Edit Menu: ' . $menu['name']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/menus/edit', [
            'menu' => $menu,
            'locations' => $locations
        ]);
    }
    
    /**
     * Update menu
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $menu = $this->menuModel->find($id);
            
            if (!$menu) {
                throw new Exception('Menu not found');
            }
            
            $data = $this->validate([
                'name' => 'required|min:3|max:255',
                'slug' => 'required|slug',
                'location' => '',
                'description' => ''
            ]);
            
            $this->menuModel->update($id, $data);
            
            $this->logActivity('update', 'menu', $id, 'Updated menu: ' . $data['name']);
            
            $this->setFlash('success', 'Menu updated successfully!');
            $this->redirect('/admin/menus');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Delete menu
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $menu = $this->menuModel->find($id);
            
            if (!$menu) {
                throw new Exception('Menu not found');
            }
            
            // Delete all menu items
            $this->db->delete('menu_items', ['menu_id' => $id]);
            
            // Delete menu
            $this->menuModel->delete($id);
            
            $this->logActivity('delete', 'menu', $id, 'Deleted menu: ' . $menu['name']);
            
            $this->setFlash('success', 'Menu deleted successfully!');
            $this->redirect('/admin/menus');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Build menu item URL based on type
     */
    private function buildMenuItemUrl($type, $objectId) {
        switch ($type) {
            case 'page':
                $pageModel = new Page();
                $page = $pageModel->find($objectId);
                return $page ? '/page/' . $page['slug'] : '#';
                
            case 'post':
                $postModel = new Post();
                $post = $postModel->find($objectId);
                return $post ? '/post/' . $post['slug'] : '#';
                
            case 'category':
                $categoryModel = new Category();
                $category = $categoryModel->find($objectId);
                return $category ? '/category/' . $category['slug'] : '#';
                
            default:
                return '#';
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
