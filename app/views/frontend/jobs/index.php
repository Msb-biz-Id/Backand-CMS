<!-- Jobs Listing Page (Dynamic) -->
<div class="hero-section" style="padding: 4rem 0; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark mb-3">Join Our Team</h1>
        <p class="lead text-dark mb-4">Find your perfect career opportunity</p>
        
        <!-- Search Form -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form action="/jobs" method="GET" class="bg-white p-2 rounded shadow">
                    <div class="input-group">
                        <input type="text" name="s" class="form-control border-0" 
                               placeholder="Search jobs, companies, locations..." 
                               value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-search-line me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <!-- Search Results Info -->
        <?php if (!empty($search)): ?>
            <div class="alert alert-info mb-4">
                <i class="ri-search-line me-2"></i>
                Search results for: <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
                (<?php echo count($jobs); ?> job<?php echo count($jobs) !== 1 ? 's' : ''; ?> found)
                <a href="/jobs" class="float-end">Clear Search</a>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Filters -->
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-filter-line me-2"></i> Filter Jobs
                        </h5>
                        
                        <!-- Job Type Filter -->
                        <h6 class="mb-2">Job Type</h6>
                        <div class="mb-3">
                            <?php foreach ($jobTypes as $typeKey => $typeName): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" 
                                           id="type-<?php echo $typeKey; ?>" 
                                           value="<?php echo $typeKey; ?>"
                                           <?php echo $selectedType === $typeKey ? 'checked' : ''; ?>
                                           onchange="window.location.href='/jobs?type=<?php echo $typeKey; ?>'">
                                    <label class="form-check-label" for="type-<?php echo $typeKey; ?>">
                                        <?php echo $typeName; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($selectedType): ?>
                                <a href="/jobs" class="btn btn-sm btn-link p-0 mt-2">Clear Filter</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="ri-information-line me-2"></i> Quick Tips
                        </h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-2">✓ Read job descriptions carefully</li>
                            <li class="mb-2">✓ Prepare your CV/Resume</li>
                            <li class="mb-2">✓ Write a compelling cover letter</li>
                            <li class="mb-2">✓ Check application deadlines</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="col-lg-9">
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card job-card mb-3 hover-lift">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="h5 mb-2">
                                            <a href="/job/<?php echo $job['slug']; ?>" class="text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h3>
                                        
                                        <div class="text-muted mb-2">
                                            <i class="ri-building-line me-1"></i>
                                            <strong><?php echo htmlspecialchars($job['company']); ?></strong>
                                        </div>
                                        
                                        <div class="d-flex gap-3 flex-wrap">
                                            <span class="text-muted small">
                                                <i class="ri-map-pin-line me-1"></i>
                                                <?php echo htmlspecialchars($job['location']); ?>
                                            </span>
                                            
                                            <span class="badge bg-primary">
                                                <?php echo ucwords(str_replace('-', ' ', $job['job_type'])); ?>
                                            </span>
                                            
                                            <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                                <span class="text-muted small">
                                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                                    Rp <?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($job['deadline']): ?>
                                            <div class="mt-2">
                                                <small class="text-danger">
                                                    <i class="ri-time-line me-1"></i>
                                                    Apply before: <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-4 text-md-end">
                                        <a href="/job/<?php echo $job['slug']; ?>" class="btn btn-primary">
                                            View Details <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">
                                        Next <i class="ri-arrow-right-line"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="ri-briefcase-line" style="font-size: 5rem; color: #ccc;"></i>
                        <h3 class="mt-3">No jobs found</h3>
                        <p class="text-muted">
                            <?php echo $search ? 'Try a different search term' : 'Check back soon for new opportunities'; ?>
                        </p>
                        <?php if ($search || $selectedType): ?>
                            <a href="/jobs" class="btn btn-primary mt-2">View All Jobs</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.job-card {
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
}

.job-card:hover {
    border-left-color: var(--success-color);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}
</style>
