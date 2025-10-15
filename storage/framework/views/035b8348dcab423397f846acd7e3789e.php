<div class="card">
    <div class="card-body">
        
        <div id="table2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                
                <div class="col-sm-12 col-md-6">
                    <?php if(isset($title)): ?>
                        <label for="">
                            <div class="form-inline justify-content-between align-items-center">
                                <h4 class="mr-3"><?php echo e($title); ?></h4>
                                <?php if(isset($urlBtnAdd)): ?>
                                    <a href="<?php echo e($urlBtnAdd); ?>" style="margin-top:-6px">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endif; ?>
                </div>

                
                <div class="col-sm-12 col-md-6">
                    <div id="table2_filter" class="dataTables_filter">
                        <label>
                            
                            <div class="d-flex">
                                
                                <?php if (isset($component)) { $__componentOriginale5d826ae10df3aa87f8449f474c11664 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5d826ae10df3aa87f8449f474c11664 = $attributes; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Form\Input::resolve(['name' => 'searchInput','id' => 'searchInput'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('adminlte-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(JeroenNoten\LaravelAdminLte\View\Components\Form\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'margin-right: 5px; flex-grow: 1;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5d826ae10df3aa87f8449f474c11664)): ?>
<?php $attributes = $__attributesOriginale5d826ae10df3aa87f8449f474c11664; ?>
<?php unset($__attributesOriginale5d826ae10df3aa87f8449f474c11664); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5d826ae10df3aa87f8449f474c11664)): ?>
<?php $component = $__componentOriginale5d826ae10df3aa87f8449f474c11664; ?>
<?php unset($__componentOriginale5d826ae10df3aa87f8449f474c11664); ?>
<?php endif; ?>
                                <input type="submit" id="searchButton" class="form-control form-control-sm btn-primary"
                                    value="Filtrar">
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="table2" style="width: 100%;"
                            class="table table-bordered table-hover table-striped dataTable no-footer">
                            <thead class="thead-dark">
                                <tr role="row">
                                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($item); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo e($body); ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="row mt-2">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" id="table2_info" role="status" aria-live="polite">
                        Mostrando <?php echo e($items->firstItem()); ?> a <?php echo e($items->lastItem()); ?> de <?php echo e($items->total()); ?>

                        registros
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="table2_paginate">
                        <?php echo e($items->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('customjs'); ?>
    <script>
        $(document).ready(function() {
            // ... (Tu código JavaScript para el filtro se mantiene igual)
            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            var queryValue = getQueryParam('query');
            if (queryValue) {
                $('#searchInput').val(queryValue);
            } else {
                $('#searchInput').val('');
            }

            document.getElementById('searchButton').addEventListener('click', function() {
                var query = document.getElementById('searchInput').value;
                window.location.href = window.location.pathname + '?query=' + encodeURIComponent(query);
            });

            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event
                        .preventDefault();
                    document.getElementById('searchButton').click();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('customcss'); ?>
<?php $__env->stopSection(); ?>
<?php /**PATH D:\laragon\www\reportefallas\resources\views/components/base-data-table-search.blade.php ENDPATH**/ ?>