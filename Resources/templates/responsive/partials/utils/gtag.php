<?php
if($google = $this->a('google')):
    $id = array_shift($google);
?>
    <?php if (preg_match("/^G-[a-zA-Z0-9-]+$/", $id)): ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', "<?= $id ?>");
        </script>
    <?php endif; ?>

    <?php foreach ($google as $k => $id): ?>
        <?php if (preg_match("/^G-[a-zA-Z0-9-]+$/", $id)): ?>

            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id ?>"></script>
            <script>
            window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', "<?= $id ?>");
            </script>
        <?php endif; ?>
    <?php endforeach; ?>

<?php endif; ?>
