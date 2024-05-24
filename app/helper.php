<?php

use App\Models\SubTaskLog;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Support\facades\Auth;
use Illuminate\Support\Facades\File;

if (!function_exists('UsersNameById')) {
    function UsersNameById($id)
    {

        $user = User::where('id', $id)->first('name');
        return $user->name ?? '';
    }
}

if (!function_exists('timeAgo')) {
function timeAgo($timestamp) {
    $currentTime = time();
    $timestamp = strtotime($timestamp);
    
    $timeDifference = $currentTime - $timestamp;

    if ($timeDifference < 60) {
        return $timeDifference . ' seconds ago';
    } elseif ($timeDifference < 3600) {
        $minutes = floor($timeDifference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($timeDifference < 86400) {
        $hours = floor($timeDifference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = floor($timeDifference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
}

}

if (!function_exists('decryptFile')) {
    function decryptFile($filePath, $fileExtension = null, $directoryPath = null)
    {
        try {
            // Check if the file exists
            if (!file_exists($filePath)) {
                return "File does not exist.";
            }

            // Read the file contents
            $fileContents = file_get_contents($filePath);

            // Extract the IV (first 16 bytes) and the encrypted contents
            $iv = substr($fileContents, 0, 16);
            $encryptedContents = substr($fileContents, 16);

            // Decrypt the file contents using AES-256-CBC decryption
            $decryptedContents = openssl_decrypt(
                $encryptedContents,
                'aes-256-cbc',
                env('AES_Secret_Key_DB'),
                0,
                $iv
            );

            if ($decryptedContents === false) {
                $decryptedContents = $fileContents;
            }
            $subFloder = Auth::user()->id . '-case-docs';

            $relativePath = 'uploads/case_docs/tmp/' . $subFloder; // Relative path to your directory
            if($directoryPath !== null){
            $fullPath = $directoryPath;
            }else{
            $fullPath = storage_path('app/public/' . $relativePath);
            }

            // check is folder = $subFloder is created in $relativePath
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }
            // Generate a unique filename within the specified directory
            // $tempFile = tempnam($fullPath, 'decrypted');
            $originalFileName = pathinfo($filePath, PATHINFO_FILENAME);
            $tempFile = tempnam($fullPath, $originalFileName . '_');

            // If a file extension is provided, add it to the temporary file
            if ($fileExtension !== null) {
                $tempFileWithExtension = $tempFile . '.' . $fileExtension;
                // Write the decrypted contents back to the file with the extension
                file_put_contents($tempFileWithExtension, $decryptedContents);
                // Move the temporary file with the extension to the desired directory
                $newTempFilePath = $fullPath . '/' . basename($tempFileWithExtension);
                rename($tempFileWithExtension, $newTempFilePath);
            } else {
                // Write the decrypted contents back to the file without an extension
                file_put_contents($tempFile, $decryptedContents);
                // Move the temporary file to the desired directory
                $newTempFilePath = $fullPath . '/' . basename($tempFile);
                rename($tempFile, $newTempFilePath);
            }

            // Use $newTempFilePath for further operations
            return basename($newTempFilePath);
        } catch (\Exception $e) {
            // Handle decryption errors if necessary
            return $e->getMessage();
        }
    }
    function encryptFile($filePath)
    {
        try {
            // Check if the file exists
            if (!file_exists($filePath)) {
                return "File does not exist.";
            }

            // Read the file contents
            $fileContents = file_get_contents($filePath);

            // Generate a random IV for encryption
            $iv = random_bytes(16);

            // Encrypt the file contents using AES-256-CBC encryption
            $encryptedContents = openssl_encrypt(
                $fileContents,
                'aes-256-cbc',
                env('AES_Secret_Key_DB'),
                0,
                $iv
            );

            // Write the encrypted contents back to the file
            file_put_contents($filePath, $iv . $encryptedContents);

            return "File encrypted successfully.";
        } catch (\Exception $e) {
            // Handle encryption errors if necessary
            dd($e->getMessage());
        }
    }
    function TasksDataById($id)
    {

        $task_logs = TaskLog::where('task_id', $id)->get();
        return $task_logs;
    }

    function SubTasksDataById($id)
    {

        $task_logs = SubTaskLog::where('task_id', $id)->get();
        return $task_logs;
    }
}
