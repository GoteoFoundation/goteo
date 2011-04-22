<?php 
$bodyClass = 'invest';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

            <p><?php echo $this['message']; ?></p>

            <h2><?php echo $this['project']->name; ?></h2>

            <form method="post" action="/invest/<?php echo $this['project']->id; ?>">
                Email: <input type="text" name="email" value="julian_1302552287_per@gmail.com" /><br />
                Amount: <input type="text" name="amount" value="" /> &euro;<br />
                <input type="checkbox" name="resign" value="1" /> Renuncia a recompensa<br />
                <?php foreach ($this['project']->individual_rewards as $reward) : ?>
                    <input type="checkbox" name="reward_<?php echo $reward->id; ?>" value="<?php echo $reward->id; ?>" /> <?php echo $reward->amount; ?>&euro; <?php echo $reward->reward; ?>: <?php echo $reward->description; ?><br />
                <?php endforeach; ?>
                <input type="checkbox" name="anonymous" value="1" /> Aporte anónimo<br />
                <br />
                <input type="submit" value="Paso siguiente" />
            </form>

        </div>
    
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';