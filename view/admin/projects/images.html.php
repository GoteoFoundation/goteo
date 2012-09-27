<?php

$project = $this['project'];
$images = $this['images'];
$sections = $this['sections'];

function the_section($current, $image, $sections) {
    $select = '<select onchange="ubica(this.value, '.$image.');">';
    foreach ($sections as $secId => $secName) {
        $curSec = ($secId == $current) ? ' selected="selected"' : '';
        $select .= '<option value="'.$secId.'"'.$curSec.'>'.$secName.'</option>';
    }
    $select .= '</select>';
    
    return $select;
}

?>
<script type="text/javascript">
function ubica (sec, img) {
    document.getElementById('setimg').value = img;
    document.getElementById('setsec').value = sec;
    document.getElementById('sec_form').submit();
    
}
</script>
<div class="widget board">
    <?php if (!empty($images)) : ?>
    <table>
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Sección</th>
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($images as $image) : ?>
            <tr>
                <td style="width:105px;text-align: left;"><img src="<?php echo $image->imageData->getLink(175, 100); ?>" alt="image" /></td>
                <td><?php echo the_section($image->section, $image->image, $sections); ?></td>
                <td><?php echo $image->order; ?></td>
                <td><a href="/admin/projects/image_up/<?php echo $project->id; ?>/?image=<?php echo $image->image; ?>">[&uarr;]</a></td>
                <td><a href="/admin/projects/image_down/<?php echo $project->id; ?>/?image=<?php echo $image->image; ?>">[&darr;]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
<form id="sec_form" action="/admin/projects/image_section/<?php echo $project->id; ?>" method="post">
    <input type="hidden" id="setimg" name="image" value="">
    <input type="hidden" id="setsec" name="section" value="">
</form>