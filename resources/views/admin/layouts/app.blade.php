<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — BlogAdmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --sb-w:252px; --sb-bg:#15171f; --sb-bg-2:#1b1e28;
            --sb-text:#a2abbd; --sb-text-strong:#f2f5fa; --sb-muted:#7d8799;
            --sb-hover:rgba(255,255,255,.055); --sb-active:#4e6ef2; --sb-border:rgba(255,255,255,.07);
            --top-h:58px;
            /* content surface tokens */
            --ink:#101828; --ink-soft:#667085;
            --line:#eaeefa; --line-soft:#f1f3fa;
            --radius:14px; --shadow:0 1px 3px rgba(16,24,40,.06);
            --head-bg:#fbfcff; --row-hover:#fafbff;
        }
        body { background:#f0f2f8; font-family:'Segoe UI',system-ui,sans-serif; margin:0; }

        /* ---------- Sidebar shell: fixed header + scrolling nav + pinned footer ---------- */
        #sidebar {
            width:var(--sb-w); height:100vh; background:var(--sb-bg); position:fixed; top:0; left:0; z-index:1040;
            display:flex; flex-direction:column; overflow:hidden;
            border-right:1px solid rgba(0,0,0,.2); transition:transform .28s cubic-bezier(.4,0,.2,1);
        }
        .sb-brand {
            flex-shrink:0; padding:0 1rem; height:var(--top-h); border-bottom:1px solid var(--sb-border);
            color:#fff; font-size:1rem; font-weight:700; display:flex; align-items:center; gap:.6rem; letter-spacing:-.01em;
        }
        .sb-brand .brand-mark {
            width:32px; height:32px; border-radius:9px; flex-shrink:0; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg,#4e6ef2,#7d92f7); color:#fff; font-size:.95rem;
            box-shadow:0 3px 10px rgba(78,110,242,.35);
        }
        .sb-brand .dot { color:#8ea2f9; font-weight:600; }
        .sb-close { margin-left:auto; background:none; border:none; color:var(--sb-muted); font-size:1.25rem; line-height:1;
                    padding:.35rem .5rem; border-radius:8px; cursor:pointer; }
        .sb-close:hover { background:var(--sb-hover); color:#fff; }

        .sb-nav { flex:1 1 auto; overflow-y:auto; overscroll-behavior:contain; padding:.5rem 0 1rem; }
        .sb-nav::-webkit-scrollbar { width:6px; }
        .sb-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,.12); border-radius:3px; }
        .sb-nav::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.2); }
        .sb-nav { scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.14) transparent; }

        .sb-section {
            padding:.85rem 1.15rem .35rem; font-size:.67rem; letter-spacing:.9px; text-transform:uppercase;
            color:var(--sb-muted); font-weight:700;
        }
        .sb-section:not(:first-child) { margin-top:.35rem; border-top:1px solid var(--sb-border); padding-top:.9rem; }

        .sb-link {
            position:relative; display:flex; align-items:center; gap:.65rem;
            margin:.1rem .6rem; padding:.55rem .7rem; border-radius:9px;
            color:var(--sb-text); text-decoration:none; font-size:.855rem; font-weight:500;
            transition:background .16s ease, color .16s ease;
        }
        .sb-link:hover { background:var(--sb-hover); color:var(--sb-text-strong); }
        .sb-link.active { background:linear-gradient(90deg,rgba(78,110,242,.22),rgba(78,110,242,.08)); color:#fff; font-weight:600; }
        .sb-link.active::before {
            content:''; position:absolute; left:-.6rem; top:50%; transform:translateY(-50%);
            width:3px; height:20px; border-radius:0 3px 3px 0; background:var(--sb-active);
        }
        .sb-link i { font-size:1.02rem; width:1.15rem; text-align:center; flex-shrink:0; color:var(--sb-muted); transition:color .16s ease; }
        .sb-link:hover i { color:var(--sb-text); }
        .sb-link.active i { color:#8ea2f9; }
        .sb-link .ext { margin-left:auto; font-size:.7rem !important; opacity:.5; width:auto; }
        .sb-badge {
            margin-left:auto; background:#e5484d; color:#fff; font-size:.65rem; min-width:19px; text-align:center;
            padding:.12em .45em; border-radius:20px; font-weight:700; line-height:1.5;
            box-shadow:0 0 0 2px rgba(229,72,77,.18);
        }

        /* ---------- Pinned footer (was scrolling off-screen) ---------- */
        .sb-footer { flex-shrink:0; border-top:1px solid var(--sb-border); background:var(--sb-bg-2); padding:.7rem; }
        .sb-user { display:flex; align-items:center; gap:.6rem; padding:.35rem .4rem .6rem; }
        .sb-avatar {
            width:34px; height:34px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg,#4e6ef2,#7d92f7); color:#fff; font-weight:700; font-size:.85rem;
        }
        .sb-user-name { color:#fff; font-size:.83rem; font-weight:600; line-height:1.25; }
        .sb-user-role { font-size:.7rem; color:var(--sb-muted); text-transform:capitalize; }
        .sb-signout {
            display:flex; align-items:center; gap:.65rem; width:100%; padding:.55rem .7rem; border:none; border-radius:9px;
            background:transparent; color:var(--sb-text); font-size:.855rem; font-weight:500; text-align:left; cursor:pointer;
            transition:background .16s ease, color .16s ease;
        }
        .sb-signout i { font-size:1.02rem; width:1.15rem; text-align:center; color:var(--sb-muted); transition:color .16s ease; }
        .sb-signout:hover { background:rgba(229,72,77,.14); color:#ff8a8d; }
        .sb-signout:hover i { color:#ff8a8d; }
        #topbar { height:var(--top-h); background:#fff; position:fixed; top:0; left:var(--sb-w); right:0; z-index:1030; border-bottom:1px solid #e5e9f2; display:flex; align-items:center; padding:0 1.5rem; gap:1rem; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .page-title { font-size:.95rem; font-weight:600; color:#1a1d23; }
        .topbar-btn { border:none; background:none; padding:.6rem .7rem; border-radius:8px; color:#6c757d; cursor:pointer; min-width:44px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; }
        .topbar-btn:hover { background:#f0f2f8; }
        #main { margin-left:var(--sb-w); padding:calc(var(--top-h) + 1.5rem) 1.5rem 2rem; min-height:100vh; }

        /* ============================================================
           SHARED DESIGN SYSTEM
           One panel primitive. .card-panel/.tbl-wrap/.admin-table/.form-panel
           /.form-card are kept as aliases so older markup keeps working.
           ============================================================ */
        .panel, .card-panel, .tbl-wrap, .admin-table, .form-panel, .form-card, .stat-card {
            background:#fff; border:1px solid var(--line); border-radius:var(--radius);
            box-shadow:var(--shadow); position:relative;
        }
        .tbl-wrap, .admin-table { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .form-panel, .form-card { padding:1.5rem; }
        .panel { overflow:hidden; }
        .panel-head {
            padding:1rem 1.15rem; border-bottom:1px solid var(--line-soft);
            display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
        }
        .panel-head h6, .panel-head h5 { margin:0; font-size:.9rem; font-weight:650; color:var(--ink); }
        .panel-body { padding:1.15rem; }
        .panel-body.p-0, .panel-body.flush { padding:0; }

        /* ---- Page header ---- */
        .page-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:.75rem; margin-bottom:1.25rem; }
        .page-head h4 { font-size:1.35rem; font-weight:700; letter-spacing:-.02em; color:var(--ink); margin:0 0 .15rem; }
        .page-head .sub { font-size:.83rem; color:var(--ink-soft); }

        /* ---- Tables (shared by static tables and DataTables) ---- */
        .tbl-wrap table, .admin-table table, .panel table { margin:0; width:100%; }
        .tbl-wrap table, .admin-table table { min-width:640px; }
        .tbl-wrap thead th, .admin-table thead th, .panel thead th {
            background:var(--head-bg); font-size:.71rem; text-transform:uppercase; letter-spacing:.6px;
            border-bottom:1px solid var(--line-soft); color:#8a94a6; font-weight:600;
            padding:.7rem 1.15rem; white-space:nowrap; vertical-align:middle;
        }
        .tbl-wrap tbody td, .admin-table tbody td, .panel tbody td {
            padding:.8rem 1.15rem; vertical-align:middle; font-size:.86rem;
            border-bottom:1px solid var(--line-soft); color:#344054;
        }
        .tbl-wrap tbody tr:last-child td, .admin-table tbody tr:last-child td, .panel tbody tr:last-child td { border-bottom:none; }
        .tbl-wrap tbody tr:hover, .admin-table tbody tr:hover, .panel tbody tr:hover { background:var(--row-hover); }
        .tbl-wrap table a:not(.btn), .admin-table table a:not(.btn), .panel table a:not(.btn) {
            color:var(--ink); text-decoration:none; font-weight:550;
        }
        .tbl-wrap table a:not(.btn):hover, .admin-table table a:not(.btn):hover, .panel table a:not(.btn):hover { color:var(--sb-active); }
        .table > :not(caption) > * > * { box-shadow:none; }

        /* ---- Pending row highlight (replaces Bootstrap's table-warning wash) ---- */
        tr.row-pending > td { background:#fffaf0; }
        tr.row-pending:hover > td { background:#fff5e3; }
        tr.row-pending > td:first-child { box-shadow:inset 3px 0 0 #f5a524; }

        /* ---- Row action buttons ---- */
        .row-actions { display:inline-flex; align-items:center; gap:.3rem; white-space:nowrap; }
        .row-actions .btn, .btn-icon {
            width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;
            border-radius:7px; font-size:.8rem; line-height:1;
        }

        /* ---- Badges / pills (replaces the broken bg-opacity-15 pattern) ---- */
        .pill, .sbadge {
            display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; font-weight:600;
            padding:.28em .7em; border-radius:20px; white-space:nowrap; line-height:1.4;
        }
        .pill.dot::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
        .tint-primary   { background:rgba(78,110,242,.12);  color:#3a55c9; }
        .tint-success   { background:rgba(25,135,84,.12);   color:#12704a; }
        .tint-warning   { background:rgba(255,169,0,.16);   color:#8d5b00; }
        .tint-info      { background:rgba(13,164,202,.12);  color:#096d81; }
        .tint-secondary { background:rgba(102,112,133,.12); color:#5a6474; }
        .tint-danger    { background:rgba(220,53,69,.12);   color:#b02a37; }
        .tint-dark      { background:rgba(29,41,57,.1);     color:#1d2939; }

        /* ---- Filter bar ---- */
        .filter-bar { padding:.85rem 1rem; }
        .filter-bar .form-control, .filter-bar .form-select { font-size:.83rem; }

        /* ---- Forms ---- */
        .form-label { font-size:.82rem; font-weight:600; color:#344054; margin-bottom:.35rem; }
        .form-control, .form-select { border-color:#dfe3ee; border-radius:9px; font-size:.875rem; color:#1d2939; }
        .form-control:focus, .form-select:focus {
            border-color:var(--sb-active); box-shadow:0 0 0 3px rgba(78,110,242,.14);
        }
        .form-control::placeholder { color:#a4acbd; }
        .form-control.is-invalid, .form-select.is-invalid { border-color:#dc3545; }
        .form-control.is-invalid:focus, .form-select.is-invalid:focus { box-shadow:0 0 0 3px rgba(220,53,69,.14); }
        .form-check-input:focus { box-shadow:0 0 0 3px rgba(78,110,242,.14); }
        .form-check-input:checked { background-color:var(--sb-active); border-color:var(--sb-active); }
        .form-section-title {
            display:flex; align-items:center; gap:.5rem; font-size:.9rem; font-weight:650;
            color:var(--ink); margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid var(--line-soft);
        }
        .form-section-title i { color:var(--sb-active); }
        .req { color:#dc3545; font-weight:600; }

        /* ---- Buttons ---- */
        .btn { border-radius:9px; font-size:.86rem; font-weight:550; }
        .btn-sm { border-radius:8px; font-size:.8rem; }
        .btn-lg { border-radius:10px; }
        .btn-primary { background:var(--sb-active); border-color:var(--sb-active); }
        .btn-primary:hover, .btn-primary:focus { background:#3f5ce0; border-color:#3f5ce0; }
        .btn:focus-visible { box-shadow:0 0 0 3px rgba(78,110,242,.3); }

        /* ---- Tag pills (post/tag pickers) ---- */
        .tag-pill input[type=checkbox], .tag-badge input[type=checkbox] { position:absolute; opacity:0; width:0; height:0; }
        .tag-pill label, .tag-badge label {
            cursor:pointer; padding:.3rem .8rem; border-radius:20px; border:1.5px solid #dfe3ee;
            font-size:.8rem; transition:all .16s; display:inline-block; color:#475467; margin:0;
        }
        .tag-pill label:hover, .tag-badge label:hover { border-color:#b9c2dd; background:#f7f9ff; }
        .tag-pill input:checked + label, .tag-badge input:checked + label {
            background:var(--sb-active); border-color:var(--sb-active); color:#fff;
        }
        .tag-pill input:focus-visible + label, .tag-badge input:focus-visible + label { box-shadow:0 0 0 3px rgba(78,110,242,.3); }

        /* ---- Empty state ---- */
        .empty-state { padding:2.5rem 1rem; text-align:center; color:#98a2b3; font-size:.86rem; }
        .empty-state > i:first-child { font-size:1.9rem; opacity:.4; display:block; margin-bottom:.6rem; }

        /* ---- Modals ---- */
        .modal-content { border:none; border-radius:14px; box-shadow:0 18px 48px rgba(16,24,40,.2); }
        .modal-header, .modal-footer { border-color:var(--line-soft); }
        .modal-title { font-size:1rem; font-weight:650; }

        /* ---- Alerts ---- */
        .alert { border-radius:11px; font-size:.875rem; }

        /* ---- DataTables integration ---- */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { font-size:.8rem; color:var(--ink-soft); }
        .dataTables_wrapper .dataTables_length select {
            border:1px solid #dfe3ee; border-radius:7px; padding:.2rem 1.5rem .2rem .5rem; margin:0 .35rem; font-size:.8rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius:7px !important; border:1px solid transparent !important; font-size:.8rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background:var(--sb-active) !important; border-color:var(--sb-active) !important; color:#fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
            background:#eef1fb !important; border-color:transparent !important; color:var(--sb-active) !important;
        }
        .dataTables_wrapper .dataTables_processing {
            border:none; background:#fff; border-radius:9px; box-shadow:var(--shadow); font-size:.82rem; color:var(--ink-soft);
        }
        table.dataTable thead th.sorting, table.dataTable thead th.sorting_asc, table.dataTable thead th.sorting_desc { cursor:pointer; }
        .min-w-0 { min-width:0; }
        .sb-close { display:none; }
        @media (max-width:991px) {
            #sidebar { transform:translateX(-100%); }
            #sidebar.open { transform:translateX(0); box-shadow:6px 0 32px rgba(0,0,0,.45); }
            #topbar, #main { left:0; margin-left:0; }
            #sidebar-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1035; }
            #sidebar-backdrop.show { display:block; }
            .sb-close { display:inline-flex; }
        }
        @media (prefers-reduced-motion:reduce) {
            #sidebar, .sb-link, .sb-signout { transition:none; }
        }
    </style>
    @stack('styles')
</head>
<body>

@php $sbPendingCount = \App\Models\Comment::where('is_approved',false)->count(); @endphp

<nav id="sidebar" aria-label="Admin navigation">
    <div class="sb-brand">
        <span class="brand-mark"><i class="bi bi-feather"></i></span>
        <span>Blog<span class="dot">Admin</span></span>
        <button type="button" class="sb-close" aria-label="Close menu"
                onclick="document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebar-backdrop').classList.remove('show')">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sb-nav">
        <div class="sb-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><i class="bi bi-speedometer2"></i> Dashboard</a>

        <div class="sb-section">Content</div>
        <a href="{{ route('admin.posts.index') }}" class="sb-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" @if(request()->routeIs('admin.posts.*')) aria-current="page" @endif><i class="bi bi-file-earmark-text"></i> Posts</a>
        <a href="{{ route('admin.hiring-posts.index') }}" class="sb-link {{ request()->routeIs('admin.hiring-posts.*') ? 'active' : '' }}" @if(request()->routeIs('admin.hiring-posts.*')) aria-current="page" @endif><i class="bi bi-briefcase"></i> Hiring Posts</a>
        <a href="{{ route('admin.categories.index') }}" class="sb-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" @if(request()->routeIs('admin.categories.*')) aria-current="page" @endif><i class="bi bi-folder2-open"></i> Categories</a>
        <a href="{{ route('admin.tags.index') }}" class="sb-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" @if(request()->routeIs('admin.tags.*')) aria-current="page" @endif><i class="bi bi-tags"></i> Tags</a>
        <a href="{{ route('admin.comments.index') }}" class="sb-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" @if(request()->routeIs('admin.comments.*')) aria-current="page" @endif>
            <i class="bi bi-chat-dots"></i> Comments
            @if($sbPendingCount > 0)
                <span class="sb-badge" title="{{ $sbPendingCount }} awaiting moderation">{{ $sbPendingCount > 99 ? '99+' : $sbPendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.media.index') }}" class="sb-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" @if(request()->routeIs('admin.media.*')) aria-current="page" @endif><i class="bi bi-images"></i> Media</a>
        <a href="{{ route('admin.newsletter.index') }}" class="sb-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}" @if(request()->routeIs('admin.newsletter.*')) aria-current="page" @endif><i class="bi bi-envelope-paper"></i> Newsletter</a>

        <div class="sb-section">Users</div>
        <a href="{{ route('admin.users.index') }}" class="sb-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif><i class="bi bi-people"></i> All Users</a>

        <div class="sb-section">Developer</div>
        <a href="{{ route('api.docs') }}" target="_blank" rel="noopener" class="sb-link">
            <i class="bi bi-braces-asterisk"></i> API Docs <i class="bi bi-box-arrow-up-right ext"></i>
        </a>
        <a href="{{ route('l5-swagger.default.api') }}" target="_blank" rel="noopener" class="sb-link">
            <i class="bi bi-file-earmark-code"></i> Swagger UI <i class="bi bi-box-arrow-up-right ext"></i>
        </a>
    </div>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="min-w-0 flex-grow-1">
                <div class="sb-user-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="sb-user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="sb-signout">
                <i class="bi bi-box-arrow-right"></i> Sign Out
            </button>
        </form>
    </div>
</nav>

<div id="sidebar-backdrop" onclick="document.getElementById('sidebar').classList.remove('open');this.classList.remove('show')"></div>

<header id="topbar">
    <button class="topbar-btn d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('open');document.getElementById('sidebar-backdrop').classList.toggle('show')">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="page-title">@yield('page-title','Dashboard')</span>
    <div class="ms-auto d-flex align-items-center gap-2">
        @if($sbPendingCount > 0)
        <a href="{{ route('admin.comments.index') }}" class="topbar-btn position-relative text-decoration-none">
            <i class="bi bi-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $sbPendingCount }}</span>
        </a>
        @endif
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Post</a>
    </div>
</header>

<main id="main">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
window.initAdminDataTable = function (selector, options) {
    return $(selector).DataTable($.extend(true, {
        processing: true,
        serverSide: true,
        dom: '<"p-3 d-flex justify-content-between align-items-center flex-wrap gap-2"l>rt<"p-3 d-flex justify-content-between align-items-center flex-wrap gap-2"ip>',
    }, options));
};
</script>
@stack('scripts')
</body>
</html>
