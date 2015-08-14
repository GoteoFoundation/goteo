<?php

use Goteo\Core\View;

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php


$filters = $this->filters;
$templates = $this->templates;


?>
<div class="widget board">
    <form id="filter-form" action="/admin/sent" method="get">
        <div style="float:left;margin:5px;">
            <label for="user-filter">ID, nombre o email del destinatario</label><br />
            <input id="user-filter" name="user" value="<?php echo $filters['user']; ?>" style="width:300px;"/>
        </div>

        <div style="float:left;margin:5px;">
            <label for="template-filter">Plantilla</label><br />
            <select id="template-filter" name="template" onchange="document.getElementById('filter-form').submit();" >
                <option value="">Todas las plantillas</option>
                <?php foreach ($templates as $templateId => $templateName) : ?>
                    <option value="<?php echo $templateId; ?>"<?php if ($filters['template'] == $templateId)
                    echo ' selected="selected"'; ?>><?php echo $templateName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br clear="both" />

<?php if ($this->nodes) : ?>
            <div style="float:left;margin:5px;">
                <label for="node-filter">Enviado por el nodo:</label><br />
                <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                    <option value="">Cualquier nodo</option>
                    <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
                        <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId)
                    echo ' selected="selected"'; ?>><?php echo $nodeName; ?></option>
            <?php endforeach; ?>
                </select>
            </div>
<?php endif; ?>

        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
<?php echo View::get('superform/element/datebox.html.php', array('value' => $filters['date_from'], 'id' => 'date-filter-from', 'name' => 'date_from', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
<?php echo View::get('superform/element/datebox.html.php', array('value' => $filters['date_until'], 'id' => 'date-filter-until', 'name' => 'date_until', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" name="filter" value="Filtrar">
        </div>

    </form>
</div>

<div id="admin-sent-list">
    <?php if ($this->sent_list) : ?>
        <div class="widget board">
            <table>
                <thead>
                    <tr>
                        <th>Éxito</th>
                        <th>&nbsp;</th>
                        <th>Destinatario</th>
                        <th>Plantilla</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th><!-- Si no ves --></th>
                        <th><!-- status--></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($this->sent_list as $sent):
                        ?>
                        <tr>
                            <td><?= sprintf('%02d',round($sent->getStats()->getEmailOpenedCollector()->getPercent())) ?>%</td>
                            <td><a href="/admin/sent/detail/<?= $sent->id ?>">[Detalles]</a></td>
                            <td><a href="/admin/users?name=<?php echo urlencode($sent->email) ?>"><?php echo $sent->email; ?></a></td>
                            <td><?= $templates[$sent->template] ?></td>
                            <td><?= $sent->getSubject() ?></td>
                            <td><?= $sent->date ?></td>
                            <td><a href="/mail/<?= $sent->getToken(false) ?>" target="_blank">[Visualizar]</a></td>
                            <td><?= '<span class="label label-'. $sent->status . '">' . $sent->status . '</span>' ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else : ?>
        <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        var reloadPage = function() {
            $('#admin-sent-list').load('/admin/sent #admin-sent-list');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
</script>
<?php $this->append() ?>
