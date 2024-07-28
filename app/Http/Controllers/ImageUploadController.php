<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json(['message' => 'No file uploaded'], 400);
            }

            $file = $request->file('image');

            if (!$file->isValid()) {
                return response()->json(['message' => 'Uploaded file is not valid'], 400);
            }

            $path = $file->store('uploads');

            if (!$path) {
                return response()->json(['message' => 'Uploaded file not found'], 404);
            }

            $imagePath = storage_path('app/' . $path);
            Log::info('imagepath: ' . $imagePath);

            return response()->json(['message' => 'Uploaded image successfully', 'imageUrl' => $imagePath], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error uploading image: ' . $e->getMessage()], 500);
        }
    }
}
