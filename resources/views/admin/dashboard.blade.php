@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* Dashboard-only styles. Panels, pills, tints, tables and empty states
       now come from the shared system in admin/layouts/app.blade.php. */
    .stat-card {
        padding:1.05rem 1.15rem; display:flex; align-items:center; gap:.9rem;
        transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        text-decoration:none; color:inherit; height:100%;
    }
    a.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(16,24,40,.09); border-color:#d7ddf5; }
    .stat-icon { width:42px; height:42px; border-radius:11px; flex-shrink:0;
                 display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
    .stat-value { font-size:1.55rem; font-weight:700; line-height:1.1; color:var(--ink); letter-spacing:-.02em; }
    .stat-label { font-size:.75rem; color:var(--ink-soft); font-weight:500; margin-top:.15rem; }

    .post-title { font-weight:550; color:#1d2939; text-decoration:none; }
    .post-title:hover { color:var(--sb-active); }

    /* Ranked list */
    .rank-row { display:flex; align-items:center; gap:.85rem; padding:.7rem 1.15rem; }
    .rank-row + .rank-row { border-top:1px solid var(--line-soft); }
    .rank-row:hover { background:var(--row-hover); }
    .rank-num { width:26px; height:26px; border-radius:8px; flex-shrink:0; display:flex; align-items:center;
                justify-content:center; font-size:.72rem; font-weight:700; background:#f2f4fb; color:var(--ink-soft); }
    .rank-row:nth-child(1) .rank-num { background:rgba(78,110,242,.12); color:#4e6ef2; }
    .rank-bar { height:4px; border-radius:4px; background:#f0f2f8; overflow:hidden; margin-top:.4rem; }
    .rank-bar span { display:block; height:100%; border-radius:4px; background:linear-gradient(90deg,#4e6ef2,#7d92f7); }

    /* Moderation queue */
    .cmt-row { padding:.8rem 1.15rem; display:flex; gap:.75rem; }
    .cmt-row + .cmt-row { border-top:1px solid var(--line-soft); }
    .cmt-avatar { width:30px; height:30px; border-radius:50%; flex-shrink:0; display:flex; align-items:center;
                  justify-content:center; font-size:.72rem; font-weight:700; background:#f2f4fb; color:var(--ink-soft); }
    .cmt-text { font-size:.8rem; color:#475467; line-height:1.45; }
</style>
@endpush

@section('content')

{{-- ===== Header ===== --}}
<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="letter-spacing:-.02em">
            {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }},
            {{ Str::before(auth()->user()->name, ' ') }}
        </h4>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, d M Y') }}
            <span class="mx-2 text-black-50">&middot;</span>
            {{ $stats['published_posts'] }} published &middot; {{ $stats['draft_posts'] }} in draft
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-light border">
            <i class="bi bi-collection me-1"></i> Manage Posts
        </a>
    </div>
</div>

{{-- ===== Stats ===== --}}
@php
    $statCards = [
        ['label'=>'Total Posts','value'=>number_format($stats['total_posts']),'icon'=>'bi-file-earmark-text','tint'=>'primary','href'=>route('admin.posts.index')],
        ['label'=>'Published','value'=>number_format($stats['published_posts']),'icon'=>'bi-check-circle','tint'=>'success','href'=>route('admin.posts.index')],
        ['label'=>'Drafts','value'=>number_format($stats['draft_posts']),'icon'=>'bi-pencil-square','tint'=>'warning','href'=>route('admin.posts.index')],
        ['label'=>'Total Views','value'=>number_format($stats['total_views']),'icon'=>'bi-eye','tint'=>'info','href'=>null],
        ['label'=>'Categories','value'=>number_format($stats['total_categories']),'icon'=>'bi-folder2-open','tint'=>'secondary','href'=>route('admin.categories.index')],
        ['label'=>'Pending Comments','value'=>number_format($stats['pending_comments']),'icon'=>'bi-chat-dots','tint'=>'danger','href'=>route('admin.comments.index')],
    ];
@endphp

<div class="row g-3 mb-4 stat-grid">
    @foreach($statCards as $card)
    <div class="col-6 col-md-4 col-xxl-2">
        @if($card['href'])
        <a href="{{ $card['href'] }}" class="stat-card">
        @else
        <div class="stat-card">
        @endif
            <div class="stat-icon tint-{{ $card['tint'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
            <div class="min-w-0">
                <div class="stat-value">{{ $card['value'] }}</div>
                <div class="stat-label">{{ $card['label'] }}</div>
            </div>
        @if($card['href'])</a>@else</div>@endif
    </div>
    @endforeach
</div>

<div class="row g-3 align-items-stretch">

    {{-- ===== Recent Posts ===== --}}
    <div class="col-xl-8">
        <div class="panel">
            <div class="panel-head">
                <h6><i class="bi bi-clock-history me-2 text-muted"></i>Recent Posts</h6>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th class="text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusTint = ['published'=>'success','draft'=>'warning','archived'=>'secondary','scheduled'=>'info'];
                        @endphp
                        @forelse($recentPosts as $post)
                        <tr>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="post-title d-block">
                                    {{ Str::limit($post->title, 48) }}
                                </a>
                                @if($post->category)
                                    <span class="text-muted" style="font-size:.72rem">
                                        <i class="bi bi-folder2 me-1"></i>{{ $post->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $post->author->name }}</td>
                            <td>
                                <span class="pill dot tint-{{ $statusTint[$post->status] ?? 'secondary' }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </td>
                            <td class="text-muted small text-end text-nowrap">{{ $post->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-file-earmark-plus"></i>
                                    No posts yet. <a href="{{ route('admin.posts.create') }}">Write your first one</a>.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Right column ===== --}}
    <div class="col-xl-4">
        <div class="d-flex flex-column gap-3 h-100">

            {{-- Most Viewed --}}
            <div class="panel">
                <div class="panel-head">
                    <h6><i class="bi bi-graph-up-arrow me-2 text-muted"></i>Most Viewed</h6>
                </div>
                <div class="panel-body p-0">
                    @php $maxViews = max(1, (int) optional($popularPosts->first())->views_count); @endphp
                    @forelse($popularPosts as $i => $post)
                    <div class="rank-row">
                        <div class="rank-num">{{ $i + 1 }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="post-title d-block small text-truncate">
                                {{ Str::limit($post->title, 44) }}
                            </a>
                            <div class="rank-bar">
                                <span style="width:{{ round($post->views_count / $maxViews * 100) }}%"></span>
                            </div>
                        </div>
                        <div class="text-muted text-nowrap" style="font-size:.72rem">
                            {{ number_format($post->views_count) }}
                        </div>
                    </div>
                    @empty
                    <div class="empty-state"><i class="bi bi-bar-chart"></i>No view data yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Moderation queue --}}
            <div class="panel">
                <div class="panel-head">
                    <h6>
                        <i class="bi bi-chat-left-dots me-2 text-muted"></i>Awaiting Moderation
                        @if($pendingComments->count())
                            <span class="badge rounded-pill bg-danger ms-1" style="font-size:.65rem">{{ $stats['pending_comments'] }}</span>
                        @endif
                    </h6>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-outline-primary">Review</a>
                </div>
                <div class="panel-body p-0">
                    @forelse($pendingComments as $comment)
                    <div class="cmt-row">
                        <div class="cmt-avatar">{{ strtoupper(Str::substr($comment->author_name ?: '?', 0, 1)) }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="fw-semibold small text-truncate">{{ $comment->author_name ?: 'Anonymous' }}</div>
                                <div class="text-muted text-nowrap" style="font-size:.7rem">{{ $comment->created_at->diffForHumans(['short' => true]) }}</div>
                            </div>
                            <div class="cmt-text">{{ Str::limit($comment->content, 90) }}</div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="m-0">
                                    @csrf
                                    <button class="btn btn-sm btn-success btn-icon" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="m-0"
                                      onsubmit="return confirm('Delete this comment?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @if($comment->post)
                                    <span class="text-muted text-truncate" style="font-size:.7rem">
                                        on {{ Str::limit($comment->post->title, 24) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state"><i class="bi bi-inbox"></i>Nothing to moderate. All caught up.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
