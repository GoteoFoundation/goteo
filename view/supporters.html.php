<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">

            <h3>Cofinanciadores del proyecto <?php echo $this['project']->name; ?></h3>


            <ul id="supporters">
		<?php
		foreach ($this['project']->investors as $investor) {
            
                echo '<p>
                   <img src="' . $investor->avatar . '" class="avatar" />
                   ' . $investor->name . '<br />
                   ' . $this['worthcracy'][$investor->worth]['name'] . '<br />
                   Cofinancia: ' . $investor->projects . ' proyectos<br />
                   Aporta: ' . $investor->amount . ' €<br />
                   ' . $investor->date . '<br />
                   </p>';

		}
		?>
                </ul>
        
            <p><?php echo \trace($this['worthcracy']); ?></p>

        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>