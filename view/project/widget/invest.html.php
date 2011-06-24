<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Model\Invest,
    Goteo\Library\Text;

$project = $this['project'];
$personal = $this['personal'];

$level = (int) $this['level'] ?: 3;

$methods = Invest::methods();

$worthcracy = Worth::getAll();

?>
<div class="widget project-invest">
    
    <h<?php echo $level ?>><?php echo Text::get('regular-invest'); ?></h<?php echo $level ?>>

    <form method="post" action="/invest/<?php echo $project->id; ?>">
        <fieldset>
            <legend>Método de pago</legend>
            <?php foreach ($methods as $id=>$name) : ?>
                <label><?php echo $name; ?><input type="radio" name="method" value="<?php echo $id; ?>" checked="checked"/></label>
            <?php endforeach; ?>
        </fieldset>
        <br /><br />
        Email / Cuenta paypal: <input type="text" name="email" value="<?php echo $_SESSION['user']->email; ?>" /><br />
        Amount: <input type="text" name="amount" value="10" /> &euro;<br />
        <input type="checkbox" name="resign" value="1" /> Renuncia a recompensa<br />
        <?php foreach ($project->individual_rewards as $reward) : ?>
            <input type="checkbox"<?php if ($reward->none) echo ' disabled="disabled"';?> name="reward_<?php echo $reward->id; ?>" value="<?php echo $reward->id; ?>" /> <?php echo $reward->amount; ?>&euro; <?php echo $reward->reward; ?>: <?php echo $reward->description; ?> <?php if ($reward->units > 0) echo "[{$reward->taken}/{$reward->units}]"; else echo "[{$reward->taken}]"; ?><br />
        <?php endforeach; ?>
        <fieldset>
            <legend>Donde quieres recibir la recompensa</legend>
            <label for="address">Dirección:</label><input type="text" id="address" name="address" value="<?php echo $personal->address; ?>" /><br />
            <label for="zipcode">Código postal:</label><input type="text" id="zipcode" name="zipcode" value="<?php echo $personal->zipcode; ?>" /><br />
            <label for="location">Ciudad:</label><input type="text" id="location" name="location" value="<?php echo $personal->location; ?>" /><br />
            <label for="country">País:</label><input type="text" id="country" name="country" value="<?php echo $personal->country; ?>" /><br />
        </fieldset>
        <br /><br />
        <input type="checkbox" name="anonymous" value="1" /> Aporte anónimo<br />
        <br />
        <input type="submit" value="Paso siguiente" />
    </form>
    
</div>

    <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main', 'level' => $_SESSION['user']->worth)); ?>
