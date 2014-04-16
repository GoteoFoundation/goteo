<?php 
/* Esta vista se usa para gestión de retornos colectivos mediante ajax
 * Desde /admin/commons  y desde /dashboard/projects/commons
 * Necesita usar la vista view/project/edit/rewars/commons.js.php
 */ 
?>
<table>
    <thead>
        <tr>
            <th style="width: 300px;">Retorno</th>
            <th style="width: 110px;">Tipo</th>
            <th style="width: 100px;">Estado</th>
            <th style="width: 145px;">Licencia</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($this['project']->social_rewards as $reward) : ?>
        <tr>
            <td style="color: #20B2B3;"><?php echo $reward->reward; ?></td>
            <td><?php echo $this['icons'][$reward->icon]; ?></td>
            <?php if (!$reward->fulsocial) : ?>
            <td>
                <div id="<?php echo 'rew'.$reward->id; ?>">
                    <span style="color: red; font-weight: bold;">Pendiente</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$this['project']->id}', '{$reward->id}', 1"; ?>)">[ok]</a>
                </div>
            </td>
            <?php else : ?>
            <td>
                <div id="<?php echo 'rew'.$reward->id; ?>">
                    <span style="color: green; font-weight: bold;">Cumplido</span>&nbsp;<a href="#" onclick="return fulsocial(<?php echo "'{$this['project']->id}', '{$reward->id}', 0"; ?>)">[X]</a>
                </div>
            </td>
            <?php endif; ?>
            <td><?php echo $reward->license; ?></td>
        </tr>
         <tr>
            <td colspan="3">
                <?php echo $reward->description; ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div id="<?php echo 'divrew'.$reward->id.'url'; ?>">
                    <a href="<?php echo $reward->url; ?>" target="_blank" class="rewurl"><?php echo $reward->url; ?></a>&nbsp;
                    <a href="#" class="doshow" rel="<?php echo $reward->id; ?>">[Url]</a>
                </div>
                <div id="<?php echo 'divrew'.$reward->id.'urlinput'; ?>" style="display:none;">
                    <input type="text" id="<?php echo 'rew'.$reward->id.'url'; ?>" style="width: 500px;" value=""/>&nbsp;
                    <input type="button" class="doreq" rew="<?php echo $reward->id; ?>" proj="<?php echo $this['project']->id; ?>" value="Aplicar" />
                    <br /><a href="#" class="dohide" rel="<?php echo $reward->id; ?>">(Cancelar)</a></div>
                </div>
            </td>
        </tr>
        <tr><td><br /></td></tr>
        <?php endforeach; ?>
    </tbody>

</table>
