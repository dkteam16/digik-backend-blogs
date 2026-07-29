@extends('admin.layouts.app')
@section('title','Newsletter')
@section('page-title','Newsletter')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Newsletter Subscribers</h4>
        <small class="text-muted">{{ $totalCount }} total subscriber{{ $totalCount === 1 ? '' : 's' }}</small>
    </div>
</div>

{{-- Filters --}}
<div class="card-panel p-3 mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" id="filter-search" class="form-control form-control-sm" placeholder="Search by email...">
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="button" id="btn-filter" class="btn btn-primary btn-sm">Filter</button>
            <button type="button" id="btn-reset" class="btn btn-outline-secondary btn-sm">Reset</button>
        </div>
    </div>
</div>

<form id="bulk-form" action="{{ route('admin.newsletter.bulk-action') }}" method="POST">
    @csrf
</form>

<div class="tbl-wrap">
    <div class="p-3 border-bottom d-flex align-items-center gap-2">
        <select name="action" form="bulk-form" class="form-select form-select-sm w-auto">
            <option value="">Bulk Actions</option>
            <option value="delete">Delete</option>
        </select>
        <button type="submit" form="bulk-form" class="btn btn-sm btn-secondary"
                onclick="return confirm('Apply action?')">Apply</button>
    </div>

    <table id="newsletter-table" class="table" style="width:100%">
        <thead>
            <tr>
                <th width="40"><input type="checkbox" id="sel-all" class="form-check-input"></th>
                <th>Email</th>
                <th>Subscribed On</th>
                <th width="80">Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
let newsletterTable;

$(function () {
    newsletterTable = initAdminDataTable('#newsletter-table', {
        ajax: {
            url: "{{ route('admin.newsletter.data') }}",
            data: function (d) {
                d.q = $('#filter-search').val();
            }
        },
        columns: [
            { data: 'checkbox',  name: 'checkbox',   orderable: false, searchable: false },
            { data: 'email',     name: 'email' },
            { data: 'date_col',  name: 'created_at' },
            { data: 'actions',   name: 'actions',    orderable: false, searchable: false },
        ],
        order: [[2, 'desc']],
    });

    $('#btn-filter').on('click', function () { newsletterTable.ajax.reload(); });
    $('#btn-reset').on('click', function () {
        $('#filter-search').val('');
        newsletterTable.ajax.reload();
    });
    $('#filter-search').on('keyup', function (e) {
        if (e.key === 'Enter') newsletterTable.ajax.reload();
    });
});

document.getElementById('sel-all').addEventListener('change', function() {
    document.querySelectorAll('.cbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
