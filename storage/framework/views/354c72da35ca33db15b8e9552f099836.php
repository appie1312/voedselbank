

<?php $__env->startSection('title', 'Welkom | Voedselbank Maaskantje'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-lg-6">
                            <img src="<?php echo e(asset('images/foodbank-home.svg')); ?>" class="img-fluid rounded" alt="Illustratie van voedselhulp en samenwerking in de voedselbank">
                        </div>
                        <div class="col-12 col-lg-6">
                            <h1 class="h3">Samen zorgen voor voedselhulp in Maaskantje</h1>
                            <p class="text-secondary">
                                Voedselbank Maaskantje helpt gezinnen die tijdelijk moeite hebben om rond te komen.
                                Met donaties van lokale ondernemers en de inzet van vrijwilligers stellen wij elke week
                                voedselpakketten samen voor mensen die het nodig hebben.
                            </p>

                            <?php if(auth()->guard()->guest()): ?>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-success" href="<?php echo e(route('login')); ?>">Inloggen</a>
                                    <a class="btn btn-outline-success" href="<?php echo e(route('register.form')); ?>">Registreren</a>
                                </div>
                            <?php else: ?>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-success" href="<?php echo e(route('dashboard.redirect')); ?>">Naar mijn dashboard</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h4">Over Ons</h2>
                    <p class="mb-0 text-secondary">
                        Onze organisatie draait op samenwerking. De directie bewaakt kwaliteit en planning,
                        magazijnmedewerkers beheren voorraad en logistiek, en vrijwilligers zorgen dat pakketten
                        met aandacht worden uitgegeven. Zo bouwen we samen aan een sterke gemeenschap waarin
                        niemand zonder hulp hoeft te staan.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h2 class="h4 mb-3">Contactpunten Voor Klanten</h2>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h5">Magazijn Medewerker</h3>
                            <p class="text-secondary">Voor vragen over pakketten, ophalen en productinformatie.</p>
                            <a class="btn btn-outline-success" href="mailto:magazijn@voedselbank-maaskantje.nl?subject=Vraag%20over%20pakket">Stuur een bericht</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h5">Vrijwilliger</h3>
                            <p class="text-secondary">Voor hulpvragen rondom uitgiftemomenten en ondersteuning.</p>
                            <a class="btn btn-outline-success" href="mailto:vrijwilliger@voedselbank-maaskantje.nl?subject=Hulpvraag">Stuur een bericht</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proef examen\voedselbank-maaskantje\resources\views/welcome.blade.php ENDPATH**/ ?>