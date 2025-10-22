<?php

/**
 * Frontend Comment Controller
 * Handle comment submission
 */
class CommentController extends Controller {
    private $commentModel;
    
    public function __construct() {
        parent::__construct();
        $this->commentModel = new Comment();
    }
    
    /**
     * Submit comment
     */
    public function submit() {
        try {
            $data = $this->validate([
                'post_id' => 'required|numeric',
                'author_name' => 'required|min:2|max:100',
                'author_email' => 'required|email',
                'author_website' => '',
                'content' => 'required|min:10',
                'parent_id' => 'numeric'
            ]);
            
            // Sanitize data
            $data['author_name'] = $this->security->sanitize($data['author_name'], 'string');
            $data['author_email'] = $this->security->sanitize($data['author_email'], 'email');
            $data['author_website'] = $this->security->sanitize($data['author_website'] ?? '', 'url');
            $data['content'] = $this->security->sanitize($data['content'], 'html');
            
            // Add metadata
            $data['author_ip'] = $this->security->getClientIP();
            $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $data['status'] = 'pending'; // Moderate by default
            
            // If user is logged in
            if ($this->isAuthenticated()) {
                $data['user_id'] = $_SESSION['user_id'];
                $data['status'] = 'approved'; // Auto-approve for logged-in users
            }
            
            $commentId = $this->commentModel->create($data);
            
            if (!$commentId) {
                throw new Exception('Failed to submit comment');
            }
            
            // Get post slug for redirect
            $post = $this->db->selectOne("SELECT slug FROM cms_posts WHERE id = ?", [$data['post_id']]);
            
            if ($data['status'] === 'pending') {
                $this->setFlash('success', 'Your comment is awaiting moderation.');
            } else {
                $this->setFlash('success', 'Comment posted successfully!');
            }
            
            $this->redirect('/post/' . $post['slug'] . '#comments');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
}
