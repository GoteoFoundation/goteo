<?php

namespace Goteo\Model {
    
    class Project extends \Goteo\Core\Model {
        
        public        
            // Node this project belongs to
            $node,
        
            // Description
            $id,
            $name,
            $image,
            $description,
            $motivations,
            $about,
            $goals,
            $categories = array(),
            $media,
            $keywords = array(),
            $status,
            $location,
                
            // Tasks
            $tasks,
            $schedule,
            
            // Rewards
            $rewards;
        
        
    }
    
}