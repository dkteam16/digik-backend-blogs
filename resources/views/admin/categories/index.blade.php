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
    <table id="categories-table" class="table table-hover mb-0" style="width:100%">
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
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    initAdminDataTable('#categories-table', {
        ajax: "{{ route('admin.categories.data') }}",
        columns: [
            { data: 'name',          name: 'name' },
            { data: 'slug',          name: 'slug',       orderable: false,
              render: data => `<code class="text-muted small">${data}</code>` },
            { data: 'parent_name',   name: 'parent.name', orderable: false },
            { data: 'posts_count',   name: 'posts_count', orderable: false,
              render: data => `<span class="badge bg-secondary bg-opacity-15 text-secondary">${data}</span>` },
            { data: 'status_label',  name: 'is_active',  orderable: false },
            { data: 'sort_order',    name: 'sort_order' },
            { data: 'actions',       name: 'actions',    orderable: false, searchable: false },
        ],
        order: [[5, 'asc']],
    });
});
</script>
@endpush
