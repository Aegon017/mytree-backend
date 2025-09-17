<?php

namespace App\Traits;

use Intervention\Image\Laravel\Facades\Image;

/**
 * @category	Trait
 * @package		Rest Response
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

trait ImageUpload
{
    protected function imageUploadOld($file, $destinationFloder, $fileName, $sizes = null, $folderReq = null)
    {
        $filename = $fileName . '.' . $file->getClientOriginalExtension();
        $image = Image::read($file);
        // Resize image
        // dd(str_replace('\\', '/', public_path($destinationFloder . $filename)));
        $image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        })->save(public_path($destinationFloder . $filename));
        return $filename;
    }
    
    protected function imageUpload($file, $destinationFolder, $fileName, $sizes = null, $folderReq = null)
    {
        $filename = $fileName . '.' . $file->getClientOriginalExtension();
        
        // Read the image
        $image = Image::read($file);
    
        // Get original width & height
        $width = $image->width();
        $height = $image->height();
    
        // Resize image to maintain original dimensions
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->save(public_path($destinationFolder . $filename));
    
        return $filename;
    }
}
