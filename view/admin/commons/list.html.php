<?php
use Goteo\Library\Text;

$filters = $this['filters'];
$status = $this['statuses'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/commons" method="get">
        <div style="float:left;margin:5px;">
            <label for="projStatus-filter">Solo proyectos en estado:</label><br />
            <select id="projStatus-filter" name="projStatus">
                <option value="">Cualquier exitoso</option>
            <?php foreach ($this['projStatus'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['projStatus'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select><br />
            <span style="font-size: 10px;">Afecta al filtro Proyecto</span>
        </div>

        <div style="float:left;margin:5px;">
            <label for="projects-filter">Proyecto:</label><br />
            <select id="projects-filter" name="project" >
                <option value="">Todos los proyectos</option>
            <?php foreach ($this['projects'] as $project) : ?>
                <option value="<?php echo $project->id; ?>"<?php if ($filters['project'] === $project->id) echo ' selected="selected"';?> status="<?php echo $project->status; ?>"><?php echo $project->name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="status-filter">Mostrar por estado del retorno:</label><br />
            <select id="status-filter" name="status" >
                <option value="">Cualquier estado</option>
            <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="icon-filter">Mostrar retornos del tipo:</label><br />
            <select id="icon-filter" name="icon" >
                <option value="">Todos los tipos</option>
            <?php foreach ($this['icons'] as $iconId=>$iconName) : ?>
                <option value="<?php echo $iconId; ?>"<?php if ($filters['icon'] == $iconId) echo ' selected="selected"';?>><?php echo $iconName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/commons/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['projects'])) : ?>
    <?php foreach ($this['projects'] as $project) : ?>

        <?php if (!empty($filters['project']) && $project->id != $filters['project']) {
                continue;
            }
        ?>

        <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>
        <?php 
        if (empty($project->social_rewards)) {
            echo '<p>Este proyecto no tiene retornos colectivos</p><hr />';
            continue; 
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th style="width: 300px;">Retorno</th>
                    <th style="width: 110px;">Tipo</th>
                    <th style="width: 100px;">Estado</th>
                    <th style="width: 145px;"></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($project->social_rewards as $reward) : ?>
                <tr>
                    <td><?php echo $reward->reward; ?></td>
                    <td><?php echo $this['icons'][$reward->icon]; ?></td>
                    <?php if (!$reward->fulsocial) : ?>
                    <td>
                        <div id="<?php echo 'rew'.$reward->id; ?>">
                            <span style="color: red; font-weight: bold;">Pendiente</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$project->id}', '{$reward->id}', 1"; ?>)">[ok]</a>
                        </div>
                    </td>
                    <?php else : ?>
                    <td>
                        <div id="<?php echo 'rew'.$reward->id; ?>">
                            <span style="color: green; font-weight: bold;">Cumplido</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$project->id}', '{$reward->id}', 0"; ?>)">[X]</a>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td colspan="4">
                        <div id="<?php echo 'divrew'.$reward->id.'url'; ?>">
                            <a href="<?php echo $reward->url; ?>" target="_blank" class="rewurl"><?php echo $reward->url; ?></a>&nbsp;
                            <a href="#" class="doshow" rel="<?php echo $reward->id; ?>">[Url]</a>
                        </div>
                        <div id="<?php echo 'divrew'.$reward->id.'urlinput'; ?>" style="display:none;">
                            <input type="text" id="<?php echo 'rew'.$reward->id.'url'; ?>" style="width: 500px;" value=""/>&nbsp;
                            <input type="button" class="doreq" rew="<?php echo $reward->id; ?>" proj="<?php echo $project->id; ?>" value="Aplicar" />
                            <br /><a href="#" class="dohide" rel="<?php echo $reward->id; ?>">(Cancelar)</a></div>
                        </div>
                    </td>
                </tr>
                <tr><td><br /></td></tr>
                <?php endforeach; ?>
            </tbody>

        </table>

        <hr />

        <?php endforeach; ?>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
<script type="text/javascript">
    function fulsocial (proj, rew, val) {
        success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '<?php echo SITE_URL; ?>/c7feb7803386d713e60894036feeee9e/ce8c56139d45ec05e0aa2261c0a48af9'}).responseText;

        if (success_text != 'OK') {
            alert('No se ha modificado, error en webservice: ' + success_text);
        } else {
            if (success_text == 'OK' && val == 1) {
                $("#rew"+rew).html('<span style="color: green; font-weight: bold;">Cumplido</span>&nbsp;<a href="#" onclick="return fulsocial(\''+proj+'\', \''+rew+'\', 0)">[X]</a>');
            } 
            if (success_text == 'OK' && val == 0) {
                $("#rew"+rew).html('<span style="color: red; font-weight: bold;">Pendiente</span>&nbsp;<a href="#" onclick="return fulsocial(\''+proj+'\', \''+rew+'\', 1)">[ok]</a>');
            }
        }
        
        return false;
    }
    
    jQuery(document).ready(function ($) {
        
        // al filtrar por estado de proyecto
        $("#projStatus-filter").change(function(){
            
            $("#filter-form").submit();
        });
        
        // al clickar, oculta el div padre y muestra el div que se llama igual que el div apdre seguido de 'input'
        $(".doshow").click(function(event){
            var rew = $(this).attr('rel');
            $("#divrew"+rew+"url").hide();
            $("#divrew"+rew+"urlinput").show();
            $("#rew"+rew+"url").focus();
            
            event.preventDefault();
        });
        
        $(".dohide").click(function(event){
            var rew = $(this).attr('rel');
            $("#divrew"+rew+"urlinput").hide();
            $("#divrew"+rew+"url").show();
            
            event.preventDefault();
        });
        
        // al clickar
        $(".doreq").click(function(event){
            var proj = $(this).attr('proj');
            var rew = $(this).attr('rew');
            var val = $('#rew'+rew+'url').val();
            success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '<?php echo SITE_URL; ?>/c7feb7803386d713e60894036feeee9e/d82318a7bec39ac2b78be96b8ec2b76e/'}).responseText;
            
            if (success_text != 'OK') {
                alert('No se ha modificado, error en webservice: ' + success_text);
            } else {
                if (success_text == 'OK') {
                    $("#divrew"+rew+"url a.rewurl").attr('href', val);
                    $("#divrew"+rew+"url a.rewurl").html(val);
                    $("#divrew"+rew+"urlinput").hide();
                    $("#divrew"+rew+"url").show();
                } 
            }
            
            event.preventDefault();
        });
    });
    
    
    
</script>