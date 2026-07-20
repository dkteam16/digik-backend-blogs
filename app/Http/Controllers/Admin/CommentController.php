<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::with(['post', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%')
                  ->orWhere('author_name', 'like', '%' . $request->search . '%');
        }

        $comments       = $query->paginate(20)->withQueryString();
        $pendingCount   = Comment::where('is_approved', false)->count();

        return view('admin.comments.index', compact('comments', 'pendingCount'));
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved!');
    }

    public function reject(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => false]);
        return back()->with('success', 'Comment rejected!');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action'      => 'required|in:approve,reject,delete',
            'comment_ids' => 'required|array',
        ]);

        $comments = Comment::whereIn('id', $request->comment_ids);

        match ($request->action) {
            'approve' => $comments->update(['is_approved' => true]),
            'reject'  => $comments->update(['is_approved' => false]),
            'delete'  => $comments->delete(),
        };

        return back()->with('success', 'Bulk action applied!');
    }
}
