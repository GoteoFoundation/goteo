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
            <label for="reply-filter">Nombre o email del remitente</label><br />
            <input id="reply-filter" name="reply" value="<?php echo $filters['reply']; ?>" style="width:300px;"/>
        </div>
        <div style="float:left;margin:5px;">
            <label for="user-filter">Email del destinatario</label><br />
            <input id="user-filter" name="user" value="<?php echo $filters['user']; ?>" style="width:300px;"/>
        </div>

        <div style="float:left;margin:5px;">
            <label for="subject-filter">Asunto</label><br />
            <input id="subject-filter" name="subject" value="<?php echo $filters['subject']; ?>" style="width:300px;"/>
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
            <?= $this->html('input', ['value' => $filters['date_from'], 'name' => 'date_from', 'attribs' => ['id'=>'date-filter-from', 'class' => 'datepicker']]) ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
            <?= $this->html('input', ['value' => $filters['date_until'], 'name' => 'date_until', 'attribs' => ['id'=>'date-filter-until', 'class' => 'datepicker']]) ?>
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
                        <th>Remitente</th>
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
                    foreach($this->sent_list as $mail):
                        $percent = round($mail->getStats()->getEmailOpenedCollector()->getPercent());
                        $success = 100;
                        if($sender = $mail->getSender()) {
                            $success = floor($sender->getStatusObject()->percent_success);
                        }
                        ?>
                        <tr>
                            <td><?= $this->percent_span($percent) ?></span></td>
                            <td><a href="/admin/sent/detail/<?= $mail->id ?>">[Detalles]</a></td>
                            <td><a href="/admin/users?email=<?php echo urlencode($mail->getReply()) ?>" title="<?= $this->ee($mail->getReplyName()) ?>"><?php echo $mail->getReply(); ?></a></td>
                            <td><a href="/admin/users?name=<?php echo urlencode($mail->email) ?>"><?php echo $mail->email; ?></a></td>
                            <td><?= $templates[$mail->template] ?></td>
                            <td><?= $mail->getSubject() ?></td>
                            <td><?= $mail->date ?></td>
                            <td><a href="/mail/<?= $mail->getToken(false) ?>" target="_blank">[Visualizar]</a></td>
                            <td><?= $this->percent_span($success, 0, $mail->getStatus(). " $success%") ?></td>
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
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        var reloadPage = function() {
            $('#admin-sent-list').load('/admin/sent?pag=<?= $this->get_query('pag') ?> #admin-sent-list');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
// @license-end
</script>
<?php $this->append() ?>
