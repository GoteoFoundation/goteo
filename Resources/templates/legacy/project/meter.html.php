<?php
use Goteo\Library\Text,
    Goteo\Library\Check,
    Goteo\Application\Currency;

$level = (int) $vars['level'] ?: 3;

$horizontal = !empty($vars['horizontal']);
$big = !empty($vars['big']);
$activable = !empty($vars['activable']);

$project = $vars['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
$days       = $project->days;
$days_round1 = $project->days_round1;
$days_total = $project->days_total;
$round      = $project->round;
$status     = $project->status;
$amount     = $project->amount;
$date_created = $project->created;
$date_updated = $project->updated;
$date_success = $project->success;
$date_closed  = $project->closed;
$date_published = $project->published;
$num_investors  = $project->num_investors;


// PHP la pifia (y mucho) con los cálculos en coma flotante
if ($reached >= $minimum) {
    $minimum_done = @floor(($reached / $minimum) * 100);
    $minimum_done_per = @floor(($reached / $minimum) * 100);
    $minimum_left = 0;

} else {

    $minimum_done = min(100, @floor(($reached / $minimum) * 100));
    $minimum_done_per = @floor(($reached / $minimum) * 100);
    $minimum_left = max(0, @floor((1 - $reached / $minimum) * 100));

    if ($minimum_done >= 100) {
        // No muestres 100 si falta aunque sea un céntimo
        $minimum_done = 99;
    }
}

if (!$horizontal) {
    // si aun no ha alcanzado el optimo controlamos la visualización para que no confunda
    // $minimum_done es el % de heigth del mercurio
    // si no ha alcanzado el óptimo, el máximo será 120%
    if ($reached < $optimum && $minimum_done > 120)
        $minimum_done = 120;

    // y si es menos del doble del optimo, que se mantenga en 140
    if ($reached > $optimum && $reached <= $optimum*1.5) {
        $minimum_done = 140;
    }

    // y si es menos de 1.5 del optimo, que se mantenga en 140
    if ($reached > $optimum && $reached <= $optimum*1.2) {
        $minimum_done = 135;
    }
}



$more  = $optimum - $minimum;
$over = $reached - $minimum;

if ($over > 0) {

    if ($over >= $more) {
        $optimum_done = 100;
    } else {
        $optimum_done = min(100, @floor($over / ($optimum - $minimum)));

        if ($optimum_done >= 100) {
            $optimum_done = 99;
        }
    }

} else {
    $optimum_done = 0;
}

$optimum_left = 100 - $optimum_done;

$minimum_ratio =  min(100, @floor(($minimum / $optimum) * 100));

$currencies = Currency::$currencies;

$num_currencies=count($currencies);

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];

?>
    <div class="meter <?php echo $horizontal ? 'hor' : 'ver'; echo $big ? ' big' : ''; echo $activable ? ' activable' : ''; ?>">
        <h<?php echo $level ?> class="title investment"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level ?>>

        <?php if (!$project->one_round && !empty($round)) : ?>
            <h<?php echo $level ?> class="title ronda">
            <?php echo $round . Text::get('regular-round'); ?></h<?php echo $level ?>>
        <?php elseif ($project->one_round) : ?>
            <h<?php echo $level ?> class="title ronda unica">
            <?php echo Text::get('regular-oneround_mark'); ?></h<?php echo $level ?>>
        <?php endif; ?>



        <?php if ($activable) : ?><h<?php echo $level ?> class="title obtained"><?php echo Text::get('project-view-metter-got'); ?></h<?php echo $level ?>><?php endif; ?>
        <div class="graph">
            <div class="optimum">
                 <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_left) ?>%"></div>
                 <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_done) ?>%"></div>
            </div>
            <div class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%">
                <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_left) ?>%"><!-- <strong><?php echo number_format($minimum_left) ?>%</strong> --></div>
                <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_done) ?>%"><strong><?php echo number_format($minimum_done_per) ?>%</strong></div>
            </div>
        </div>
        <?php if (!$horizontal&&($num_currencies>1)) { ?>
        <div style="position:relative;">
        <?php } ?>
        <dl>
            <dt class="optimum"><?php echo Text::get('project-view-metter-optimum'); ?></dt>
            <dd class="optimum"><strong><?php echo \amount_format($optimum) ?></strong> </dd>

            <dt class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>% <?php if($num_currencies<2) {?>;margin-bottom:0px; <?php }?>"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%"><strong><?php echo \amount_format($minimum); ?></strong> </dd>

            <dt class="reached"><span><?php echo Text::get('project-view-metter-got'); ?></span></dt>
            <dd class="reached"><strong><?php echo \amount_format($reached) ?> </strong></dd>

            <?php
            if ($status == 3) { // en campaña
                if ($days > 2) {
                    $days_left = number_format($days);
                    $days_left2 = Text::get('regular-days');
                } else {
                    $part = strtotime($date_published);

                    if ($round == 1) {
                        $plus = $days_round1;
                    } elseif ($round == 2) {
                        $plus = $days_total;
                    }

                    $final_day = date('Y-m-d', mktime(0, 0, 0, date('m', $part), date('d', $part)+$plus, date('Y', $part)));
                    $days_left = Check::time_togo($final_day, 1);
                    $days_left2 = '';
                }
                ?>
            <dt class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span></dt>
            <dd class="days"><strong><?php echo $days_left; ?></strong> <?php echo $days_left2; ?></dd>
            <?php
            } elseif (!empty($status)) {
                switch ($status) {
                    case 1: // en edicion
                        $text = 'project-view-metter-day_created';
                        $date = $date_created;
                        break;

                    case 2: // pendiente de valoración
                        $text = 'project-view-metter-day_updated';
                        $date = $date_updated;
                        break;

                    case 4: // financiado
                    case 5: // retorno cumplido
                        $text = 'project-view-metter-day_success';
                        $date = $date_success;
                        break;

                    case 6: // archivado
                        $text = 'project-view-metter-day_closed';
                        $date = $date_closed;
                        break;
                }
            ?>
            <dt class="days long"><span><?php echo Text::get($text); ?></span></dt>
            <dd class="days long"><strong><?php echo date('d / m / Y', strtotime($date)) ?></strong></dd>
            <?php
            }
            ?>

            <dt class="supporters"><span><?php echo Text::get('project-view-metter-investors'); ?></span></dt>
            <dd class="supporters"><strong><?php echo $num_investors ?></strong></dd>

        </dl>
        <?php if (!$horizontal&&($num_currencies>1)) { ?>
        <div class="currency">
            <span class="symbol"><?php echo $select_currency." ".$_SESSION['currency']; ?></span>
            <span class="change">
                <hr>
                <?php echo Text::get('currency-switch'); ?>
            </span>
            <div>
                <ul>
                    <?php foreach ($currencies as $ccyId => $ccy): ?>
                        <?php if ($ccyId == $_SESSION['currency']) continue; ?>
                            <li>
                                <a href="?currency=<?php echo $ccyId ?>"><?php echo $ccy['html'].' '.$ccyId; ?></a>
                            </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        </div>
        <?php } ?>

        <?php if ($activable) : ?>
        <div class="obtained">
            <strong><?php echo \amount_format($reached) ?></strong>
            <span class="percent"><?php echo number_format($minimum_done_per) ?>%</span>
        </div>
        <?php endif; ?>

    <?php /*
    // si en estado 3 ha alcanzado el optimo o segunda ronda, "aun puedes seguir aportando" junto al quedan tantos días
    if ($status == 3 && ($round == 2  || $amount >= $optimum || ($round == 1  && $amount >= $minimum) )) : ?>
        <div class="keepiton"><?php echo Text::get('regular-keepiton') ?></div>
    <?php endif; */ ?>

    </div>
