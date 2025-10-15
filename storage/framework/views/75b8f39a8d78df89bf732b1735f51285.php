<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <?php echo $__env->yieldPushContent('css'); ?>
    <?php echo $__env->yieldContent('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('classes_body', $layoutHelper->makeBodyClasses()); ?>

<?php $__env->startSection('body_data', $layoutHelper->makeBodyData()); ?>

<?php $__env->startSection('body'); ?>
    <div class="wrapper bg">
        <?php $__env->startSection('tasa_del_dia'); ?>
            <?php if (isset($component)) { $__componentOriginala75af2ac821b567d0b23cd21a0d73177 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala75af2ac821b567d0b23cd21a0d73177 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dailyRate','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dailyRate'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala75af2ac821b567d0b23cd21a0d73177)): ?>
<?php $attributes = $__attributesOriginala75af2ac821b567d0b23cd21a0d73177; ?>
<?php unset($__attributesOriginala75af2ac821b567d0b23cd21a0d73177); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala75af2ac821b567d0b23cd21a0d73177)): ?>
<?php $component = $__componentOriginala75af2ac821b567d0b23cd21a0d73177; ?>
<?php unset($__componentOriginala75af2ac821b567d0b23cd21a0d73177); ?>
<?php endif; ?>
        <?php $__env->stopSection(); ?>
        <?php $__env->startSection('tasa_promedio'); ?>
            <?php if (isset($component)) { $__componentOriginalc77ddc966c9d2b355cb20a5b9ffed2e0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc77ddc966c9d2b355cb20a5b9ffed2e0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.averageRate','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('averageRate'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc77ddc966c9d2b355cb20a5b9ffed2e0)): ?>
<?php $attributes = $__attributesOriginalc77ddc966c9d2b355cb20a5b9ffed2e0; ?>
<?php unset($__attributesOriginalc77ddc966c9d2b355cb20a5b9ffed2e0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc77ddc966c9d2b355cb20a5b9ffed2e0)): ?>
<?php $component = $__componentOriginalc77ddc966c9d2b355cb20a5b9ffed2e0; ?>
<?php unset($__componentOriginalc77ddc966c9d2b355cb20a5b9ffed2e0); ?>
<?php endif; ?>
        <?php $__env->stopSection(); ?>
    
    

    
    <?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
    <?php echo $__env->make('adminlte::partials.navbar.navbar-layout-topnav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('adminlte::partials.navbar.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php if(!$layoutHelper->isLayoutTopnavEnabled()): ?>
        <?php echo $__env->make('adminlte::partials.sidebar.left-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php if(empty($iFrameEnabled)): ?>
        <?php echo $__env->make('adminlte::partials.cwrapper.cwrapper-default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('adminlte::partials.cwrapper.cwrapper-iframe', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php if (! empty(trim($__env->yieldContent('footer')))): ?>
        <?php echo $__env->make('adminlte::partials.footer.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <?php if(config('adminlte.right_sidebar')): ?>
        <?php echo $__env->make('adminlte::partials.sidebar.right-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('adminlte_js'); ?>
<?php echo $__env->yieldPushContent('js'); ?>
<?php echo $__env->yieldContent('js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\reportefallas\resources\views/vendor/adminlte/page.blade.php ENDPATH**/ ?>