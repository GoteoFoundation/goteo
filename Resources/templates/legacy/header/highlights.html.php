<?php

use Goteo\Library\Text,
    Goteo\Model\News;

//activamos la cache
\Goteo\Core\DB::cache(true);

$highlights = News::getAll(true);

$see_more = Text::get('regular-see_more');

?>
<div id="highlights">

    <h2><a href="/news"><?php echo Text::get('regular-news'); ?></a></h2>

    <ul>
        <?php foreach ($highlights as $i => $hl) : ?>
        <li><?php echo htmlspecialchars($hl->title) ?> <?php if (!empty ($hl->url)) : ?><a href="<?php echo $hl->url; ?>" target="_blank"><?php echo $see_more; ?></a><?php endif; ?></li>
        <?php endforeach ?>
    </ul>

    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    jQuery(document).ready(function ($) {

        var ul = $('#highlights > ul'),
            lis = ul.children('li'),
            li = lis.first(),
            stopped = false;

        setInterval(function () {

            if (stopped) return;

            li = li.next();

            var m;

            if (!li.length) {
                li = lis.first();
                m = 0;
            } else {
                m = '-=' + Math.abs(li.position().top * -1);
            }

            ul.animate({
               'margin-top': m
            }, 400);

        }, 8000);

        ul.bind('mouseenter', function () {
            stopped = true;
            ul.bind('mouseleave', function () {
                stopped = false;
            });
        });

    });
    // @license-end
    </script>

</div>
