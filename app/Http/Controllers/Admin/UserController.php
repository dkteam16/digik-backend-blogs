<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = User::withCount('posts');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('user_col', fn (User $user) => '
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:.8rem">
                        '.strtoupper(substr($user->name, 0, 1)).'
                    </div>
                    <div>
                        <div class="fw-semibold" style="line-height:1.2">'.e($user->name).'</div>
                        <small class="text-muted" style="font-size:.75rem">'.e($user->email).'</small>
                    </div>
                </div>
            ')
            ->addColumn('role_label', fn (User $user) => match($user->role) {
                'admin'  => '<span class="sbadge bg-danger bg-opacity-10 text-danger">Admin</span>',
                'editor' => '<span class="sbadge bg-primary bg-opacity-10 text-primary">Editor</span>',
                'author' => '<span class="sbadge bg-info bg-opacity-10 text-info">Author</span>',
                default  => '<span class="sbadge bg-secondary bg-opacity-10 text-secondary">'.ucfirst($user->role).'</span>',
            })
            ->addColumn('status_label', fn (User $user) => $user->is_active
                ? '<span class="sbadge bg-success bg-opacity-10 text-success">Active</span>'
                : '<span class="sbadge bg-secondary bg-opacity-10 text-secondary">Inactive</span>'
            )
            ->addColumn('actions', fn (User $user) => '
                <div class="d-flex gap-1">
                    <a href="'.route('admin.users.edit', $user).'" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                    <form action="'.route('admin.users.toggle-status', $user).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        <button type="submit" class="btn btn-sm '.($user->is_active ? 'btn-outline-warning' : 'btn-outline-success').'" title="'.($user->is_active ? 'Deactivate' : 'Activate').'">
                            <i class="bi '.($user->is_active ? 'bi-slash-circle' : 'bi-check-circle').'"></i>
                        </button>
                    </form>
                    '.($user->id !== auth()->id() ? '
                    <form action="'.route('admin.users.destroy', $user).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete user?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                    ' : '').'
                </div>
            ')
            ->editColumn('created_at', fn (User $user) => $user->created_at->format('M d, Y'))
            ->rawColumns(['user_col', 'role_label', 'status_label', 'actions'])
            ->make(true);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|in:admin,editor,author',
            'is_active'             => 'boolean',
        ]);

        $validated['password']  = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'role'      => 'required|in:admin,editor,author',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully!");
    }
}
