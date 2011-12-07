<?php

use Goteo\Core\View,
    Goteo\Library\Text;

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div>
            <h2><?php echo $this['description']; ?></h2>
        </div>
    </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div id="main">

        <div class="widget">
            <h3 class="title">Crear convocatoria</h3>
            <p>Para crear una nueva convocatoria es necesario especificar el identificador y el usuario convocador (si no es uno mismo).</p>
            <form method="post" action="/call/create">
                <input type="hidden" name="action" value="continue" />
                <input type="hidden" name="confirm" value="true" />
                <label>Identificador:<br />
                    <input type="text" name="name" value="" />
                </label>

                <!-- desplegable de usuarios convocadores o algo -->
<!--
                <label>Convocador: (solo si no es uno mismo)<br />
                    <input type="text" name="caller" value="" />
                </label>
-->

                <input type="submit" name="create" value="Crear nueva convocatoria" />
            </form>
        </div>

    </div>

   <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
