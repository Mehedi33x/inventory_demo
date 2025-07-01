<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public function storeFile($file, $directory)
    {
        $filename = date('Ymdhsi') . '.' . $file->getClientOriginalExtension();
        $file->storeAs($directory, $filename, 'public');
        return $filename;
    }
    public function updateFile($newFile, $directory, $oldFilename = null)
    {
        dd($oldFilename);
        if ($oldFilename) {
            $this->deleteFile($directory . '/' . $oldFilename);
        }
        return $this->storeFile($newFile, $directory);
    }
    public function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
