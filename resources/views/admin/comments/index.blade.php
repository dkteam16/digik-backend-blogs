@extends('admin.layouts.app')
@section('title','Comments')
@section('page-title','Comments')

@section('content')
<div class="page-head">
    <div>
        <h4>Comments</h4>
        <div class="sub">
            @if($pendingCount > 0)
                <span class="pill dot tint-warning">{{ $pendingCount }} pending approval</span>
            @else
                <span class="pill dot tint-success">All caught up</span>
            @endif
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="panel mb-3">
    <div class="filter-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Search by name or content...">
                </div>
            </div>
            <div class="col-md-2">
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="button" id="btn-filter" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <button type="button" id="btn-reset" class="btn btn-outline-secondary btn-sm">Reset</button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk action (kept outside the table so per-row approve/reject/delete forms below are not nested inside it) --}}
<form id="bulk-form" action="{{ route('admin.comments.bulk-action') }}" method="POST">
    @csrf
</form>

<div class="panel">
    <div class="panel-head">
        <div class="d-flex align-items-center gap-2">
            <select name="action" form="bulk-form" class="form-select form-select-sm w-auto">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" form="bulk-form" class="btn btn-sm btn-outline-secondary"
                    onclick="return confirm('Apply action?')">Apply</button>
        </div>
    </div>

    <div class="table-responsive">
    <table id="comments-table" class="table" style="width:100%">
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
        <tbody></tbody>
    </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let commentsTable;

$(function () {
    commentsTable = initAdminDataTable('#comments-table', {
        ajax: {
            url: "{{ route('admin.comments.data') }}",
            data: function (d) {
                d.status = $('#filter-status').val();
                d.q      = $('#filter-search').val();
            }
        },
        columns: [
            { data: 'checkbox',     name: 'checkbox',     orderable: false, searchable: false },
            { data: 'author_col',   name: 'author_name' },
            { data: 'content_col',  name: 'content',      orderable: false },
            { data: 'post_col',     name: 'post.title',   orderable: false },
            { data: 'status_label', name: 'is_approved',  orderable: false },
            { data: 'date_col',     name: 'created_at' },
            { data: 'actions',      name: 'actions',      orderable: false, searchable: false },
        ],
        order: [[5, 'desc']],
    });

    $('#btn-filter').on('click', function () { commentsTable.ajax.reload(); });
    $('#btn-reset').on('click', function () {
        $('#filter-search').val('');
        $('#filter-status').val('');
        commentsTable.ajax.reload();
    });
    $('#filter-search').on('keyup', function (e) {
        if (e.key === 'Enter') commentsTable.ajax.reload();
    });
});

document.getElementById('sel-all').addEventListener('change', function() {
    document.querySelectorAll('.cbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
