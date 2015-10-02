<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$contact = $vars['contact'];

// calculo fecha de vencimiento (timestamp de un año despues de financiado)
$deadline = mktime(0, 0, 0,
    date('m', strtotime($contact->success_date)),
    date('d', strtotime($contact->success_date)),
    date('Y', strtotime($contact->success_date)) + 1
);

?>
    <dl>
        <dt>Impulsor:</dt>
        <dd><?php echo $contact->owner_name ?> - <a href="mailto:<?php echo $contact->owner_email ?>"><?php echo $contact->owner_email ?></a></dd>
    </dl>

    <dl>
        <dt>Promotor:</dt>
        <dd><?php echo $contact->contract_name ?> - <a href="mailto:<?php echo $contact->contract_email ?>"><?php echo $contact->contract_email ?></a></dd>
    </dl>

    <dl>
        <dt>Telefono:</dt>
        <dd><?php echo $contact->phone ?></dd>
    </dl>

    <dl>
        <dt>Cuentas:</dt>
        <dd><?php
            if (!empty($contact->twitter)) {
                echo '<a href="'.$contact->twitter.'" target="_blank">Twitter</a> ';
            }
            if (!empty($contact->facebook)) {
                echo '<a href="'.$contact->facebook.'" target="_blank">Facebook</a> ';
            }
            if (!empty($contact->google)) {
                echo '<a href="'.$contact->google.'" target="_blank">Google+</a> ';
            }
            if (!empty($contact->identica)) {
                echo '<a href="'.$contact->identica.'" target="_blank">Identica</a> ';
            }
            if (!empty($contact->linkedin)) {
                echo '<a href="'.$contact->linkedin.'" target="_blank">Linkedin</a> ';
            }
        ?></dd>
    </dl>

    <dl>
        <dt>Fecha final campaña:</dt>
        <dd><?php echo date('d-m-Y', strtotime($contact->success_date)); ?></dd>
    </dl>

    <dl>
        <dt>Fecha vencimiento de contrato:</dt>
        <dd><?php echo date('d-m-Y', $deadline); ?></dd>
    </dl>
