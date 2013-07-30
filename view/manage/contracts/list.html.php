<?php
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $this['filters'];
?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/manage/contracts" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <div style="float:left;margin:5px;">
            <label for="projects-filter">Segun estado del proyecto</label><br />
            <select id="projects-filter" name="project" onchange="document.getElementById('filter-form').submit();">
                <option value="all"<?php echo ($filters['project'] == 'all') ? ' selected="selected"' : ''; ?>>En campaña o financiados</option>
                <option value="goingon"<?php echo ($filters['project'] == 'goingon') ? ' selected="selected"' : ''; ?>>En campa&ntilde;a</option>
                <option value="passed"<?php echo ($filters['project'] == 'passed') ? ' selected="selected"' : ''; ?>>Pasado la primera ronda</option>
                <option value="succed"<?php echo ($filters['project'] == 'succed') ? ' selected="selected"' : ''; ?>>Terminado la segunda ronda</option>
            </select>
        </div>
        <div style="float:left;margin:5px;">
            <label for="contract-filter">Segun estado del contrato</label><br />
            <select id="contract-filter" name="contract" onchange="document.getElementById('filter-form').submit();">
                <option value="all"<?php echo ($filters['contract'] == 'all') ? ' selected="selected"' : ''; ?>>En cualquier estado</option>
                <option value="none"<?php echo ($filters['contract'] == 'none') ? ' selected="selected"' : ''; ?>>Sin rellenar</option>
                <option value="filled"<?php echo ($filters['contract'] == 'filled') ? ' selected="selected"' : ''; ?>>Contrato rellenado</option>
                <option value="sended"<?php echo ($filters['contract'] == 'sended') ? ' selected="selected"' : ''; ?>>Contrato enviado</option>
                <option value="checked"<?php echo ($filters['contract'] == 'checked') ? ' selected="selected"' : ''; ?>>Contrato revisado</option>
                <option value="ready"<?php echo ($filters['contract'] == 'ready') ? ' selected="selected"' : ''; ?>>Documento generado</option>
            </select>
        </div>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/manage/contracts/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if (!empty($this['list'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th styles="min-width:20%;">Proyecto</th>
                <th styles="min-width:20%;">Estado del Proyecto</th>
                <th styles="min-width:20%;">Impulsor</th>
                <th styles="min-width:20%;">Estado del Contrato</th>
                <th>Número</th>
                <th>Documento</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $item) : 
                
                // objeto contrato
                $contract = $this['contracts'][$item->id];
                
                // filtro segun estado de proyecto y/o estadod e contrato
                $filtered = false;
                
                if ($filters['project'] != 'all') {
                    if ( ($filters['project'] == 'goingon' && $item->status != 3) 
                        || ($filters['project'] == 'passed' && empty($item->passed)) 
                        || ($filters['project'] == 'succed' && $item->status != 4) 
                            ) {
                        continue;
                    }
                }
            
                if ($filters['contract'] != 'all') {
                    if ( ($filters['contract'] == 'none' && empty($contract) )
                        || ($filters['contract'] == 'filled' && !empty($contract) ) 
                        || ($filters['contract'] == 'sended' && (empty($contract) || !$contract->status->owner )) 
                        || ($filters['contract'] == 'checked' && (empty($contract) || !$contract->status->admin )) 
                        || ($filters['contract'] == 'ready' && (empty($contract) || empty($contract->status->pdf) )) 
                            ) {
                        continue;
                    }
                }
            
                ?>
            <tr>
                <td><?php if (!empty($contract)) : ?><a href="/manage/contracts/manage/<?php echo $item->id ?>">[Revisar]<?php endif; ?></a></td>
                <td><?php echo Text::recorta($item->name, 20) ?></td>
                <td><?php if ($item->status == 3 && !empty($item->passed)) 
                    echo 'Pasado la primera ronda';
                else
                    echo $this['status'][$item->status] ?></td>
                <td><a href="/admin/users/manage/<?php echo $item->owner ?>"><?php echo Text::recorta($item->user->name, 20) ?></a></td>
                <td><a href="/manage/contracts/preview/<?php echo $item->id ?>"><?php 
                    if (empty($contract)) {
                        echo 'Pendiente';
                    } elseif (!$contract->status->owner) {
                        echo  'No enviado';
                    } elseif (!$contract->status->admin) {
                        echo  'No revisado';
                    } elseif (!$contract->status->pdf) {
                        echo  'Sin documento';
                    }
                ?></a></td>
                <td><?php echo $contract->number ?></td>
                <td><?php if (!empty($contract->status->pdf)) echo $contract->status->pdf ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay ningun proyecto con contrato en curso.</p>
<?php endif;?>
</div>