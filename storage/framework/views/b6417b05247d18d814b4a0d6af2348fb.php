<?php $__env->startSection('title', 'Equipos'); ?>

<?php $__env->startSection('content_header'); ?>

    <h1>Crear equipo</h1>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('admin.sucursal.equipment.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($back_url): ?>
                    <input type="hidden" name="back_url" value="<?php echo e($back_url); ?>">
                <?php endif; ?>

                <div class="row">
                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'internal_code','label' => 'Código interno','placeholder' => '','class' => 'col-md-3','value' => ''.e(old('internal_code')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'internal_code','label' => 'Código interno','placeholder' => '','class' => 'col-md-3','value' => ''.e(old('internal_code')).'']); ?>
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

                    <div class="col-md-5">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Proyecto','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Proyecto','required' => true]); ?>
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
                        <?php echo e(Form::select('project_id', $projects, null, ['class' => 'select2 form-control'])); ?>

                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Datos físicos</h5>

                <div class="row">

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['required' => true,'name' => 'placa','placeholder' => '','value' => ''.e(old('placa')).'','label' => 'Placa','class' => 'col-md-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['required' => true,'name' => 'placa','placeholder' => '','value' => ''.e(old('placa')).'','label' => 'Placa','class' => 'col-md-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['required' => true,'name' => 'brand_name','placeholder' => '','value' => ''.e(old('brand_name')).'','label' => 'Marca','class' => 'col-md-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['required' => true,'name' => 'brand_name','placeholder' => '','value' => ''.e(old('brand_name')).'','label' => 'Marca','class' => 'col-md-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['required' => true,'name' => 'vehicle_model','label' => 'Modelo','placeholder' => '','value' => ''.e(old('vehicle_model')).'','class' => 'col-md-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['required' => true,'name' => 'vehicle_model','label' => 'Modelo','placeholder' => '','value' => ''.e(old('vehicle_model')).'','class' => 'col-md-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Año','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Año','required' => true]); ?>
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
                        <?php echo e(Form::select('model_year', $modelYears, null, ['class' => 'select2 form-control'])); ?>

                    </div>

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['required' => true,'name' => 'color','label' => 'Color','placeholder' => '','value' => ''.e(old('color')).'','class' => 'col-md-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['required' => true,'name' => 'color','label' => 'Color','placeholder' => '','value' => ''.e(old('color')).'','class' => 'col-md-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Racda']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Racda']); ?>
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
                        <?php echo e(Form::select('racda', ['Si', 'No', 'N/A'], null, ['class' => 'form-control'])); ?>

                    </div>

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'serial_niv','label' => 'Serial N.I.V','placeholder' => '','value' => ''.e(old('serial_niv')).'','class' => 'col-md-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'serial_niv','label' => 'Serial N.I.V','placeholder' => '','value' => ''.e(old('serial_niv')).'','class' => 'col-md-4']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'body_serial_number','label' => 'Serial carrocería','placeholder' => '','value' => ''.e(old('body_serial_number')).'','class' => 'col-md-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'body_serial_number','label' => 'Serial carrocería','placeholder' => '','value' => ''.e(old('body_serial_number')).'','class' => 'col-md-4']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'chassis_serial_number','label' => 'Serial chasis','placeholder' => '','value' => ''.e(old('chassis_serial_number')).'','class' => 'col-md-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chassis_serial_number','label' => 'Serial chasis','placeholder' => '','value' => ''.e(old('chassis_serial_number')).'','class' => 'col-md-4']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'engine_serial_number','label' => 'Serial motor','placeholder' => '','value' => ''.e(old('engine_serial_number')).'','class' => 'col-md-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'engine_serial_number','label' => 'Serial motor','placeholder' => '','value' => ''.e(old('engine_serial_number')).'','class' => 'col-md-4']); ?>
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

                <hr>

                <h5 class="mb-3">Datos legales</h5>

                <div class="row">

                    <?php if (isset($component)) { $__componentOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d56ceae387c15d2ab7eb61b935e3e06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'owner','label' => 'Propietario','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('owner')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'owner','label' => 'Propietario','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('owner')).'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-custom','data' => ['name' => 'origin','label' => 'Origen','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('origin')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-custom'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'origin','label' => 'Origen','placeholder' => '','class' => 'col-md-4','value' => ''.e(old('origin')).'']); ?>
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
                    <a href="<?php echo e(request()->back_url ?? route('admin.sucursal.equipment.index')); ?>"
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
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/V1/AdminBranch/Equipment/create.blade.php ENDPATH**/ ?>