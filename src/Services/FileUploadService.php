<?php
// src/Services/FileUploadService.php

namespace Services;

class FileUploadService
{
    private $uploadPath;
    private $allowedTypes;
    private $maxFileSize;
    
    public function __construct()
    {
        $this->uploadPath = __DIR__ . '/../../public/uploads/products/';
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }
    
    public function uploadSingle($file, $subfolder = 'products')
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Upload failed with error code: ' . $file['error']];
        }
        
        // Validate file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP'];
        }
        
        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            return ['error' => 'File too large. Max size: 5MB'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $this->uploadPath . $filename;
        
        // Move file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['error' => 'Failed to move uploaded file'];
    }
    
    public function uploadMultiple($files, $subfolder = 'products')
    {
        $uploaded = [];
        $errors = [];
        
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $tmpName,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
                
                $result = $this->uploadSingle($file, $subfolder);
                
                if (isset($result['success']) && $result['success']) {
                    $uploaded[] = $result['filename'];
                } else {
                    $errors[] = $result['error'] ?? 'Unknown error';
                }
            }
        }
        
        return $uploaded;
    }
    
    public function deleteFile($filename, $subfolder = 'products')
    {
        $filePath = $this->uploadPath . $filename;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}