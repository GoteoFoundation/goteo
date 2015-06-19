<?php
use Goteo\Library\Text,
    Goteo\Model\License;

$level = (int) $vars['level'] ?: 3;

$project = $vars['project'];
$only = (in_array($vars['only'], array('individual', 'social'))) ? $vars['only'] : null;

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}

if (empty($project->social_rewards) && empty($project->individual_rewards))
    return '';

uasort($project->individual_rewards,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );
?>
<div class="widget project-rewards collapsable" id="project-rewards">

    <?php if (!isset($only) || $only == 'social') : ?>
    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-rewards-supertitle'); ?></h<?php echo $level + 1?>>
    <?php endif; ?>

    <?php if (!empty($project->individual_rewards) && (!isset($only) || $only == 'individual')) : ?>
    <div class="individual">
        <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
        <ul>
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?>">

            <div class="amount"><?php echo Text::get('regular-investing'); ?> <span class="figure"><?php echo \amount_format($individual->amount); ?></span></div>
            <h<?php echo $level + 1 ?> class="name"><?php echo htmlspecialchars($individual->icon_name) . ': ' . htmlspecialchars($individual->reward) ?></h<?php echo $level + 1 ?>
            <p><?php echo htmlspecialchars($individual->description)?></p>

                <?php if (!empty($individual->units)) : ?>
                <strong><?php echo Text::get('project-rewards-individual_reward-limited'); ?></strong><br />
                <?php $units = ($individual->units - $individual->taken);
                echo Text::html('project-rewards-individual_reward-units_left', $units); ?><br />
            <?php endif; ?>
            <div class="investors"><span class="taken"><?php echo $individual->taken; ?></span><?php echo Text::get('project-view-metter-investors'); ?></div>
            <?php if ($project->status ==3 && !$individual->none) : ?><a href="<?php echo SEC_URL."/project/{$project->id}/invest?amount=".\amount_format($individual->amount, 0, true); ?>" class="button violet" ><?php echo Text::get('regular-getit'); ?></a><?php endif; ?>
        </li>
        <?php endforeach ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($project->social_rewards) && (!isset($only) || $only == 'social')) : ?>
    <div class="social">
        <h<?php echo $level + 1 ?> class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 1 ?>>
        <ul>
        <?php foreach ($project->social_rewards as $social) : ?>
            <li class="<?php echo $social->icon ?>">
                <h<?php echo $level + 1 ?> class="name"><?php echo htmlspecialchars($social->icon_name) . ': ' .htmlspecialchars($social->reward) ?></h<?php echo $level + 1 ?>>
                <p><?php echo htmlspecialchars($social->description)?></p>
                <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                <div class="license <?php echo htmlspecialchars($social->license) ?>">
                    <h<?php echo $level + 2 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 2 ?>>
                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                    <?php if (!empty($licenses[$social->license]->description)): ?>
                    <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                    <?php endif ?>
                    </a>
                </div>
                <?php endif ?>
                <?php if ($social->url) : ?><a href="<?php echo $social->url ?>" target="_blank" class="button green tipsy" title="<?php echo Text::get('social_reward-access_title'); ?>"><?php echo Text::get('social_reward-access'); ?></a><?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; // end of social rewards ?>


    <?php if (!empty($project->bonus_rewards) && (!isset($only) || $only == 'social')) : ?>
        <div class="social">
            <h<?php echo $level + 1 ?> class="title"><?php echo Text::get('project-rewards-bonus_reward-title'); ?></h<?php echo $level + 1 ?>>
            <ul>
                <?php foreach ($project->bonus_rewards as $bonus) : ?>
                    <li class="<?php echo $bonus->icon ?>">
                        <h<?php echo $level + 1 ?> class="name"><?php echo htmlspecialchars($bonus->icon_name) . ': ' .htmlspecialchars($bonus->reward) ?></h<?php echo $level + 1 ?>
                        <p><?php echo htmlspecialchars($bonus->description)?></p>
                        <?php if (!empty($bonus->license) && array_key_exists($bonus->license, $licenses)): ?>
                            <div class="license <?php echo htmlspecialchars($bonus->license) ?>">
                                <h<?php echo $level + 2 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 2 ?>>
                                <a href="<?php echo htmlspecialchars($licenses[$bonus->license]->url) ?>" target="_blank">
                                    <strong><?php echo htmlspecialchars($licenses[$bonus->license]->name) ?></strong>

                                    <?php if (!empty($licenses[$bonus->license]->description)): ?>
                                        <p><?php echo htmlspecialchars($licenses[$bonus->license]->description) ?></p>
                                    <?php endif ?>
                                </a>
                            </div>
                        <?php endif ?>
                        <?php if ($bonus->url) : ?><a href="<?php echo $bonus->url ?>" target="_blank" class="button green tipsy" title="<?php echo Text::get('social_reward-access_title'); ?>"><?php echo Text::get('social_reward-access'); ?></a><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; // end of bonus rewards ?>

    <a class="more" href="/project/<?php echo $project->id; ?>/rewards#social-rewards"><?php echo Text::get('regular-see_more'); ?></a>


</div>
