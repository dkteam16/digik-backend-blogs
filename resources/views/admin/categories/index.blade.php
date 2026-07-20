@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Categories</h4>
        <small class="text-muted">Organise your blog content</small>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Category
    </a>
</div>

<div class="admin-table">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Parent</th>
                <th>Posts</th>
                <th>Status</th>
                <th>Order</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td class="fw-medium">{{ $category->name }}</td>
                <td><code class="text-muted small">{{ $category->slug }}</code></td>
                <td class="text-muted small">{{ $category->parent->name ?? '—' }}</td>
                <td><span class="badge bg-secondary bg-opacity-15 text-secondary">{{ $category->posts_count }}</span></td>
                <td>
                    @if($category->is_active)
                        <span class="status-badge bg-success bg-opacity-15 text-success">Active</span>
                    @else
                        <span class="status-badge bg-secondary bg-opacity-15 text-secondary">Inactive</span>
                    @endif
                </td>
                <td class="text-muted small">{{ $category->sort_order }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                    No categories yet. <a href="{{ route('admin.categories.create') }}">Create one!</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-3">{{ $categories->links() }}</div>
</div>
@endsection
