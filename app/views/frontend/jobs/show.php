<!-- Single Job Page (Dynamic) -->
<article class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <!-- Job Header -->
                        <div class="mb-4">
                            <span class="badge bg-primary mb-3">
                                <?php echo ucwords(str_replace('-', ' ', $job['job_type'])); ?>
                            </span>
                            
                            <h1 class="display-6 fw-bold mb-3"><?php echo htmlspecialchars($job['title']); ?></h1>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <i class="ri-building-line text-primary me-2"></i>
                                    <strong><?php echo htmlspecialchars($job['company']); ?></strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="ri-map-pin-line text-primary me-2"></i>
                                    <?php echo htmlspecialchars($job['location']); ?>
                                </div>
                                <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                    <div class="col-md-6 mb-2">
                                        <i class="ri-money-dollar-circle-line text-primary me-2"></i>
                                        <strong>Rp <?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?></strong> / month
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-6 mb-2">
                                    <i class="ri-calendar-line text-primary me-2"></i>
                                    Posted: <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                                </div>
                            </div>
                            
                            <?php if ($job['deadline']): ?>
                                <div class="alert alert-warning">
                                    <i class="ri-alarm-warning-line me-2"></i>
                                    <strong>Application Deadline:</strong> <?php echo date('F d, Y', strtotime($job['deadline'])); ?>
                                    <?php 
                                    $daysLeft = floor((strtotime($job['deadline']) - time()) / (60 * 60 * 24));
                                    if ($daysLeft > 0): ?>
                                        <span class="badge bg-warning text-dark ms-2"><?php echo $daysLeft; ?> days left</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Job Description -->
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="ri-file-text-line me-2"></i> Job Description
                            </h3>
                            <div class="job-content">
                                <?php echo $job['description']; ?>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <?php if ($job['requirements']): ?>
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="ri-checkbox-circle-line me-2"></i> Requirements
                            </h3>
                            <div class="job-content">
                                <?php echo $job['requirements']; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Benefits -->
                        <?php if ($job['benefits']): ?>
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="ri-star-line me-2"></i> Benefits
                            </h3>
                            <div class="job-content">
                                <?php echo $job['benefits']; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Apply Card -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body text-center p-4">
                        <i class="ri-send-plane-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-3">Interested in this position?</h5>
                        <p class="text-muted small mb-3">Submit your application now</p>
                        
                        <button class="btn btn-primary btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#applyModal">
                            <i class="ri-file-text-line me-2"></i> Apply Now
                        </button>
                        
                        <a href="/jobs" class="btn btn-outline-secondary w-100">
                            <i class="ri-arrow-left-line me-1"></i> Back to Jobs
                        </a>
                    </div>
                </div>

                <!-- Share Job -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="ri-share-line me-2"></i> Share this job
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="ri-facebook-fill me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($job['title'] . ' at ' . $job['company']); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-info">
                                <i class="ri-twitter-fill me-1"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($job['title'] . ' at ' . $job['company'] . ' - ' . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-success">
                                <i class="ri-whatsapp-fill me-1"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Jobs -->
                <?php if (!empty($relatedJobs)): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="ri-briefcase-line me-2"></i> Similar Jobs
                        </h6>
                        <?php foreach ($relatedJobs as $related): ?>
                            <a href="/job/<?php echo $related['slug']; ?>" class="d-block text-decoration-none mb-3 pb-3 border-bottom">
                                <h6 class="mb-1"><?php echo htmlspecialchars($related['title']); ?></h6>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($related['company']); ?> • 
                                    <?php echo htmlspecialchars($related['location']); ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<!-- Application Modal -->
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-file-text-line me-2"></i> Apply for <?php echo htmlspecialchars($job['title']); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/job/<?php echo $job['id']; ?>/apply" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Resume/CV <span class="text-danger">*</span></label>
                        <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">Accepted formats: PDF, DOC, DOCX (Max: 5MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cover Letter <span class="text-danger">*</span></label>
                        <textarea name="cover_letter" class="form-control" rows="5" required 
                                  placeholder="Tell us why you're a great fit for this position..."></textarea>
                        <small class="text-muted">Minimum 50 characters</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <small>By submitting this application, you agree to our terms and conditions.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="ri-send-plane-line me-2"></i> Submit Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.job-content {
    font-size: 1rem;
    line-height: 1.8;
}

.job-content ul,
.job-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.job-content li {
    margin-bottom: 0.5rem;
}

.job-content h3,
.job-content h4 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}
</style>

<!-- Bootstrap JS (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
