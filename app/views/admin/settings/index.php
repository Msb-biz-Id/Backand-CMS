<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Settings</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeTab === 'general' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#general" role="tab">
                            <i class="ri-settings-3-line me-1"></i> General
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeTab === 'seo' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#seo" role="tab">
                            <i class="ri-search-line me-1"></i> SEO
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeTab === 'analytics' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#analytics" role="tab">
                            <i class="ri-line-chart-line me-1"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeTab === 'performance' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#performance" role="tab">
                            <i class="ri-speed-line me-1"></i> Performance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeTab === 'security' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#security" role="tab">
                            <i class="ri-shield-check-line me-1"></i> Security
                        </a>
                    </li>
                </ul>

                <form method="POST" action="/admin/settings">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <!-- Tab panes -->
                    <div class="tab-content p-3">
                        
                        <!-- General Settings -->
                        <div class="tab-pane <?php echo $activeTab === 'general' ? 'active' : ''; ?>" id="general" role="tabpanel">
                            <h5 class="mb-3">General Settings</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Site Name</label>
                                        <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars($settings['general'][0]['value'] ?? 'Advanced CMS'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Site Language</label>
                                        <select class="form-select" name="site_language">
                                            <option value="id">Indonesian</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Site Description</label>
                                <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($settings['general'][1]['value'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Site Logo</label>
                                        <input type="text" class="form-control" name="site_logo" value="">
                                        <small class="text-muted">Upload logo via Media Library</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Favicon</label>
                                        <input type="text" class="form-control" name="site_favicon" value="">
                                        <small class="text-muted">Upload favicon via Media Library</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select" name="site_timezone">
                                            <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Items Per Page</label>
                                        <input type="number" class="form-control" name="items_per_page" value="15" min="5" max="100">
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="allow_registration" value="1" id="allow_registration">
                                <label class="form-check-label" for="allow_registration">
                                    Allow User Registration
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode">
                                <label class="form-check-label" for="maintenance_mode">
                                    Maintenance Mode
                                </label>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="tab-pane <?php echo $activeTab === 'seo' ? 'active' : ''; ?>" id="seo" role="tabpanel">
                            <h5 class="mb-3">SEO Settings</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Default Meta Title</label>
                                <input type="text" class="form-control" name="seo_title" value="Advanced CMS - Modern Content Management">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Default Meta Description</label>
                                <textarea class="form-control" name="seo_description" rows="2">Modern CMS with advanced SEO features</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Default Meta Keywords</label>
                                <input type="text" class="form-control" name="seo_keywords" value="cms, seo, content management">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="sitemap_enabled" value="1" id="sitemap_enabled" checked>
                                <label class="form-check-label" for="sitemap_enabled">
                                    Enable XML Sitemap
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="robots_txt_enabled" value="1" id="robots_txt_enabled" checked>
                                <label class="form-check-label" for="robots_txt_enabled">
                                    Enable Robots.txt
                                </label>
                            </div>
                        </div>

                        <!-- Analytics Settings -->
                        <div class="tab-pane <?php echo $activeTab === 'analytics' ? 'active' : ''; ?>" id="analytics" role="tabpanel">
                            <h5 class="mb-3">Analytics & Tracking</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Google Analytics ID</label>
                                <input type="text" class="form-control" name="google_analytics_id" placeholder="G-XXXXXXXXXX">
                                <small class="text-muted">Your Google Analytics measurement ID</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Facebook Pixel ID</label>
                                <input type="text" class="form-control" name="facebook_pixel_id" placeholder="1234567890">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Google Tag Manager ID</label>
                                <input type="text" class="form-control" name="gtm_id" placeholder="GTM-XXXXXXX">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enable_internal_analytics" value="1" id="enable_internal_analytics" checked>
                                <label class="form-check-label" for="enable_internal_analytics">
                                    Enable Internal Analytics
                                </label>
                            </div>
                        </div>

                        <!-- Performance Settings -->
                        <div class="tab-pane <?php echo $activeTab === 'performance' ? 'active' : ''; ?>" id="performance" role="tabpanel">
                            <h5 class="mb-3">Performance Settings</h5>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enable_cache" value="1" id="enable_cache" checked>
                                <label class="form-check-label" for="enable_cache">
                                    Enable Caching
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cache Duration (seconds)</label>
                                <input type="number" class="form-control" name="cache_duration" value="3600">
                                <small class="text-muted">Default: 3600 (1 hour)</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enable_lazy_loading" value="1" id="enable_lazy_loading" checked>
                                <label class="form-check-label" for="enable_lazy_loading">
                                    Enable Lazy Loading for Images
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enable_minification" value="1" id="enable_minification" checked>
                                <label class="form-check-label" for="enable_minification">
                                    Enable HTML/CSS/JS Minification
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="gzip_compression" value="1" id="gzip_compression" checked>
                                <label class="form-check-label" for="gzip_compression">
                                    Enable GZIP Compression
                                </label>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="tab-pane <?php echo $activeTab === 'security' ? 'active' : ''; ?>" id="security" role="tabpanel">
                            <h5 class="mb-3">Security Settings</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Max Login Attempts</label>
                                <input type="number" class="form-control" name="max_login_attempts" value="5" min="3" max="10">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Login Lockout Time (seconds)</label>
                                <input type="number" class="form-control" name="login_lockout_time" value="900">
                                <small class="text-muted">Default: 900 (15 minutes)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Session Lifetime (seconds)</label>
                                <input type="number" class="form-control" name="session_lifetime" value="7200">
                                <small class="text-muted">Default: 7200 (2 hours)</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="enable_2fa" value="1" id="enable_2fa">
                                <label class="form-check-label" for="enable_2fa">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="force_https" value="1" id="force_https">
                                <label class="form-check-label" for="force_https">
                                    Force HTTPS (Production only)
                                </label>
                            </div>

                            <div class="alert alert-info">
                                <i class="ri-information-line me-2"></i>
                                <strong>Security Note:</strong> These settings affect system security. Change with caution.
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Save Settings
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
