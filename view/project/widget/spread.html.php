<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$user    = $_SESSION['user'];
$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$url = SITE_URL . '/widget/project/' . $project->id;
$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="250px"></iframe>';
$widget_code_investor = '<iframe frameborder="0" height="380px" src="'.$url.'/invested/'.$user->id.'" width="250px"></iframe>';

?>
<div class="widget project-spread">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>
    
    <div class="widget projects">
   		 
          <div class="left">
              <div class="subtitle" id="s1">
                <span class="primero">Difunde este proyecto</span>
                <span class="segundo"><?php echo Text::get('project-spread-widget')?></span>        
              </div>
         	  
              <!-- meter aquí llamada a vista + código embed -->
              <div>
			  <?php
        
                    // el proyecto de trabajo
                    echo new View('view/project/widget/project.html.php', array(
                    'project'   => $project));
                ?>
              </div>
              <div id="widget-code">
                <div class="wc-embed">CÓDIGO EMBED</div> 
                <textarea onclick="this.focus();this.select()" readonly="readonly">
                    <?php echo htmlentities($widget_code); ?>       
                </textarea>       
              </div>
              <!-- fin de meter aquí llamada a vista + código embed -->
          </div>
            
          <div class="right">
             <div class="subtitle" id="s2">
                 <span class="primero">Deja saber a tu red que</span>    
                 <span class="segundo"><?php echo Text::get('project-share-header')?></span>
	        </div>
            
	   	  	<!-- meter aquí llamada a vista + código embed -->  
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
                <div class="wc-embed">CÓDIGO EMBED</div> 
                <textarea onclick="this.focus();this.select()" readonly="readonly">
                    <?php echo htmlentities($widget_code_investor); ?>       
                </textarea>       
    		</div>          
             <!-- fin de meter aquí llamada a vista + código embed -->
            
          </div>
          
    </div>
        
</div>