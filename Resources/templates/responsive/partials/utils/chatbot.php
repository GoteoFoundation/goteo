<?php 

$chatbot = $this->a('chatbot');

if($chatbot['url']):

?>

  <!-- Chatbot code -->
  <?php $current_lang=$this->lang_current(); ?>

  <script src="<?= $chatbot['url'] ?>/widget/widget.js"></script>
  <script>
      (window.goteoHelpWidget=window.goteoHelpWidget||{}).load("<?= $chatbot['url'] ?>", "<?= $current_lang ?>", <?= $chatbot['id'] ?>, false);
  </script>

  <!-- End Chatbot code -->

<?php endif ?>