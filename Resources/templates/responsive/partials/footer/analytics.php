<?php
$google = $this->get_config('analytics.google');

if($google):
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  <?php foreach($google as $k => $id): ?>
      ga('create', <?= $id ?>, 'auto', 'tracker<?= $k ?>');
      ga(''tracker<?= $k ?>'.send', 'pageview');
  <?php endforeach ?>

</script>
<?php endif ?>
