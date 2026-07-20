@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <small class="text-muted">Welcome back, {{ auth()->user()->name }}!</small>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Post
    </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['label' => 'Total Posts',     'value' => $stats['total_posts'],     'icon' => 'bi-file-earmark-text', 'color' => 'primary'],
            ['label' => 'Published',       'value' => $stats['published_posts'], 'icon' => 'bi-check-circle',     'color' => 'success'],
            ['label' => 'Drafts',          'value' => $stats['draft_posts'],     'icon' => 'bi-pencil-square',    'color' => 'warning'],
            ['label' => 'Total Views',     'value' => number_format($stats['total_views']), 'icon' => 'bi-eye', 'color' => 'info'],
            ['label' => 'Categories',      'value' => $stats['total_categories'], 'icon' => 'bi-folder2-open',   'color' => 'secondary'],
            ['label' => 'Pending Comments','value' => $stats['pending_comments'], 'icon' => 'bi-chat-dots',      'color' => 'danger'],
        ];
    @endphp

    @foreach($statCards as $card)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                    <i class="bi {{ $card['icon'] }}"></i>
                </div>
            </div>
            <div class="fw-bold fs-4">{{ $card['value'] }}</div>
            <div class="text-muted small">{{ $card['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <!-- Recent Posts -->
    <div class="col-lg-7">
        <div class="admin-table">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Recent Posts</h6>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $post)
                    <tr>
                        <td>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-decoration-none fw-medium text-dark">
                                {{ Str::limit($post->title, 40) }}
                            </a>
                        </td>
                        <td><span class="text-muted small">{{ $post->author->name }}</span></td>
                        <td>
                            @php
                                $statusColors = ['published' => 'success', 'draft' => 'warning', 'archived' => 'secondary', 'scheduled' => 'info'];
                            @endphp
                            <span class="status-badge bg-{{ $statusColors[$post->status] ?? 'secondary' }} bg-opacity-15 text-{{ $statusColors[$post->status] ?? 'secondary' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td><span class="text-muted small">{{ $post->created_at->diffForHumans() }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No posts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popular Posts -->
    <div class="col-lg-5">
        <div class="admin-table">
            <div class="p-3 border-bottom">
                <h6 class="mb-0 fw-semibold">Most Viewed Posts</h6>
            </div>
            <div class="p-3">
                @forelse($popularPosts as $i => $post)
                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                    <div class="fw-bold text-muted" style="min-width:1.5rem">{{ $i + 1 }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-medium text-dark small">{{ Str::limit($post->title, 45) }}</div>
                        <div class="text-muted" style="font-size:.75rem">
                            <i class="bi bi-eye me-1"></i>{{ number_format($post->views_count) }} views
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
