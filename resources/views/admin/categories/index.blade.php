@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<div class="page-head">
    <div>
        <h4>Categories</h4>
        <div class="sub">Organise your blog content</div>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Category
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table id="categories-table" class="table mb-0" style="width:100%">
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
</div>

{{-- Delete Confirmation Modal (shared by all rows) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Category</h5>
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
$(function () {
    initAdminDataTable('#categories-table', {
        ajax: "{{ route('admin.categories.data') }}",
        columns: [
            { data: 'name',          name: 'name' },
            { data: 'slug',          name: 'slug',       orderable: false,
              render: data => `<code class="text-muted small">${data}</code>` },
            { data: 'parent_name',   name: 'parent.name', orderable: false },
            { data: 'posts_count',   name: 'posts_count', orderable: false,
              render: data => `<span class="pill tint-secondary">${data}</span>` },
            { data: 'status_label',  name: 'is_active',  orderable: false },
            { data: 'sort_order',    name: 'sort_order' },
            { data: 'actions',       name: 'actions',    orderable: false, searchable: false },
        ],
        order: [[5, 'asc']],
    });
});

// Populate the shared delete modal from the clicked row's data-* attributes.
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('delete-form').action = button.getAttribute('data-delete-url');
    document.getElementById('delete-item-name').textContent = button.getAttribute('data-item-name');
});
</script>
@endpush
