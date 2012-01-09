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
		public $max_multiplier = 32; //màxim multiplicador de tamanys
		public $w_size = 32; //tamaño (width) de la imagen mínima en pixeles
		public $h_size = 32; //tamaño (height) de la imagen mínima en pixeles
		public $w_padding = 0;
		public $h_padding = 0;
		public $show_title = true; //enseña o no el titulo del widget (publi goteo)
		/**
         *
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        public function __construct ($id, $all_avatars=true, $with_title = true) {
			if($this->project = Project::get($id)) {
				$this->show_title = $with_title;
				$this->investors = $this->project->investors;

				$avatars = array();
				foreach($this->investors as $i) {
					if($i->avatar->id != 1 || $all_avatars)
						$avatars[$i->user] = $i->amount;

				}
				$this->avatars = self::pondera($avatars,$this->max_multiplier);

				//arsort($this->avatars);

				$keys = array_keys( $this->avatars );
				shuffle( $keys );
				$this->avatars = array_merge( array_flip( $keys ) , $this->avatars );
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
		 * Retorna les imatges i contingut en html
		 *
		 * $num_icons: el numero de icones per fila del widget
		 * */
		public function html_content($num_icons = 19) {
			$ret = array();
			foreach($this->avatars as $user => $mult) {
				$style = '';
				$w = $this->w_size;
				$h = $this->h_size;

				$src = SITE_URL . '/image/1/'."$w/$h";
				if($this->investors[$user]->avatar instanceof \Goteo\Model\Image)
					$src = $this->investors[$user]->avatar->getLink($w,$h, true);



				$img = '<a href="'.SITE_URL.'/user/profile/'.$user.'"><img'.$style.' src="' . $src . '" alt="'.$this->investors[$user]->name.'" title="'.$this->investors[$user]->name.'" /></a>';

				for($i = 0; $i<$mult+1; $i++) {

					$ret[] = $img;
					$total = count($ret);

					if($num_icons > 14) {
						//final de 1a fila, 2a columna
						if(in_array($total , array($num_icons + 1, $num_icons * 2 - 10, $num_icons * 3 - 21))) {
							$ret[] = '<div class="a"></div>';
						}

						if(in_array($total , array($num_icons + 3, $num_icons * 2 - 8, $num_icons * 3 - 19))) {
							//1 columnes despres
							$ret[] = '<div class="b"></div>';
						}
					}
					else {
						if(in_array($total , array($num_icons))) {
							$ret[] = '<div class="a"></div>';
						}

						if(in_array($total , array($num_icons + 2, $num_icons + 4, $num_icons + 6))) {
							if($total != $num_icons + 6) $ret[] = '<div class="b"></div><div class="a"></div>';
							else $ret[] = '<div class="b"></div>';
						}
					}
					if($num_icons > 17) {
						if(in_array($total , array($num_icons * 5 - 32, $num_icons * 6 - 48, $num_icons * 7 - 64))) {
							$ret[] = '<div class="c"></div>';
						}
					}
					elseif($num_icons > 14) {
						if($total == $num_icons * 5 - 33) {
							$ret[] = '<div class="c"></div><div class="c"></div><div class="c"></div>';
						}
					}
					else {
						if($total == $num_icons * 2 + 7) {
							$ret[] = '<div class="c"></div><div class="c"></div><div class="c"></div>';
						}
					}
				}
			}

			return $ret;
		}

		/**
		 * Muestra un div con las imagenes en pantalla.
		 * @param type int	$width
		 * @param type int	$height
		 *
		*/
		public function html($width = 608) {

			//cal que siguin multiples del tamany
			$wsize = $this->w_size + $this->w_padding * 2;
			$hsize = $this->h_size + $this->h_padding * 2;
			//num icones per fila
			$num_icons = floor($width / $wsize);
			//tamany minim
			if($num_icons < 15) $num_icons = 14;
			//amplada efectiva
			$width = $wsize * $num_icons;

			$style = "<style type=\"text/css\">";

            // estatico
			$style .= "div.wof {font-size: 12px;color: #58595b;font-family: \"Liberation Sans\", Helvetica, \"Helvetica Neue\", Arial, Geneva, sans-serif;background-color: transparent;display:inline-block;height:auto}";
			$style .= "div.wof>div.ct {position:relative;clear:both}";
			$style .= "div.wof a,div.wof a:link,div.wof a:visited,div.wof a:active,div.wof a:hover {text-decoration:none;color: #58595b}";
			$style .= "div.wof h2 { display:block; background:url('/view/css/project/tagmark_green.png') no-repeat right top; font-size: 14px; color:#fff;padding:0;margin: 0;text-transform:uppercase}";
			$style .= "div.wof h2 a,div.wof h2 a:link,div.wof h2 a:visited,div.wof h2 a:active,div.wof h2 a:hover {display:block;height:21px;overflow:hidden; background:#19b5b3;color:#fff;padding: 7px 0 0 0}";
			$style .= "div.wof h2 span {display:block;float:left;width:30px;height:28px;background:url('/view/css/project/tagmark_green.png') no-repeat -1px -2px}";
			$style .= "div.wof>div.ct>div.i h3 {color:#95268D;font-size:64px;font-weight:bold;text-align:center;padding:0;margin:0}";
			$style .= "div.wof>div.ct>div.i h3 a {color:#95268D}";
			$style .= "div.wof>div.ct>div.i h3>img {vertical-align:top;padding:11px 0 0 0}";
			$style .= "div.wof>div.ct>div.i p {color:#58595c;font-size:14px;text-align:right;padding:0 12px 0 0;margin:0}";
			$style .= "div.wof>div.ct>div.i.b h3 {color:#0b4f99}";
			$style .= "div.wof>div.ct>div.i.b h3 a {color:#0b4f99}";
			$style .= "div.wof>div.ct>div.i.b p {color:#0b4f99;text-transform:uppercase;text-align:center}";
			$style .= "div.wof>div.ct>div.i.b p a {color:#0b4f99}";
			$style .= "div.wof>div.ct>div.i.c h3 {color:#1db3b2;font-size:32px;line-height:32px;text-transform:uppercase;text-align:left;padding:4px 0 4px 4px}";
			$style .= "div.wof>div.ct>div.i.c h3 a{color:#1db3b2}";
			$style .= "div.wof>div.ct>div.i.c p {color:#58595c;;text-align:left;padding:0 0 0 4px}";
			$style .= "div.wof>div.ct>div.i.c>div.c1 p {padding:10px;font-size:10px;line-height:10px;text-align:center}";
			$style .= "div.wof>div.ct>div.i.c>div.c1 p img {padding:0 0 4px 0}";
			$style .= "div.wof>div.ct>div.i.c>div.c1 a {color:#1db3b2}";

            // dinamico
			$style .= "div.wof>div.ct>a>img {border:0;width:{$this->w_size}px;height:{$this->h_size}px;display:inline-block;padding:{$this->h_padding}px {$this->w_padding}px {$this->h_padding}px {$this->w_padding}px}";
			$style .= "div.wof>div.ct>div.a {display:inline-block;width:" . ($wsize * 5) . "px;height:" . $hsize . "px}";
			$style .= "div.wof>div.ct>div.b {display:inline-block;width:" . ($wsize * 8) . "px;height:" . $hsize . "px}";
			$style .= "div.wof>div.ct>div.c {display:inline-block;width:" . ($wsize * ($num_icons < 18 ? $num_icons : 17)) . "px;height:" . $hsize . "px}";
			$style .= "div.wof>div.ct>div.i {overflow:hidden;padding:0;margin:0;position:absolute;height:" . ($hsize * 3) . "px;background:#fff;left:" . ($num_icons < 15 ? "0" : $wsize) . "px;top:" . $hsize . "px}";
			$style .= "div.wof>div.ct>div.b.i {left:" . ($wsize * ($num_icons <15 ? 6 : 7)) . "px;top:" . $hsize . "px}";
			$style .= "div.wof>div.ct>div.c.i {left:" . ($num_icons < 18 ? "0" : $wsize) . "px;top:" . ( $hsize * 5) . "px}";
			$style .= "div.wof>div.ct>div.c>div.c1 {float:left;height:" . ($wsize * 3) . "px;width:" . ($wsize * 3) . "px}";
			$style .= "div.wof>div.ct>div.c>div.c2 {float:right;height:" . ($wsize * 3) . "px;width:" . ($wsize * ($num_icons < 18 ? $num_icons - 3 : 14)) . "px}";
			$style .= "</style>";

			$title = '<h2><span></span><a href="'.SITE_URL.'/project/'.$this->project->id.'" style="width:'.($width - 50).'px;">'.GOTEO_META_TITLE.'</a></h2>';

			//num finançadors
			$info = '<div class="a i"><h3><a href="'.SITE_URL.'/project/'.$this->project->id.'">' . count($this->project->investors) . '</a></h3><p><a href="'.SITE_URL.'/project/'.$this->project->id.'">'.Text::get('project-view-metter-investors').'</a></p></div>';

			//financiacio, data
			$info .= '<div class="b i"><h3><a href="'.SITE_URL.'/project/'.$this->project->id.'">' . number_format($this->project->invested,0,'',','). '<img src="'.SITE_URL.'/view/css/euro/violet/xxl.png" alt="&euro;"></a></h3>';
			$info .= '<p><a href="'.SITE_URL.'/project/'.$this->project->id.'">' . Text::get('project-view-metter-days') . " {$this->project->days} " . Text::get('regular-days') .'</a></p></div>';

			//impulsores, nom, desc
			$info .= '<div class="c i">';
			$info .= '<div class="c1"><p><a href="'.SITE_URL.'/user/'.$this->project->owner.'"><img src="'.SITE_URL.'/image/'.$this->project->user->avatar->id.'/56/56/1" alt="'.$this->project->user->name.'" title="'.$this->project->user->name.'"><br />' . Text::get('regular-by') . ' '  . $this->project->user->name . '</a></p></div>';
			$info .= '<div class="c2"><h3><a href="'.SITE_URL.'/project/'.$this->project->id.'">' . $this->project->name . '</a></h3><p><a href="'.SITE_URL.'/project/'.$this->project->id.'">'.$this->project->subtitle.'</a></p></div>';
			$info .= '</div>';
			return $style . '<div class="wof" style="width:'.$width.'px;">' . ($this->show_title ? $title : '') . '<div class="ct">' . $info . implode("",$this->html_content($num_icons)).'</div></div>';
		}
    }
}
