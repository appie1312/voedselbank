

<?php $__env->startSection('title', 'Registreren | Voedselbank Maaskantje'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-2">Registreren</h1>
                    <p class="text-secondary mb-4">Maak een account aan. Nieuwe accounts worden automatisch vrijwilliger.</p>

                    <form method="POST" action="<?php echo e(route('register')); ?>" class="row g-3">
                        <?php echo csrf_field(); ?>

                        <div class="col-12">
                            <label for="name" class="form-label">Naam</label>
                            <input id="name" name="name" type="text" class="form-control" value="<?php echo e(old('name')); ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input id="email" name="email" type="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password_confirmation" class="form-label">Bevestig wachtwoord</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">Registreren</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\odaib\Herd\voedselbank-maaskantje\resources\views/auth/register.blade.php ENDPATH**/ ?>