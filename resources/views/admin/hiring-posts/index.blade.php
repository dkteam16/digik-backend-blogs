@extends('admin.layouts.app')
@section('title', 'Hiring Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Hiring Posts</h4>
        <small class="text-muted">Manage job listings</small>
    </div>
    <a href="{{ route('admin.hiring-posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Hiring Post
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" placeholder="Search hiring posts...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['draft','published','closed','archived'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.hiring-posts.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Action (kept outside the table so per-row delete forms below are not nested inside it) -->
<form id="bulk-form" action="{{ route('admin.hiring-posts.bulk-action') }}" method="POST">
    @csrf
</form>

<div class="admin-table">
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
            <select name="action" form="bulk-form" class="form-select form-select-sm w-auto">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="close">Close</option>
                <option value="archive">Archive</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" form="bulk-form" class="btn btn-sm btn-secondary"
                    onclick="return confirm('Apply bulk action?')">Apply</button>
            <span class="ms-auto text-muted small">{{ $posts->total() }} hiring posts found</span>
        </div>

        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="select-all" class="form-check-input"></th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Employment Type</th>
                    <th>Status</th>
                    <!-- <th>Views</th> -->
                    <th>Date</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td><input type="checkbox" name="post_ids[]" form="bulk-form" value="{{ $post->id }}" class="form-check-input post-check"></td>
                    <td>
                        <div class="fw-medium">{{ Str::limit($post->title, 50) }}</div>
                        @if($post->is_featured)
                            <span class="badge bg-warning bg-opacity-20 text-warning" style="font-size:.65rem">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $post->department ?? '—' }}</td>
                    <td class="text-muted small">{{ $post->location ?? '—' }}</td>
                    <td class="text-muted small">{{ ucfirst($post->employment_type) }}</td>
                    <td>
                        @php $sc = ['published'=>'success','draft'=>'warning','archived'=>'secondary','closed'=>'danger'] @endphp
                        <span>
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <!-- <td class="text-muted small">{{ number_format($post->views_count) }}</td> -->
                    <td class="text-muted small">{{ $post->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.hiring-posts.edit', $post) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-delete-url="{{ route('admin.hiring-posts.destroy', $post) }}"
                                    data-item-name="{{ $post->title }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-briefcase fs-2 d-block mb-2"></i>
                        No hiring posts found. <a href="{{ route('admin.hiring-posts.create') }}">Create one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $posts->links() }}
        </div>
</div>

<!-- Delete Confirmation Modal (shared by all rows) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Hiring Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="delete-item-name"></strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.post-check').forEach(cb => cb.checked = this.checked);
});

document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('delete-form').action = button.getAttribute('data-delete-url');
    document.getElementById('delete-item-name').textContent = button.getAttribute('data-item-name');
});
</script>
@endpush
