<?php 

$author=$this->post->getAuthor();


$share_url = $this->get_url() . '/blog/' . $this->post->id;

$author_twitter = str_replace(
                        array(
                            'https://',
                            'http://',
                            'www.',
                            'twitter.com/',
                            '#!/',
                            '@'
                        ), '', $this->post->user->twitter);
$author_share = !empty($author_twitter) ? ' '.$this->text('regular-by').' @'.$author_twitter.' ' : '';
$share_title = $this->post->title . $author_share;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');

?>

<div class="section post-header">
	<div class="image">
		<?php if($this->post->header_image): ?>
			<img src="<?= $this->post->header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="<?= $this->post->header_image->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="<?= $this->post->header_image->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="<?= $this->post->header_image->getLink(750, 450, true) ?>" class="img-responsive visible-xs">

		<?php else: ?>
			<img src="/assets/img/blog/header_default.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="/assets/img/blog/header_default.png" width="1400" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="/assets/img/blog/header_default.png" width="1051" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="/assets/img/blog/header_default.png" width="750"  class="img-responsive header-default visible-xs">
		<?php endif; ?>

	</div>
	<div class="info">
		<div class="container">
			<h1>
				<?= $this->post->title ?>
			</h1>
			<div class="subtitle">
				<?= $this->post->subtitle ?>
			</div>
			<ul class="info-extra list-inline">
				<li>
					<img src="<?= $author->avatar->getLink(64, 64, true); ?>" ?>
				</li>
				<li>
					<span class="author">
					<?= $this->text('regular-by').' '?> <strong><?= $author->name ?></strong>
					</span>
					<span class="date">
						<?= date_formater($this->post->date) ?>	
					</span>					
				<li>
				<li class="social hidden-xs">
					<a class="fa fa-twitter" title="" target="_blank" href="<?= $twitter_url ?>"></a>
          			<a class="fa fa-facebook" title="" target="_blank" href="<?= $facebook_url ?>"></a>
          			<a class="fa fa-telegram" title="" target="_blank" href="https://telegram.me/share/url?url=<?= $share_url ?>&text=<?= urlencode($share_title) ?>"></a>
          			<!--<a class="fa fa-whatsapp" title="" target="_blank" href=""></a>-->
				</li>
			</div>
		</div>
	</div>
</div>