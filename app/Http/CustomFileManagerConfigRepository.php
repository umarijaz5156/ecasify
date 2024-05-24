<?php

namespace App\Http;

use Alexusmai\LaravelFileManager\Services\ConfigService\DefaultConfigRepository;
use Alexusmai\LaravelFileManager\Services\ConfigService\ConfigRepository;

use Illuminate\Support\Facades\Auth;

class CustomFileManagerConfigRepository extends DefaultConfigRepository
{


    public function getLeftDisk(): ?string
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            return 'default_admin_disk'; // Use your admin disk name
        }

        return 'custom_filemanager'; // Use your regular disk name
    }

    public function getRightDisk(): ?string
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            return 'default_admin_disk'; // Use your admin disk name
        }

        return 'custom_filemanager'; // Use your regular disk name
    }

    public function getLeftPath(): ?string
    {
        $user = Auth::user();
        
        if ($user) {
            if ($user->isAdmin()) {
                return 'admin_path'; // Use your admin path
            } else {
                return "uploads/case_docs/documents/{$user->id}";
            }
        }

        return parent::getLeftPath(); // Use the default path
    }

    public function getRightPath(): ?string
    {
        $user = Auth::user();
        
        if ($user) {
            if ($user->isAdmin()) {
                return 'admin_path'; // Use your admin path
            } else {
                return "uploads/case_docs/documents/{$user->id}";
            }
        }

        return parent::getRightPath(); // Use the default path
    }
}