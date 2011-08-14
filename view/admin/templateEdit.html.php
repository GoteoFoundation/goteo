<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Gestión de plantillas emails automáticos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/templates">Plantillas</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <h3>Editando la plantilla '<?php echo $this['template']->name; ?>'</h3>
            
            <p><?php echo $this['template']->purpose; ?></p>

            <div class="widget board">
                <form method="post" action="/admin/templates/edit/<?php echo $this['template']->id; ?>">
                    <p>
                        <label for="tpltitle">Título:</label><br />
                        <input id="tpltitle" type="text" name="title" size="120" value="<?php echo $this['template']->title; ?>" />
                    </p>

                    <p>
                        <label for="tpltext">Contenido:</label><br />
                        <textarea id="tpltext" name="text" cols="120" rows="20"><?php echo $this['template']->text; ?></textarea>
                    </p>
                    
                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';