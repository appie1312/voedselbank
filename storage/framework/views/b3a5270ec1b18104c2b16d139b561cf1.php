

<?php $__env->startSection('title', 'Home | Voedselbank Maaskantje'); ?>

<?php $__env->startSection('page_styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/home.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="hero">
        <h1>Samen tegen voedselverspilling en armoede</h1>
        <p>
            Welkom bij Voedselbank Maaskantje. Wij ondersteunen gezinnen met een voedselpakket,
            werken samen met lokale partners en zorgen voor een eerlijke verdeling van donaties.
        </p>

        <?php if(auth()->guard()->guest()): ?>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?php echo e(route('login')); ?>">Inloggen</a>
                <a class="btn" href="<?php echo e(route('register.form')); ?>">Registreren</a>
            </div>
        <?php else: ?>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?php echo e(route('dashboard.redirect')); ?>">Naar mijn dashboard</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="grid-info">
        <article class="info-card">
            <h2>Directie</h2>
            <p>Overzicht van gebruikers, coördinatie van beleid en voortgang.</p>
        </article>
        <article class="info-card">
            <h2>Magazijn Medewerker</h2>
            <p>Beheer van voorraad, ontvangst en uitgifte van producten.</p>
        </article>
        <article class="info-card">
            <h2>Vrijwilliger</h2>
            <p>Ondersteuning bij uitgifte, logistiek en hulp aan cliënten.</p>
        </article>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\odaib\Herd\voedselbank-maaskantje\resources\views/home.blade.php ENDPATH**/ ?>