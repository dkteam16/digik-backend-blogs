<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiringPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class HiringPostController extends Controller
{
    public function index(): View
    {
        $departments = HiringPost::whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.hiring-posts.index', compact('departments'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = HiringPost::with('author');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        return DataTables::of($query)
            ->addColumn('checkbox', fn (HiringPost $post) => '<input type="checkbox" name="post_ids[]" value="'.$post->id.'" class="form-check-input post-check">')
            ->addColumn('title_col', fn (HiringPost $post) => '
                <div>
                    <a href="'.route('admin.hiring-posts.edit', $post).'" class="fw-semibold text-decoration-none text-dark">'.e($post->title).'</a>
                    '.($post->is_featured ? '<span class="pill tint-warning ms-1">Featured</span>' : '').'
                </div>
            ')
            ->addColumn('employment_type_label', fn (HiringPost $post) => ucfirst($post->employment_type))
            ->addColumn('status_label', fn (HiringPost $post) => match($post->status) {
                'published' => '<span class="pill dot tint-success">Published</span>',
                'draft'     => '<span class="pill dot tint-secondary">Draft</span>',
                'closed'    => '<span class="pill dot tint-danger">Closed</span>',
                'archived'  => '<span class="pill dot tint-warning">Archived</span>',
                default     => '<span class="pill dot tint-secondary">'.$post->status.'</span>',
            })
            ->addColumn('actions', fn (HiringPost $post) => '
                <div class="row-actions">
                    <a href="'.route('admin.hiring-posts.edit', $post).'" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-delete-url="'.route('admin.hiring-posts.destroy', $post).'" data-item-name="'.e($post->title).'"><i class="bi bi-trash"></i></button>
                </div>
            ')
            ->editColumn('location', fn (HiringPost $post) => $post->location ?? '—')
            ->editColumn('department', fn (HiringPost $post) => $post->department ?? '—')
            ->editColumn('created_at', fn (HiringPost $post) => $post->created_at->format('M d, Y'))
            ->rawColumns(['checkbox', 'title_col', 'status_label', 'actions'])
            ->make(true);
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
