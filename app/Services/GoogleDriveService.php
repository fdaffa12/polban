<?php

namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use Google\Service\Drive\Permission;

class GoogleDriveService
{
    protected $client;
    protected $service;
    protected $folderId = '1aWlzDOWhIrlhSuG6c8C4pSVZe_45HnUp';

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->service = new Google_Service_Drive($this->client);
    }

    public function uploadFile($file, $type)
    {
        try {
            // 1. Buat file di Google Drive
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $file->getClientOriginalName(),
                'parents' => [$this->folderId]
            ]);

            $content = file_get_contents($file->getPathname());
            $mimeType = $file->getMimeType();

            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink',
            ]);

            // 2. Set permission "anyone with the link can edit"
            $permission = new Permission([
                'type' => 'anyone',
                'role' => 'writer',
                'allowFileDiscovery' => false
            ]);

            $this->service->permissions->create(
                $file->id, 
                $permission, 
                ['fields' => 'id']
            );

            // 3. Update file untuk mengaktifkan link sharing
            $updatedFile = new Google_Service_Drive_DriveFile([
                'copyRequiresWriterPermission' => false,
                'viewersCanCopyContent' => true,
                'writersCanShare' => true
            ]);

            $this->service->files->update(
                $file->id, 
                $updatedFile, 
                ['fields' => 'id,webViewLink']
            );

            // 4. Return URL dengan format yang benar
            return "https://drive.google.com/file/d/" . $file->id . "/view?usp=sharing";

        } catch (\Exception $e) {
            \Log::error('Google Drive Upload Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
