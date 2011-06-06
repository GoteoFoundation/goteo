<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;


// @todo? Esto como que lo tendría que hacer un modelo o algo

// Preparo los datos
$schedule = new stdClass();

// Si la agenda es muy larga, mostraré meses en lugar de semanas
$view = 'weeks';

// Obtengo la primera y última fechas en el tiempo
$from = $until = 0;

foreach ($project->costs as $cost) {        
    
    $cost_from = strtotime($cost->from);
    
    if ($cost_from && (!$from || ($cost_from < $from))) {
        $from = $cost_from;
    }
    
    $cost_until = strtotime($cost->until);
    
    if ($cost->until && ($cost_until > $until)) {
        $until = $cost_until;
    }
    
}

$diff = date_diff($until, $from, true);


$max_weeks = 52;

?>

<?php if ($from && $until): ?>

<div class="widget project-schedule">
    
    <h<?php echo $level ?> class="title">Agenda</h<?php echo $level ?>>
    
    <table>
        <thead>
            <tr>
                <th></th>
                <?php for ($d = $from; $d <= $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d))): ?>
                <th><span><?php echo date('d', $d) ?></span></th>
                <?php endfor ?>
            </tr>
        </thead>
        
        <tbody>
                        
            <?php foreach ($project->costs as $cost): ?>
            <tr>
                <th><?php echo htmlspecialchars($cost->cost) ?></th>
                <?php for ($d = $from, $cost_from = strtotime($cost->from), $cost_until = strtotime($cost->until); $d <= $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d))): ?>
                <td<?php if ($d >= $cost_from && $d <= $cost_until) echo ' class="on"' ?>><span>X</span></td>
                <?php endfor ?>
            </tr>
            <?php endforeach ?>
            
        </tbody>
    </table>
    
</div>
<?php endif ?>