<?php

/**
 * Admin Media Controller
 * Upload, manage, dan organize media files
 */
class MediaController extends Controller {
    private $mediaModel;
    private $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'video' => ['mp4', 'webm', 'ogg', 'avi', 'mov'],
        'audio' => ['mp3', 'wav', 'ogg']
    ];
    
    public function __construct() {
        parent::__construct();
        $this->mediaModel = new Media();
    }
    
    /**
     * Media library index
     */
    public function index() {
        $this->requireAuth();
        
        // Get filters
        $type = $this->input('type');
        $search = $this->input('search');
        $page = $this->input('page', 1);
        $perPage = 24; // Grid view
        
        // Build conditions
        $conditions = [];
        if ($type) {
            $conditions['type'] = $type;
        }
        
        // Get media
        if ($search) {
            $media = $this->mediaModel->search($search);
        } else {
            $result = $this->mediaModel->paginate($page, $perPage, $conditions, 'created_at DESC');
            $media = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get statistics
        $stats = $this->mediaModel->getStatistics();
        $totalStorage = $this->mediaModel->getTotalStorage();
        
        // SEO
        $this->seo->setTitle('Media Library');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/media/index', [
            'media' => $media,
            'pagination' => $pagination ?? null,
            'stats' => $stats,
            'totalStorage' => $totalStorage,
            'type' => $type,
            'search' => $search,
            'extraCss' => ['/assets/libs/dropzone/min/dropzone.min.css'],
            'extraJs' => ['/assets/libs/dropzone/min/dropzone.min.js']
        ]);
    }
    
    /**
     * Upload media
     */
    public function upload() {
        $this->requireAuth();
        
        try {
            if (!isset($_FILES['file'])) {
                throw new Exception('No file uploaded');
            }
            
            $file = $_FILES['file'];
            
            // Validate file
            $validation = $this->security->validateFileUpload($file, 'image');
            
            if (!$validation['valid']) {
                throw new Exception($validation['error']);
            }
            
            // Get file info
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
            $filename = $this->generateUniqueFilename($extension);
            
            // Determine type
            $type = $this->getFileType($extension);
            
            // Determine upload path
            $folder = $type . 's'; // images, documents, videos
            $uploadPath = $this->config['media']['upload_path'] . '/' . $folder;
            
            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $filePath = $uploadPath . '/' . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Failed to move uploaded file');
            }
            
            // Get file info
            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);
            $fileUrl = '/' . $folder . '/' . $filename;
            
            // Get image dimensions if image
            $width = null;
            $height = null;
            if ($type === 'image') {
                $imageInfo = getimagesize($filePath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
                
                // Optimize image
                if ($this->config['media']['optimize']) {
                    $this->optimizeImage($filePath, $mimeType);
                }
            }
            
            // Save to database
            $mediaId = $this->mediaModel->create([
                'user_id' => $_SESSION['user_id'],
                'filename' => $filename,
                'original_filename' => $file['name'],
                'file_path' => $filePath,
                'file_url' => $fileUrl,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'file_extension' => $extension,
                'type' => $type,
                'width' => $width,
                'height' => $height,
                'folder' => $folder,
                'is_optimized' => ($type === 'image' && $this->config['media']['optimize']) ? 1 : 0
            ]);
            
            // Get media data
            $media = $this->mediaModel->find($mediaId);
            
            // Log activity
            $this->logActivity('upload', 'media', $mediaId, 'Uploaded file: ' . $file['name']);
            
            $this->success([
                'media' => $media,
                'url' => $fileUrl
            ], 'File uploaded successfully');
            
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }
    
    /**
     * Update media details
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $media = $this->mediaModel->find($id);
            
            if (!$media) {
                throw new Exception('Media not found');
            }
            
            // Check permission
            if (!$this->hasRole(['admin', 'editor']) && $media['user_id'] != $_SESSION['user_id']) {
                throw new Exception('You do not have permission to edit this media');
            }
            
            // Update metadata
            $data = [
                'title' => $this->sanitizeInput('title', 'string'),
                'alt_text' => $this->sanitizeInput('alt_text', 'string'),
                'caption' => $this->sanitizeInput('caption', 'string'),
                'description' => $this->sanitizeInput('description', 'string'),
            ];
            
            $this->mediaModel->update($id, $data);
            
            // Log activity
            $this->logActivity('update', 'media', $id, 'Updated media details');
            
            $this->success(null, 'Media updated successfully');
            
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }
    
    /**
     * Delete media
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $media = $this->mediaModel->find($id);
            
            if (!$media) {
                throw new Exception('Media not found');
            }
            
            // Check permission
            if (!$this->hasRole(['admin', 'editor']) && $media['user_id'] != $_SESSION['user_id']) {
                throw new Exception('You do not have permission to delete this media');
            }
            
            // Delete physical file
            if (file_exists($media['file_path'])) {
                unlink($media['file_path']);
            }
            
            // Delete from database
            $this->mediaModel->delete($id);
            
            // Log activity
            $this->logActivity('delete', 'media', $id, 'Deleted media: ' . $media['filename']);
            
            $this->success(null, 'Media deleted successfully');
            
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }
    
    /**
     * Generate unique filename
     */
    private function generateUniqueFilename($extension) {
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Get file type from extension
     */
    private function getFileType($extension) {
        foreach ($this->allowedTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }
        return 'other';
    }
    
    /**
     * Optimize image
     */
    private function optimizeImage($path, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($path);
                if ($image) {
                    imagejpeg($image, $path, 85);
                    imagedestroy($image);
                }
                break;
                
            case 'image/png':
                $image = imagecreatefrompng($path);
                if ($image) {
                    imagepng($image, $path, 8);
                    imagedestroy($image);
                }
                break;
                
            case 'image/gif':
                $image = imagecreatefromgif($path);
                if ($image) {
                    imagegif($image, $path);
                    imagedestroy($image);
                }
                break;
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
