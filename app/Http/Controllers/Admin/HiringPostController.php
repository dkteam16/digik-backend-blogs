<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiringPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HiringPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = HiringPost::with('author')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts       = $query->paginate(15)->withQueryString();
        $departments = HiringPost::whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.hiring-posts.index', compact('posts', 'departments'));
    }

    public function create(): View
    {
        return view('admin.hiring-posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateHiringPost($request);
        $validated['slug']      = Str::slug($validated['title']);
        $validated['author_id'] = auth()->id();

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        HiringPost::create($validated);

        return redirect()->route('admin.hiring-posts.index')
            ->with('success', 'Hiring post created successfully!');
    }

    public function edit(HiringPost $hiringPost): View
    {
        return view('admin.hiring-posts.edit', ['post' => $hiringPost]);
    }

    public function update(Request $request, HiringPost $hiringPost): RedirectResponse
    {
        $validated = $this->validateHiringPost($request);
        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published' && !$hiringPost->published_at) {
            $validated['published_at'] = now();
        }

        $hiringPost->update($validated);

        return redirect()->route('admin.hiring-posts.index')
            ->with('success', 'Hiring post updated successfully!');
    }

    public function destroy(HiringPost $hiringPost): RedirectResponse
    {
        $hiringPost->delete();

        return redirect()->route('admin.hiring-posts.index')
            ->with('success', 'Hiring post deleted successfully!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action'   => 'required|in:delete,publish,draft,close,archive',
            'post_ids' => 'required|array',
        ]);

        $posts = HiringPost::whereIn('id', $request->post_ids);

        match ($request->action) {
            'delete'  => $posts->delete(),
            'publish' => $posts->update(['status' => 'published', 'published_at' => now()]),
            'draft'   => $posts->update(['status' => 'draft']),
            'close'   => $posts->update(['status' => 'closed']),
            'archive' => $posts->update(['status' => 'archived']),
        };

        return back()->with('success', 'Bulk action applied successfully!');
    }

    private function validateHiringPost(Request $request): array
    {
        return $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'qualification'         => 'nullable|string',
            'experience'            => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'location'              => 'nullable|string|max:255',
            'work_type'             => 'nullable|in:onsite,remote,hybrid',
            'employment_type'       => 'required|in:full-time,part-time,contract,internship',
            'apply_url'             => 'nullable|url|max:255',
            'status'                => 'required|in:draft,published,closed,archived',
            'is_featured'           => 'boolean',
            'application_deadline'  => 'nullable|date',
            'meta_title'            => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
            'meta_keywords'         => 'nullable|string|max:255',
            'published_at'          => 'nullable|date',
        ]);
    }
}
