<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php ($dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home')); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ($dashboard_url = $dashboard_url ? route($dashboard_url) : ''); ?>
<?php else: ?>
    <?php ($dashboard_url = $dashboard_url ? url($dashboard_url) : ''); ?>
<?php endif; ?>


<a href="#"
    <?php if($layoutHelper->isLayoutTopnavEnabled()): ?> class="navbar-brand <?php echo e(config('adminlte.classes_brand')); ?>"
    <?php else: ?>
        class="brand-link <?php echo e(config('adminlte.classes_brand')); ?>" <?php endif; ?>>

    
    

    <img src="<?php echo e(session('branch')->logo ? asset('storage/' . session('branch')->logo) : asset('logo.webp')); ?>"
        alt="<?php echo e(config('adminlte.logo_img_alt', 'Reporte de fallas')); ?>" class="img-fluid" style="opacity:1">

    
    <span class="brand-text font-weight-light <?php echo e(config('adminlte.classes_brand_text')); ?>">
        
        
        &nbsp;
    </span>

</a>
<?php /**PATH D:\laragon\www\reportefallas\src\resources\views/vendor/adminlte/partials/common/brand-logo-xs.blade.php ENDPATH**/ ?>