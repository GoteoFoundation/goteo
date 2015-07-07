<?php
$filters = $vars['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/manage/projects" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <div style="float:left;margin:5px;">
            <label for="owner-filter">Id del impulsor:</label><br />
            <input type="text" id ="owner-filter" name="owner" value ="<?php echo $filters['owner']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del impulsor:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="proj_name-filter">Nombre del proyecto:</label><br />
            <input id="proj_name-filter" name="proj_name" value="<?php echo $filters['proj_name']; ?>" style="width:250px"/>
        </div>

        <div style="float:left;margin:5px;">
            <label for="node-filter">Del nodo:</label><br />
            <select id="node-filter" name="node">
                <option value="">Cualquier nodo</option>
            <?php foreach ($vars['nodes'] as $nodeId=>$nodeName) : ?>
                <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="projects-filter">Estado de campa&ntilde;a</label><br />
            <select id="projects-filter" name="projectStatus">
                <option value="all"<?php echo ($filters['projectStatus'] == 'all') ? ' selected="selected"' : ''; ?>>En campaña o financiados</option>
            <?php foreach ($vars['projectStatus'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['projectStatus'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div style="float:left;margin:5px;">
            <label for="contract-filter">Estado de proceso de contrato</label><br />
            <select id="contract-filter" name="contractStatus">
                <option value="all"<?php echo ($filters['contractStatus'] == 'all') ? ' selected="selected"' : ''; ?>>En cualquier estado</option>
            <?php foreach ($vars['contractStatus'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['contractStatus'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="order-filter">Ordenar por:</label><br />
            <select id="order-filter" name="order">
            <?php foreach ($vars['orders'] as $orderId=>$orderName) : ?>
                <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
            <a href="/manage/projects?reset=filters">Quitar filtros</a>
        </div>

    </form>
    <br clear="both" />
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario filtrar</p>
<?php elseif (empty($vars['projects'])) : ?>
    <p>No se han encontrado registros</p>
<?php else: ?>
    <p><strong>OJO!</strong> Resultado limitado a 999 registros como máximo.</p>
<?php endif; ?>
</div>


<?php foreach ($vars['projects'] as $project) :

    // calculo fecha de vencimiento (timestamp de un año despues de financiado)
    $deadline = mktime(0, 0, 0,
        date('m', strtotime($project->success)),
        date('d', strtotime($project->success)),
        date('Y', strtotime($project->success)) + 1
    );

?>
<a name="<?php echo $project->id; ?>"></a>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th colspan="2" style="width: 250px;">Proyecto</th> <!-- pagina proyecto -->
                <th style="min-width: 150px;">Impulsor</th> <!-- mailto -->
                <th style="min-width: 80px;">Estado proyecto</th>
                <th>N&ordm; Contrato</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td colspan="2"><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview" style="<?php if (isset($project->called)) echo 'color: blue;'; ?>"><?php echo $project->name; ?></a></td>
                <td><a href="mailto:<?php echo $project->user->email; ?>"><?php echo substr($project->user->email, 0, 100); ?></a></td>
                <td><?php echo $vars['status'][$project->status]; ?></td>
                <td><?php echo $project->cName; ?></td>
            </tr>
            <tr>
                <td colspan="5">OPERACIONES:&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo "/manage/projects/accounts/{$project->id}"; ?>">[Cambiar cuentas]</a>
                    <a href="/contract/<?php echo $project->id; ?>" target="_blank">[Pdf]</a>
                    <?php if (empty($project->contract)) : ?>
                        <a href="/manage/projects/create/<?php echo $project->id; ?>" title="Crea el registro de contrato" onclick="return confirm('Se va a crear el registro de contrato cogiendo el siguiente número, ok?');">[CREAR Registro de Contrato]</a>
                    <?php else : ?>
                        <a href="/contract/edit/<?php echo $project->id; ?>" target="_blank" title="Abre el formulario de contrato">[Formulario Contrato]</a>
                    <?php endif; ?>
                    <a href="/admin/users?id=<?php echo $project->owner; ?>" target="_blank" title="Abre la gestión de usuarios del admin">[Impulsor]</a>
                    <a href="/admin/accounts?projects=<?php echo $project->id; ?>" target="_blank" title="Abre la gestión de aportes del admin">[Aportes]</a>
                    <a href="/manage/projects/report/<?php echo $project->id; ?>" target="_blank">[Informe Financiacion]</a>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <?php if ($project->status == 3) : ?>
                    <p>
                        <strong>Conf. P.R.: </strong> <?php echo $project->days_round1; ?>&nbsp;&nbsp;
                        <?php if ($project->one_round) : ?><strong>Ronda &uacute;nica: </strong>&nbsp;&nbsp;
                        <?php else : ?><strong>Conf. S.R.: </strong> <?php echo $project->days_round2; ?>&nbsp;&nbsp;
                        <?php endif; ?>
                        <strong>Total: </strong> <?php echo $project->days_total; ?>&nbsp;&nbsp;
                        <strong>Lleva: </strong> <?php echo $project->days_active; ?> d&iacute;as.&nbsp;&nbsp;
                        <strong>Está en la ronda: </strong> <?php echo $project->round; ?>&nbsp;&nbsp;
                        Le quedan <strong><?php echo $project->days; ?></strong> d&iacute;as.&nbsp;&nbsp;
                    </p>
                    <?php endif; ?>
                    <p>
                    <strong>Publicado el</strong> <?php echo date('d-m-Y', strtotime($project->published)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Final primera:</strong> <?php echo date('d-m-Y', strtotime($project->passed)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Final segunda:</strong> <?php echo date('d-m-Y', strtotime($project->success)); ?>
                    <strong>Vencimiento contrato:</strong> <?php echo date('d-m-Y', $deadline); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="5">PROCESO:&nbsp;&nbsp;&nbsp;
                <?php
                foreach ($project->flags as $flag=>$fval) {
                    if (empty($flag) || $flag == 'contract' || strpos($flag, '_') > 0) continue;
                    $fname = $vars['contractStatus'][$flag];
                    // los no gestionables
                    if (in_array($flag, array('noreg', 'onform'))) {
                        if ($fval) echo '<strong>'.$fname.'</strong>&nbsp;&nbsp;';
                        continue;
                    }

                    $fdate = $project->flags[$flag.'_date'];
                    $fuser = $project->flags[$flag.'_user'];
                    $title = (!empty($fdate) && !empty($fuser)) ? "Modificado por {$fuser} el {$fdate}" : '';

                    // los fval = 1 se pueden quitar (con un confirm)
                    if ($fval) {?><a href="/manage/projects/unsetflag/<?php echo $project->id ?>/<?php echo $flag ?>" style="color:green;text-decoration:none;" onclick="return confirm('¿Seguro que quieres desmarcar el flag \'<?php echo $fname; ?>\' ?');" <?php if (!empty($title)) echo 'title="'.$title.'"'; ?>>[<?php echo $fname; ?>]</a>&nbsp;&nbsp;
                    <?php } else { // los fval = 0 se pueden marcar ?><a href="/manage/projects/setflag/<?php echo $project->id ?>/<?php echo $flag ?>" style="color:red;text-decoration:none;" onclick="return confirm('Vas a marcar el flag \'<?php echo $fname; ?>\', ok?');" <?php if (!empty($title)) echo 'title="'.$title.'"'; ?>>[<?php echo $fname; ?>]</a>&nbsp;&nbsp;<?php }

                }
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <p>
                        <strong>M&iacute;nimo: </strong><?php echo $project->mincost; ?>&euro;&nbsp;&nbsp;&nbsp;
                        <strong>&Oacute;ptimo: </strong><?php echo $project->maxcost; ?>&euro;&nbsp;&nbsp;&nbsp;
                        <strong>Conseguido: </strong><?php echo $project->amount; ?>&euro;&nbsp;&nbsp;&nbsp
                        <?php if (!empty($project->issues)) : ?><strong>Incidencias: </strong><?php echo $project->issues; ?>&euro; <a href="/admin/accounts?projects=<?php echo $project->id; ?>&issue=show" target="_blank" title="Ver aportes con incidencia">[Ver]</a><?php endif; ?>
                    </p>
                </td>
            </tr>
        </tbody>

    </table>
</div>
<?php endforeach; ?>
