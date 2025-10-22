<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">🎨 Theme Manager</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Themes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            <strong>Theme System Info:</strong> Themes di Advanced CMS hanya untuk visual customization (CSS/JS/Assets). Logic tetap di core system untuk keamanan dan maintainability.
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Available Themes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($themes)): ?>
                        <?php foreach ($themes as $themeKey => $theme): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 <?php echo $themeKey === $activeTheme ? 'border-primary' : ''; ?>">
                                    <?php if (file_exists(PUBLIC_PATH . '/themes/' . $themeKey . '/screenshot.png')): ?>
                                        <img src="/themes/<?php echo $themeKey; ?>/screenshot.png" class="card-img-top" alt="<?php echo $theme['name']; ?>">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="ri-image-line fs-1 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($theme['name']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars($theme['description']); ?></p>
                                        
                                        <p class="mb-1 small">
                                            <strong>Version:</strong> <?php echo $theme['version']; ?>
                                        </p>
                                        <p class="mb-3 small">
                                            <strong>Author:</strong> <?php echo $theme['author']; ?>
                                        </p>
                                        
                                        <?php if ($themeKey === $activeTheme): ?>
                                            <button class="btn btn-success w-100" disabled>
                                                <i class="ri-check-line me-1"></i> Active Theme
                                            </button>
                                            <button type="button" class="btn btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#customizeModal">
                                                <i class="ri-palette-line me-1"></i> Customize
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" action="/admin/themes/activate">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                <input type="hidden" name="theme" value="<?php echo $themeKey; ?>">
                                                <button type="submit" class="btn btn-outline-primary w-100">
                                                    <i class="ri-check-line me-1"></i> Activate
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="ri-palette-line" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No themes found. Please install a theme.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customize Modal -->
<div class="modal fade" id="customizeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customize Theme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/admin/themes/customize">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Primary Color</label>
                        <input type="color" name="primary_color" class="form-control" value="<?php echo $customization['primary_color'] ?? '#007bff'; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Secondary Color</label>
                        <input type="color" name="secondary_color" class="form-control" value="<?php echo $customization['secondary_color'] ?? '#6c757d'; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Font Family</label>
                        <select name="font_family" class="form-select">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Poppins', sans-serif">Poppins</option>
                            <option value="'Inter', sans-serif">Inter</option>
                            <option value="'Roboto', sans-serif">Roboto</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Custom CSS</label>
                        <textarea name="custom_css" class="form-control" rows="5" placeholder="/* Your custom CSS */"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Save Customization
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
