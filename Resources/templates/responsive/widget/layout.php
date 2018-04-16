<!DOCTYPE html>
<html lang="<?= $this->lang_current() ?>">

    <head>
    <?php $this->section('head') ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script>
        /*
        @licstart  The following is the entire license notice for the
        JavaScript code in this page.

        Copyright (C) 2010  Goteo Foundation

        The JavaScript code in this page is free software: you can
        redistribute it and/or modify it under the terms of the GNU
        General Public License (GNU GPL) as published by the Free Software
        Foundation, either version 3 of the License, or (at your option)
        any later version.  The code is distributed WITHOUT ANY WARRANTY;
        without even the implied warranty of MERCHANTABILITY or FITNESS
        FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

        As additional permission under GNU GPL version 3 section 7, you
        may distribute non-source (e.g., minimized or compacted) forms of
        that code without the copy of the GNU GPL normally required by
        section 4, provided you include this license notice and a URL
        through which recipients can access the Corresponding Source.


        @licend  The above is the entire license notice
        for the JavaScript code in this page.
        */
        </script>

        <?= $this->insert('partials/header/metas') ?>

        <?= $this->supply('lang-metas', $this->insert('partials/header/lang_metas')) ?>

        <title><?= $this->title ?></title>

        <link rel="icon" type="image/x-icon" href="/favicon.ico" >

        <?= $this->insert('partials/header/styles') ?>


    <?php $this->stop() ?>

    </head>

    <body role="document">

    <div class="page-wrap">
      <div id="main">

        <div id="main-content">
            <?= $this->supply('content') ?>
        </div>

      </div>
    </div>
    <?php $this->section('footer') ?>

        <!-- Bootstrap core JavaScript -->

        <?= $this->insert('partials/footer/widgets_javascript') ?>

        <?= $this->insert('partials/footer/analytics') ?>

    <?php $this->stop() ?>

    </body>
</html>
