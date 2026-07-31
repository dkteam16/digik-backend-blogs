<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CommentController extends Controller
{
    public function index(): View
    {
        $pendingCount = Comment::where('is_approved', false)->count();

        return view('admin.comments.index', compact('pendingCount'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = Comment::with(['post', 'user']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('checkbox', fn (Comment $comment) => '<input type="checkbox" name="comment_ids[]" value="'.$comment->id.'" form="bulk-form" class="form-check-input cbox">')
            ->addColumn('author_col', fn (Comment $comment) => '
                <div>
                    <div class="fw-semibold" style="font-size:.85rem">'.e($comment->user->name ?? $comment->author_name ?? 'Guest').'</div>
                    <small class="text-muted" style="font-size:.72rem">'.e($comment->user->email ?? $comment->author_email ?? '').'</small>
                </div>
            ')
            ->addColumn('content_col', fn (Comment $comment) => Str::limit($comment->content, 80))
            ->addColumn('post_col', fn (Comment $comment) => $comment->post
                ? '<a href="'.route('admin.posts.edit', $comment->post).'" class="text-decoration-none text-truncate d-inline-block" style="max-width:180px">'.e($comment->post->title).'</a>'
                : '—'
            )
            ->addColumn('status_label', fn (Comment $comment) => $comment->is_approved
                ? '<span class="pill dot tint-success">Approved</span>'
                : '<span class="pill dot tint-warning">Pending</span>'
            )
            ->addColumn('date_col', fn (Comment $comment) => $comment->created_at->diffForHumans())
            ->addColumn('actions', fn (Comment $comment) => '
                <div class="row-actions">
                    '.($comment->is_approved
                        ? '<form action="'.route('admin.comments.reject', $comment).'" method="POST" class="d-inline">'.csrf_field().'<button type="submit" class="btn btn-sm btn-outline-warning" title="Reject"><i class="bi bi-x-circle"></i></button></form>'
                        : '<form action="'.route('admin.comments.approve', $comment).'" method="POST" class="d-inline">'.csrf_field().'<button type="submit" class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-circle"></i></button></form>'
                    ).'
                    <form action="'.route('admin.comments.destroy', $comment).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete comment?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            ')
            ->rawColumns(['checkbox', 'author_col', 'post_col', 'status_label', 'actions'])
            ->setRowClass(fn (Comment $comment) => !$comment->is_approved ? 'row-pending' : '')
            ->make(true);
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
