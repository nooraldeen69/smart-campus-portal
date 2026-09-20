<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Smart Campus Portal'); ?></title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-light">
    <main class="px-2 px-md-4">
        <?php if(session('error')): ?>
            <div class="alert alert-danger mt-3 mb-0" role="alert"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if(session('status')): ?>
            <div class="alert alert-success mt-3 mb-0" role="status"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>
<?php /**PATH D:\الترم الخامس\ITPM\Project\smart-campus-portal\resources\views/layouts/app.blade.php ENDPATH**/ ?>