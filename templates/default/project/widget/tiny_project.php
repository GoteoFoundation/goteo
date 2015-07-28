<?php

use Goteo\Library\Check,
    Goteo\Model\Image;

$project = $this->project;

$proj_id        = $project->id;
$proj_name      = $project->name;
$proj_subtitle  = $project->subtitle;
$proj_description = $project->description;
$categories     = $project->categories;
$tagmark        = $project->tagmark;
$image          = $project->image;
$user           = $project->user;
$days           = $project->days;
$days_round1    = $project->days_round1;
$days_total     = $project->days_total;
$round          = $project->round;
$status         = $project->status;
$amount         = $project->amount;
$per_amount     = $project->per_amount;
$date_created   = $project->created;
$date_updated   = $project->updated;
$date_success   = $project->success;
$date_closed    = $project->closed;
$date_published = $project->published;

$url = '';

?>
<li>
    <a href="<?php echo $url ?>/project/<?php echo $proj_id ?>" class="expand" target="_blank"></a>
    <div class="image">
        <?php switch ($tagmark) {
            case 'oneround': // "ronda única"
                echo '<div class="tagmark aqua">' . $this->text('regular-oneround_mark') . '</div>';
                break;
            case 'onrun': // "en marcha"
                echo '<div class="tagmark aqua">' . $this->text('regular-onrun_mark') . '</div>';
                break;
            case 'keepiton': // "aun puedes"
                echo '<div class="tagmark aqua">' . $this->text('regular-keepiton_mark') . '</div>';
                break;
            case 'onrun-keepiton': // "en marcha" y "aun puedes"
                  echo '<div class="tagmark aqua twolines"><span class="small"><strong>' . $this->text('regular-onrun_mark') . '</strong><br />' . $this->text('regular-keepiton_mark') . '</span></div>';
                break;
            case 'gotit': // "financiado"
                echo '<div class="tagmark violet">' . $this->text('regular-gotit_mark') . '</div>';
                break;
            case 'success': // "exitoso"
                echo '<div class="tagmark green">' . $this->text('regular-success_mark') . '</div>';
                break;
            case 'fail': // "caducado"
                echo '<div class="tagmark grey">' . $this->text('regular-fail_mark') . '</div>';
                break;
        }  ?>

        <?php if ($image instanceof Image): ?>
        <a href="<?php echo $url ?>/project/<?php echo $proj_id ?>"><img src="<?php echo $image->getLink(150, 98, true) ?>" alt="<?php echo $proj_name ?>"/></a>
        <?php endif ?>
        <?php if (!empty($categories)): ?>
        <div class="categories">
        <?php $sep = '';
        foreach ($categories as $key => $value) :
            echo $sep.htmlspecialchars($value);
            $sep = ', ';
        endforeach; ?></div>
        <?php endif ?>
    </div>
    <h3 class="title"><a href="<?php echo $url ?>/project/<?php echo $proj_id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars($this->text_truncate($proj_name, 50)) ?></a></h3>
    <div class="description"><?php echo empty($proj_subtitle) ? $this->text_truncate($proj_description, 100) : $this->text_truncate($proj_subtitle, 100); ?></div>
    <h4 class="author"><?php echo $this->text('regular-by')?> <a href="<?php echo $url ?>/user/profile/<?php echo htmlspecialchars($user->id) ?>" target="_blank"><?php echo htmlspecialchars($this->text_truncate($user->name,40)) ?></a></h4>
    <span class="obtained"><?php echo $this->text('project-view-metter-got'); ?></span>
    <div class="obtained">
        <strong><?php echo \amount_format($amount) ?></strong>
        <span class="percent"><?php echo $per_amount ?> &#37;</span>
    </div>
    <?php
    switch ($status) {
        case 1: // en edicion
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-day_created'); ?></span> <?php echo date('d / m / Y', strtotime($date_created)) ?></div>
        <?php
        break;

        case 2: // enviado a revision
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-day_updated'); ?></span> <?php echo date('d / m / Y', strtotime($date_updated)) ?></div>
        <?php
        break;

        case 4: // financiado
        case 5: // caso de exito
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-day_success'); ?></span> <?php echo date('d / m / Y', strtotime($date_success)) ?></div>
        <?php
        break;

        case 6: // archivado
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-day_closed'); ?></span> <?php echo date('d / m / Y', strtotime($date_closed)) ?></div>
        <?php
        break;

        default:
            if ($days > 2 || $days == 0) :
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-days'); ?></span> <?php echo $days ?> <?php echo $this->text('regular-days'); ?></div>
        <?php
            else :
                $part = strtotime($date_published);

                if ($round == 1) {
                    $plus = $days_round1;
                } elseif ($round == 2) {
                    $plus = $days_total;
                }

                $final_day = date('Y-m-d', mktime(0, 0, 0, date('m', $part), date('d', $part)+$plus, date('Y', $part)));
                $timeTogo = Check::time_togo($final_day,1);
        ?>
    <div class="days"><span><?php echo $this->text('project-view-metter-days'); ?></span> <?php echo $timeTogo ?></div>
        <?php
            endif;
        break;
    }
    ?>
</li>
