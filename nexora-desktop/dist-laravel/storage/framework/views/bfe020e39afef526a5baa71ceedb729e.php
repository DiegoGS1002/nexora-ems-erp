<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $__env->yieldContent('title', 'Sistema de controle'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app()->environment() === 'production' ? secure_asset('css/home.css') : asset('css/home.css')); ?>">
</head>
<body>

    <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

</body>
</html>
<?php /**PATH C:\Users\Usuario\Desktop\nexora-ems-erp\nexora-desktop\laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>