<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Voorraadoverzicht - Voedselbank Maaskantje</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <h1>Overzicht Voorraad</h1>

    <!-- Terugkoppeling aan eindgebruiker -->
    <?php if (!empty($melding)): ?>
        <div class="alert">
            <p><strong>Melding:</strong> <?php echo htmlspecialchars($melding); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($voorraad)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Categorie</th>
                    <th>Aantal</th>
                    <th>Locatie</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voorraad as $item): ?>
                    <tr class="<?php echo ($item->status == 'Aanvullen') ? 'warning' : ''; ?>">
                        <td><?php echo htmlspecialchars($item->product_naam); ?></td>
                        <td><?php echo htmlspecialchars($item->categorie_naam); ?></td>
                        <td><?php echo htmlspecialchars($item->hoeveelheid); ?></td>
                        <td><?php echo htmlspecialchars($item->locatie); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($item->status); ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
