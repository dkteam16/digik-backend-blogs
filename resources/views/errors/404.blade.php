<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        @media(max-width:576px){.error-icon{font-size:3rem!important}.error-code{font-size:3rem!important}}
    </style>
</head>
<body>
<div class="text-center px-3">
    <i class="bi bi-exclamation-triangle text-warning error-icon" style="font-size:5rem"></i>
    <h1 class="display-1 fw-bold text-muted error-code">404</h1>
    <h4 class="fw-semibold mb-2">Page Not Found</h4>
    <p class="text-muted mb-4">The page you are looking for doesn't exist or has been moved.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">
        <i class="bi bi-house me-1"></i> Go Home
    </a>
</div>
</body>
</html>
