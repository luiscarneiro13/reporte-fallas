<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['value', 'btnAddModalTarget' => null, 'required' => false]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['value', 'btnAddModalTarget' => null, 'required' => false]); ?>
<?php foreach (array_filter((['value', 'btnAddModalTarget' => null, 'required' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div>
    <label class="mt-3" <?php echo e($attributes->merge(['class' => ''])); ?>>
        <?php echo e($value ?? $slot); ?>

        <?php if($btnAddModalTarget): ?>
            <a href="#" data-toggle="modal" data-target="<?php echo e($btnAddModalTarget); ?>" class="small-box-footer">
                <i class="fas fa-plus-circle"></i>
            </a>
        <?php endif; ?>
        <?php if($required): ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
</div>
<?php /**PATH D:\laragon\www\reportefallas\resources\views/components/label.blade.php ENDPATH**/ ?>