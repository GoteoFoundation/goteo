<?php
$path = realpath(dirname(dirname(dirname(__DIR__))));
$file = $path .'/config/settings.yml';

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <title>Configuration error</title>

    <link rel="stylesheet" type="text/css" href="/view/css/goteo.css" />

    </head>

<body>

<div id="sub-header">
    <div>
        <h2><?=$this->msg?></h2>
        <h3>Error <?=$this->code?></h3>
    </div>
</div>

<div id="main">
    <div class="widget">
    <p>Your <b><?= $file ?></b> seems incomplete</p>
    <p>Be sure that you have all this variables defined in your <b><?= $file ?></b> config file:</p>
    <pre style="padding:10px;border:1px solid #999999;font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
"><?= htmlspecialchars(file_get_contents($path . '/config/demo-settings.yml')); ?></pre>
    </div>
</div>

</body>
</html>
