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
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="filter-search" class="form-control form-control-sm" placeholder="Search hiring posts...">
            </div>
            <div class="col-md-2">
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['draft','published','closed','archived'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter-department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="button" id="btn-filter" class="btn btn-primary btn-sm flex-fill">Filter</button>
                <button type="button" id="btn-reset" class="btn btn-outline-secondary btn-sm flex-fill">Reset</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action (kept outside the table so per-row delete buttons below are not nested inside it) -->
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
        </div>

        <table id="hiring-posts-table" class="table table-hover mb-0" style="width:100%">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="select-all" class="form-check-input"></th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Employment Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
let hiringPostsTable;

$(function () {
    hiringPostsTable = initAdminDataTable('#hiring-posts-table', {
        ajax: {
            url: "{{ route('admin.hiring-posts.data') }}",
            data: function (d) {
                d.status     = $('#filter-status').val();
                d.department = $('#filter-department').val();
                d.q          = $('#filter-search').val();
            }
        },
        columns: [
            { data: 'checkbox',              name: 'checkbox',   orderable: false, searchable: false },
            { data: 'title_col',             name: 'title' },
            { data: 'department',            name: 'department' },
            { data: 'location',              name: 'location',   orderable: false },
            { data: 'employment_type_label', name: 'employment_type', orderable: false },
            { data: 'status_label',          name: 'status',     orderable: false },
            { data: 'created_at',            name: 'created_at' },
            { data: 'actions',               name: 'actions',    orderable: false, searchable: false },
        ],
        order: [[6, 'desc']],
    });

    $('#btn-filter').on('click', function () { hiringPostsTable.ajax.reload(); });
    $('#btn-reset').on('click', function () {
        $('#filter-search').val('');
        $('#filter-status').val('');
        $('#filter-department').val('');
        hiringPostsTable.ajax.reload();
    });
    $('#filter-search').on('keyup', function (e) {
        if (e.key === 'Enter') hiringPostsTable.ajax.reload();
    });
});

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
