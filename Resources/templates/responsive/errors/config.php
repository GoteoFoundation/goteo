<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <title>Configuration error</title>

    <style type="text/css">
    * {
        font-family: "Liberation Sans", Helvetica, "Helvetica Neue", Arial, Geneva, sans-serif;
    }
    body, td, th, a, a:link, a:visited, a:active, a:hover, input, textarea, button {
        font-size: 12px;
        color: #58595b;
    }
    html, body {
        padding: 0;
        background: #faf8f8;
        height: 100%;
    }
    #main {
        background-color: #e7e7e7;
        height: 90%;
        padding: 20px;
        margin: 0 auto;
    }
    #header {
        max-width: 940px;
        margin: 0 auto;
        padding: 20px;
        height: 10%;
        /*max-width: 940px;*/
    }
    #header {
        background-color: #faf8f8;
    }
    #header {
        margin-bottom: 24px;
    }
    .widget {
        max-width: 940px;
        margin: 0 auto;
        background-color: white;
        padding: 15px 20px;
    }
    </style>
    </head>

<body>

<div id="header">
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
"><?= htmlspecialchars(file_get_contents(GOTEO_PATH . 'config/demo-settings.yml')); ?></pre>
    </div>
<?php endif ?>


</div>

</body>
</html>
