<?php

$filters = $this->filters;

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/translates" class="button">Asignar traductores</a>

<div class="widget board">
    <form id="filter-form" action="/admin/projects" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <table>
            <tr>
                <td>
                    <label for="name-filter">Alias/Email del autor:</label><br />
                    <input type="text" id ="name-filter" name="name" value ="<?= $filters['name']?>" />
                </td>
                <td>
                    <label for="category-filter">De la categoría:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($this->categories as $categoryId => $categoryName) : ?>
                        <option value="<?= $categoryId ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?= $categoryName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <?php if ($this->nodes) : ?>
                <td>
                    <label for="node-filter">Del nodo:</label><br />
                    <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier nodo</option>
                    <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
                        <option value="<?= $nodeId ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?= $nodeName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <?php endif ?>
            </tr>
            <tr>
                <td>
                    <label for="proj_name-filter">Nombre del proyecto:</label><br />
                    <input id="proj_name-filter" name="proj_name" value="<?= $filters['proj_name'] ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="-1"<?php if ($filters['status'] == -1) echo ' selected="selected"';?>>Todos excepto sin ID</option>
                        <option value="-3"<?php if ($filters['status'] == -3) echo ' selected="selected"';?>>Todos los estados</option>
                        <option value="-2"<?php if ($filters['status'] == -2) echo ' selected="selected"';?>>En negociacion</option>
                    <?php foreach ($this->status as $statusId => $statusName) : ?>
                        <option value="<?= $statusId ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?= $statusName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <?php if ($this->calls) : ?>
                <td>
                    <label for="call-filter">En la convocatoria:</label><br />
                    <select id="call-filter" name="called" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                        <option value="all"<?php if ($filters['called'] == 'all') echo ' selected="selected"';?>>En alguna</option>
                        <option value="none"<?php if ($filters['called'] == 'none') echo ' selected="selected"';?>>En ninguna</option>
                    <?php foreach ($this->calls as $callId => $callName) : ?>
                        <option value="<?= $callId ?>"<?php if ($filters['called'] == $callId) echo ' selected="selected"';?>><?= $callName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            <?php endif ?>
            </tr>
            <tr>
                <td>
                    <label for="proj_id-filter">Id del proyecto:</label><br />
                    <input id="proj_id-filter" name="proj_id" value="<?= $filters['proj_id'] ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="order-filter">Ordenar por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this->orders as $orderId => $orderName) : ?>
                        <option value="<?= $orderId ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?= $orderName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="consultant-filter">Asesorado por:</label><br />
                    <select id="consultant-filter" name="consultant" onchange="document.getElementById('filter-form').submit();">
                        <option value="-1"<?php if ($filters['consultant'] == -1) echo ' selected="selected"';?>>Cualquier admin</option>
                    <?php foreach ($this->admins as $userId => $userName) : ?>
                        <option value="<?= $userId ?>"<?php if ($filters['consultant'] == $userId) echo ' selected="selected"';?>><?= $userName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="Buscar">
                </td>
            </tr>
        </table>
    </form>
    <br clear="both" />
    <a href="/admin/projects?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>


<?php if ($this->projects): ?>
    <?php foreach ($this->projects as $project): ?>
    <hr>

    <table>
        <thead>
            <tr>
                <th style="width: 250px;">Proyecto</th> <!-- edit -->
                <th style="min-width: 150px;">Creador</th> <!-- mailto -->
                <th style="min-width: 75px;">Recibido</th> <!-- enviado a revision -->
                <th style="min-width: 80px;">Estado</th>
                <th style="min-width: 50px;">Nodo</th>
                <th style="min-width: 50px;">M&iacute;nimo</th>
                <th style="min-width: 50px;">&Oacute;ptimo</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><a href="/project/<?= $project->id ?>" target="_blank" title="Preview" style="<?php if ($project->called) echo 'color: blue;' ?>"><?php echo (!empty($project->name)) ? $project->name : 'SIN NOMBRE' ?></a></td>
                <td><a href="mailto:<?= $project->user->email ?>"><?php echo substr($project->user->email, 0, 100) ?></a></td>
                <td><?php echo date('d-m-Y', strtotime($project->updated)) ?></td>
                <td><?php echo ($project->status == 1 && !$project->draft) ? '<span style="color: green;">En negociación</span>' : $this->status[$project->status] ?></td>
                <td style="text-align: center;"><?= $project->node ?></td>
                <td style="text-align: right;"><?php echo \euro_format($project->mincost).'€' ?></td>
                <td style="text-align: right;"><?php echo \euro_format($project->maxcost).'€' ?></td>
            </tr>
            <tr>
                <td colspan="7"><?php
                    $add = array();
                    if ($project->status < 3) {
                        $add[] = "Información al <strong>{$project->progress}%</strong>";
                    } elseif ($project->status == 3) {
                        $add[] = "Lleva {$project->days_active} días de campaña";
                        $add[] = "Le quedan {$project->days} días de la {$project->round}ª ronda";
                        $add[] = "<strong>Conseguido:</strong> ".\euro_format($project->amount)."€";
                        $add[] = "<strong>Cofin:</strong> {$project->num_investors}";
                        $add[] = "<strong>Colab:</strong> {$project->num_messengers}";
                    }

                    if ($project->publishing_estimation) {
                        $date=new \Datetime($project->publishing_estimation);
                        $date_publishing=$date->format("d-m-Y");
                        $add[] = "Fecha publicación estimada: <strong>{$date_publishing}</strong>";
                    }


                    if ($project->getConsultants()) {
                        $consultants = array();
                        foreach($project->getConsultants() as $id => $name) {
                            if($this->is_module_admin('users', $project->node)) {
                                $consultants[] = '<a href="/admin/users/manage/'.$id.'">' . $name . '</a>';
                            }
                            else {
                                $consultants[] = $name;
                            }
                        }
                        if($consultants) $add[] = "Asesorado por: " . implode(', ', $consultants);
                    }

                    if ($project->project_location) {
                        $add[] = "<strong>{$project->project_location}</strong>";
                    }

                    echo implode(' | ', $add);
                ?></td>
            </tr>
            <?php if($project->userCanView($this->user)): ?>
              <tr>
                <td colspan="7">
                    IR A:&nbsp;
                    <a href="/project/<?= $project->id ?>" target="_blank">[Página publica]</a>
                    <?php if($project->userCanEdit($this->user)): ?>
                        <a href="/project/edit/<?= $project->id ?>" target="_blank">[Editar]</a>
                        <a href="/admin/projects/report/<?= $project->id ?>" target="_blank">[Informe Financiacion]</a>
                    <?php endif ?>
                    <?php if($this->is_module_admin('users', $project->node)): ?>
                        <a href="/admin/users?id=<?= $project->owner ?>" target="_blank">[Impulsor]</a>
                        <a href="/admin/users?project=<?= $project->id ?>" title="Ver sus cofinanciadores">[Cofinanciadores]</a>
                    <?php endif ?>
                    <?php if($this->is_module_admin('Invests', $project->node)): ?>
                        <a href="/admin/invests?projects=<?= $project->id ?>" title="Ver sus aportes">[Aportes]</a>
                    <?php elseif($this->is_module_admin('Accounts', $project->node)): ?>
                    <?php elseif($this->is_module_admin('Accounts', $project->node)): ?>
                        <a href="/admin/accounts?projects=<?= $project->id ?>" title="Ver sus aportes">[Aportes]</a>
                    <?php endif ?>
                </td>
              </tr>
            <?php endif ?>
            <?php if($project->userCanEdit($this->user)): ?>
              <tr>
                <td colspan="7">
                    CAMBIAR:&nbsp;
                    <a href="<?php echo "/admin/projects/dates/{$project->id}" ?>" title="Para modificar a mano las fechas clave">[Fechas]</a>
                    <a href="<?php echo "/admin/projects/location/{$project->id}" ?>" title="Modificar la localización del proyecto">[Localización]</a>
                    <?php if($project->userCanModerate($this->user)): ?>
                        <a href="<?php echo "/admin/projects/accounts/{$project->id}" ?>" title="Para cambiar las cuentas bancarias">[Cuentas]</a>
                        <?php if ($project->status < 4) : ?><a href="<?php echo "/admin/projects/rebase/{$project->id}" ?>" title="Para cambiar la Url del proyecto" onclick="return confirm('Esto es MUY DELICADO, seguimos?');">[Id]</a><?php endif ?>
                    <?php endif ?>
                    <?php if($project->userCanAdmin($this->user)): ?>
                        <a href="<?php echo "/admin/projects/move/{$project->id}" ?>" title="Para pasar el proyecto a otro nodo">[Nodo]</a>
                        <a href="<?php echo "/admin/projects/open_tags/{$project->id}" ?>" title="Para asignar Opentags">[Agrupación]</a>
                        <a href="<?php echo "/admin/projects/consultants/{$project->id}" ?>" title="Para gestionar los asesosres">[Asesor]</a>
                    <?php endif ?>
                    <a href="<?php echo "/admin/projects/conf/{$project->id}" ?>" title="Para cambiar los días de primera y segunda ronda">[Configuración de campaña]</a>
                </td>
              </tr>
            <?php endif ?>
            <?php if($project->userCanModerate($this->user)): ?>
              <tr>
                <td colspan="7">
                    ACCIONES:&nbsp;
                    <?php if ($project->status == 0) : ?><a href="<?php echo "/admin/projects/enable/{$project->id}" ?>" title="Pasa el proyecto al estado Editándose">[Reabrir edición]</a><?php endif ?>
                    <?php if ($project->inEdition()): ?><a href="<?php echo "/admin/projects/review/{$project->id}" ?>" title="<?= $this->text('admin-project-to-review-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-to-review-sure'), 'js') ?>');">[<?= $this->text('admin-project-to-review') ?>]</a><?php endif ?>
                    <?php if ($project->status < 3 && $project->status > 0) : ?><a href="<?php echo "/admin/projects/publish/{$project->id}" ?>" title="<?= $this->text('admin-project-to-review-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-to-publish-sure'), 'js') ?>');">[<?= $this->text('admin-project-to-publish') ?>]</a><?php endif ?>
                    <a href="<?php echo "/admin/projects/enable/{$project->id}" ?>" title="<?= $this->text('admin-project-to-negotiation-desc') ?>"<?php if ($project->inEdition()) : ?> onclick="return confirm(<?= $this->ee($this->text('admin-project-to-negotiation-sure'), 'js') ?>);"<?php endif ?>>[<?= $this->text('admin-project-to-negotiation') ?>]</a>
                    <?php if ($project->status == 4) : ?><a href="<?php echo "/admin/projects/fulfill/{$project->id}" ?>" title="Pasa el proyecto al estado Retorno cumplido" onclick="return confirm('El proyecto pasara a ser un caso de éxito, ok?');">[Retorno Cumplido]</a><?php endif ?>
                    <?php if ($project->status == 5) : ?><a href="<?php echo "/admin/projects/unfulfill/{$project->id}" ?>" title="Pasa el proyecto al estado Financiado" onclick="return confirm('Lo echamos un paso atras, ok?');">[Retorno Pendiente]</a><?php endif ?>
                    <?php if ($project->status < 3 && $project->status > 0) : ?><a href="<?php echo "/admin/projects/cancel/{$project->id}" ?>" title="Pasa el proyecto al estado Descartado" onclick="return confirm('El proyecto solo aparecerá si se filtra expresamente Estado Descartado, seguimos?');">[<?= $this->text('regular-discard') ?>]</a><?php endif ?>
                    <?php if ($project->status == 3 && ! $project->noinvest) : ?><a href="<?php echo "/admin/projects/noinvest/{$project->id}" ?>" title="No permitir más aportes" onclick="return confirm('No se podrá aportar más pero sigue técnicamente en campaña hasta final de ronda, ok?');">[Cerrar el grifo]</a><?php endif ?>
                    <?php if ($project->status == 3 && $project->noinvest) : ?><a href="<?php echo "/admin/projects/openinvest/{$project->id}" ?>" title="Volver a permitir aportes" onclick="return confirm('Seguro que quieres abrirle el grifo de nuevo a este proyecto?');">[Abrir el grifo]</a><?php endif ?>
                    <?php if ($project->status == 3) : ?><a href="<?php echo "/admin/projects/finish/{$project->id}" ?>" title="Fuerza el final de ronda" onclick="return confirm('La campaña finalizará esta noche. Esta acción no se puede deshacer! Seguimos?');">[Finalizar campaña]</a><?php endif ?>
                    <?php if ($project->watch) { ?>
                        <a href="<?php echo "/admin/projects/unwatch/{$project->id}" ?>" title="Los asesores dejarán de recibir copia de las notificaciones automáticas al impulsor">[Dejar de vigilar]</a>
                    <?php } else { ?>
                        <a href="<?php echo "/admin/projects/watch/{$project->id}" ?>" title="Los asesores recibirán copia de las notificaciones automáticas al impulsor">[Vigilar]</a>
                    <?php } ?>
                    <?php if (!$project->isApproved()) : ?><a href="<?php echo "/admin/projects/reject/{$project->id}" ?>" title="<?= $this->text('admin-project-express-discard-desc') ?>" onclick="return confirm('<?= $this->ee($this->text('admin-project-express-discard-sure'),'js') ?>');">[<?= $this->text('admin-project-express-discard') ?>]</a><?php endif ?>
                </td>
              </tr>
            <?php endif ?>
            <?php if($project->userCanEdit($this->user)): ?>
              <tr>
                <td colspan="5">
                    GESTIONAR:&nbsp;
                    <?php if($this->is_module_admin('Reviews', $project->node)): ?>
                        <?php if ($project->status == 1) : ?><a href="<?php echo "/admin/reviews/add/{$project->id}" ?>" onclick="return confirm('Se va a iniciar revisión de un proyecto en estado Edición, ok?');">[Iniciar revisión]</a><?php endif ?>
                        <?php if ($project->status == 2) : ?><a href="<?php echo "/admin/reviews?project=".urlencode($project->id) ?>">[Ir a la revisión]</a><?php endif ?>
                    <?php endif ?>
                    <?php if($this->is_module_admin('Translates', $project->node)): ?>
                        <?php if ($project->translate) : ?><a href="<?php echo "/admin/translates/edit/{$project->id}" ?>">[Ir a la traducción]</a>
                        <?php else : ?><a href="<?php echo "/admin/translates/add?project={$project->id}" ?>">[Habilitar traducción]</a><?php endif ?>
                    <?php endif ?>
                    <a href="/admin/projects/images/<?= $project->id ?>">[Organizar imágenes]</a>
                    <?php if (in_array($project->status, array('1', '2', '3')) && !$project->called) : ?><a href="<?php echo "/admin/projects/assign/{$project->id}" ?>">[Asignarlo a una convocatoria]</a><?php endif ?>
                    <?php if ($project->status == 4 || $project->status == 5) : ?><a href="/admin/commons?project=<?= $project->id ?>">[Retornos colectivos]</a><?php endif ?>
                    <?php
                    if($project->userCanAdmin($this->user)): ?>
                        <?php if (isset($this->contracts[$project->id])) : ?><a href="<?php echo "/contract/{$project->id}" ?>" target="_blank">[Contrato]</a><?php endif ?>
                    <?php endif ?>
                </td>
              </tr>
            <?php endif ?>
        </tbody>

    </table>

    <?php endforeach ?>

<?php else: ?>

    <p>No se han encontrado registros!</p>

<?php endif ?>

</div>

<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php $this->replace() ?>
