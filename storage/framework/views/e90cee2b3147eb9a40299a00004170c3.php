<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
    'help' => '',
    'type' => '',
    'id' => '',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
    'help' => '',
    'type' => '',
    'id' => '',
]); ?>
<?php foreach (array_filter(([
    'placeholder' => '',
    'name' => '',
    'value' => '',
    'label' => '',
    'required' => false,
    'class' => '',
    'help' => '',
    'type' => '',
    'id' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="mt-3 <?php echo e($class); ?>">
    <label for="<?php echo e($name); ?>"><?php echo e($label); ?> <?php if($required): ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
    <input type="<?php echo e($type ?? 'text'); ?>" name="<?php echo e($name); ?>" id="<?php echo e($id); ?>" class="form-control"
        placeholder="<?php echo e($placeholder); ?>" value="<?php echo e($value); ?>">

    
    <?php if($help): ?>
        <small class="form-text text-muted">
            <?php echo e($help); ?>

        </small>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/resources/views/components/input-custom.blade.php ENDPATH**/ ?>