<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$list = $this->list;
$templates = $this->templates;

?>
<div class="widget board">
    <p>Seleccionar la plantilla. Se utilizará el contenido traducido, quizás quieras <a href="/admin/templates?group=massive" target="_blank">revisarlas</a></p>
    <p><strong>NOTA:</strong> con este sistema no se pueden añadir variables en el contenido, se genera el mismo contenido para todos los destinatarios.<br/>
    Para contenido personalizado hay que usar la funcionalidad <a href="/admin/mailing" >Comunicaciones</a>.</p>

    <form action="/admin/newsletter/init" method="post" onsubmit="return confirm('El envio NO se activará automáticamente. Puedes revisar el contenido y destinatarios y enviarlo después');">

    <p>
        <label>Plantillas masivas:
            <select id="template" name="template" >
            <?php foreach ($templates as $tplId=>$tplName) : ?>
                <option value="<?= $tplId ?>" <?php if ( $tplId == $tpl) echo 'selected="selected"' ?>><?= $tplName ?></option>
            <?php endforeach ?>
            </select>
            <a href="/admin/templates/edit/" onclick="window.location=$(this).attr('href')+$('#template').val();return false;"><?= $this->text('regular-edit') ?></a>
            <a href="/translate/template/" onclick="window.location=$(this).attr('href')+$('#template').val();return false;"><?= $this->text('regular-translate') ?></a>
        </label>
    </p>
    <p>
        <label><input type="checkbox" name="test" value="1" checked="checked"/> Es una prueba (se envia a los destinatarios de pruebas)</label>
    </p>

    <p>
        <label><input type="checkbox" name="nolang" value="1" checked="checked"/>Solo en español (no tener en cuenta idioma preferido de usuario)</label>
    </p>

    <p>
        <input type="submit" name="init" value="Iniciar" />
    </p>

    </form>
</div>

<div id="admin-newsletter-list">
<?php if ($list) : ?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Fecha</th>
                <th>Asunto</th>
                <th>Estado</th>
                <th>% envío</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item) :
            $percent = round($item->getStatusObject()->percent);
            $sent = $percent == 100;
            ?>
            <tr<?= ($sent ? ' style="opacity:0.5"': ' style="font-weight:bold"' ) ?>>
                <td><a href="/admin/newsletter/detail/<?= $item->id ?>">[Detalles]</a></td>
                <td><?= $item->date ?></td>
                <td<?= $item->blocked ? ' style="color:red"' : '' ?>><?= $item->subject ?></td>
                <td><span class="label label-<?= $item->getStatus() ?>"><?= $item->getStatus() ?></span></td>
                <td><?= $this->percent_span($percent) ?></td>
                <td><a href="<?= $item->getLink() ?>" target="_blank">[Prever]</a></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<?php endif ?>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        var reloadPage = function() {
            $('#admin-newsletter-list').load('/admin/newsletter?pag=<?= $this->get_query('pag') ?> #admin-newsletter-list');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
// @license-end
</script>
<?php $this->append() ?>
