<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$user    = $_SESSION['user'];
$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$lsuf = (LANG != 'es') ? '?lang='.LANG : '';

$url = SITE_URL . '/widget/project/' . $project->id;
$widget_code = Text::widget($url . $lsuf);
$widget_code_investor = Text::widget($url.'/invested/'.$user->id.'/'.$lsuf);
?>
<div class="widget project-spread">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>
    
    <div class="widget projects">
   		 
          <div class="left">
              <div class="subtitle" id="s1">
                <span class="primero"><?php echo Text::get('project-spread-pre_widget')?></span>
                <span class="segundo"><?php echo Text::get('project-spread-widget')?></span>        
              </div>
         	             
              <div>
			  <?php
        
                    // el proyecto de trabajo
                    echo new View('view/project/widget/project.html.php', array(
                    'project'   => $project));
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
                    echo new View('view/project/widget/project.html.php', array(
                    'project'   => $project,
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