<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Gestión de plantillas de emailsautomáticos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
                <table>
                    <thead>
                        <tr>
                            <th>Plantilla</th>
                            <th>Descripción</th>
                            <th><!-- Editar --></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this['templates'] as $template) : ?>
                        <tr>
                            <td><?php echo $template->name; ?></td>
                            <td><?php echo $template->purpose; ?></td>
                            <td><a href="/admin/templates/edit/<?php echo $template->id; ?>">[Edit]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';