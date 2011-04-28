<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Gestión de páginas institucionales</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <ul>
            <?php foreach ($this['pages'] as $page) : ?>
                <li><?php echo $page->name; ?>: <?php echo $page->description; ?> 
                    <a href="/admin/pages?page=<?php echo $page->id; ?>">[Editar]</a>
                    <a href="<?php echo $page->url; ?>" target="_blank">[Previsualizar]</a>
                </li>
            <?php endforeach; ?>
            </ul>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';