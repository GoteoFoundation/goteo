<?php

use Goteo\Library\Text;

$amount = (isset($_GET['amount'])) ? $_GET['amount'] : null;
$user   = (isset($_GET['user'])) ? $_GET['user'] : null;
$proj   = (isset($_GET['proj'])) ? $_GET['proj'] : null;


$users = array();
foreach ($vars['users'] as $uI=>$uN) {
    $users[] = '{ value: "'.str_replace(array("'", '"'), '`', $uN).' ['.$uI.']", id: "'.$uI.'" }';
    if ($uI == $user) $preU = "$uN [$uI]";
}

$projs = array();
foreach ($vars['projects'] as $pI=>$pN) {
    $projs[] = '{ value: "'.str_replace(array("'", '"'), '`', $pN).'", id: "'.$pI.'" }';
    if ($pI == $proj) $preP = "$pN [$pI]";
}


?>
<div class="widget">
    <form id="filter-form" action="/admin/accounts/add" method="post">
        <input type="hidden" id="user" name="user" value="<?php echo $user; ?>" /> <br />
        <input type="hidden" id="project" name="project" value="<?php echo $proj; ?>" />

        <div class="ui-widget">
            <label for="busca-user">Buscador usuario:</label><br />
            <input type="text" id="busca-user" name="user_searcher" value="<?php echo $preU; ?>" style="width:500px;"/>
        </div>

        <br />

        <div class="ui-widget">
            <label for="busca-proj">Buscador proyecto: (solo proyectos en campaña)</label><br />
            <input type="text" id="busca-proj" name="proj_searcher" value="<?php echo $preP; ?>" style="width:500px;"/>
        </div>

        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="<?php echo $amount; ?>" />
        </p>

        <p>
            <label for="invest-campaign">Convocatoria:</label><br />
            <select id="invest-campaign" name="campaign">
                <option value="">Seleccionar la convocatoria que riega este aporte</option>
            <?php foreach ($vars['calls'] as $campaignId=>$campaignName) : ?>
                <option value="<?php echo $campaignId; ?>"><?php echo substr($campaignName, 0, 100); ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="invest-anonymous">Aporte anónimo:</label><br />
            <input id="invest-anonymous" type="checkbox" name="anonymous" value="1">
        </p>

        <input type="submit" name="add" value="Generar aporte" />

    </form>
</div>
<script type="text/javascript">
$(function () {

    var users = [<?php echo implode(', ', $users); ?>];
    var projs = [<?php echo implode(', ', $projs); ?>];

    /* Autocomplete para usuario y proyecto */
    $( "#busca-user" ).autocomplete({
      source: users,
      minLength: 2,
      autoFocus: true,
      select: function( event, ui) {
                $("#user").val(ui.item.id);
            }
    });

    $( "#busca-proj" ).autocomplete({
      source: projs,
      minLength: 2,
      autoFocus: true,
      select: function( event, ui) {
                $("#project").val(ui.item.id);
            }
    });

});
</script>
