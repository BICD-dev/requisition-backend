<?php
require dirname(__DIR__, 1) . '/vendor/autoload.php';


use Google\Client;
use Google\Service\Drive;

// Upload to Google Drive
function uploadToGoogleDrive($filePath, $fileName) {
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->addScope(Drive::DRIVE_FILE);

    $driveService = new Drive($client);

    $folderId = '1VLmtYq-Xc74GgYESRxspJ0XczZB3Hpg9'; // Google Drive folder ID

    $fileMetadata = new Drive\DriveFile([
        'name' => $fileName,
        'parents' => [$folderId]
    ]);

    $content = file_get_contents($filePath);
    $file = $driveService->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => mime_content_type($filePath),
        'uploadType' => 'multipart',
    ]);

    $permission = new Drive\Permission();
    $permission->setType('anyone');
    $permission->setRole('reader');
    $driveService->permissions->create($file->id, $permission);

    $fileUrl = "https://drive.google.com/file/d/{$file->id}/view";
    preg_match('/\/d\/(.*)\/view/', $fileUrl, $matches);
    
    return isset($matches[1]) ? "https://drive.google.com/uc?export=download&id={$matches[1]}" : $fileUrl;
    
    
}

// Example usage
// $filePath = 'invoices/sample-invoice.pdf';
// $invoiceLink = uploadToGoogleDrive($filePath, 'invoice-12345.pdf');
// echo "Invoice Link: $invoiceLink";
