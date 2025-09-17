<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Move file to public/uploads/ckeditor/
            $file->move(public_path('uploads/ckeditor/'), $filename);
    
            // Return public URL
            return response()->json([
                'uploaded' => true,
                'url' => asset('uploads/ckeditor/' . $filename)
            ], 200);
        }
    
        return response()->json(['error' => 'No file uploaded'], 400);
    }
    // public function upload(Request $request)
    // {
    //     if ($request->hasFile('upload')) {
    //         $file = $request->file('upload');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $path = $file->storeAs('uploads', $filename, 'public');

    //         return response()->json([
    //             'url' => asset('storage/' . $path)
    //         ]);
    //     }
    //     return response()->json(['error' => 'No file uploaded'], 400);
    // }
    // public function upload(Request $request)
    // {
    //     if ($request->hasFile('upload')) {
    //         $file = $request->file('upload');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $path = $file->storeAs('uploads', $filename, 'public');

    //         // CKEditor expects a `url` key in the JSON response
    //         return response()->json([
    //             'url' => asset('storage/' . $path)  // Ensure this URL is publicly accessible
    //         ]);
    //     }

    //     return response()->json(['error' => 'No file uploaded'], 400);
    // }
    public function uploadOldWorking(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/ckeditor/', $filename, 'public');

            // Ensure CKEditor gets a proper JSON response
            return response()->json([
                'uploaded' => true,
                'url' => asset('storage/uploads/ckeditor/' . $filename)
            ], 200);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }


    // public function upload(Request $request)
    // {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     // Store the uploaded file
    //     $path = $request->file('upload')->store('public/uploads');

    //     // Get the public URL of the stored file
    //     $url = Storage::url($path);

    //     // Return the URL of the uploaded image
    //     return response()->json([
    //         'url' => $url,
    //     ]);
    // }

}
