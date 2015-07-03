<?php

$words = $vars['words'];
//$categories = $vars['categories'];
$list = array();
$uses = array();
//$incat = array();
/*
foreach ($categories as $c) {
    $cats[] = \Goteo\Core\Model::idealiza(str_replace('.','',  strtolower($c)));
}
 *
 */
foreach ($words as $v) {
    if (empty($v)) continue;

//    $clean = \Goteo\Core\Model::idealiza(str_replace('.','', strtolower($v)));
    if (in_array($v, $list)) {
        $uses[$v] += 1;
    } else {
        $list[] = $v;
        $uses[$v] = 1;
        /*
        if (in_array($clean, $cats)) {
            $incat[$v] = true;
        }
         *
         */
    }
}

?>

<a href="/admin/categories" class="button">Volver a categorías</a>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Palabra</th>
                <th>usos</th>
                <th>Categoría</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($list as $word) : ?>
            <tr>
                <td><?php echo $word; ?></td>
                <td><?php echo $uses[$word]; ?></td>
                <td><a href="/admin/categories/add?word=<?php echo urlencode($word); ?>" target="_blank">Añadir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
