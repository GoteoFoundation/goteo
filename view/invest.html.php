<?php 
$bodyClass = 'invest';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

            <p><?php echo $this['message']; ?></p>

            <h2><?php echo $this['project']->name; ?></h2>

            <form method="post" action="/invest/<?php echo $this['project']->id; ?>">
                <fieldset>
                    <legend>Método de pago</legend>
                    <?php foreach ($this['methods'] as $id=>$name) : ?>
                        <label><?php echo $name; ?><input type="radio" name="method" value="<?php echo $id; ?>" /></label>
                    <?php endforeach; ?>
                </fieldset>
                <br /><br />
                Email / Cuenta paypal: <input type="text" name="email" value="<?php echo $_SESSION['user']->email; ?>" /><br />
                Amount: <input type="text" name="amount" value="10" /> &euro;<br />
                <input type="checkbox" name="resign" value="1" /> Renuncia a recompensa<br />
                <?php foreach ($this['project']->individual_rewards as $reward) : ?>
                    <input type="checkbox"<?php if ($reward->none) echo ' disabled="disabled"';?> name="reward_<?php echo $reward->id; ?>" value="<?php echo $reward->id; ?>" /> <?php echo $reward->amount; ?>&euro; <?php echo $reward->reward; ?>: <?php echo $reward->description; ?> <?php if ($reward->units > 0) echo "[{$reward->taken}/{$reward->units}]"; else echo "[{$reward->taken}]"; ?><br />
                <?php endforeach; ?>
                    <fieldset>
                        <legend>Donde quieres recibir la recompensa</legend>
                        <label for="address">Dirección:</label><input type="text" id="address" name="address" value="<?php echo $this['personal']['address']; ?>" /><br />
                        <label for="zipcode">Código postal:</label><input type="text" id="zipcode" name="zipcode" value="<?php echo $this['personal']['zipcode']; ?>" /><br />
                        <label for="location">Ciudad:</label><input type="text" id="location" name="location" value="<?php echo $this['personal']['location']; ?>" /><br />
                        <label for="country">País:</label><input type="text" id="country" name="country" value="<?php echo $this['personal']['country']; ?>" /><br />
                    </fieldset>
                <br /><br />
                <input type="checkbox" name="anonymous" value="1" /> Aporte anónimo<br />
                <br />
                <input type="submit" value="Paso siguiente" />
            </form>

        </div>
    
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';