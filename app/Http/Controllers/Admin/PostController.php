<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.posts.index', compact('categories'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = Post::with(['author', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        return DataTables::of($query)
            ->addColumn('checkbox', fn (Post $post) => '<input type="checkbox" name="post_ids[]" value="'.$post->id.'" class="form-check-input post-check">')
            ->addColumn('title_col', fn (Post $post) => '
                <div class="d-flex align-items-center">
                    '.($post->featured_image ? '<img src="'.asset('storage/'.$post->featured_image).'" class="rounded me-2" width="40" height="40" style="object-fit:cover">' : '').'
                    <div>
                        <a href="'.route('admin.posts.edit', $post).'" class="fw-semibold text-decoration-none text-dark">'.e($post->title).'</a>
                        '.($post->is_featured ? '<span class="pill tint-warning ms-1">Featured</span>' : '').'
                    </div>
                </div>
            ')
            ->addColumn('author_name', fn (Post $post) => $post->author->name ?? '—')
            ->addColumn('category_name', fn (Post $post) => $post->category->name ?? '—')
            ->addColumn('status_label', fn (Post $post) => match($post->status) {
                'published' => '<span class="pill dot tint-success">Published</span>',
                'draft'     => '<span class="pill dot tint-secondary">Draft</span>',
                'scheduled' => '<span class="pill dot tint-info">Scheduled</span>',
                'archived'  => '<span class="pill dot tint-warning">Archived</span>',
                default     => '<span class="pill dot tint-secondary">'.$post->status.'</span>',
            })
            ->addColumn('actions', fn (Post $post) => '
                <div class="row-actions">
                    <a href="'.route('admin.posts.edit', $post).'" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-delete-url="'.route('admin.posts.destroy', $post).'" data-item-name="'.e($post->title).'"><i class="bi bi-trash"></i></button>
                </div>
            ')
            ->editColumn('views_count', fn (Post $post) => number_format($post->views_count))
            ->editColumn('created_at', fn (Post $post) => $post->created_at->format('M d, Y'))
            ->rawColumns(['checkbox', 'title_col', 'status_label', 'actions'])
            ->make(true);
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->get();
        $tags       = Tag::all();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePost($request);
        $validated['slug']      = Str::slug($validated['title']);
        $validated['author_id'] = auth()->id();

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        }

        $post = Post::create($validated);

        if ($request->filled('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post): View
    {
        $categories = Category::where('is_active', true)->get();
        $tags       = Tag::all();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $this->validatePost($request, $post->id);
        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('posts', 'public');
        }

        $post->update($validated);
        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action'   => 'required|in:delete,publish,draft,archive',
            'post_ids' => 'required|array',
        ]);

        $posts = Post::whereIn('id', $request->post_ids);

        match ($request->action) {
            'delete'  => $posts->delete(),
            'publish' => $posts->update(['status' => 'published', 'published_at' => now()]),
            'draft'   => $posts->update(['status' => 'draft']),
            'archive' => $posts->update(['status' => 'archived']),
        };

        return back()->with('success', 'Bulk action applied successfully!');
    }

    private function validatePost(Request $request, ?int $postId = null): array
    {
        return $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'category_id'      => 'nullable|exists:categories,id',
            'status'           => 'required|in:draft,published,scheduled,archived',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'featured_image'   => 'nullable|image|max:2048',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'published_at'     => 'nullable|date',
        ]);
    }
}
