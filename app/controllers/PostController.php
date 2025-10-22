<?php

/**
 * Frontend Post Controller
 * Blog/post display (dynamic like WordPress)
 */
class PostController extends Controller {
    private $postModel;
    private $categoryModel;
    private $tagModel;
    private $commentModel;
    
    public function __construct() {
        parent::__construct();
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
        $this->commentModel = new Comment();
    }
    
    /**
     * Post listing (blog index)
     */
    public function index() {
        $page = $this->input('page', 1);
        $search = $this->input('s');
        
        // Get posts with pagination
        if ($search) {
            $posts = $this->postModel->search($search);
            $pagination = null;
        } else {
            $result = $this->postModel->paginate($page, 12);
            $posts = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get categories & tags for sidebar
        $categories = $this->categoryModel->getByType('post');
        $popularPosts = $this->postModel->getPublished(5);
        
        // SEO
        $this->seo->setTitle('Blog');
        $this->seo->setDescription('Read our latest blog posts and articles');
        
        $this->view('frontend/posts/index', [
            'posts' => $posts,
            'pagination' => $pagination,
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'search' => $search
        ], 'frontend');
    }
    
    /**
     * Single post view (dynamic)
     */
    public function show($slug) {
        $post = $this->postModel->getBySlug($slug);
        
        if (!$post || $post['status'] !== 'published') {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        // Increment view count
        $this->postModel->incrementViews($post['id']);
        
        // Get post categories & tags
        $categories = $this->categoryModel->getByPost($post['id']);
        $tags = $this->tagModel->getByPost($post['id']);
        
        // Get comments
        $comments = $this->commentModel->getByPost($post['id'], 'approved');
        $commentTree = $this->commentModel->buildCommentTree($comments);
        
        // Get related posts
        $relatedPosts = [];
        if (!empty($categories)) {
            $categoryIds = array_column($categories, 'id');
            $relatedPosts = $this->postModel->getRelated($post['id'], $categoryIds[0], 4);
        }
        
        // SEO from post data
        $this->seo->setTitle($post['meta_title'] ?? $post['title']);
        $this->seo->setDescription($post['meta_description'] ?? substr(strip_tags($post['content']), 0, 160));
        $this->seo->setKeywords($post['meta_keywords'] ?? '');
        $this->seo->setCanonicalUrl('/post/' . $post['slug']);
        
        // Open Graph
        $this->seo->setOpenGraph([
            'type' => 'article',
            'title' => $post['title'],
            'description' => $post['excerpt'] ?? substr(strip_tags($post['content']), 0, 200),
            'url' => '/post/' . $post['slug'],
            'image' => $post['featured_image'] ?? ''
        ]);
        
        // Article Schema
        $this->seo->addSchema('Article', [
            'headline' => $post['title'],
            'datePublished' => $post['published_at'],
            'dateModified' => $post['updated_at'],
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name'] ?? 'Admin'
            ],
            'image' => $post['featured_image'] ?? '',
            'publisher' => [
                '@type' => 'Organization',
                'name' => $this->config['app']['name'],
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => '/assets/images/logo.png'
                ]
            ]
        ]);
        
        $this->view('frontend/posts/show', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'comments' => $commentTree,
            'relatedPosts' => $relatedPosts
        ], 'frontend');
    }
    
    /**
     * Posts by category
     */
    public function category($slug) {
        $category = $this->categoryModel->getBySlug($slug, 'post');
        
        if (!$category) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        $page = $this->input('page', 1);
        $posts = $this->postModel->getByCategory($category['id']);
        
        // Get all categories for sidebar
        $categories = $this->categoryModel->getByType('post');
        
        $this->seo->setTitle('Category: ' . $category['name']);
        $this->seo->setDescription($category['description'] ?? 'Posts in ' . $category['name']);
        
        $this->view('frontend/posts/category', [
            'category' => $category,
            'posts' => $posts,
            'categories' => $categories
        ], 'frontend');
    }
    
    /**
     * Posts by tag
     */
    public function tag($slug) {
        $tag = $this->tagModel->getBySlug($slug, 'post');
        
        if (!$tag) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        $posts = $this->postModel->getByTag($tag['id']);
        
        $this->seo->setTitle('Tag: ' . $tag['name']);
        $this->seo->setDescription('Posts tagged with ' . $tag['name']);
        
        $this->view('frontend/posts/tag', [
            'tag' => $tag,
            'posts' => $posts
        ], 'frontend');
    }
}
