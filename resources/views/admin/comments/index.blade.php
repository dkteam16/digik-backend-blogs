@extends('admin.layouts.app')
@section('title','Comments')
@section('page-title','Comments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Comments</h4>
        <small class="text-muted">
            @if($pendingCount > 0)
                <span class="text-danger fw-semibold">{{ $pendingCount }} pending approval</span>
            @else
                All caught up!
            @endif
        </small>
    </div>
</div>

{{-- Filters --}}
<div class="card-panel p-3 mb-3">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control form-control-sm" placeholder="Search by name or content...">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Bulk action --}}
<form id="bulk-form" action="{{ route('admin.comments.bulk-action') }}" method="POST">
    @csrf
    <div class="tbl-wrap">
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
            <select name="action" class="form-select form-select-sm w-auto">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn btn-sm btn-secondary"
                    onclick="return confirm('Apply action?')">Apply</button>
            <span class="ms-auto text-muted small">{{ $comments->total() }} comments</span>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="sel-all" class="form-check-input"></th>
                    <th>Author</th>
                    <th>Comment</th>
                    <th>Post</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th width="130">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                <tr class="{{ !$comment->is_approved ? 'table-warning bg-opacity-25' : '' }}">
                    <td><input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="form-check-input cbox"></td>
                    <td>
                        <div class="fw-medium small">{{ $comment->author_name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $comment->author_email }}</div>
                    </td>
                    <td class="text-muted small">{{ Str::limit($comment->content, 80) }}</td>
                    <td class="text-muted small">
                        @if($comment->post)
                            <a href="{{ route('admin.posts.edit', $comment->post) }}" class="text-decoration-none">
                                {{ Str::limit($comment->post->title, 35) }}
                            </a>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($comment->is_approved)
                            <span class="sbadge bg-success bg-opacity-15 text-success">Approved</span>
                        @else
                            <span class="sbadge bg-warning bg-opacity-15 text-warning">Pending</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $comment->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            @if(!$comment->is_approved)
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-success" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning" title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                                  onsubmit="return confirm('Delete comment?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-chat-dots fs-2 d-block mb-2"></i>No comments found.
                </td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $comments->links() }}</div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('sel-all').addEventListener('change', function() {
    document.querySelectorAll('.cbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
