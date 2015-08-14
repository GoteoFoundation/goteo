<?php

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <title>Configuration error</title>

    <link rel="stylesheet" type="text/css" href="/view/css/goteo.css" />

    </head>

<body>

<div id="sub-header">
    <div>
        <h2>Error <?=$this->code?></h2>
        <h3 style="text-transform: none"><?= $this->msg ?></h3>
    </div>
</div>

<div id="main">
    <div class="widget">
    <p>Error info:</p>
    <div style="padding:10px;border:1px solid #999999;font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
"><?= $this->raw('info'); ?></div>
    </div>
</div>

</body>
</html>
