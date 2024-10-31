<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class FileService
{
    protected $disk;

    public function __construct($disk = 'public')
    {
        $this->disk = $disk;
    }


public function upload($file, $directory = 'uploads')
{
    $path = Storage::disk($this->disk ?? 'public')->put($directory, $file);
    return Storage::disk($this->disk ?? 'public')->url($path);
}


//     public function upload($file, $directory = 'uploads')
//     {
//         if($this->disk == 'public') {
//         $path = Storage::disk($this->disk)->put($directory, $file);
//         return Storage::disk($this->disk)->url($path);
//         }
//         return $path;

// //        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
//         // $path = Storage::disk($this->disk)->put($directory ,$file);
//     }

    public function update($file, $existingFilePath, $directory = 'uploads')
    {

        if (Storage::disk($this->disk)->exists($existingFilePath)) {
            Storage::disk($this->disk)->delete($existingFilePath);
        }

        // سپس فایل جدید را آپلود می‌کنیم
        return $this->upload($file, $directory);
    }

    /**
     * خواندن فایل و نمایش آن
     */
    public function read($filePath)
    {
        if (Storage::disk($this->disk)->exists($filePath)) {
            if($this->disk == 'public'){
                return Storage::disk($this->disk)->url($filePath);
            } else {
                $content = Storage::disk($this->disk)->get($filePath);
                return base64_encode($content);
//                return "file exist but cant read";
            }
        }

        return null;
    }

    /**
     * حذف فایل
     */
    public function delete($filePath)
    {
        if (Storage::disk($this->disk)->exists($filePath)) {
            return Storage::disk($this->disk)->delete($filePath);
        }

        return false;
    }
}
