<?php
$contract = $vars['contract'];
?>
<div class="widget">
    <h2>Gestion de contrato</h2>
    <p>
        <a href="/contract/edit/<?php echo $contract->id; ?>" target="_blank">Editar datos</a>
    </p>
    <p>
        Para ponerle número de factura, o cambiarle la fecha...
    </p>
    <?php echo \trace($contract); ?>
</div>
