<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        $files = collect(Storage::disk('public')->files('posts'))
            ->merge(Storage::disk('public')->files('categories'))
            ->map(function ($path) {
                return [
                    'path'     => $path,
                    'url'      => asset('storage/' . $path),
                    'name'     => basename($path),
                    'size'     => Storage::disk('public')->size($path),
                    'modified' => Storage::disk('public')->lastModified($path),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        return view('admin.media.index', compact('files'));
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'    => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'folder'  => 'in:posts,categories,general',
        ]);

        $folder = $request->input('folder', 'general');
        $path   = $request->file('file')->store($folder, 'public');

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'path'    => $path,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $path = $request->input('path');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', 'File deleted!');
    }
}
