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
    
    if ($cost->from && $cost->until) {
    
        $cost_from = strtotime($cost->from);

        if ($cost_from && (!$from || ($cost_from < $from))) {
            $from = $cost_from;
        }

        $cost_until = strtotime($cost->until);

        if ($cost->until && ($cost_until > $until)) {
            $until = $cost_until;
        }
        
    }
    
}

if ($from && until): 

$diff = date_diff(new DateTime("@$until"), new DateTime("@$from"), true);

$a = $diff->format('%a');

$max_weeks = 52;
$min_weeks = 4;

// Pongo $from a dia 1 del mes
$from = mktime(0, 0, 0, date('m', $from), 1, date('Y', $from));

// Pongo $until al últim día del mes
$until = mktime(0, 0, 0, date('m', $until) + 1, -1, date('Y', $until));


?>
<div class="widget project-schedule">
    
    <h<?php echo $level ?> class="title">Agenda</h<?php echo $level ?>>
    
    <table>
                        
        <thead class="months">
            <tr>
                <th>Meses</th>
                <?php 
                
                for ($d = $from; $d <= $until;) {
                    
                    $to = mktime(0, 0, 0, date('m', $d), date('t', $d), date('Y', $d));
                    
                    if ($to > $until) {
                        $to = $until;
                    }
                    
                    $span = date('j', $to) - date('j', $d) + 1;
                    
                    echo '<th colspan="', $span, '"><span>';
                    
                    if ($span > 10) {
                        echo htmlspecialchars(date('F', $d));
                    }
                    
                    echo '</span></th>';
                    
                    $d = mktime(0, 0, 0, date('m', $to), date('j', $to) + 1, date('Y', $to));
                    
                }
                ?>
            </tr>
        </thead>      
        
        <thead class="days">
            <tr>
                <th>Días</th>
                <?php 
                for ($d = $from; $d <= $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d))) {
                    $j = date('j', $d);
                    if ($j == 1) {
                        echo '<th class="sta_m">';
                    } else if ($j == date('t', $d)) {
                        echo '<th class="end_m">';
                    } else {
                        echo '<th>';
                    }
                    echo '<span>', $j, '</span></th>';
                } ?>
            </tr>
        </thead>
        
        <thead class="weeks">
            <tr>
                <th>Semanas</th>
                <?php for ($i = 0, $d = $from; $d <= $until; $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 7, date('Y', $d))): $i++; ?>
                <th colspan="7"><span><?php echo $i ?></span></th>
                <?php endfor ?>
            </tr>
        </thead>        
        <tbody>                        
            <?php 
            $iCost = 0; 
            
            foreach ($project->costs as $cost) if ($cost->from && $cost->until): 
                
            $iCost++; 
            $cost_from = strtotime($cost->from);
            $cost_until = strtotime($cost->until);
            
            ?>
            <tr>
                <th><strong><?php echo $iCost ?></strong>
                    <span><?php echo htmlspecialchars($cost->cost) ?></span></th>
                
                    <?php                                                 

                    $d = $from;

                    $span = 0;
                    $i = 0;

                    while (true) {
                        
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

                        $span++;
                        $i++;                    
                        $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d));

                    }               

                    $span = 0;

                    while ($d <= $cost_until) {
                        $span++;                    
                        $i++;
                        $d = mktime(0, 0, 0, date('m', $d), date('d', $d) + 1, date('Y', $d));
                    }

                    echo '<td';

                    if ($span > 1) {
                        echo ' colspan="', $span, '"';
                    }

                    echo ' class="on"><span title="', date('d/m/Y', $cost_from), ' - ', date('d/m/Y', $cost_until), '">', 
                        htmlspecialchars($cost->cost), '</span></td>';

                    $span = 0;

                    while (true) {

                        if ($d > $until) {

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

                            $span = 1;

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
        
    </table>
    
</div>
<?php endif ?>