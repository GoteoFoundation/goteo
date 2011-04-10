<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">
			Página pública de un proyecto<br />
			<pre><?php echo print_r($this['project'], 1) ?></pre>
        </div>

        <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>