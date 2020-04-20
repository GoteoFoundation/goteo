<?php

  $posts = $this->channel->getPosts(10);
  if ($posts) :
?>

  <div class="section posts">
    <div class="container">
      <h2 class="title"><span class="icon icon-news icon-3x"></span>Últimas noticias</h2>
      
      <div class="description">
        Entérate de las últimas novedades sobre CrowdCoop
      </div>
      
      <div class="row spacer-20 slider slider-post">
        <?php foreach($posts as $related_post): ?>
          <?= $this->insert('channel/call/partials/post_widget', [
              'post' => $related_post
          ]) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>