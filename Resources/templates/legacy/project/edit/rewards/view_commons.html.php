<?php
/* Esta vista se usa para gestión de retornos colectivos mediante ajax
 * Desde /admin/commons  y desde /dashboard/projects/commons
 * Necesita usar la vista view/project/edit/rewars/commons.js.php
 */
$licenses = $vars['licenses'];
$project = $vars['project'];
$path = $vars['path'];

// en dashboard, solo editar y eliminar retornos bonus
$chk = (strpos($path, 'dashboard') !== false);
?>
<table>
    <thead>
        <tr>
            <th style="width: 100px;"></th>
            <th style="width: 600px; text-align: left;">Retorno (tipo)</th>
            <th style="width: 100px;">Estado</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($project->social_rewards as $reward) : ?>
        <tr>
            <td>
                <?php if ( !$chk || ( $chk && $reward->bonus ) ) : ?>
                <a href="<?php echo $path; ?>/edit/<?php echo $project->id; ?>?reward_id=<?php echo $reward->id; ?>">[Editar]</a>
                <?php endif; ?>
            </td>
            <td style="color: #20B2B3;"><?php echo $reward->reward; echo ' ('.$vars['icons'][$reward->icon].')'; ?></td>
            <td>
                <?php if (!$reward->fulsocial) : ?>
                    <div id="<?php echo 'rew'.$reward->id; ?>">
                        <span style="color: red; font-weight: bold;">Pendiente</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$vars['project']->id}', '{$reward->id}', 1"; ?>)">[ok]</a>
                    </div>
                <?php else : ?>
                    <div id="<?php echo 'rew'.$reward->id; ?>">
                        <span style="color: green; font-weight: bold;">Cumplido</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$vars['project']->id}', '{$reward->id}', 0"; ?>)">[X]</a>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
         <tr>
             <td><?php if (!$chk && $reward->bonus) : ?><span style="color: green; font-weight: bold;">BONUS!</span><?php endif; ?></td>
            <td>
                <?php echo $reward->description; ?><br />Licencia: <a href="<?php echo $licenses[$reward->license]->url; ?>" title="Leer texto licencia" target="_blank"><?php echo $licenses[$reward->license]->name; ?></a>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div id="<?php echo 'divrew'.$reward->id.'url'; ?>">
                    <a href="<?php echo $reward->url; ?>" target="_blank" class="rewurl"><?php echo $reward->url; ?></a>&nbsp;
                    <a href="#" class="doshow" rel="<?php echo $reward->id; ?>">[Url]</a>
                </div>
                <div id="<?php echo 'divrew'.$reward->id.'urlinput'; ?>" style="display:none;">
                    <input type="text" id="<?php echo 'rew'.$reward->id.'url'; ?>" style="width: 500px;" value="<?php echo $reward->url; ?>"/>&nbsp;
                    <input type="button" class="doreq" rew="<?php echo $reward->id; ?>" proj="<?php echo $vars['project']->id; ?>" value="Aplicar" />
                    <br /><a href="#" class="dohide" rel="<?php echo $reward->id; ?>">(Cancelar)</a></div>
                </div>
            </td>
            <td>
                <?php if ( !$chk || ( $chk && $reward->bonus ) ) : ?>
                <a href="<?php echo $path; ?>/delete/<?php echo $project->id; ?>?reward_id=<?php echo $reward->id; ?>" style="color: red; font-weight: bold;" onclick="return confirm('Seguro que eliminamos este retorno?');">[Eliminar]</a>
                <?php endif; ?>
            </td>
        </tr>
        <tr><td colspan="3"><hr /></td></tr>
        <?php endforeach; ?>
    </tbody>

</table>
