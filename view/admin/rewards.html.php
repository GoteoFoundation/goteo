<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Gestión de retornos</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['projects'])) : ?>
            
                <?php foreach ($this['projects'] as $project) : ?>
                    <h3><?php echo $project->name; ?></h3>
                    <?php foreach ($project->invests as $key=>$invest) : ?>
                    <?php echo '<p>
                           <img src="' . $invest->user->avatar . '" class="avatar" />
                           ' . $invest->user->name . '<br />
                           Aporta: ' . $invest->amount . ' €  el ' . $invest->invested . '<br />';

                           if ($invest->resign == 1)  {
                               echo 'Renuncia a recompensa por este aporte<br />';
                           } else {
                               echo 'Dirección de entrega: '.$invest->address->address.', '.$invest->address->location.', '.$invest->address->zipcode.'  '.$invest->address->country.'<br />';

                                if (!empty($invest->rewards)) {
                                    echo '<span>Recompensas esperadas</span><br />';
                                    foreach ($invest->rewards as $reward) {
                                        echo \trace($reward);

                                    }
                                }
                           }

                           echo '</p>';

                    ?>
                    <?php endforeach; ?>
                    <br />
            <?php endforeach; ?>
            <?php else : ?>
                <p>No hay aportes en los proyectos financiados.</p>
            <?php endif;?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';