<?php
use Goteo\Library\Page;

$pages = Page::getAll($_SESSION['translator_lang']);
?>
<h3 class="title">Traducción de páginas institucionales</h3>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Descripción</th>
                <!-- <th>Previsualizar</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
            <tr>
                <td><a href="/translate/pages/edit/<?php echo $page->id; ?>">[Edit]</a></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
<!--                <td><a href="<?php echo $page->url; ?>" target="_blank">[Preview]</a></td> -->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
