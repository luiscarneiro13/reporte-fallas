<?php $__env->startSection('title', 'Proyectos'); ?>

<?php $__env->startSection('content_header'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $headers = ['Cliente', 'Proyecto', 'División', 'Area geográfica', ''];
    ?>

    <?php if (isset($component)) { $__componentOriginalc3bb9a15a5f747221a204b851ffb93b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc3bb9a15a5f747221a204b851ffb93b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.base-data-table-search','data' => ['title' => 'Proyectos','items' => $projects,'headers' => $headers,'urlBtnAdd' => ''.e(route('admin.sucursal.projects.create')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('base-data-table-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proyectos','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($projects),'headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($headers),'urlBtnAdd' => ''.e(route('admin.sucursal.projects.create')).'']); ?>
         <?php $__env->slot('body', null, []); ?> 
            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->customer_name); ?></td>
                    <td><?php echo e($item->project_name); ?></td>
                    <td><?php echo e($item->division_name); ?></td>
                    <td><?php echo e($item->project_geographic_area); ?></td>
                    <td>
                        <div class="input-group" style="cursor:pointer;">
                            <div>
                                <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="<?php echo e(route('admin.sucursal.projects.edit', $item)); ?>">
                                        <i class="fa fa-edit">&nbsp;</i>
                                        Editar
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form class="formEliminar"
                                        action="<?php echo e(route('admin.sucursal.projects.destroy', $item)); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('delete'); ?>
                                        <button class="dropdown-item" type="submit">
                                            <i class="fa fa-trash">&nbsp;</i>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc3bb9a15a5f747221a204b851ffb93b4)): ?>
<?php $attributes = $__attributesOriginalc3bb9a15a5f747221a204b851ffb93b4; ?>
<?php unset($__attributesOriginalc3bb9a15a5f747221a204b851ffb93b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3bb9a15a5f747221a204b851ffb93b4)): ?>
<?php $component = $__componentOriginalc3bb9a15a5f747221a204b851ffb93b4; ?>
<?php unset($__componentOriginalc3bb9a15a5f747221a204b851ffb93b4); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('customjs'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\reportefallas\src\resources\views/V1/AdminBranch/Projects/index.blade.php ENDPATH**/ ?>