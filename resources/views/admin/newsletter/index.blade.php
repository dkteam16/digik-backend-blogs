@extends('admin.layouts.app')
@section('title','Newsletter')
@section('page-title','Newsletter')

@section('content')
<div class="page-head">
    <div>
        <h4>Newsletter Subscribers</h4>
        <div class="sub">{{ number_format($totalCount) }} total subscriber{{ $totalCount === 1 ? '' : 's' }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="panel mb-3">
    <div class="filter-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Search by email...">
                </div>
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

<form id="bulk-form" action="{{ route('admin.newsletter.bulk-action') }}" method="POST">
    @csrf
</form>

<div class="panel">
    <div class="panel-head">
        <div class="d-flex align-items-center gap-2">
            <select name="action" form="bulk-form" class="form-select form-select-sm w-auto">
                <option value="">Bulk Actions</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" form="bulk-form" class="btn btn-sm btn-outline-secondary"
                    onclick="return confirm('Apply action?')">Apply</button>
        </div>
    </div>

    <div class="table-responsive">
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
