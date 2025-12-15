<?php

namespace App\Http\Controllers;

use App\Models\ProposalFile;
use App\Http\Request;
use App\Http\Response;
use App\Support\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        
        if (!$file || !$file['tmp_name']) {
            return Response::json(['error' => 'No file uploaded'], 400);
        }

        // Валидация
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimes)) {
            return Response::json(['error' => 'Invalid file type'], 400);
        }

        if ($file['size'] > $maxSize) {
            return Response::json(['error' => 'File too large'], 400);
        }

        // Сохранение
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(20)) . '.' . $extension;
        $path = 'uploads/' . date('Y/m') . '/' . $filename;

        $uploadDir = storage_path('app/public/' . dirname($path));
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], storage_path('app/public/' . $path));

        $proposalFile = ProposalFile::create([
            'user_id' => session('user_id'),
            'proposal_id' => $request->input('proposal_id'),
            'type' => $request->input('type', 'image'),
            'original_name' => $file['name'],
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $file['size'],
        ]);

        return Response::json([
            'file' => $proposalFile,
            'url' => asset('storage/' . $path),
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $file = ProposalFile::findOrFail($id);

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin' && $file->user_id !== $userId) {
            abort(403);
        }

        if (file_exists(storage_path('app/public/' . $file->path))) {
            unlink(storage_path('app/public/' . $file->path));
        }

        $file->delete();

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Deleted']);
        }

        session('success', 'Файл удален');
        redirect_back();
    }
}

