<?php $__env->startSection('title', 'Sign in - Smart Campus Portal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width: 460px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 text-center">
            <h1 class="h3 text-primary fw-bold mb-1">Smart Campus Portal</h1>
            <p class="text-muted mb-4">Al-Rasheed Smart University</p>

            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary btn-lg w-100">Go to my dashboard</a>
            <?php else: ?>
                <a href="<?php echo e(route('sso.redirect')); ?>" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-shield-lock me-2"></i>Sign in with University ID
                </a>
                <p class="small text-muted mt-3 mb-0">
                    One login for SIS, LMS and Library.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\الترم الخامس\ITPM\Project\smart-campus-portal\resources\views/welcome.blade.php ENDPATH**/ ?>