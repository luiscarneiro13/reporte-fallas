<div <?php echo e($attributes->merge(['class' => $makeModalClass(), 'id' => $id])); ?>

     <?php if(isset($staticBackdrop)): ?> data-backdrop="static" data-keyboard="false" <?php endif; ?>>

    <div class="<?php echo e($makeModalDialogClass()); ?>">
    <div class="modal-content">

        
        <div class="<?php echo e($makeModalHeaderClass()); ?>">
            <h4 class="modal-title">
                <?php if(isset($icon)): ?><i class="<?php echo e($icon); ?> mr-2"></i><?php endif; ?>
                <?php if(isset($title)): ?><?php echo e($title); ?><?php endif; ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        
        <?php if(! $slot->isEmpty()): ?>
            <div class="modal-body"><?php echo e($slot); ?></div>
        <?php endif; ?>

        
        

    </div>
    </div>

</div>
<?php /**PATH D:\laragon\www\reportefallas\resources\views/vendor/adminlte/components/tool/modal.blade.php ENDPATH**/ ?>