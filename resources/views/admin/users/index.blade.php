@extends('admin.layouts.app')
@section('title','Users')
@section('page-title','Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Users</h4>
        <small class="text-muted">Manage admin panel users</small>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New User
    </a>
</div>

<div class="card-panel p-3 mb-3">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control form-control-sm" placeholder="Search by name or email...">
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select form-select-sm">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role')==='admin' ? 'selected' : '' }}>Admin</option>
                <option value="editor" {{ request('role')==='editor' ? 'selected' : '' }}>Editor</option>
                <option value="author" {{ request('role')==='author' ? 'selected' : '' }}>Author</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="tbl-wrap">
    <table class="table">
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
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                             style="width:36px;height:36px;font-size:.85rem;flex-shrink:0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-medium">{{ $user->name }}</div>
                            <div class="text-muted" style="font-size:.78rem">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php $roleColors = ['admin'=>'danger','editor'=>'warning','author'=>'info'] @endphp
                    <span class="sbadge bg-{{ $roleColors[$user->role] ?? 'secondary' }} bg-opacity-15 text-{{ $roleColors[$user->role] ?? 'secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td><span class="sbadge bg-secondary bg-opacity-15 text-secondary">{{ $user->posts_count }}</span></td>
                <td>
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                title="{{ $user->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                            <i class="bi {{ $user->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </td>
                <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Delete user {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-3">{{ $users->links() }}</div>
</div>
@endsection
