<?php $__env->startSection('title', 'Edit Post'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 320px; }
    .form-card { background: #fff; border-radius: 12px; padding: 1.5rem; border: none; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
    .tag-badge input[type=checkbox] { display: none; }
    .tag-badge label { cursor: pointer; padding: .25rem .65rem; border-radius: 20px; border: 1.5px solid #dee2e6; font-size: .8rem; transition: all .2s; display: inline-block; }
    .tag-badge input:checked + label { background: #4e6ef2; border-color: #4e6ef2; color: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Edit Post</h4>
        <small class="text-muted"><?php echo e($post->title); ?></small>
    </div>
    <a href="<?php echo e(route('admin.posts.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form action="<?php echo e(route('admin.posts.update', $post)); ?>" method="POST" enctype="multipart/form-data" id="post-form">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="form-card mb-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('title', $post->title)); ?>" required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="form-label fw-semibold">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="form-control"><?php echo e(old('excerpt', $post->excerpt)); ?></textarea>
                </div>
            </div>

            <div class="form-card mb-3">
                <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                <div id="quill-editor"><?php echo old('content', $post->content); ?></div>
                <input type="hidden" name="content" id="content-input">
            </div>

            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-search me-2"></i>SEO</h6>
                <div class="mb-3">
                    <label class="form-label small">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control form-control-sm"
                           value="<?php echo e(old('meta_title', $post->meta_title)); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm"><?php echo e(old('meta_description', $post->meta_description)); ?></textarea>
                </div>
                <div>
                    <label class="form-label small">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control form-control-sm"
                           value="<?php echo e(old('meta_keywords', $post->meta_keywords)); ?>">
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-send me-2"></i>Publish</h6>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <?php $__currentLoopData = ['draft'=>'Draft','published'=>'Published','scheduled'=>'Scheduled','archived'=>'Archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e(old('status',$post->status) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="<?php echo e(old('published_at', $post->published_at?->format('Y-m-d\TH:i'))); ?>">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                           <?php echo e(old('is_featured', $post->is_featured) ? 'checked' : ''); ?>>
                    <label class="form-check-label small" for="is_featured">Mark as Featured</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" value="1"
                           <?php echo e(old('allow_comments', $post->allow_comments) ? 'checked' : ''); ?>>
                    <label class="form-check-label small" for="allow_comments">Allow Comments</label>
                </div>
                <div class="mt-3 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Update Post
                    </button>
                </div>
            </div>

            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-folder2-open me-2"></i>Category</h6>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">— Uncategorised —</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id',$post->category_id) == $cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-tags me-2"></i>Tags</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php $selectedTags = old('tags', $post->tags->pluck('id')->toArray()) ?>
                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tag-badge">
                        <input type="checkbox" name="tags[]" id="tag-<?php echo e($tag->id); ?>" value="<?php echo e($tag->id); ?>"
                               <?php echo e(in_array($tag->id, $selectedTags) ? 'checked' : ''); ?>>
                        <label for="tag-<?php echo e($tag->id); ?>"><?php echo e($tag->name); ?></label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-image me-2"></i>Featured Image</h6>
                <?php if($post->featured_image): ?>
                    <img src="<?php echo e($post->featured_image_url); ?>" class="img-fluid rounded mb-2" style="max-height:130px">
                <?php endif; ?>
                <input type="file" name="featured_image" class="form-control form-control-sm" accept="image/*">
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic','underline'],['blockquote'],
            [{list:'ordered'},{list:'bullet'}],['link','image'],['clean']] }
    });
    document.getElementById('post-form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH G:\xampp\htdocs\laravel-blog\resources\views/admin/posts/edit.blade.php ENDPATH**/ ?>