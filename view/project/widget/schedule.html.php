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


$diff = date_diff(new DateTime("@$until"), new DateTime("@$from"), true);

$a = $diff->format('%a');

$max_weeks = 52;
$min_weeks = 4;

if ($a > ($max_weeks * 7)) {
    
    $view = 'months';
    // Pongo $from a dia 1 del mes
    $from = mktime(0, 0, 0, date('m', $from), 1, date('Y', $from));
    // Pongo $until al últim día del mes
    $until = mktime(0, 0, 0, date('m', $until) + 1, -1, date('Y', $until));
    
} else if ($a < ($min_weeks * 7)) {
    // Añado días hasta hacer, por lo menos, 2 semanas
    $d = ($min_weeks * 7) - $a;
    $h = floor($d / 2);    
    $from = mktime(0, 0, 0, date('m', $from), date('d', $from) - $h, date('Y', $from));    
    if ($h * 2 !== $d) $h++;
    $until = mktime(0, 0, 0, date('m', $until), date('d', $until) + $h, date('Y', $until));
}


echo 'From: ', date('Y-m-d', $from), ' to ', date('Y-m-d', $until);

?>

<?php if ($from && $until): ?>

<div class="widget project-schedule">
    
    <h<?php echo $level ?> class="title">Agenda</h<?php echo $level ?>>
    
    <table>
        
        <thead class="days">
            <tr>
                <th>Días</th>
                <?php for ($d = $from; $d < $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d))): ?>
                <th><span><?php echo date('d', $d) ?></span></th>
                <?php endfor ?>
            </tr>
        </thead>
        
        <?php if ($view === 'months'): ?>
        <?php else: ?>
        <thead class="weeks">
            <tr>
                <th>Semanas</th>
                <?php for ($d = $from; $d < $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 7, date('Y', $d))): ?>
                <th colspan="7"><span><?php echo date('d', $d) ?></span></th>
                <?php endfor ?>
            </tr>
        </thead>        
        <tbody>                        
            <?php foreach ($project->costs as $cost) if ($cost->from && $cost->until): ?>
            <tr>
                <th><?php echo htmlspecialchars($cost->cost) ?></th>
                
                <?php 
                
                $cost_from = strtotime($cost->from);
                $cost_until = strtotime($cost->until);
                
                $d = $from;
                
                $span = 0;
                $i = 0;
                
                while (true) {
                    
                    $span++;
                    $i++;
                                                                                                    
                    if ($span === 7) {
                        echo '<td colspan="7"></td>';
                        $span = 0;
                    } else if ($d >= $cost_from) {
                        if ($span > 1) {
                            echo '<td colspan="', $span, '"></td>';
                        } else if ($span) {
                            echo '<td></td>';
                        }
                        break;
                    }                   
                    
                    $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d));
                    
                }               
                                        
                $span = 0;
                
                while ($d < $cost_until) {
                    $span++;                                        
                    $i++;
                    $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d));
                }
                
                echo '<td';
                
                if ($span > 1) {
                    echo ' colspan="', $span, '"';
                }
                
                echo ' class="on"><span>', htmlspecialchars($cost->cost), '</span></td>';
                           
                $span = 0;
                
                while (true) {
                                                                                                                                            
                    if ($d >= $until) {                        
                        if ($span) {
                            echo '<td colspan="', $span, '"></td>';                                                
                        }
                        break;
                        
                    } else if (!($i % 7)) {
                        
                        if ($span > 1) {
                            echo '<td colspan="', $span, '"></td>';
                        } else if ($span) {
                            echo '<td></td>';
                        }
                        
                        $span = 0;
                        
                    } else {
                        $span++;
                    }
                    
                    $i++;                                        
                    $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d));                    
                    
                }    
                ?>
            </tr>
            <?php endif ?>            
        </tbody>
        <?php endif ?>
        
    </table>
    
</div>
<?php endif ?>