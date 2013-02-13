<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

foreach ($call->projects as $key => $proj) {

    if ($proj->status < 3) {
        unset($call->projects[$key]);
    }
}

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>
<div id="main">
    <?php echo new View('view/call/widget/title.html.php', $this); ?>
    <div id="banners-social">
        <?php echo new View('view/call/widget/banners.html.php', $this) ?>
        <?php echo new View('view/call/widget/social.html.php', $this) ?>
    </div>

    <div id="info">
        <div id="content">

            <div class="freetext">

                <h2 class="title"><?php echo Text::get('call-info-main-header') ?></h2>

                <div id="call-description"><?php echo nl2br(Text::urlink($call->description)) ?></div>

                <h3 class="title"><?php echo Text::get('call-field-whom'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->whom)) ?></p>

                <h3 class="title"><?php echo Text::get('call-field-apply'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->apply)) ?></p>

                <?php if (count($call->projects) > 0) : //en campaña ?>
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
                            echo \amount_format($proj->amount_call) . ' &euro;'; ?></td>
                                    <td class="max"><?php echo \amount_format($proj->amount_users) ?> &euro;</td>
                                </tr>
    <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="total"><?php echo Text::get('regular-total'); ?></th>
                                <th class="min"><?php if (!empty($call->amount))
                                    echo \amount_format($tot_call) . ' &euro;'; ?></th>
                                <th class="max"><?php echo \amount_format($tot_users) ?> &euro;</th>
                            </tr>
                        </tfoot>
                    </table>
<?php endif; ?>

            </div>

            <p class="block">
                <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/terms"><?php echo Text::get('call-terms-main-header') ?></a>
            </p>
            <p>
                <?php if ($call->status == 3) : //inscripcion  ?>
                    <?php if (!$call->expired) : // sigue abierta  ?>
                        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
                    <?php endif; ?>
                <?php else : //en campaña  ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>
            </p>
        </div>
<?php echo new View('view/call/side.html.php', $this); ?>
    </div>

    <div id="supporters-sponsors">
        <?php echo new View('view/call/widget/supporters.html.php', $this); ?>
        <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
    </div>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>