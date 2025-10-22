<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Menu Builder: <?php echo htmlspecialchars($menu['name']); ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/menus">Menus</a></li>
                    <li class="breadcrumb-item active">Builder</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Add Items Panel -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Menu Items</h5>
            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pages-tab" role="tab">
                            Pages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#posts-tab" role="tab">
                            Posts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#custom-tab" role="tab">
                            Custom
                        </a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Pages Tab -->
                    <div class="tab-pane active" id="pages-tab" role="tabpanel">
                        <div class="mb-3">
                            <input type="search" class="form-control form-control-sm" placeholder="Search pages..." id="search-pages">
                        </div>
                        <div class="list-group list-group-flush" id="pages-list" style="max-height: 300px; overflow-y: auto;">
                            <?php if (!empty($pages)): ?>
                                <?php foreach ($pages as $page): ?>
                                    <div class="list-group-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="<?php echo $page['id']; ?>" id="page-<?php echo $page['id']; ?>">
                                            <label class="form-check-label" for="page-<?php echo $page['id']; ?>">
                                                <?php echo htmlspecialchars($page['title']); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">No pages found</p>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-3 w-100" onclick="addSelectedPages()">
                            Add Selected Pages
                        </button>
                    </div>

                    <!-- Posts Tab -->
                    <div class="tab-pane" id="posts-tab" role="tabpanel">
                        <div class="mb-3">
                            <input type="search" class="form-control form-control-sm" placeholder="Search posts..." id="search-posts">
                        </div>
                        <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                            <?php if (!empty($posts)): ?>
                                <?php foreach ($posts as $post): ?>
                                    <div class="list-group-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="<?php echo $post['id']; ?>" id="post-<?php echo $post['id']; ?>">
                                            <label class="form-check-label" for="post-<?php echo $post['id']; ?>">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-3 w-100" onclick="addSelectedPosts()">
                            Add Selected Posts
                        </button>
                    </div>

                    <!-- Custom Link Tab -->
                    <div class="tab-pane" id="custom-tab" role="tabpanel">
                        <form id="custom-link-form">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" name="url" placeholder="https://" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Target</label>
                                <select class="form-select" name="target">
                                    <option value="">Same Window</option>
                                    <option value="_blank">New Window</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icon (optional)</label>
                                <input type="text" class="form-control" name="icon" placeholder="ri-home-line">
                                <small class="text-muted">Remix Icon class</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-add-line me-1"></i> Add Custom Link
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Menu Structure -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Menu Structure</h5>
                <a href="/admin/menus" class="btn btn-sm btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back to Menus
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Tip:</strong> Drag and drop to reorder items. Click to edit.
                </div>

                <div id="menu-items-container">
                    <?php if (!empty($menu['items'])): ?>
                        <ul class="list-group" id="sortable-menu">
                            <?php foreach ($menu['items'] as $item): ?>
                                <?php echo renderMenuItem($item); ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5" id="empty-state">
                            <i class="ri-menu-line display-3 text-muted"></i>
                            <p class="text-muted mt-3">No menu items yet. Add items from the left panel.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-success" onclick="saveMenuOrder()">
                        <i class="ri-save-line me-1"></i> Save Menu
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function renderMenuItem($item, $level = 0) {
    $indent = str_repeat('  ', $level);
    $html = '<li class="list-group-item" data-id="' . $item['id'] . '" style="margin-left: ' . ($level * 20) . 'px;">';
    $html .= '<div class="d-flex justify-content-between align-items-center">';
    $html .= '<div>';
    $html .= '<i class="ri-drag-move-line me-2" style="cursor: move;"></i>';
    if ($item['icon']) {
        $html .= '<i class="' . htmlspecialchars($item['icon']) . ' me-1"></i>';
    }
    $html .= '<strong>' . htmlspecialchars($item['title']) . '</strong>';
    $html .= '<br><small class="text-muted ms-4">' . htmlspecialchars($item['url']) . '</small>';
    $html .= '</div>';
    $html .= '<div>';
    $html .= '<button type="button" class="btn btn-sm btn-info me-1" onclick="editMenuItem(' . $item['id'] . ')">';
    $html .= '<i class="ri-edit-line"></i>';
    $html .= '</button>';
    $html .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteMenuItem(' . $item['id'] . ')">';
    $html .= '<i class="ri-delete-bin-line"></i>';
    $html .= '</button>';
    $html .= '</div>';
    $html .= '</div>';
    
    if (!empty($item['children'])) {
        $html .= '<ul class="list-group mt-2">';
        foreach ($item['children'] as $child) {
            $html .= renderMenuItem($child, $level + 1);
        }
        $html .= '</ul>';
    }
    
    $html .= '</li>';
    return $html;
}
?>

<script>
// Initialize Sortable for drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const menuList = document.getElementById('sortable-menu');
    if (menuList) {
        new Sortable(menuList, {
            animation: 150,
            handle: '.ri-drag-move-line',
            onEnd: function() {
                console.log('Order changed');
            }
        });
    }
});

// Add custom link
document.getElementById('custom-link-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        type: 'custom',
        title: formData.get('title'),
        url: formData.get('url'),
        target: formData.get('target'),
        icon: formData.get('icon'),
        csrf_token: '<?php echo $csrfToken; ?>'
    };
    
    addMenuItem(data);
});

// Add selected pages
function addSelectedPages() {
    const checkboxes = document.querySelectorAll('#pages-list input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        const pageId = checkbox.value;
        const title = checkbox.nextElementSibling.textContent.trim();
        
        addMenuItem({
            type: 'page',
            object_id: pageId,
            title: title,
            csrf_token: '<?php echo $csrfToken; ?>'
        });
        
        checkbox.checked = false;
    });
}

// Add selected posts
function addSelectedPosts() {
    const checkboxes = document.querySelectorAll('#posts-tab input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        const postId = checkbox.value;
        const title = checkbox.nextElementSibling.textContent.trim();
        
        addMenuItem({
            type: 'post',
            object_id: postId,
            title: title,
            csrf_token: '<?php echo $csrfToken; ?>'
        });
        
        checkbox.checked = false;
    });
}

// Add menu item via AJAX
function addMenuItem(data) {
    fetch('/admin/menus/<?php echo $menu['id']; ?>/add-item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload(); // Reload to show new item
        } else {
            alert('Error: ' + result.message);
        }
    });
}

// Edit menu item
function editMenuItem(itemId) {
    // Implement edit modal
    alert('Edit functionality - item ID: ' + itemId);
}

// Delete menu item
function deleteMenuItem(itemId) {
    if (!confirm('Delete this menu item?')) return;
    
    fetch('/admin/menus/item/' + itemId + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            csrf_token: '<?php echo $csrfToken; ?>'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    });
}

// Save menu order
function saveMenuOrder() {
    const items = [];
    document.querySelectorAll('#sortable-menu > li').forEach((li, index) => {
        items.push(li.dataset.id);
    });
    
    fetch('/admin/menus/<?php echo $menu['id']; ?>/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            items: items,
            csrf_token: '<?php echo $csrfToken; ?>'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Menu order saved successfully!');
        } else {
            alert('Error: ' + result.message);
        }
    });
}
</script>
