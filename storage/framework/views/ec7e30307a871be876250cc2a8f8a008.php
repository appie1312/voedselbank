<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-success" href="<?php echo e(route('home')); ?>">
            <span class="rounded-circle border border-success-subtle bg-success-subtle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">VB</span>
            <span>Voedselbank Maaskantje</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">Home</a>
                </li>


                <?php if(auth()->guard()->check()): ?>
                                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('voorraad')); ?>">Voorraad</a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('leveranciers.index')); ?>">Leveranciers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('voorraad')); ?>">Voorraad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('dashboard.redirect')); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('profile.edit')); ?>">Profiel</a>
                    </li>

                    <?php if(auth()->user()->isDirectie()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('klanten.index')); ?>">Klanten</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-2">
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">Uitloggen</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('login')); ?>">Inloggen</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-success btn-sm ms-lg-2" href="<?php echo e(route('register.form')); ?>">Registreren</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\Proef examen\voedselbank-maaskantje\resources\views/components/navbar.blade.php ENDPATH**/ ?>