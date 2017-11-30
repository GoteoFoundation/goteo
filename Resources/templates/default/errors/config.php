<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <title>Configuration error</title>

    <link rel="stylesheet" type="text/css" href="/view/css/goteo.css" />

    </head>

<body>

<div id="sub-header">
    <div>
        <h2>Config error</h2>
        <h3>Error <?=$this->code?></h3>
        <h4 style="text-transform: none"><?=$this->msg?></h4>
    </div>
</div>

<div id="main">

<?php if($this->info): ?>
    <div class="widget">
    <p>Error info:</p>
    <div style="padding:10px;border:1px solid #999999;font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
"><?= $this->raw('info'); ?></div>
<?php endif ?>

    </div>
<?php if($this->file): ?>
    <div class="widget">
    <p>Your <b><?= $this->file ?></b> seems incomplete or erroneous</p>
    <p>Be sure that you have all this variables defined in your <b><?= $file ?></b> config file:</p>
    <pre style="padding:10px;border:1px solid #999999;font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
"><?= htmlspecialchars(file_get_contents($path . '/config/demo-settings.yml')); ?></pre>
    </div>
<?php endif ?>


</div>

</body>
</html>
