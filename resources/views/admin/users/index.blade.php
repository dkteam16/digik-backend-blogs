@extends('admin.layouts.app')
@section('title','Users')
@section('page-title','Users')

@section('content')
<div class="page-head">
    <div>
        <h4>Users</h4>
        <div class="sub">Manage admin panel users</div>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New User
    </a>
</div>

<div class="panel mb-3">
    <div class="filter-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Search by name or email...">
                </div>
            </div>
            <div class="col-md-2">
                <select id="filter-role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="author">Author</option>
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

<div class="panel">
    <div class="table-responsive">
        <table id="users-table" class="table" style="width:100%">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Posts</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th width="140">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let usersTable;

$(function () {
    usersTable = initAdminDataTable('#users-table', {
        ajax: {
            url: "{{ route('admin.users.data') }}",
            data: function (d) {
                d.role = $('#filter-role').val();
                d.q    = $('#filter-search').val();
            }
        },
        columns: [
            { data: 'user_col',     name: 'name' },
            { data: 'role_label',   name: 'role',       orderable: false },
            { data: 'posts_count',  name: 'posts_count', orderable: false,
              render: data => `<span class="pill tint-secondary">${data}</span>` },
            { data: 'status_label', name: 'is_active',  orderable: false },
            { data: 'created_at',   name: 'created_at' },
            { data: 'actions',      name: 'actions',    orderable: false, searchable: false },
        ],
        order: [[4, 'desc']],
    });

    $('#btn-filter').on('click', function () { usersTable.ajax.reload(); });
    $('#btn-reset').on('click', function () {
        $('#filter-search').val('');
        $('#filter-role').val('');
        usersTable.ajax.reload();
    });
    $('#filter-search').on('keyup', function (e) {
        if (e.key === 'Enter') usersTable.ajax.reload();
    });
});
</script>
@endpush
