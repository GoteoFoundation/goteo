<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$story = $this['story'];

if(!empty($story->post))
    $link_title='/blog/'.$story->post;
else if(!empty($story->url))
    $link_title=$story->url;
else
    $link_title='/project/'.$story->project->id;
if(empty($story->url))
    $link_background='/project/'.$story->project->id;
else
    $link_background=$story->url;

$open_tags='<a href="/blog/'.$story->open_tags_post.'">'.$story->open_tags_name.'</a>';

?>
<div class="stories-banner<?php if (!empty($story->project)) echo ' activable'; ?>"<?php if ($story->image instanceof \Goteo\Model\Image) : ?> style="background: url('<?php echo $story->image->getLink(940, 385, true); ?>'); background-repeat: no-repeat; background-size:940px 383px;"<?php endif; ?>>
    <a href="<?php echo $link_background;?>" class="expand" target="_blank"></a>
    <div class="title_story"><strong><?php echo Text::get('home-stories-header').': '?></strong><?php echo $open_tags; ?></div>
        <div class="info">
            <a href="<?php echo $link_title;?>" target="_blank">
                <div class="info_title"><?php echo htmlspecialchars($story->title); ?></div>
                <div class="review"><?php echo htmlspecialchars($story->review); ?></div>
            </a>
            <div class="line"></div>
            <div class="description"><blockquote>
                <?php
                    echo htmlspecialchars($story->description).'</blockquote>. ';
                    echo Text::get('stories-user-project',
                        '<a href="/user/profile/'.$story->project->user->id.'" target="_blank">'.htmlspecialchars($story->project->user->name).'</a>', '<a href="/project/'.$story->project->id.'" target="_blank">'.htmlspecialchars($story->project->name).'</a>'
                    );
                ?>
            </div>
        </div>
        <div class="info_extra">
            <span class="cofinanciadores"><?php echo  mb_strtoupper(Text::get('project-view-metter-investors'));?></span> <strong class="ncofinanciadores"><?php echo $story->project->num_investors;?></strong>
            <span class="obtenido"><span><?php echo  mb_strtoupper(Text::get('project-view-metter-got'));?></span><strong><?php echo \amount_format($story->project->amount); ?></strong></span>
        </div>
    </div>
