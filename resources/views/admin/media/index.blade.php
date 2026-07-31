@extends('admin.layouts.app')
@section('title','Media Library')
@section('page-title','Media Library')

@push('styles')
<style>
    .media-card { overflow:hidden; transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
    .media-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(16,24,40,.09); border-color:#d7ddf5; }
    /* Bootstrap has no border-dashed utility, so the dashed edge needs a real rule. */
    .upload-zone {
        border:2px dashed #cbd3e6; border-radius:12px; background:#fbfcff;
        padding:2.5rem 1rem; text-align:center; cursor:pointer; transition:all .18s ease;
    }
    .upload-zone:hover, .upload-zone.dragging { border-color:var(--sb-active); background:#f4f7ff; }
    .upload-zone.dragging { transform:scale(1.01); }
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h4>Media Library</h4>
        <div class="sub">{{ count($files) }} file{{ count($files) === 1 ? '' : 's' }} uploaded</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload me-1"></i> Upload Image
    </button>
</div>

@if(count($files) > 0)
<div class="row g-3">
    @foreach($files as $file)
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="panel media-card h-100 d-flex flex-column">
            <div class="ratio ratio-1x1" style="background:#f4f6fc">
                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" loading="lazy"
                     class="w-100 h-100" style="object-fit:cover">
            </div>
            <div class="p-2 border-top" style="border-color:var(--line-soft)!important">
                <div class="text-truncate fw-medium" style="font-size:.73rem;color:#344054" title="{{ $file['name'] }}">
                    {{ $file['name'] }}
                </div>
                <div class="text-muted" style="font-size:.68rem">
                    {{ round($file['size'] / 1024, 1) }} KB
                </div>
            </div>
            <div class="p-2 pt-0 d-flex gap-2 mt-auto">
                <button class="btn btn-sm btn-outline-secondary flex-fill py-1"
                        onclick="copyUrl('{{ $file['url'] }}')" title="Copy URL">
                    <i class="bi bi-clipboard" style="font-size:.75rem"></i>
                </button>
                <form action="{{ route('admin.media.destroy') }}" method="POST"
                      onsubmit="return confirm('Delete this file?')" class="flex-fill">
                    @csrf @method('DELETE')
                    <input type="hidden" name="path" value="{{ $file['path'] }}">
                    <button class="btn btn-sm btn-outline-danger w-100 py-1" title="Delete">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="panel">
    <div class="empty-state">
        <i class="bi bi-images"></i>
        <p class="mb-3">No files uploaded yet.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-upload me-1"></i>Upload First Image
        </button>
    </div>
</div>
@endif

{{-- Upload Modal --}}
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-semibold">Upload Image</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="upload-zone" class="upload-zone" onclick="document.getElementById('file-input').click()">
                    <i class="bi bi-cloud-upload fs-1 text-muted d-block mb-2"></i>
                    <p class="mb-1 fw-medium">Click to browse or drag &amp; drop</p>
                    <small class="text-muted">JPG, PNG, GIF, WEBP — max 5 MB each</small>
                </div>
                <input type="file" id="file-input" class="d-none" accept="image/*" multiple>
                <div id="upload-preview" class="mt-3 d-none">
                    <img id="preview-img" src="#" class="img-fluid rounded-3 mb-2" style="max-height:180px">
                    <div id="upload-status"></div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="upload-btn" disabled>
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const fileInput  = document.getElementById('file-input');
const uploadBtn  = document.getElementById('upload-btn');
const preview    = document.getElementById('upload-preview');
const previewImg = document.getElementById('preview-img');
const status     = document.getElementById('upload-status');
let selectedFiles = [];

fileInput.addEventListener('change', function() {
    // The input is multiple; keep every selected file, not just the first.
    selectedFiles = Array.from(this.files);
    if (!selectedFiles.length) return;

    const reader = new FileReader();
    reader.onload = e => { previewImg.src = e.target.result; preview.classList.remove('d-none'); };
    reader.readAsDataURL(selectedFiles[0]);

    uploadBtn.disabled = false;
    const totalKb = (selectedFiles.reduce((n, f) => n + f.size, 0) / 1024).toFixed(1);
    status.innerHTML = selectedFiles.length === 1
        ? `<small class="text-muted">${selectedFiles[0].name} (${totalKb} KB)</small>`
        : `<small class="text-muted">${selectedFiles.length} files selected (${totalKb} KB total)</small>`;
});

uploadBtn.addEventListener('click', async function() {
    if (!selectedFiles.length) return;
    uploadBtn.disabled = true;

    let ok = 0, failed = 0;
    for (const [i, file] of selectedFiles.entries()) {
        uploadBtn.innerHTML =
            `<span class="spinner-border spinner-border-sm me-1"></span>Uploading ${i + 1}/${selectedFiles.length}...`;

        const form = new FormData();
        form.append('file', file);
        form.append('_token', csrfToken);

        try {
            const res  = await fetch('{{ route("admin.media.upload") }}', { method:'POST', body:form });
            const data = await res.json();
            if (data.success) {
                ok++;
                // Only the last successful URL is worth putting on the clipboard.
                if (i === selectedFiles.length - 1) navigator.clipboard.writeText(data.url).catch(() => {});
            } else {
                failed++;
            }
        } catch(e) {
            failed++;
        }
    }

    if (ok && !failed) {
        showToast(ok === 1 ? 'Uploaded successfully! URL copied.' : `${ok} files uploaded.`, 'success');
        setTimeout(() => location.reload(), 1200);
    } else if (ok && failed) {
        showToast(`${ok} uploaded, ${failed} failed.`, 'warning');
        setTimeout(() => location.reload(), 1800);
    } else {
        showToast('Upload failed. Please try again.', 'danger');
    }

    uploadBtn.disabled = false;
    uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Upload';
});

// Drag & drop
const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragging'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragging'));
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragging');
    fileInput.files = e.dataTransfer.files; fileInput.dispatchEvent(new Event('change'));
});

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => showToast('URL copied to clipboard!', 'success'));
}

function showToast(msg, type) {
    const id = 'toast-' + Date.now();
    document.getElementById('toast-container').insertAdjacentHTML('beforeend',
        `<div id="${id}" class="toast align-items-center text-bg-${type} border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${id}').remove()"></button>
            </div>
        </div>`
    );
    setTimeout(() => document.getElementById(id)?.remove(), 3000);
}
</script>
@endpush
