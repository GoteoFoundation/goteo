<?php

use Goteo\Library\Text,
    Goteo\Library\Paypal;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Administración de transacciones</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <p><?php echo $this['content']; ?></p>

            <?php if (!empty($this['projects'])) :
                foreach ($this['projects'] as $project) : ?>
                    <h3><?php echo $project->name; ?></h3>
                    <?php foreach ($project->investors as $key=>$investor) : $errors = array();?>
                        <p><?php echo $investor['name']; ?>: <?php echo $investor['amount']; ?> &euro;</p>
                        <div>
                            <?php
                            $invest = $investor['invest'];
                            if (empty($invest->preapproval)) {
                                //si no tiene preaproval, cancelar
                                echo 'Cancelamos porque no ha hecho bien el preapproval.<br />';
                                $invest->cancel();
                            } else {
                                if (empty($invest->payment)) {
                                    //si tiene preaprval y no tiene pago, cargar
                                    echo 'Detalles del preapproval comentados.<br />';
                                    $preapproval = Paypal::preapprovalDetails($invest->preapproval, $errors);
                                    echo '
                                    <!-- <fieldset><legend>Preapproval</legend>
                                        <pre>' . print_r($preapproval, 1) . '</pre>
                                    </fieldset> -->
                                    ';
                                    echo 'Ejecutamos el cargo.<br />';
                                    Paypal::pay($invest, $errors);
                                } else {
                                    //si tiene preaproval y tiene pago, ok
                                    echo 'Detalles del pago comentados<br />';
                                    $payment = Paypal::paymentDetails($invest->payment, $errors);
                                    echo '
                                    <!-- <fieldset><legend>Payment</legend>
                                        <pre>' . print_r($payment, 1) . '</pre>
                                    </fieldset> -->
                                    ';
                                    echo 'Todo ok.<br />';
                                }
                            }

                            if (!empty($errors))
                                echo 'ERROR: ' . implode('. ', $errors);
                            ?>
                        </div>
                    <?php endforeach; ?>
                    <br />
            <?php endforeach;
                endif;?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';