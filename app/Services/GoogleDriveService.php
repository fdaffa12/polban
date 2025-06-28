<?php

namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use Google\Service\Drive\Permission;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $service;
    protected $sharedDriveId = '0AL7wXp44-Q_-Uk9PVA';
    protected $targetFolderId = '1hs93Zshm6f3n0NQeNLphbqZR2E5dYyTU';

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));

        // Add all necessary scopes
        $this->client->addScope([
            Google_Service_Drive::DRIVE,
            Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE_METADATA
        ]);

        // Set additional configurations
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');

        $this->service = new Google_Service_Drive($this->client);

        // Verify folder exists
        try {
            $folder = $this->service->files->get($this->targetFolderId, [
                'supportsAllDrives' => true,
                'fields' => 'id,name,mimeType'
            ]);

            Log::info('Target folder found:', [
                'folder_name' => $folder->getName(),
                'folder_id' => $folder->getId(),
                'mime_type' => $folder->getMimeType()
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying target folder: ' . $e->getMessage());
        }
    }

    public function uploadFile($file, $type)
    {
        try {
            // 1. Buat file metadata dengan parents ke folder target
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $file->getClientOriginalName(),
                'parents' => [$this->targetFolderId]
            ]);

            // 2. Set parameter untuk upload
            $optParams = [
                'data' => file_get_contents($file->getPathname()),
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id',
                'supportsAllDrives' => true
            ];

            // 3. Upload file
            $uploadedFile = $this->service->files->create($fileMetadata, $optParams);

            // 4. Log file ID untuk debugging
            Log::info('File uploaded successfully', [
                'file_id' => $uploadedFile->getId(),
                'target_folder_id' => $this->targetFolderId,
                'file_name' => $file->getClientOriginalName()
            ]);

            // 5. Set permission
            try {
                $permission = new Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                    'allowFileDiscovery' => false
                ]);

                $this->service->permissions->create(
                    $uploadedFile->getId(),
                    $permission,
                    ['supportsAllDrives' => true]
                );
            } catch (\Exception $e) {
                Log::error('Error setting permission: ' . $e->getMessage());
                // Tetap lanjutkan karena file sudah terupload
            }

            return "https://drive.google.com/file/d/" . $uploadedFile->getId() . "/view?usp=sharing";
        } catch (\Exception $e) {
            Log::error('Google Drive Upload Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteFile($fileUrl)
    {
        try {
            // Ekstrak file ID dari URL Google Drive
            $pattern = '/[-\w]{25,}/';
            preg_match($pattern, $fileUrl, $matches);

            if (!isset($matches[0])) {
                return false;
            }

            $fileId = $matches[0];

            $this->service->files->delete($fileId, [
                'supportsAllDrives' => true
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Drive Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    public function createFolder($folderName)
    {
        try {
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$this->targetFolderId] // Create subfolder in target folder
            ]);

            $folder = $this->service->files->create($fileMetadata, [
                'supportsAllDrives' => true
            ]);

            Log::info('Folder created successfully', [
                'folder_id' => $folder->getId(),
                'parent_folder_id' => $this->targetFolderId
            ]);

            return $folder->getId();
        } catch (\Exception $e) {
            Log::error('Google Drive Create Folder Error: ' . $e->getMessage());
            throw $e;
        }
    }
}