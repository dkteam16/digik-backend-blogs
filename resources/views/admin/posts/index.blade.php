@extends('admin.layouts.app')
@section('title', 'Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Posts</h4>
        <small class="text-muted">Manage all blog posts</small>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Post
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" placeholder="Search posts...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['draft','published','scheduled','archived'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Action -->
<form id="bulk-form" action="{{ route('admin.posts.bulk-action') }}" method="POST">
    @csrf
    <div class="admin-table">
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
            <select name="action" class="form-select form-select-sm w-auto">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="archive">Archive</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn btn-sm btn-secondary"
                    onclick="return confirm('Apply bulk action?')">Apply</button>
            <span class="ms-auto text-muted small">{{ $posts->total() }} posts found</span>
        </div>

        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="select-all" class="form-check-input"></th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td><input type="checkbox" name="post_ids[]" value="{{ $post->id }}" class="form-check-input post-check"></td>
                    <td>
                        <div class="fw-medium">{{ Str::limit($post->title, 50) }}</div>
                        @if($post->is_featured)
                            <span class="badge bg-warning bg-opacity-20 text-warning" style="font-size:.65rem">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $post->author->name }}</td>
                    <td class="text-muted small">{{ $post->category->name ?? '—' }}</td>
                    <td>
                        @php $sc = ['published'=>'success','draft'=>'warning','archived'=>'secondary','scheduled'=>'info'] @endphp
                        <span class="status-badge bg-{{ $sc[$post->status] ?? 'secondary' }} bg-opacity-15 text-{{ $sc[$post->status] ?? 'secondary' }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ number_format($post->views_count) }}</td>
                    <td class="text-muted small">{{ $post->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.posts.edit', $post) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-file-earmark-text fs-2 d-block mb-2"></i>
                        No posts found. <a href="{{ route('admin.posts.create') }}">Create one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $posts->links() }}
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.post-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
