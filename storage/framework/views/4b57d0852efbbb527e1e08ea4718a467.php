<?php if(session('status_success')): ?>
    <div class="alert alert-success alert-dismissible fade show flash-auto" role="alert" data-auto-dismiss="5000">
        <?php echo e(session('status_success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session('status_error')): ?>
    <div class="alert alert-danger" role="alert"><?php echo e(session('status_error')); ?></div>
<?php endif; ?>

<?php if(isset($status_error)): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($status_error); ?></div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Controleer je invoer:</strong>
        <ul class="mb-0 mt-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH C:\Proef examen\voedselbank-maaskantje\resources\views/partials/flash.blade.php ENDPATH**/ ?>