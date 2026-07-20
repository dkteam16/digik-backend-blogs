<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>body{background:#f0f2f8;display:flex;align-items:center;justify-content:center;min-height:100vh;}</style>
</head>
<body>
<div class="text-center px-3">
    <i class="bi bi-shield-x text-danger" style="font-size:5rem"></i>
    <h1 class="display-1 fw-bold text-muted">403</h1>
    <h4 class="fw-semibold mb-2">Access Denied</h4>
    <p class="text-muted mb-4">You do not have permission to view this page.</p>
    <a href="{{ url('/') }}" class="btn btn-primary"><i class="bi bi-house me-1"></i> Go Home</a>
</div>
</body>
</html>
