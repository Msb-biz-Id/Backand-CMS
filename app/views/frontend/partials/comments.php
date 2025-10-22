<div id="comments" class="mt-5">
    <h4 class="mb-4">
        Comments (<?php echo count($comments ?? []); ?>)
    </h4>

    <?php if (!empty($comments)): ?>
        <div class="comment-list mb-5">
            <?php foreach ($comments as $comment): ?>
                <?php echo renderComment($comment); ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No comments yet. Be the first to comment!</p>
    <?php endif; ?>

    <!-- Comment Form -->
    <div class="comment-form mt-5">
        <h5 class="mb-3">Leave a Comment</h5>
        
        <form method="POST" action="/comments/submit">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            
            <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="author_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="author_email" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Website (optional)</label>
                <input type="url" class="form-control" name="author_website" placeholder="https://">
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label class="form-label">Comment <span class="text-danger">*</span></label>
                <textarea class="form-control" name="content" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="ri-send-plane-line me-1"></i> Post Comment
            </button>
        </form>
    </div>
</div>

<?php
function renderComment($comment, $level = 0) {
    $indent = $level > 0 ? 'ms-' . ($level * 4) : '';
    
    $html = '<div class="comment-item border-bottom pb-3 mb-3 ' . $indent . '">';
    $html .= '<div class="d-flex">';
    $html .= '<div class="flex-shrink-0 me-3">';
    $html .= '<div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center">';
    $html .= '<span class="text-primary fw-bold">' . strtoupper(substr($comment['author_name'], 0, 1)) . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="flex-grow-1">';
    $html .= '<h6 class="mb-1">' . htmlspecialchars($comment['author_name']) . '</h6>';
    $html .= '<p class="text-muted small mb-2">' . date('F d, Y \a\t g:i A', strtotime($comment['created_at'])) . '</p>';
    $html .= '<p class="mb-2">' . nl2br(htmlspecialchars($comment['content'])) . '</p>';
    // Reply button could be added here for threaded comments
    $html .= '</div>';
    $html .= '</div>';
    
    if (!empty($comment['children'])) {
        foreach ($comment['children'] as $child) {
            $html .= renderComment($child, $level + 1);
        }
    }
    
    $html .= '</div>';
    
    return $html;
}
?>

<style>
.comment-item {
    transition: background-color 0.3s;
}
.comment-item:hover {
    background-color: #f8f9fa;
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
