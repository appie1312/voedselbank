

<?php $__env->startSection('title', 'Accounts Overzicht'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-3">
        <h1 class="h3 mb-1">Accounts Overzicht</h1>
        <p class="text-secondary mb-0">Alle gebruikers voor directie, met gegevens via INNER JOIN.</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Telefoon</th>
                            <th>Afdeling</th>
                            <th>Aangemaakt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($account->name); ?></td>
                                <td><?php echo e($account->email); ?></td>
                                <td><?php echo e(str_replace('_', ' ', ucfirst($account->role))); ?></td>
                                <td><?php echo e($account->telefoon ?: '-'); ?></td>
                                <td><?php echo e($account->afdeling ?: '-'); ?></td>
                                <td><?php echo e(\Illuminate\Support\Carbon::parse($account->created_at)->format('d-m-Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6">Geen accounts beschikbaar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\odaib\Herd\voedselbank-maaskantje\resources\views/accounts/index.blade.php ENDPATH**/ ?>