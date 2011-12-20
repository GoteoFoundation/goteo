<?php

/*
 * Este modelo es para la
 */
namespace Goteo\Library {

	use Goteo\Model\Invest,
        Goteo\Model\Project,
        Goteo\Core\Exception;

    class WallFriends {
		public $project = '';
		public $investors = array();
		public $avatars = array(); //listado de avatars válidos con su multiplicador de tamaño
		public $max_multiplier = 4; //màxim multiplicador de tamanys
		public $w_size = 30; //tamaño (width) de la imagen mínima en pixeles
		public $h_size = 30; //tamaño (height) de la imagen mínima en pixeles
		public $w_padding = 1;
		public $h_padding = 1;
		/**
         *
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        public function __construct ($id, $all_avatars=true) {
			if($this->project = Project::get($id)) {
				$this->investors = $this->project->investors;

				$avatars = array();
				foreach($this->investors as $i) {
					if($i->avatar != 1 || $all_avatars)
						$avatars[$i->user] = $i->amount;

				}
				$this->avatars = self::pondera($avatars,$this->max_multiplier);
				arsort($this->avatars);
				//print_r($this->project);die;

			}
			else {
				//quizá otro mensaje de error?
                throw new \Goteo\Core\Error('404', Text::html('fatal-error-project'));
			}

        }

        /**
         * Pondera un array amb valor minim 1 i valor maxim ?
         * */
        public static function pondera($array = array(),$max_multiplier = 4) {
			$new = array();
			$min = min($array);
			$max = max($array);

			foreach($array as $i => $n) {
				//minim 1, màxim el que toqui
				$num = $n/$min;
				//apliquem alguna funcio que "comprimeixi" els resultats
				$num = round(sqrt($num));
				if($num > $max_multiplier) $num = $max_multiplier;
				$new[$i] = $num;
			}
			return $new;
		}

		/**
		 * Muestra un div con las imagenes en pantalla.
		 * @param type int	$width
		 * @param type int	$height
		 *
		*/
		public function html($width = 200, $mode = 0) {
			$ret = array();
			foreach($this->avatars as $user => $mult) {
				$style = '';
				$w = $this->w_size;
				$h = $this->h_size;

				if($mode == 1 && $mult!=1) {
					$w *= $mult;
					$h *= $mult;
					$style = ' style="width:'.$w.'px;height:'.$h.'px;"';
				}

				$img = '<a href="'.SITE_URL.'/user/profile/'.$user.'"><img'.$style.' src="'.SITE_URL.'/image/'.$this->investors[$user]->avatar.'/'.$w.'/'.$h.'/1" alt="'.$this->investors[$user]->name.'" title="'.$this->investors[$user]->name.'" /></a>';

				if($mode == 0) {
					for($i = 0; $i<$mult-1; $i++) $img .= $img;
				}

				$ret[] = $img;
			}

			//recalcular width i height
			if($this->max_multiplier * $this->w_size > $width) $width = $this->max_multiplier * $this->w_size > $width;
			//cal que siguin multiples del tamany
			$wsize = $this->w_size + $this->w_padding * 2;
			$width = $wsize * round($width / $wsize);

			$style = "<style type=\"text/css\">";
			$style .= "div.wof {font-size: 12px;color: #58595b;font-family: \"Liberation Sans\", Helvetica, \"Helvetica Neue\", Arial, Geneva, sans-serif;background-color: #deddde;display:inline-block;width:{$width}px;height:auto;}";
			$style .= "div.wof img {border:0;width:{$this->w_size}px;height:{$this->h_size}px;float:left;display:inline-block;padding:{$this->h_padding}px {$this->w_padding}px {$this->h_padding}px {$this->w_padding}px;}";
			$style .= "div.wof a,div.wof a:link,div.wof a:visited,div.wof a:active,div.wof a:hover {text-decoration:none;color: #58595b;}";
			$style .= "div.wof h2 { display:block; background:url(".SITE_URL."/view/css/project/tagmark_green.png) no-repeat right top; font-size: 14px; color:#fff;padding:0;margin: 0;}";
			$style .= "div.wof h2 a,div.wof h2 a:link,div.wof h2 a:visited,div.wof h2 a:active,div.wof h2 a:hover {display:block;width:" . ($width - 50) . "px;height:21px;overflow:hidden; background:#19b5b3;color:#fff;padding: 7px 0 0 0}";
			$style .= "div.wof h2 span {display:block;float:left;width:30px;height:28px;background:url(".SITE_URL."/view/css/project/tagmark_green.png) no-repeat -1px -7px;}";
			$style .= "div.wof>div {clear:both;}";
			$style .= "</style>";
			$title = '<h2><span></span><a href="'.SITE_URL.'/project/'.$this->project->id.'">'.$this->project->name.'</a></h2>';
			return $style . '<div class="wof">' . $title . '<div>' . implode("",$ret).'</div></div>';
		}
    }
}
