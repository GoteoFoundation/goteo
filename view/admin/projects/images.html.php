<?php

$project = $this['project'];
$images = $this['images'];
$sections = $this['sections'];

function the_section($current, $image, $sections) {
    $select = '<select name="section_image_'.$image.'">';
    foreach ($sections as $secId => $secName) {
        $curSec = ($secId == $current) ? ' selected="selected"' : '';
        $select .= '<option value="'.$secId.'"'.$curSec.'>'.$secName.'</option>';
    }
    $select .= '</select>';
    
    return $select;
}

function the_link($current, $image) {
    return '<input type="text" name="url_image_'.$image.'"  value="'.$current.'" style="width: 100%;"/>';
}

?>
<script type="text/javascript">
function move (img, direction, section) {
    document.getElementById('the_action').value = direction;
    document.getElementById('the_section').value = section;
    document.getElementById('move_pos').value = img;
    document.getElementById('images_form').submit();
}
</script>

<a href="/admin/projects" class="button">Volver</a>
&nbsp;&nbsp;&nbsp;
<a href="/project/<?php echo $project->id; ?>" class="button" target="_blank">Ver proyecto</a>
&nbsp;&nbsp;&nbsp;
<a href="/project/edit/<?php echo $project->id; ?>" class="button" target="_blank">Editar proyecto</a>
<div class="widget board">
    <?php if (!empty($images)) : ?>
    <form id="images_form" action="/admin/projects/images/<?php echo $project->id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $project->id; ?>" />
        <input type="hidden" id="the_action" name="action" value="apply" />
        <input type="hidden" id="the_section" name="section" value="" />
        <input type="hidden" id="move_pos" name="move" value="" />
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th style="width:30px;"></th>
                <th colspan="2"></th> <!-- posicion -->
            </tr>
        </thead>

        <tbody>
        <?php foreach ($sections as $sec=>$secName) : 
            if (empty($images[$sec])) continue; 
            ?>
            <tr>
                <td colspan="5" style="text-align: left;"><h3><?php echo $secName; ?></h3></td>
            </tr>
            <?php foreach ($images[$sec] as $image) : ?>
            <tr>
                <td style="width:105px;text-align: left;"><img src="<?php echo $image->imageData->getLink(175, 100); ?>" alt="image" /></td>
                <td>
                    <table>
                        <tr>
                            <td><label>Sección:<br /><?php echo the_section($image->section, $image->image, $sections); ?></label></td>
                        </tr>
                        <tr>
                            <td><label>Enlace:<br /><?php echo the_link($image->url, $image->image); ?></label></td>
                        </tr>
                    </table>
                </td>
                <td>&nbsp;</td>
                <td><a href="#" onclick="move('<?php echo $image->image; ?>', 'up', '<?php echo $image->section; ?>'); return false;">[&uarr;]</a></td>
                <td><a href="#" onclick="move('<?php echo $image->image; ?>', 'down', '<?php echo $image->section; ?>'); return false;">[&darr;]</a></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>

    </table>
        <input type="submit" name="apply_changes" value="Aplicar" />
    </form>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
<form id="sec_form" action="/admin/projects/image_section/<?php echo $project->id; ?>" method="post">
    <input type="hidden" id="setimg" name="image" value="">
    <input type="hidden" id="setsec" name="section" value="">
</form>