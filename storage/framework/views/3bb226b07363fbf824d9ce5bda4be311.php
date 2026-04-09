

<?php $__env->startSection('title', 'Directie Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Directie Dashboard</h1>
            <p class="text-secondary mb-0">Overzicht van medewerkers en rollen binnen de voedselbank.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Totaal accounts</h2>
                    <p class="display-6 mb-0"><?php echo e($accounts->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Directie</h2>
                    <p class="display-6 mb-0"><?php echo e($accounts->where('role', 'directie')->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Magazijn</h2>
                    <p class="display-6 mb-0"><?php echo e($accounts->where('role', 'magazijn_medewerker')->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Vrijwilligers</h2>
                    <p class="display-6 mb-0"><?php echo e($accounts->where('role', 'vrijwilliger')->count()); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="h5 mb-3">Gebruikersoverzicht (INNER JOIN users + user_profiles)</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Telefoon</th>
                            <th>Afdeling</th>
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
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5">Nog geen accounts gevonden.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\voedselbank-maaskantje\resources\views/dashboard/directie.blade.php ENDPATH**/ ?>