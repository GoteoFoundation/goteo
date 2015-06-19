<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

$user    = $_SESSION['user'];
$project = $vars['project'];
$the_project = Project::getWidget($project);

$level = (int) $vars['level'] ?: 3;

$lsuf = (LANG != 'es') ? '?lang='.LANG : '';
$URL = \SITE_URL;

$url = $URL . '/widget/project/' . $project->id;
$widget_code = Text::widget($url . $lsuf);
$widget_code_investor = Text::widget($url.'/invested/'.$user->id.'/'.$lsuf);

$author_twitter = str_replace(
                        array(
                            'https://',
                            'http://',
                            'www.',
                            'twitter.com/',
                            '#!/',
                            '@'
                        ), '', $project->user->twitter);
$author = !empty($author_twitter) ? ' '.Text::get('regular-by').' @'.$author_twitter : '';
$share_title = Text::get('project-spread-social', $project->name . $author);
if (!\Goteo\Application\Config::isMasterNode())
    $share_title = str_replace ('#goteo', '#'.strtolower (NODE_NAME), $share_title);
$share_url = $URL . '/project/'.$project->id;
$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url);
?>
<div class="widget project-spread">

    <h<?php echo $level ?> class="title"><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>

        <ul class="share-goteo">
            <li class="twitter"><a href="<?php echo htmlspecialchars($twitter_url) ?>" target="_blank"><?php echo Text::get('spread-twitter'); ?></a></li>
            <li class="facebook"><a href="<?php echo htmlspecialchars($facebook_url) ?>" target="_blank"><?php echo Text::get('spread-facebook'); ?></a></li>
        </ul>
        <br clear="both" />

        <div class="widget projects">

          <div class="left">
              <div class="subtitle" id="s1">
                <span class="primero"><?php echo Text::get('project-spread-pre_widget')?></span>
                <span class="segundo"><?php echo Text::get('project-spread-widget')?></span>
              </div>

              <div>
			  <?php

                    // el proyecto de trabajo
                    echo View::get('project/widget/project.html.php', array(
                    'project'   => $the_project));
                ?>
              </div>

              <div id="widget-code">
                <div class="wc-embed" onclick="$('#widget_code').focus();$('#widget_code').select()"><?php echo Text::get('project-spread-embed_code'); ?></div>
                <textarea id="widget_code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
              </div>

          </div>

          <div class="right">
             <div class="subtitle" id="s2">
                 <span class="primero"><?php echo Text::get('project-share-pre_header')?></span>
                 <span class="segundo"><?php echo Text::get('project-share-header')?></span>
	        </div>


            <div>
				<?php

                    // el proyecto de trabajo
                    echo View::get('project/widget/project.html.php', array(
                    'project'   => $the_project,
                    'investor'  => $user
                    ));
                ?>
            </div>

            <div>
                <div id="widget-code">
	                <div class="wc-embed" onclick="$('#investor_code').focus();$('#investor_code').select()"><?php echo Text::get('project-spread-embed_code'); ?></div>
              		<textarea id="investor_code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code_investor); ?></textarea>
    			</div>


          </div>

   		 </div>
    </div>

</div>
