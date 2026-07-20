<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_posts'       => Post::count(),
            'published_posts'   => Post::published()->count(),
            'draft_posts'       => Post::draft()->count(),
            'total_categories'  => Category::count(),
            'total_users'       => User::count(),
            'pending_comments'  => Comment::where('is_approved', false)->count(),
            'total_views'       => Post::sum('views_count'),
        ];

        $recentPosts = Post::with(['author', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingComments = Comment::with('post')
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $popularPosts = Post::published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'pendingComments', 'popularPosts'));
    }
}
