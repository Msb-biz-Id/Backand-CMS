<?php

/**
 * Frontend Job/Career Controller
 * Job listings display (dynamic like WordPress)
 */
class JobController extends Controller {
    private $jobModel;
    
    public function __construct() {
        parent::__construct();
        $this->jobModel = new Job();
    }
    
    /**
     * Job listing page
     */
    public function index() {
        $page = $this->input('page', 1);
        $type = $this->input('type');
        $location = $this->input('location');
        $search = $this->input('s');
        
        // Get jobs based on filters
        if ($search) {
            $jobs = $this->jobModel->search($search);
            $pagination = null;
        } elseif ($type) {
            $jobs = $this->jobModel->getByType($type);
            $pagination = null;
        } else {
            $result = $this->jobModel->paginate($page, 12, ['status' => 'active'], 'created_at DESC');
            $jobs = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get job types for filter
        $jobTypes = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'freelance' => 'Freelance',
            'internship' => 'Internship',
            'remote' => 'Remote'
        ];
        
        // SEO
        $this->seo->setTitle('Careers - Job Openings');
        $this->seo->setDescription('Browse our latest job openings and career opportunities');
        
        $this->view('frontend/jobs/index', [
            'jobs' => $jobs,
            'pagination' => $pagination,
            'jobTypes' => $jobTypes,
            'selectedType' => $type,
            'search' => $search
        ], 'frontend');
    }
    
    /**
     * Single job view (dynamic)
     */
    public function show($slug) {
        $job = $this->jobModel->getBySlug($slug);
        
        if (!$job || $job['status'] !== 'active') {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        // Increment views
        $this->jobModel->incrementViews($job['id']);
        
        // Get related jobs (same type)
        $relatedJobs = $this->jobModel->getByType($job['job_type'], 4);
        $relatedJobs = array_filter($relatedJobs, function($j) use ($job) {
            return $j['id'] !== $job['id'];
        });
        $relatedJobs = array_slice($relatedJobs, 0, 3);
        
        // SEO
        $this->seo->setTitle($job['meta_title'] ?? ($job['title'] . ' - ' . $job['company']));
        $this->seo->setDescription($job['meta_description'] ?? substr(strip_tags($job['description']), 0, 160));
        $this->seo->setCanonicalUrl('/job/' . $job['slug']);
        
        // JobPosting Schema
        $this->seo->addSchema('JobPosting', [
            'title' => $job['title'],
            'description' => strip_tags($job['description']),
            'datePosted' => $job['created_at'],
            'validThrough' => $job['deadline'] ?? null,
            'employmentType' => strtoupper(str_replace('-', '_', $job['job_type'])),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job['company']
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job['location']
                ]
            ],
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => 'IDR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job['salary_min'],
                    'maxValue' => $job['salary_max'],
                    'unitText' => 'MONTH'
                ]
            ]
        ]);
        
        $this->view('frontend/jobs/show', [
            'job' => $job,
            'relatedJobs' => $relatedJobs
        ], 'frontend');
    }
    
    /**
     * Apply for job
     */
    public function apply($id) {
        try {
            $job = $this->jobModel->find($id);
            
            if (!$job || $job['status'] !== 'active') {
                throw new Exception('Job not found or no longer active');
            }
            
            $this->validate([
                'full_name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                'cover_letter' => 'required|min:50'
            ]);
            
            $data = $this->input();
            
            // Handle resume upload
            $resumePath = null;
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
                $upload = $this->uploadFile($_FILES['resume'], 'resumes', 'document');
                $resumePath = $upload['path'];
            }
            
            // Save application
            $this->db->insert('job_applications', [
                'job_id' => $id,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'cover_letter' => $data['cover_letter'],
                'resume_path' => $resumePath,
                'status' => 'pending',
                'ip_address' => $this->security->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            // TODO: Send email notification
            
            $this->setFlash('success', 'Your application has been submitted successfully!');
            $this->redirect('/job/' . $job['slug']);
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
}
