<?php $__env->startSection('title', 'Ejecutores'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Crear ejecutor</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('admin.sucursal.executors.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($back_url): ?>
                    <input type="hidden" name="back_url" value="<?php echo e($back_url); ?>">
                <?php endif; ?>

                <h5 class="mb-3">Datos básicos</h5>

                <div class="row">

                    
                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'identification_number','label' => 'Cédula','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('identification_number')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'identification_number','label' => 'Cédula','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('identification_number')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $attributes = $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $component = $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'first_name','label' => 'Nombre','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('first_name')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'first_name','label' => 'Nombre','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('first_name')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $attributes = $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $component = $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'last_name','label' => 'Apellido','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('last_name')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'last_name','label' => 'Apellido','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('last_name')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $attributes = $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $component = $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>

                    
                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'phone_number','label' => 'Teléfono','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('phone_number')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone_number','label' => 'Teléfono','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('phone_number')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $attributes = $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $component = $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>

                    <div class="col-md-2">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Tipo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Tipo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                        <?php echo e(Form::select('external', [0 => 'Interno', 1 => 'Externo'], null, ['class' => 'form-control'])); ?>

                    </div>

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'address','label' => 'Dirección (opcional)','placeholder' => '','class' => 'col-md-6','value' => ''.e(old('address')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'address','label' => 'Dirección (opcional)','placeholder' => '','class' => 'col-md-6','value' => ''.e(old('address')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $attributes = $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06)): ?>
<?php $component = $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06; ?>
<?php unset($__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06); ?>
<?php endif; ?>
                </div>

                <div class="row mt-5">
                    <a href="<?php echo e(request()->back_url ?? route('admin.sucursal.executors.index')); ?>"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <?php if (isset($component)) { $__componentOriginal84b78d66d5203b43b9d8c22236838526 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84b78d66d5203b43b9d8c22236838526 = $attributes; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Form\Button::resolve(['type' => 'submit','label' => 'Guardar','theme' => 'primary','icon' => 'fas fa-lg fa-save'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('adminlte-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(JeroenNoten\LaravelAdminLte\View\Components\Form\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84b78d66d5203b43b9d8c22236838526)): ?>
<?php $attributes = $__attributesOriginal84b78d66d5203b43b9d8c22236838526; ?>
<?php unset($__attributesOriginal84b78d66d5203b43b9d8c22236838526); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84b78d66d5203b43b9d8c22236838526)): ?>
<?php $component = $__componentOriginal84b78d66d5203b43b9d8c22236838526; ?>
<?php unset($__componentOriginal84b78d66d5203b43b9d8c22236838526); ?>
<?php endif; ?>
                </div>
            </form>
        </div>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/V1/AdminBranch/Executors/create.blade.php ENDPATH**/ ?>