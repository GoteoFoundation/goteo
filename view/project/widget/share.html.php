<?php
$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$share_title = $project->name;

$share_url = 'http://goteo.org/project/' . $project->id;
$facebook_url = 'http://facebook.com/sharer.php?u=' . rawurlencode($share_url) . '&t=' . rawurlencode($share_title . ' | Goteo.org');
$twitter_url = 'http://twitter.com/home?status=' . rawurlencode($share_title . ': ' . $share_url . ' #Goteo');

?>
<div class="widget project-share">    
    </script>
    <h<?php echo $level ?> class="title">Comparte este proyecto</h<?php echo $level ?>>        
    <ul>
        <li class="twitter"><a target="_blank" href="<?php echo htmlspecialchars($twitter_url) ?>">Twitter</a></li>
        <li class="facebook"><a target="_blank" href="<?php echo htmlspecialchars($facebook_url) ?>">Facebook</a></li>        
        <li onclick="$(this).children('input').focus(); return false;" class="url"><span>URL: </span> <input type="text" onfocus="this.select();" readonly="readonly" size="35" value="<?php echo htmlspecialchars($share_url) ?>" /></li>
    </ul>
</div>