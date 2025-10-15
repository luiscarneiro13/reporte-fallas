<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
    <?php ($def_container_class = 'container'); ?>
<?php else: ?>
    <?php ($def_container_class = 'container-fluid'); ?>
<?php endif; ?>


<div class="content-wrapper <?php echo e(config('adminlte.classes_content_wrapper', '')); ?>">

    
    <?php if (! empty(trim($__env->yieldContent('content_header')))): ?>
        <div class="content-header">
            <div class="<?php echo e(config('adminlte.classes_content_header') ?: $def_container_class); ?>">
                <?php echo $__env->yieldContent('content_header'); ?>
                <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="content">
        <div class="<?php echo e(config('adminlte.classes_content') ?: $def_container_class); ?>">
            <?php echo $__env->yieldPushContent('content'); ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <div class="separacion-movil-600"></div>
</div>

<style>
    /*
    Esta Media Query aplica los estilos a pantallas con un ancho MÁXIMO de 991px.
    Esto cubre típicamente móviles y tablets (Bootstrap usa 992px como punto de quiebre para 'md').
    */
    @media (max-width: 991px) {
        .separacion-movil-600 {
            height: 600px !important;
            /* ¡Importante! Asegura que se aplique */
            margin: 0;
            padding: 0;
            display: block;
        }
    }

    /* Para pantallas grandes, esta clase no tendrá efecto (height: 0 o simplemente se omite) */
    @media (min-width: 992px) {
        .separacion-movil-600 {
            height: 0 !important;
        }
    }
</style>
<?php /**PATH D:\laragon\www\reportefallas\resources\views/vendor/adminlte/partials/cwrapper/cwrapper-default.blade.php ENDPATH**/ ?>