<?php

// @todo Esto tiene que ir a un Model\Project\Media o algo así...


function __getYouTubeCode ($video, $https = false) {
    

    return '<iframe width="100%" height="100%" src="' . ($https ? 'https' : 'http') . '://www.youtube.com/embed/' . $video . '" frameborder="0" allowfullscreen></iframe>';
}

function __getVimeoCode ($id, $https = false) {
    return '<iframe src="http://player.vimeo.com/video/' . $id . '?title=0&amp;byline=0&amp;portrait=0" width="100%" height="100%" frameborder="0"></iframe>';
}

$code = '';

if (isset($this['project']->media)) {
    
    $media = trim($this['project']->media);
    
    switch (true) {
        
        case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtube\.com/watch[/\?\&\#$]#', $media)):                        
            // Video de Youtube.com            
            if ($url = parse_url($media)) {
                if (!empty($url['query'])) {
                    parse_str($url['query'], $query);
                    if (!empty($query['v'])) {
                        $code = __getYouTubeCode($query['v'], $url['scheme'] === 'https');
                    }
                }
            }
            break;                    
        
        case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtu\.be/(?<video>[^\#\&]+)#', $media, $yt)):
            // URL corta de YouTube
            $code = __getYouTubeCode($yt['video'], !empty($yt['https']));            
            break;      
        
         case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?vimeo.com/(?<video>\d+)#', $media, $vm)):
            // URL de Vimeo
            $code = __getVimeoCode($vm['video'], !empty($vm['https']));            
            break;  
        
        default:            
            // Mirar si es código HTML
            $dom = new DOMDocument();
            if ($dom->loadHTML($media)) {
                $code = $media;
            }
            
    }
    
}

?>

<?php if ($code !== ''): ?>
<div class="widget project-media">
    <?php echo $code ?>
</div>
<?php endif ?>