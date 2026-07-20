<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API Documentation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
body{background:#0d1117;color:#c9d1d9;font-family:'Segoe UI',system-ui,sans-serif;}
.sidebar{width:250px;min-height:100vh;background:#161b22;border-right:1px solid #30363d;position:fixed;top:0;left:0;overflow-y:auto;}
.main{margin-left:250px;padding:2rem;}
.api-brand{padding:1.2rem 1.4rem;border-bottom:1px solid #30363d;font-size:1rem;font-weight:700;color:#58a6ff;}
.nav-group{padding:.5rem 1rem .2rem;font-size:.67rem;text-transform:uppercase;letter-spacing:1px;color:#484f58;}
.nav-item a{display:flex;align-items:center;gap:.4rem;padding:.38rem 1.4rem;color:#8b949e;text-decoration:none;font-size:.82rem;border-left:2px solid transparent;transition:all .15s;}
.nav-item a:hover{color:#c9d1d9;background:rgba(255,255,255,.03);}
.mbadge{font-size:.68rem;padding:.18em .5em;border-radius:4px;font-weight:700;}
.get{background:#1f6feb22;color:#58a6ff;border:1px solid #1f6feb55;}
.post{background:#1a7f3722;color:#3fb950;border:1px solid #1a7f3755;}
.put{background:#9e6a0322;color:#d29922;border:1px solid #9e6a0355;}
.del{background:#da363022;color:#f85149;border:1px solid #da363055;}
.ec{background:#161b22;border:1px solid #30363d;border-radius:10px;margin-bottom:1rem;overflow:hidden;}
.eh{padding:.85rem 1.1rem;display:flex;align-items:center;gap:.7rem;cursor:pointer;}
.eh:hover{background:#1c2128;}
.ep{font-family:'Courier New',monospace;font-size:.87rem;color:#e6edf3;}
.ed{font-size:.78rem;color:#8b949e;margin-left:auto;}
.eb{padding:1.2rem;border-top:1px solid #21262d;display:none;}
.eb.open{display:block;}
.cb{background:#0d1117;border:1px solid #30363d;border-radius:7px;padding:.9rem;font-size:.78rem;font-family:'Courier New',monospace;color:#e6edf3;overflow-x:auto;white-space:pre;}
.pt td,.pt th{padding:.45rem .7rem;border-color:#21262d;font-size:.82rem;}
.pt thead th{background:#0d1117;color:#8b949e;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;}
h2{color:#e6edf3;border-bottom:1px solid #30363d;padding-bottom:.6rem;margin-top:2.2rem;margin-bottom:1.1rem;font-size:1.25rem;}
h5{color:#e6edf3;font-size:.93rem;margin-bottom:.65rem;}
.anote{background:#1a2232;border:1px solid #1f6feb44;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.1rem;font-size:.84rem;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;}}
</style>
</head>
<body>
<nav class="sidebar">
    <div class="api-brand"><i class="bi bi-braces-asterisk me-2"></i>API Reference</div>
    <div class="nav-group">Auth</div>
    <ul class="list-unstyled m-0">
        <li class="nav-item"><a href="#r-register"><span class="mbadge post">POST</span> Register</a></li>
        <li class="nav-item"><a href="#r-login"><span class="mbadge post">POST</span> Login</a></li>
        <li class="nav-item"><a href="#r-logout"><span class="mbadge post">POST</span> Logout</a></li>
        <li class="nav-item"><a href="#r-me"><span class="mbadge get">GET</span> Profile</a></li>
    </ul>
    <div class="nav-group">Posts</div>
    <ul class="list-unstyled m-0">
        <li class="nav-item"><a href="#r-posts"><span class="mbadge get">GET</span> List Posts</a></li>
        <li class="nav-item"><a href="#r-post-show"><span class="mbadge get">GET</span> Get Post</a></li>
        <li class="nav-item"><a href="#r-featured"><span class="mbadge get">GET</span> Featured</a></li>
        <li class="nav-item"><a href="#r-related"><span class="mbadge get">GET</span> Related</a></li>
        <li class="nav-item"><a href="#r-post-create"><span class="mbadge post">POST</span> Create Post</a></li>
        <li class="nav-item"><a href="#r-post-update"><span class="mbadge put">PUT</span> Update Post</a></li>
        <li class="nav-item"><a href="#r-post-delete"><span class="mbadge del">DEL</span> Delete Post</a></li>
    </ul>
    <div class="nav-group">Categories</div>
    <ul class="list-unstyled m-0">
        <li class="nav-item"><a href="#r-cats"><span class="mbadge get">GET</span> List</a></li>
        <li class="nav-item"><a href="#r-cat"><span class="mbadge get">GET</span> Single</a></li>
    </ul>
    <div class="nav-group">Tags</div>
    <ul class="list-unstyled m-0">
        <li class="nav-item"><a href="#r-tags"><span class="mbadge get">GET</span> All Tags</a></li>
    </ul>
    <div class="nav-group">Comments</div>
    <ul class="list-unstyled m-0">
        <li class="nav-item"><a href="#r-comments"><span class="mbadge get">GET</span> List</a></li>
        <li class="nav-item"><a href="#r-comment-add"><span class="mbadge post">POST</span> Add Comment</a></li>
    </ul>
    <div class="p-3 mt-3" style="border-top:1px solid #30363d">
        <small class="text-muted d-block mb-1">Base URL</small>
        <div class="cb p-2" style="font-size:.72rem">{{ config('app.url') }}/api</div>
    </div>
</nav>

<main class="main">
<div style="max-width:850px">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div>
            <h1 style="color:#e6edf3;font-size:1.7rem;margin:0">Blog API</h1>
            <p class="text-muted mb-0 small">RESTful JSON API — use with any frontend (React, Vue, Next.js, mobile, etc.)</p>
        </div>
        <span class="badge bg-success ms-auto">v1.0</span>
    </div>

    <div class="anote">
        <i class="bi bi-shield-check text-info me-2"></i>
        <strong>Auth:</strong> Protected endpoints require <code>Authorization: Bearer {token}</code>.
        Get a token via <a href="#r-login" class="text-info">/api/auth/login</a>.
    </div>

    <h2><i class="bi bi-key me-2 text-warning"></i>Authentication</h2>

    <div class="ec" id="r-register">
        <div class="eh" onclick="toggle(this)"><span class="mbadge post">POST</span><span class="ep">/api/auth/register</span><span class="ed">Register new user</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb">
            <h5>Request Body</h5>
            <div class="cb">{ "name": "John", "email": "john@x.com", "password": "secret123", "password_confirmation": "secret123" }</div>
            <h5 class="mt-3">Response 201</h5>
            <div class="cb">{ "success": true, "data": { "user": { "id":1, "name":"John", "role":"author" }, "token": "1|abc...", "token_type":"Bearer" } }</div>
        </div>
    </div>

    <div class="ec" id="r-login">
        <div class="eh" onclick="toggle(this)"><span class="mbadge post">POST</span><span class="ep">/api/auth/login</span><span class="ed">Get access token</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb">
            <div class="cb">POST /api/auth/login
{ "email": "admin@blog.com", "password": "password" }

// Response
{ "success": true, "data": { "user": {...}, "token": "2|xyz...", "token_type": "Bearer" } }</div>
        </div>
    </div>

    <div class="ec" id="r-logout">
        <div class="eh" onclick="toggle(this)"><span class="mbadge post">POST</span><span class="ep">/api/auth/logout</span><span class="ed">Revoke token <span class="badge bg-warning text-dark" style="font-size:.6rem">Auth</span></span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">POST /api/auth/logout
Authorization: Bearer {token}
// Response: { "success": true, "message": "Logged out successfully" }</div></div>
    </div>

    <div class="ec" id="r-me">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/auth/me</span><span class="ed">Current user <span class="badge bg-warning text-dark" style="font-size:.6rem">Auth</span></span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/auth/me
Authorization: Bearer {token}
// Returns current user object</div></div>
    </div>

    <h2><i class="bi bi-file-earmark-text me-2 text-info"></i>Posts</h2>

    <div class="ec" id="r-posts">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/posts</span><span class="ed">Paginated published posts</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb">
            <h5>Query Parameters</h5>
            <table class="table table-dark pt"><thead><tr><th>Param</th><th>Type</th><th>Description</th></tr></thead><tbody>
                <tr><td>page</td><td>int</td><td>Page (default 1)</td></tr>
                <tr><td>per_page</td><td>int</td><td>Max 50, default 10</td></tr>
                <tr><td>category</td><td>string</td><td>Category slug</td></tr>
                <tr><td>tag</td><td>string</td><td>Tag slug</td></tr>
                <tr><td>search</td><td>string</td><td>Search title/content</td></tr>
                <tr><td>featured</td><td>bool</td><td>Featured posts only</td></tr>
                <tr><td>sort_by</td><td>string</td><td>created_at|views_count|published_at</td></tr>
                <tr><td>sort_dir</td><td>string</td><td>asc|desc</td></tr>
            </tbody></table>
            <div class="cb">GET /api/posts?category=technology&amp;per_page=5&amp;sort_by=views_count

{ "success": true, "data": [...], "meta": { "current_page":1, "last_page":4, "total":20 } }</div>
        </div>
    </div>

    <div class="ec" id="r-post-show">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/posts/{slug}</span><span class="ed">Single post — increments view count</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/posts/getting-started-with-laravel

{ "success": true, "data": {
    "id": 1, "title": "Getting Started with Laravel",
    "content": "...", "reading_time": "3 min read",
    "author": { "id":1, "name":"Admin" },
    "category": { "slug":"technology" },
    "tags": [{ "name":"Laravel" }],
    "seo": { "meta_title":"...", "meta_description":"..." }
}}</div>
        </div>
    </div>

    <div class="ec" id="r-featured">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/posts/featured</span><span class="ed">Latest 6 featured posts</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/posts/featured</div></div>
    </div>

    <div class="ec" id="r-related">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/posts/{slug}/related</span><span class="ed">4 same-category posts</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/posts/my-post/related</div></div>
    </div>

    <div class="ec" id="r-post-create">
        <div class="eh" onclick="toggle(this)"><span class="mbadge post">POST</span><span class="ep">/api/posts</span><span class="ed">Create post <span class="badge bg-warning text-dark" style="font-size:.6rem">Auth</span></span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">{
  "title": "My Post",         // required
  "content": "&lt;p&gt;...&lt;/p&gt;",  // required
  "excerpt": "Short summary",
  "category_id": 1,
  "tags": [1, 2],             // tag IDs
  "status": "published",      // draft|published|scheduled
  "is_featured": false,
  "allow_comments": true,
  "published_at": "2024-06-01T10:00:00",
  "meta_title": "...",
  "meta_description": "...",
  "meta_keywords": "..."
}</div></div>
    </div>

    <div class="ec" id="r-post-update">
        <div class="eh" onclick="toggle(this)"><span class="mbadge put">PUT</span><span class="ep">/api/posts/{id}</span><span class="ed">Update post <span class="badge bg-warning text-dark" style="font-size:.6rem">Auth</span></span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">PUT /api/posts/1
Authorization: Bearer {token}

{ "status": "archived", "title": "New Title" }</div></div>
    </div>

    <div class="ec" id="r-post-delete">
        <div class="eh" onclick="toggle(this)"><span class="mbadge del">DELETE</span><span class="ep">/api/posts/{id}</span><span class="ed">Soft delete <span class="badge bg-warning text-dark" style="font-size:.6rem">Auth</span></span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">DELETE /api/posts/1
Authorization: Bearer {token}
// Response: { "success": true, "message": "Post deleted successfully" }</div></div>
    </div>

    <h2><i class="bi bi-folder2-open me-2 text-warning"></i>Categories</h2>

    <div class="ec" id="r-cats">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/categories</span><span class="ed">All active categories</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/categories
{ "success": true, "data": [{ "id":1, "name":"Technology", "slug":"technology", "posts_count":12 }] }</div></div>
    </div>

    <div class="ec" id="r-cat">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/categories/{slug}</span><span class="ed">Single category</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/categories/technology</div></div>
    </div>

    <h2><i class="bi bi-tags me-2 text-success"></i>Tags</h2>

    <div class="ec" id="r-tags">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/tags</span><span class="ed">All tags sorted by usage</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/tags
{ "success": true, "data": [{ "id":1, "name":"Laravel", "slug":"laravel", "posts_count":8 }] }</div></div>
    </div>

    <h2><i class="bi bi-chat-dots me-2 text-danger"></i>Comments</h2>

    <div class="ec" id="r-comments">
        <div class="eh" onclick="toggle(this)"><span class="mbadge get">GET</span><span class="ep">/api/posts/{slug}/comments</span><span class="ed">Approved comments</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">GET /api/posts/my-post/comments?page=1</div></div>
    </div>

    <div class="ec" id="r-comment-add">
        <div class="eh" onclick="toggle(this)"><span class="mbadge post">POST</span><span class="ep">/api/posts/{slug}/comments</span><span class="ed">Submit comment (held for moderation)</span><i class="bi bi-chevron-down ms-2 text-muted"></i></div>
        <div class="eb"><div class="cb">{ "author_name": "Jane", "author_email": "jane@x.com", "content": "Great!", "parent_id": null }</div></div>
    </div>

    <h2><i class="bi bi-code-slash me-2 text-primary"></i>Usage Examples</h2>

    <h5 class="text-muted mb-2">JavaScript Fetch</h5>
    <div class="cb">// Public — list posts
const { data } = await fetch('/api/posts').then(r => r.json());

// Authenticated — create post
await fetch('/api/posts', {
  method: 'POST',
  headers: { 'Content-Type':'application/json', 'Authorization':'Bearer '+token },
  body: JSON.stringify({ title:'Hello', content:'World', status:'published' })
});</div>

    <h5 class="text-muted mt-3 mb-2">React + Axios</h5>
    <div class="cb">import axios from 'axios';
const api = axios.create({ baseURL: '/api' });
api.interceptors.request.use(cfg => {
  cfg.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
  return cfg;
});
const { data } = await api.get('/posts', { params: { category:'tech', per_page:6 } });</div>

    <h5 class="text-muted mt-3 mb-2">Next.js App Router</h5>
    <div class="cb">// app/blog/page.tsx
async function getPosts() {
  const res = await fetch('{{ config('app.url') }}/api/posts', { next: { revalidate: 60 } });
  return res.json();
}
export default async function BlogPage() {
  const { data: posts } = await getPosts();
  return posts.map(p => &lt;article key={p.id}&gt;&lt;h2&gt;{p.title}&lt;/h2&gt;&lt;/article&gt;);
}</div>

    <h5 class="text-muted mt-3 mb-2">Vue 3 Composable</h5>
    <div class="cb">import { ref } from 'vue';
import axios from 'axios';
export function usePosts() {
  const posts = ref([]);
  const fetch = async (params = {}) => {
    const { data } = await axios.get('/api/posts', { params });
    posts.value = data.data;
  };
  return { posts, fetch };
}</div>

    <div class="mt-4 pt-3 text-muted text-center" style="border-top:1px solid #30363d;font-size:.76rem">
        BlogAdmin API v1.0 • All responses return application/json
    </div>
</div>
</main>

<script>
function toggle(header) {
    const body = header.nextElementSibling;
    body.classList.toggle('open');
    const icon = header.querySelector('i.bi');
    if (icon) icon.className = icon.className.includes('down')
        ? icon.className.replace('down','up') : icon.className.replace('up','down');
}
</script>
</body>
</html>
