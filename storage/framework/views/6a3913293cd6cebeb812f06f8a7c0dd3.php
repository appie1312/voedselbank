

<?php $__env->startSection('title', 'Technische Logs | Directie'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Technische Logs</h1>
            <p class="text-secondary mb-0">Laatste 200 regels uit <code><?php echo e($logPath); ?></code>.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?php echo e(route('directie.dashboard')); ?>">Terug naar directie dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Tijd</th>
                            <th>Level</th>
                            <th>Bericht</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($entry['tijd']); ?></td>
                                <td>
                                    <?php
                                        $badgeClass = match ($entry['level']) {
                                            'info' => 'text-bg-info',
                                            'warning' => 'text-bg-warning',
                                            'error' => 'text-bg-danger',
                                            default => 'text-bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e(strtoupper($entry['level'])); ?></span>
                                </td>
                                <td class="font-monospace small" style="white-space: pre-wrap; word-break: break-word;"><?php echo e($entry['bericht']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3">Nog geen technische logs gevonden.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\odaib\Herd\voedselbank-maaskantje\resources\views/dashboard/logs.blade.php ENDPATH**/ ?>