<?php

/**
 * Admin Comment Controller
 * Comment moderation & management
 */
class CommentController extends Controller {
    private $commentModel;
    
    public function __construct() {
        parent::__construct();
        $this->commentModel = new Comment();
    }
    
    /**
     * List all comments
     */
    public function index() {
        $this->requireAuth();
        
        $page = $this->input('page', 1);
        $status = $this->input('status');
        $search = $this->input('search');
        
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }
        
        if ($search) {
            $sql = "SELECT c.*, p.title as post_title
                    FROM cms_comments c
                    LEFT JOIN cms_posts p ON c.post_id = p.id
                    WHERE (c.author_name LIKE ? OR c.content LIKE ?)
                    AND c.deleted_at IS NULL
                    ORDER BY c.created_at DESC";
            
            $searchTerm = "%{$search}%";
            $comments = $this->db->select($sql, [$searchTerm, $searchTerm]);
            $pagination = null;
        } else {
            $result = $this->commentModel->paginate($page, 20, $filters, 'created_at DESC');
            
            // Get post info for each comment
            foreach ($result['data'] as &$comment) {
                $post = $this->db->selectOne("SELECT title, slug FROM cms_posts WHERE id = ?", [$comment['post_id']]);
                $comment['post_title'] = $post['title'] ?? 'Unknown';
                $comment['post_slug'] = $post['slug'] ?? '';
            }
            
            $comments = $result['data'];
            $pagination = $result['pagination'];
        }
        
        $stats = $this->commentModel->getStatistics();
        
        $this->seo->setTitle('Comments');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/comments/index', [
            'comments' => $comments,
            'pagination' => $pagination,
            'status' => $status,
            'search' => $search,
            'stats' => $stats
        ]);
    }
    
    /**
     * Approve comment
     */
    public function approve($id) {
        $this->requireAuth();
        
        try {
            $comment = $this->commentModel->find($id);
            
            if (!$comment) {
                throw new Exception('Comment not found');
            }
            
            $this->commentModel->approve($id);
            
            $this->logActivity('approve', 'comment', $id, 'Approved comment');
            
            $this->setFlash('success', 'Comment approved!');
            $this->back();
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Reject comment
     */
    public function reject($id) {
        $this->requireAuth();
        
        try {
            $comment = $this->commentModel->find($id);
            
            if (!$comment) {
                throw new Exception('Comment not found');
            }
            
            $this->commentModel->reject($id);
            
            $this->logActivity('reject', 'comment', $id, 'Rejected comment');
            
            $this->setFlash('success', 'Comment rejected!');
            $this->back();
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Mark as spam
     */
    public function spam($id) {
        $this->requireAuth();
        
        try {
            $comment = $this->commentModel->find($id);
            
            if (!$comment) {
                throw new Exception('Comment not found');
            }
            
            $this->commentModel->markAsSpam($id);
            
            $this->logActivity('spam', 'comment', $id, 'Marked comment as spam');
            
            $this->setFlash('success', 'Comment marked as spam!');
            $this->back();
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Delete comment
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $comment = $this->commentModel->find($id);
            
            if (!$comment) {
                throw new Exception('Comment not found');
            }
            
            $this->commentModel->delete($id);
            
            $this->logActivity('delete', 'comment', $id, 'Deleted comment');
            
            $this->setFlash('success', 'Comment deleted!');
            $this->back();
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Bulk action
     */
    public function bulkAction() {
        $this->requireAuth();
        
        try {
            $action = $this->input('action');
            $commentIds = $this->input('comment_ids', []);
            
            if (empty($commentIds)) {
                throw new Exception('No comments selected');
            }
            
            foreach ($commentIds as $id) {
                switch ($action) {
                    case 'approve':
                        $this->commentModel->approve($id);
                        break;
                    case 'reject':
                        $this->commentModel->reject($id);
                        break;
                    case 'spam':
                        $this->commentModel->markAsSpam($id);
                        break;
                    case 'delete':
                        $this->commentModel->delete($id);
                        break;
                }
            }
            
            $this->setFlash('success', 'Bulk action completed!');
            $this->back();
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
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
