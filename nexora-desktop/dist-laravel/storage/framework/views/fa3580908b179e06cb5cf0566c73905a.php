<?php $__env->startSection('title', 'Pagina inicial'); ?>

<?php $__env->startSection('content'); ?>
    <main class="welcome">
        <div class="welcome-message">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo">
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Usuario\Desktop\nexora-ems-erp\nexora-desktop\laravel\resources\views/home-page.blade.php ENDPATH**/ ?>