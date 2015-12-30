<?php

$amount = (isset($_GET['amount'])) ? $_GET['amount'] : null;
$user   = (isset($_GET['user'])) ? $_GET['user'] : null;
$proj   = (isset($_GET['proj'])) ? $_GET['proj'] : null;



$projs = array();
foreach ($this->projects as $pI=>$pN) {
    $projs[] = '{ value: "'.str_replace(array("'", '"'), '`', $pN).'", id: "'.$pI.'" }';
    if ($pI == $proj) $preP = "$pN [$pI]";
}



$this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget">

        <div class="ui-widget">
            <label for="change_user_input">Buscador usuario:</label><br />
            <?= $this->insert('admin/partials/typeahead_form', [
                                                                'id' => 'change_user_input',
                                                                // 'hidden' => true
                                                                ]) ?>
        </div>

        <br />

    <form id="filter-form" action="/admin/accounts/add" method="post">
        <div class="ui-widget">
            <label for="busca-proj">Buscador proyecto: (solo proyectos en campaña)</label><br />
            <input type="text" id="busca-proj" name="proj_searcher" value="<?= $preP ?>" style="width:500px;"/>
        </div>

        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="<?= $amount ?>" />
        </p>

        <p>
            <label for="invest-campaign">Convocatoria:</label><br />
            <select id="invest-campaign" name="campaign">
                <option value="">Seleccionar la convocatoria que riega este aporte</option>
            <?php foreach ($this->calls as $campaignId=>$campaignName) : ?>
                <option value="<?= $campaignId ?>"><?= substr($campaignName, 0, 100) ?></option>
            <?php endforeach ?>
            </select>
        </p>

        <p>
            <label for="invest-anonymous">Aporte anónimo:</label><br />
            <input id="invest-anonymous" type="checkbox" name="anonymous" value="1">
        </p>


        <input type="hidden" id="user" name="user" value="<?= $user ?>" /> <br />
        <input type="hidden" id="project" name="project" value="<?= $proj ?>" />
        <input type="submit" name="add" value="Generar aporte" />

    </form>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
$(function(){
    $.typeahead({
        input: "#change_user_input",
        // order: "asc",
        dynamic:true,
        hint:true,
        searchOnFocus:true,
        accent:true,
        emptyTemplate: 'No result for "{{query}}"',
        display: ["id", "name", "email"],
        template: '<span>' +
            '<span class="avatar">' +
                '<img src="/img/tinyc/{{avatar}}">' +
            '</span> ' +
            '<span class="name">{{name}}</span> ' +
            '<span class="info">({{id}}, {{email}})</span>' +
        '</span>',
        source: {
            list: {
                url: [{
                        url: "/api/users",
                        data: {
                            q: "{{query}}",
                        }
                    }, 'list']
            }
        },
        callback: {
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                // node.val(item.id);
                $("#user").val(item.id);
            }
        },
        debug: true
    });

    var projs = [<?= implode(', ', $projs) ?>];

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

<?php $this->append() ?>
