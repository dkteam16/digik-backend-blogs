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
        :root { --sb-w:255px; --sb-bg:#16181d; --sb-text:#8b949e; --sb-hover:rgba(255,255,255,.05); --sb-active:#4e6ef2; --top-h:58px; }
        body { background:#f0f2f8; font-family:'Segoe UI',system-ui,sans-serif; margin:0; }
        #sidebar { width:var(--sb-w); min-height:100vh; background:var(--sb-bg); position:fixed; top:0; left:0; z-index:1040; display:flex; flex-direction:column; overflow-y:auto; transition:transform .28s ease; }
        .sb-brand { padding:1.1rem 1.4rem; border-bottom:1px solid rgba(255,255,255,.06); color:#fff; font-size:1.05rem; font-weight:700; display:flex; align-items:center; gap:.5rem; }
        .sb-brand .dot { color:var(--sb-active); }
        .sb-section { padding:.75rem 1.4rem .2rem; font-size:.68rem; letter-spacing:1px; text-transform:uppercase; color:#3d4450; font-weight:600; margin-top:.4rem; }
        .sb-link { display:flex; align-items:center; gap:.6rem; padding:.52rem 1.4rem; color:var(--sb-text); text-decoration:none; font-size:.845rem; border-left:3px solid transparent; transition:all .18s; }
        .sb-link:hover { background:var(--sb-hover); color:#e6edf3; }
        .sb-link.active { background:rgba(78,110,242,.15); color:#fff; border-left-color:var(--sb-active); }
        .sb-link i { font-size:1rem; width:1.1rem; text-align:center; flex-shrink:0; }
        .sb-badge { margin-left:auto; background:#e74c3c; color:#fff; font-size:.65rem; padding:.15em .5em; border-radius:10px; font-weight:700; }
        #topbar { height:var(--top-h); background:#fff; position:fixed; top:0; left:var(--sb-w); right:0; z-index:1030; border-bottom:1px solid #e5e9f2; display:flex; align-items:center; padding:0 1.5rem; gap:1rem; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .page-title { font-size:.95rem; font-weight:600; color:#1a1d23; }
        .topbar-btn { border:none; background:none; padding:.6rem .7rem; border-radius:8px; color:#6c757d; cursor:pointer; min-width:44px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; }
        .topbar-btn:hover { background:#f0f2f8; }
        #main { margin-left:var(--sb-w); padding:calc(var(--top-h) + 1.5rem) 1.5rem 2rem; min-height:100vh; }
        .card-panel { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.06); border:none; }
        .stat-card { background:#fff; border-radius:12px; padding:1.2rem 1.4rem; box-shadow:0 1px 4px rgba(0,0,0,.06); }
        .stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
        .tbl-wrap { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .tbl-wrap .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .tbl-wrap table { margin:0; min-width:560px; }
        .tbl-wrap thead th { background:#f8f9fc; font-size:.76rem; text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid #eaecf4; color:#6c757d; padding:.85rem 1rem; }
        .tbl-wrap tbody td { padding:.75rem 1rem; vertical-align:middle; font-size:.875rem; border-bottom:1px solid #f4f6fb; }
        .tbl-wrap tbody tr:last-child td { border-bottom:none; }
        .tbl-wrap tbody tr:hover { background:#fafbff; }
        .sbadge { font-size:.7rem; padding:.25em .7em; border-radius:20px; font-weight:600; display:inline-block; }
        .form-panel { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 1px 4px rgba(0,0,0,.06); }
        .admin-table { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .admin-table table { min-width:640px; }
        .tag-pill input[type=checkbox] { display:none; }
        .tag-pill label { cursor:pointer; padding:.28rem .75rem; border-radius:20px; border:1.5px solid #dee2e6; font-size:.8rem; transition:all .18s; display:inline-block; }
        .tag-pill input:checked + label { background:var(--sb-active); border-color:var(--sb-active); color:#fff; }
        @media (max-width:991px) {
            #sidebar { transform:translateX(-100%); }
            #sidebar.open { transform:translateX(0); box-shadow:4px 0 24px rgba(0,0,0,.3); }
            #topbar, #main { left:0; margin-left:0; }
            #sidebar-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1035; }
            #sidebar-backdrop.show { display:block; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav id="sidebar">
    <div class="sb-brand"><i class="bi bi-feather"></i>Blog<span class="dot">Admin</span></div>
    @php $pendingComments = \App\Models\Comment::where('is_approved',false)->count(); @endphp

    <div class="sb-section">Main</div>
    <a href="{{ route('admin.dashboard') }}" class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <div class="sb-section">Content</div>
    <a href="{{ route('admin.posts.index') }}" class="sb-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Posts</a>
    <a href="{{ route('admin.hiring-posts.index') }}" class="sb-link {{ request()->routeIs('admin.hiring-posts.*') ? 'active' : '' }}"><i class="bi bi-briefcase"></i> Hiring Posts</a>
    <a href="{{ route('admin.categories.index') }}" class="sb-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-folder2-open"></i> Categories</a>
    <a href="{{ route('admin.tags.index') }}" class="sb-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Tags</a>
    <a href="{{ route('admin.comments.index') }}" class="sb-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
        <i class="bi bi-chat-dots"></i> Comments
        @if($pendingComments > 0)<span class="sb-badge">{{ $pendingComments }}</span>@endif
    </a>
    <a href="{{ route('admin.media.index') }}" class="sb-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}"><i class="bi bi-images"></i> Media</a>
    <a href="{{ route('admin.newsletter.index') }}" class="sb-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}"><i class="bi bi-envelope-paper"></i> Newsletter</a>

    <div class="sb-section">Users</div>
    <a href="{{ route('admin.users.index') }}" class="sb-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> All Users</a>

    <div class="sb-section">Developer</div>
    <a href="{{ route('api.docs') }}" target="_blank" class="sb-link"><i class="bi bi-braces-asterisk"></i> API Docs</a>
    <a href="{{ route('l5-swagger.default.api') }}" target="_blank" class="sb-link"><i class="bi bi-file-earmark-code"></i> Swagger UI</a>

    <div class="mt-auto p-3" style="border-top:1px solid rgba(255,255,255,.06)">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px;height:32px;font-size:.8rem">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
            <div>
                <div class="text-white" style="font-size:.82rem;line-height:1.2">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:#555d6b">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="sb-link w-100 border-0 text-start bg-transparent" style="cursor:pointer;border-radius:6px">
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
        @if($pendingComments > 0)
        <a href="{{ route('admin.comments.index') }}" class="topbar-btn position-relative text-decoration-none">
            <i class="bi bi-bell fs-5 text-muted"></i>
            <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $pendingComments }}</span>
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
