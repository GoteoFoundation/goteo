<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Model\Invest,
    Goteo\Library\Text,
    Goteo\Model\License;

$project = $this['project'];
$personal = $this['personal'];

$level = (int) $this['level'] ?: 3;

$methods = Invest::methods();
unset($methods['cash']);

$worthcracy = Worth::getAll();

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}


?>
<div class="widget project-invest">
    <h<?php echo $level ?> class="title"><?php echo Text::get('invest-amount') ?></h<?php echo $level ?>>
    
    <form method="post" action="/invest/<?php echo $project->id; ?>">

    <label><input type="text" id="amount" name="amount" value="10" /><?php echo Text::get('invest-amount-tooltip') ?></label>
</div>

    
<div class="widget project-invest">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-individual-header') ?></h<?php echo $level ?>>
    
    <div class="individual">
        <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
        <ul>
            <li><label class="resign"><input type="checkbox" name="resign" value="1" /><?php echo Text::get('invest-resign') ?></label></li>
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?>">
            <label class="amount"><input type="checkbox"<?php if ($individual->none) echo ' disabled="disabled"';?> name="reward_<?php echo $individual->id; ?>" value="<?php echo $individual->amount; ?>" /><?php echo $individual->amount; ?> &euro;</label>
            <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 2 ?>
            <p><?php echo htmlspecialchars($individual->description)?></p>
            <?php if ($individual->none) : ?><p><?php echo Text::get('invest-reward-cestfini') ?></p><?php endif; ?>
        </li>
        <?php endforeach ?>
    </div>

</div>

<div class="widget project-invest address">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-address-header') ?></h<?php echo $level ?>>
    <table>
        <tr>
            <td><label for="address"><?php echo Text::get('invest-address-address-field') ?></label></td>
            <td colspan="3"><input type="text" id="address" name="address" value="<?php echo $personal->address; ?>" /></td>
            <td><label for="zipcode"><?php echo Text::get('invest-address-zipcode-field') ?></label></td>
            <td><input type="text" id="zipcode" name="zipcode" value="<?php echo $personal->zipcode; ?>" /></td>
        </tr>
        <tr>
            <td><label for="location"><?php echo Text::get('invest-address-location-field') ?></label></td>
            <td><input type="text" id="location" name="location" value="<?php echo $personal->location; ?>" /></td>
            <td><label for="country"><?php echo Text::get('invest-address-country-field') ?></label></td>
            <td><input type="text" id="country" name="country" value="<?php echo $personal->country; ?>" /></td>
            <td colspan="2"></td>
        </tr>
    </table>
</div>


<div class="widget project-invest">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('project-invest-continue') ?></h<?php echo $level ?>>
        <fieldset>
            <legend><?php echo Text::get('invest-payment_method-header') ?></legend>
            <?php foreach ($methods as $id=>$name) : ?>
                <label><?php echo $name; ?><input type="radio" name="method" value="<?php echo $id; ?>" checked="checked"/></label>
            <?php endforeach; ?>
        </fieldset>
        
        <p>
            <label><?php echo Text::get('invest-payment-email') ?><br />
                <input type="text" id="email" name="email" value="<?php echo $_SESSION['user']->email; ?>" />
            </label>
        </p>
            
        <p>
            <label><input type="checkbox" name="anonymous" value="1" /><?php echo Text::get('invest-anonymous') ?></label>
        </p>
        
<input type="submit" value="<?php echo Text::get('invest-next_step') ?>" />

</form>
</div>

<?php echo new View('view/project/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $_SESSION['user']->worth)) ?>


<div class="widget project-invest">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-social-header') ?></h<?php echo $level ?>>

    <div class="social">
        <h<?php echo $level + 1 ?> class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 1 ?>>
        <ul>
        <?php foreach ($project->social_rewards as $social) : ?>
            <li class="<?php echo $social->icon ?>">
                <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($social->reward) ?></h<?php echo $level + 2 ?>
                <p><?php echo htmlspecialchars($social->description)?></p>
                <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                <div class="license <?php echo htmlspecialchars($social->license) ?>">
                    <h<?php echo $level + 3 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 3 ?>>
                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                    <?php if (!empty($licenses[$social->license]->description)): ?>
                    <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                    <?php endif ?>
                    </a>
                </div>
                <?php endif ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

