<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Previsualización<br />
GUÍA: <?php echo $guideText;  ?><br />
<?php include 'view/project/errors.html.php' ?>
<?php if ($finish == true) : ?>
<a href="/project/close">[LISTO PARA REVISIÓN]</a>
<?php endif; ?>
<hr />
<pre><?php echo print_r($project, 1) ?></pre>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>