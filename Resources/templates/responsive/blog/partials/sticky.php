<!-- sticky menu -->

<?php

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
$author = !empty($author_twitter) ? ' '.$this->text('regular-by').' @'.$author_twitter.' ' : '';
$share_title = $this->post->title . $author;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');

?>



<div class="sticky-menu" data-offset-top="600" data-spy="affix">
	<div class="container-fluid">
		<ul class="list-inline text-center">
			<li class="social">
				<a class="fa fa-twitter" title="" target="_blank" href="<?= $twitter_url ?>"></a>
      			<a class="fa fa-facebook" title="" target="_blank" href="<?= $facebook_url ?>"></a>
      			<a class="fa fa-telegram" title="" target="_blank" href="https://telegram.me/share/url?url=<?= $share_url ?>&text=<?= urlencode($share_title) ?>"></a>
      			<a class="fa fa-whatsapp hidden-md hidden-lg" title="" target="_blank" href="whatsapp://send?text=<?= urlencode($share_title).' '.$share_url ?>" data-action="share/whatsapp/share" ></a>
			</li>
		</ul>
	</div>
</div>