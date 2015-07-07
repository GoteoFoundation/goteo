<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $vars['call'];

foreach ($call->projects as $key => $proj) {

    if ($proj->status < 3) {
        unset($call->projects[$key]);
    }
}
?>
<h3><?php echo Text::get('call-splash-selected_projects-header') ?></h3>

<?php $ths = explode('-', Text::get('call-projects_table-head')); ?>
<table class="info-table" width="100%">
    <thead class="task">
        <tr>
            <th class="title"><?php echo $ths[0]; ?></th>
            <th class="min"><?php if (!empty($call->amount))
    echo $ths[1]; ?></th>
            <th class="max"><?php echo $ths[2]; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tot_call = 0;
        $tot_users = 0;
        $odd = true;

        foreach ($call->projects as $proj) :

            $tot_call += $proj->amount_call;
            $tot_users += $proj->amount_users;
            ?>
            <tr class="<?php
        if ($odd) {
            echo 'odd';
            $odd = false;
        } else {
            echo 'even';
            $odd = true;
        }
            ?>">
                <th class="summary">
                    <a href="/project/<?php echo $proj->id ?>"><span><?php echo $proj->name ?></span><br />
                        <blockquote><?php echo empty($proj->subtitle) ? Text::recorta($proj->description, 200) : $proj->subtitle; ?></blockquote>
                    </a>
                </th>
                <td class="min"><?php if (!empty($call->amount))
                echo \amount_format($proj->amount_call); ?></td>
                <td class="max"><?php echo \amount_format($proj->amount_users) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="total"><?php echo Text::get('regular-total'); ?></th>
            <th class="min"><?php if (!empty($call->amount))
            echo \amount_format($tot_call); ?></th>
            <th class="max"><?php echo \amount_format($tot_users) ?></th>
        </tr>
    </tfoot>
</table>
