<?php 
$bodyClass = 'invest';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

            <p><?php echo $this['message']; ?></p>

            <h2><?php echo $this['project']->name; ?></h2>

            <form method="post" action="/invest/<?php echo $this['project']->id; ?>">
                Email: <input type="text" name="email" value="<?php echo $_SESSION['user']->email; ?>" /><br />
                Si tienes una cuenta Paypal, indícala aquí.<br />
                Amount: <input type="text" name="amount" value="10" /> &euro;<br />
                <input type="checkbox" name="resign" value="1" /> Renuncia a recompensa<br />
                <?php foreach ($this['project']->individual_rewards as $reward) : ?>
                    <input type="checkbox" name="reward_<?php echo $reward->id; ?>" value="<?php echo $reward->id; ?>" /> <?php echo $reward->amount; ?>&euro; <?php echo $reward->reward; ?>: <?php echo $reward->description; ?><br />
                <?php endforeach; ?>
                    <fieldset>
                        <legend>Donde quieres recibir la recompensa</legend>
                        <label for="address">Dirección:</label><input type="text" id="address" name="address" value="" /><br />
                        <label for="zipcode">Código postal:</label><input type="text" id="zipcode" name="zipcode" value="" /><br />
                        <label for="location">Ciudad:</label><input type="text" id="location" name="location" value="" /><br />
                        <label for="country">País:</label><input type="text" id="country" name="" value="v" /><br />
                    </fieldset>
                <input type="checkbox" name="anonymous" value="1" /> Aporte anónimo<br />
                <br />
                <input type="submit" value="Paso siguiente" />
            </form>

        </div>
    
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';