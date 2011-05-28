<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Gestión de retornos y recompensas</h2>
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
            <?php if (!empty($this['projects'])) : ?>
                <?php foreach ($this['projects'] as $project) : ?>
                    <h3><?php echo $project->name; ?></h3>
                    <?php foreach ($project->invests as $key=>$invest) : ?>
                        <?php echo '<p>
                               <img src="' . $invest->user->avatar . '" class="avatar" />
                               <strong>' . $invest->user->name . '</strong><br />
                               Aporta: ' . $invest->amount . ' €  el ' . $invest->invested . '<br />';

                               if ($invest->resign == 1)  {
                                   echo 'Renuncia a recompensa por este aporte<br />';
                               } else {
                                   echo 'Dirección de entrega: '.$invest->address->address.', '.$invest->address->location.', '.$invest->address->zipcode.'  '.$invest->address->country.'<br />';
                                    if (!empty($invest->rewards)) {
                                        echo '<strong>Recompensas esperadas:</strong><br />';
                                        foreach ($invest->rewards as $reward) {
                                            echo $reward->reward;
                                            if ($reward->fulfilled) {
                                                echo ' CUMPLIDA';
                                            } else {
                                                echo ' <a href="/admin/rewards/fulfill/'.$invest->id.','.$reward->id.'">[Cumplir]</a>';
                                            }
                                            echo '<br />';
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