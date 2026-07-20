<?php $__env->startSection('title','Media Library'); ?>
<?php $__env->startSection('page-title','Media Library'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Media Library</h4>
        <small class="text-muted"><?php echo e(count($files)); ?> file(s) uploaded</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload me-1"></i> Upload Image
    </button>
</div>

<?php if(count($files) > 0): ?>
<div class="row g-3">
    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden media-card h-100">
            <div class="ratio ratio-1x1" style="background:#f4f6fc">
                <img src="<?php echo e($file['url']); ?>" alt="<?php echo e($file['name']); ?>"
                     class="object-fit-cover w-100 h-100" style="object-fit:cover">
            </div>
            <div class="card-body p-2">
                <div class="text-truncate" style="font-size:.72rem;color:#444" title="<?php echo e($file['name']); ?>">
                    <?php echo e($file['name']); ?>

                </div>
                <div class="text-muted" style="font-size:.68rem">
                    <?php echo e(round($file['size'] / 1024, 1)); ?> KB
                </div>
            </div>
            <div class="card-footer bg-transparent p-2 border-0 d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary flex-fill py-1"
                        onclick="copyUrl('<?php echo e($file['url']); ?>')" title="Copy URL">
                    <i class="bi bi-clipboard" style="font-size:.75rem"></i>
                </button>
                <form action="<?php echo e(route('admin.media.destroy')); ?>" method="POST"
                      onsubmit="return confirm('Delete this file?')" class="flex-fill">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="path" value="<?php echo e($file['path']); ?>">
                    <button class="btn btn-sm btn-outline-danger w-100 py-1">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-images fs-1 d-block mb-3 opacity-25"></i>
    <p class="mb-2">No files uploaded yet.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload me-1"></i>Upload First Image
    </button>
</div>
<?php endif; ?>


<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-semibold">Upload Image</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="upload-zone" class="border-2 border-dashed border-secondary rounded-3 p-5 text-center"
                     style="cursor:pointer;background:#f8f9fc" onclick="document.getElementById('file-input').click()">
                    <i class="bi bi-cloud-upload fs-1 text-muted d-block mb-2"></i>
                    <p class="mb-1 fw-medium">Click to browse or drag & drop</p>
                    <small class="text-muted">JPG, PNG, GIF, WEBP — max 5 MB</small>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const fileInput  = document.getElementById('file-input');
const uploadBtn  = document.getElementById('upload-btn');
const preview    = document.getElementById('upload-preview');
const previewImg = document.getElementById('preview-img');
const status     = document.getElementById('upload-status');
let selectedFile = null;

fileInput.addEventListener('change', function() {
    selectedFile = this.files[0];
    if (!selectedFile) return;
    const reader = new FileReader();
    reader.onload = e => { previewImg.src = e.target.result; preview.classList.remove('d-none'); };
    reader.readAsDataURL(selectedFile);
    uploadBtn.disabled = false;
    status.innerHTML = `<small class="text-muted">${selectedFile.name} (${(selectedFile.size/1024).toFixed(1)} KB)</small>`;
});

uploadBtn.addEventListener('click', async function() {
    if (!selectedFile) return;
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';

    const form = new FormData();
    form.append('file', selectedFile);
    form.append('_token', csrfToken);

    try {
        const res  = await fetch('<?php echo e(route("admin.media.upload")); ?>', { method:'POST', body:form });
        const data = await res.json();
        if (data.success) {
            showToast('Uploaded successfully! URL copied.', 'success');
            navigator.clipboard.writeText(data.url).catch(() => {});
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('Upload failed. Please try again.', 'danger');
        }
    } catch(e) {
        showToast('Upload error: ' + e.message, 'danger');
    }
    uploadBtn.disabled = false;
    uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Upload';
});

// Drag & drop
const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-primary'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-primary'));
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('border-primary');
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH G:\xampp\htdocs\laravel-blog\resources\views/admin/media/index.blade.php ENDPATH**/ ?>