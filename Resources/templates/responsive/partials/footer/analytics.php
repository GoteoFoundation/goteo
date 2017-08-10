<?php
$google = $this->get_config('analytics.google');

if($google):
    $id = array_shift($google);
?>
<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    // Unamed first tracker
    ga('create', '<?= $id ?>', 'auto');
    ga('send', 'pageview');

<?php if($google): ?>
    <?php foreach($google as $k => $id): ?>
        ga('create', '<?= $id ?>', 'auto', 'tracker<?= $k ?>');
        ga('tracker<?= $k ?>.send', 'pageview');
    <?php endforeach ?>

    // Pronto ajax compatibility for named analytics
    $(function(){
         $(window).on("pronto.load", function(e){
            var url = e.currentTarget.location.href;
            // Trigger additional google analytics (pronto only tracks the first)
            // Strip domain
            url = url.replace(window.location.protocol + "//" + window.location.host, "");
            <?php foreach($google as $k => $i): ?>
                console.log('tracker', url, 'tracker<?= $k ?>', e);
                ga('tracker<?= $k ?>'+'.send', 'pageview', url);
            <?php endforeach ?>
         });
     });
<?php endif ?>

    // @license-end
</script>
<?php endif ?>
