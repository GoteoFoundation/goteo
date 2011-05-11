<?php

namespace Goteo\Model\Project {
    
    use \DOMDocument;

    class Media {
        
        public 
            $project,
            $url = '';
        
        public function __construct ($url) {
            $this->url = $url;
        }
        
        protected static function getYouTubeCode ($video, $https = false) {    
            
            return '<iframe width="100%" height="100%" src="' 
                   . ($https ? 'https' : 'http') . '://www.youtube.com/embed/' 
                   . $video . '" frameborder="0" allowfullscreen></iframe>';
            
        }
        
        protected static function getVimeoCode ($id, $https = false) {
            
            return '<iframe src="http://player.vimeo.com/video/' 
                   . $id . '?title=0&amp;byline=0&amp;portrait=0" width="100%" height="100%" frameborder="0"></iframe>';
            
        }
        
        public function getEmbedCode () {
            
            $code = '';
            
            if (!empty($this->url)) {
                
                switch (true) {
                    
                    case trim($this->url) === '':
                        break;
        
                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtube\.com/watch[/\?\&\#$]#', $this->url)):                        
                        // Video de Youtube.com            
                        if ($url = parse_url($this->url)) {
                            if (!empty($url['query'])) {
                                parse_str($url['query'], $query);
                                if (!empty($query['v'])) {
                                    $code = static::getYouTubeCode($query['v'], $url['scheme'] === 'https');
                                }
                            }
                        }
                        break;                    

                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtu\.be/(?<video>[^\#\&]+)#', $this->url, $yt)):
                        // URL corta de YouTube
                        $code = static::getYouTubeCode($yt['video'], !empty($yt['https']));            
                        break;      

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?vimeo.com/(?<video>\d+)#', $media, $vm)):
                        // URL de Vimeo
                        $code = static::getVimeoCode($vm['video'], !empty($vm['https']));            
                        break;  

                    default:            
                        // Mirar si es código HTML
                        $dom = new DOMDocument();
                        if ($dom->loadHTML($media)) {
                            $code = $media;
                        }

                }
                
                
            }
            
            return $code;
            
        }
        
        public function __toString () {
            return $this->url;
        }
        
        

    }
    
}