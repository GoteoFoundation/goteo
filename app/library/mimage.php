<?php

namespace Goteo\Library {

    use Goteo\Core\Model,
    	Goteo\Core\Exception;

	/**
	* @file classes/images.php
	* @author Ivan Vergés
	* @brief Image Manipulation class\n
	* This class provides methods to resize, convert, save & flush images\n
	* This class is used by the file functions/images.php
	*
	* @section usage Usage
	* $img = new mImage("file.jpg");\n
	* $img->set_proportional(2);
	* $img->set_quality(90);
	* $img->resize(200,100);
	* $img->flush();
	*/

	class MImage {
		protected $gd            = null;
		public $type             = null;
		public $width            = null;
		public $height           = null;
		protected $file          = '';
		protected $fallback_type = 'exception'; //'exception', 'auto', mImage class, or existing file
		protected $fallback_text = null; //null means error text, aplies on $fallback type mImage, image file or 'auto'
		protected $proportional  = 0;
		protected $last_error    = '';
		protected $quality       = 90;

		/**
		 * Constructor
		 * @param mixed $file a string containing a file, a mImage class, a GD resource
		 */
		public function __construct ($file=''){
			if(self::is_gd($file)) {
				$this->gd = $file;
				$this->width  = imagesx($file);
				$this->height = imagesy($file);
				$this->type = IMAGETYPE_PNG;
			}
			elseif($file instanceOf mImage) {
				$this->width  = $file->width;
				$this->height = $file->height;
				$this->type   = $file->type;
				$this->file   = $file->file();
				$this->gd     = $file->gd();
			}
			else $this->file($file);
		}

		/**
		 * Sets the fallback behaviour
		 * @param  mixed $type  'exception' means throwing standard Exception
		 *                      'auto' means create a white image with the error text
		 *                      others strings will return this file as a fallback (don't be checked for existence)
		 *                      mImage class will return this image on failure
		 * @param  string $text the string to put over the fall back image (cases 'auto' and mImage)
		 * @return [type]       [description]
		 */
		public function fallback($type = 'exception', $text = null) {
			if($type) $this->fallback_type = $type;
			if(isset($text)) $this->fallback_text = $text;
			return $this->fallback_type;
		}
		/**
		 * checks if is a valid GD reource
		 * @param  resource  $gd gd resorce to analyze
		 * @return boolean       true if $gd is a valid GD resource
		 */
		static function is_gd($gd) {
			if(is_resource($gd) && get_resource_type($gd) == 'gd') {
				return true;
			}
			return false;
		}

		public function file($file=''){
			if($file) {
				$this->file = $file;
			}
			return $this->file;
		}
		/**
		 * Sets the $proportional parameter
		 * @param $proportional
	     * - \b 0 => the image will be resized to the specified w/h without keeping aspect ratio
		 * - \b 1 => the image will be resized to the specified w/h keeping aspect ratio (by cropping width or height)
		 * - \b 2 => the image will be resized to the max w/h keeping aspect ratio (without cropping).
		 * */
		public function proportional($p=null) {
			if(!is_null($p)) $this->proportional = $p;
			return 			 $this->proportional;
		}
		/**
		 * Sets the quality of the returned image (usefull for JPEG images only)
		 * @param $p quality from 0 to 100
		 * */
		public function quality($p) {
			$p = (int)$p;
			if($p >=0 && $p <= 100) $this->quality = $p;
			return 					$this->quality;
		}

		/**
		 * Returns the image type
		 * */
		protected function image_type() {
			if ( ( list($width, $height, $type, $attr) = @getimagesize( $this->file ) ) === false ) {
				if ( function_exists( 'exif_imagetype' ) ) {
					$type = @exif_imagetype($this->file);
				}
			}
			$this->width  = $width;
			$this->height = $height;
			$this->type   = $type;

			return $this->type;
		}

		/**
		 * Opens a file or returns the cached file if is already processed
		 * @param $file file to open
		 * */
		public function open($file=null) {

			if(self::is_gd($this->gd)) {
				if($file && $file != $this->file) imagedestroy($this->gd);
				else return $this->gd;
			}

			if($file) $this->file = $file;
			else 	  $file = $this->file;
			$type = $this->image_type($file);

			switch ($type) {
				case IMAGETYPE_GIF :
					$this->gd = imagecreatefromgif($file);
					break;
				case IMAGETYPE_JPEG :
					$this->gd = imagecreatefromjpeg($file);
					break;
				case IMAGETYPE_PNG :
					$this->gd = imagecreatefrompng($file);
					break;
				case IMAGETYPE_WBMP :
					$this->gd = imagecreatefromwbmp($file);
					break;
				case IMAGETYPE_XBM :
					$this->gd = imagecreatefromxbm($file);
					break;
			}

			if(self::is_gd($this->gd)) {
				$this->width  = imagesx($this->gd);
				$this->height = imagesy($this->gd);
				return $this->gd;
			}
			$this->throwError("Cannot open image for file $file!");
			return false;
		}

		/**
		 * Creates a new image to process
		 * @param $copy uses imagecopy instead of imagecreatetruecolor
		 * @param $type string jpg, gif, png
		 * */
		public function create_image($width, $height, $type=null) {
			$gd = imagecreatetruecolor($width, $height);
			if(!is_null($type)) {
				if($type == 'png') 	   $type = IMAGETYPE_PNG;
				elseif($type == 'gif') $type = IMAGETYPE_GIF;
				else 				   $type = IMAGETYPE_JPEG;
			}
			else $type = $this->type;
			if($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) $this->add_transparency($gd);

			return $gd;
		}

		/*
		 * Add the transparent color to the image
		 *  */
		protected function add_transparency($gd=null) {
			if(is_null($gd)) $gd = $this->gd;
			if(!self::is_gd($gd))	    $this->throwError("New GD is not a valid resource");

			$trnprt_indx = imagecolortransparent($this->gd);
			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {
				// Get the original image's transparent color's RGB values
				$trnprt_color = @imagecolorsforindex($this->gd, $trnprt_indx);
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($gd, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				// Completely fill the background of the new image with allocated color.
				imagefill($gd, 0, 0, $trnprt_indx);
				// Set the background color for new image to transparent
				imagecolortransparent($gd, $trnprt_indx);
			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($this->type == IMAGETYPE_PNG) {
				// Turn off transparency blending (temporarily)
				imagealphablending($gd, false);
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($gd, 1, 0, 0, 127);
				// Completely fill the background of the new image with allocated color.
				imagefill($gd, 0, 0, $color);
				// Restore transparency blending
				imagesavealpha($gd, true);
			}
			return true;
		}

		/**
		 * Resizes a image, returns the processed image
		 * @param $width Width of the new image
		 * @param $height Height of the new image
		 * @param $file the orginal file
		 * */
		public function resize($width=0, $height=0) {
			$width  = (int)$width;
			$height = (int)$height;
			//open & analize the file
			if(!$this->open()) return false;

			if($width == 0 && $height == 0) {
				$width  = $this->width;
				$height = $this->height;
			}
			elseif($width == 0) {
				//calculate $width
				$width = round($this->width * $height / $this->height);
			}
			elseif($height == 0) {
				//calculate $height
				$height = round($this->height * $width / $this->width);
			}
			$new_width  = $width;
			$new_height = $height;

			$dst_x = 0;
			$dst_y = 0;
			$src_x = 0;
			$src_y = 0;
			$dst_w = $new_width;
			$dst_h = $new_height;
			$src_w = $this->width;
			$src_h = $this->height;

			if($this->proportional == 2 || $this->proportional == 3) {
				//use the one of the 2 sizes
				//mantain proportions
				if($this->width > $this->height) {
					//landscape, static size is width:
					$dst_w = $width;
					//calculate height
					$dst_h = round($this->height * $width / $this->width);
				}
				else {
					//portrait, static size is height:
					$dst_h = $height;
					//calculate height
					$dst_w = round($this->width * $height / $this->height);
				}

				if($this->proportional == 3) {
					if($dst_w > $width) {
						$dst_h = round( $dst_h * $width / $dst_w);
						$dst_w = $width;
					}
					if($dst_h > $height) {
						$dst_w = round( $dst_w * $height / $dst_h);
						$dst_h = $height;
					}
					//die("$width/$height | $dst_w/$dst_h | $src_w/$src_h");
				}

				$new_width  = $dst_w;
				$new_height = $dst_h;

				$src_x = round( ($this->width - $src_w) / 2 );
				$src_y = round( ($this->height - $src_h) / 2 );
			}
			elseif($this->proportional) {
				//mantain proportions
				$factor = $src_w/$new_width;
				if($factor > $src_h/$new_height) $factor = $src_h/$new_height;
				//si en comptes de ">"  poses "<" tens la imatge empetitida amb color de fons (negre)
				$src_w = $new_width * $factor;
				$src_h = $new_height * $factor;
				$src_x = round( ($this->width - $src_w) / 2 );
				$src_y = round( ($this->height - $src_h) / 2 );
			}

			$gd = $this->create_image($new_width, $new_height);
			if(imagecopyresampled($gd, $this->gd, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
				$this->gd = $gd;
				$this->width = $new_width;
				$this->height = $new_height;
			}

		}

		/**
		 * adds a image to be mixed on the principal
		 * @param $position_x : 'left', 'center', 'right' or a number (pixel position)
		 * @param $position_y : 'top', 'center', 'bottom' or a number (pixel position)
		 * */
		function mix_gd($gd, $position_x = 'center', $position_y = 'center', $alpha = 50) {

			if(!$this->open()) return false;
			if(!self::is_gd($gd)) $this->throwError("$gd is not a GD resource");
			$dst_x = 0;
			$dst_y = 0;
			$src_x  = 0;
			$src_y  = 0;
			$src_w  = imagesx($gd);
			$src_h  = imagesy($gd);
			if($position_x == 'center') {
				$dst_x = round( ($this->width - $src_w ) / 2);
			}
			elseif($position_x == 'right') {
				$dst_x = $this->width - $src_w ;
			}
			else {
				$dst_x = (int) $position_x;
			}
			if($position_y == 'center') {
				$dst_y = round( ($this->height - $src_h ) / 2);
			}
			elseif($position_y == 'bottom') {
				$dst_y = $this->height - $src_h;
			}
			else {
				$dst_y = (int) $position_y;
			}
			//$dest_x = (int) $dest_x;
			//$dest_y = (int) $dest_y;
			//Workaround for alpha channel
			// creating a cut resource
	        $cut = imagecreatetruecolor($src_w, $src_h);

	        // copying relevant section from background to the cut resource
	        imagecopy($cut, $this->gd, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

	        // copying relevant section from watermark to the cut resource
	        imagecopy($cut, $gd, 0, 0, $src_x, $src_y, $src_w, $src_h);

			//die("$dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha");
			return imagecopymerge($this->gd, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha);
		}
		/**
		 * adds a text to a image with imagestring method
		 * @param $text text to be added
		 * @param $html_color color of the text (in HTML code RRGGBB)
		 * @param $size size of the text
		 * */
		function add_string($text='', $color='000000', $size=2) {
			//open && analyze files
			if(!$this->open()) return false;

			list($red, $green, $blue) = sscanf($color, "%02x%02x%02x");
			$text_color = imagecolorallocate($this->gd, $red, $green, $blue);
			$font_w     = imagefontwidth($size);
			$font_h     = imagefontheight($size);
			$parts      = explode("\n", $text);
			$h          = count($parts);
			$y          = ($this->height - $font_h*$h)/2;
			foreach($parts as $i => $t) {
				$w = strlen($t);
				//centered
				$x = ($this->width - $font_w*$w)/2;
				imagestring($this->gd, $size, $x, $y,  $t, $text_color);
				$y += $font_h;
			}
		}

		/**
		 * Creates a empty fallback image (shown when the original image cannot be processed)
		 * @param $text text to write in the fallback image
		 * @param array $margins array(top, right, bottom, left)
		 * */
		public function image_from_text($text='', $font_size = 2,  $bgcolor = 'ffffff', $margins = array(1, 1, 1, 1)) {
			if(empty($text)) $text = 'NO IMAGE';
			$font_w = imagefontwidth($font_size);
			$font_h = imagefontheight($font_size);

			$parts = explode("\n", $text);
			$this->height = count($parts) * $font_h;
			$this->width = $font_w;
			foreach($parts as $line) {
				$w = strlen($line) * $font_w ;
				if($w > $this->width) $this->width = $w;
			}
			$this->width  += (int) $margins[1] + $margins[3];
			$this->height += (int) $margins[0] + $margins[2];

			//create the image
			$this->gd = imagecreatetruecolor($this->width, $this->height);
			if($bgcolor == 'transparent') {
				$bg_color = imagecolorallocate($this->gd, 1, 0, 0);
				imagecolortransparent($this->gd, $bg_color);
			}
			else {
				list($red, $green, $blue) = sscanf($bgcolor, "%02x%02x%02x");
				$bg_color = imagecolorallocate($this->gd, $red, $green, $blue);
			}
			imagefill($this->gd, 0, 0, $bg_color);

			$this->type = IMAGETYPE_PNG;

			return $this->gd;
		}

		/**
		 * save a image, $file can be a gd image or a file
		 * @param $file file or gd image
		 * */
		public function save($file) {
			$ok = false;
			switch ($this->type) {
				case IMAGETYPE_GIF :
					$ok = imagegif($this->gd, $file);
					break;
				case IMAGETYPE_JPEG :
					$ok = imagejpeg($this->gd, $file, $this->quality);
					break;
				case IMAGETYPE_PNG :
					$ok = imagepng($this->gd, $file);
					break;
				case IMAGETYPE_WBMP :
					$ok = imagewbmp($this->gd, $file);
					break;
				case IMAGETYPE_XBM :
					$ok = imagexbm($this->gd, $file);
					break;
				default : $this->throwError("Cannot save image for file $file!");
			}
			return $ok;
		}
		/**
		 * streams a image
		 * @param $file original file
		 * */
		public function flush($exit = true) {
			$gd = $this->gd();

			header("Content-type: " . image_type_to_mime_type($this->type));
			header('Content-Disposition: inline; filename="' . str_replace("'", "\'", basename($this->file)) . '"');

			switch ($this->type) {
				case IMAGETYPE_GIF :
					imagegif($gd);
					break;
				case IMAGETYPE_JPEG :
					imagejpeg($gd, null, $this->quality);
					break;
				case IMAGETYPE_PNG :
					imagepng($gd);
					break;
				case IMAGETYPE_WBMP :
					imagewbmp($gd);
					break;
				case IMAGETYPE_XBM :
					imagexbm($gd);
					break;
				default : $this->throwError("Cannot output image for file {$this->file}!");
			}
			if($exit) exit;
		}

		public function gd() {
			if(self::is_gd($this->gd)) return $this->gd;
			else $this->throwError("Not a valid GD to return!");
		}

		public function destroy() {
			if(self::is_gd($this->gd)) imagedestroy($this->gd);
		}
		/**
		 * Passthru a file with content-type, name
		 * @param  [type] $file [description]
		 * @return [type]       [description]
		 */
		static function stream($file, $exit = true) {
			//redirection if is http stream
			if(substr($file, 0 , 7) == 'http://' || substr($file, 0 , 8) == 'https://') {
				header("Location: $file");
			}
			else {
				list($width, $height, $type, $attr) = @getimagesize( $file );
				if(!$type && function_exists( 'exif_imagetype' ) ) {
					$type = exif_imagetype($file);
				}
				if($type) {
					 $type = image_type_to_mime_type($type);
				}
				else {
					$type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
					die($type);
					if($type == 'jpg') $type = "jpeg";
					if(!in_array($type, array('jpeg', 'png', 'gif'))) die("file $type not image!");
					$type = "image/$type";
				}

				header("Content-type: " . $type);
				header('Content-Disposition: inline; filename="' . str_replace("'", "\'", basename($file)) . '"');
				header("Content-Length: " . @filesize($file));
				readfile($file);
			}
			if($exit) exit;
		}
		/**
		 * Show the last error
		 */
		function getError() {
			return $this->last_error;
		}
		public function has_errors() {
			return !empty($this->last_error);
		}
		/**
		 * throw errors
		 */
		function throwError($msg='') {
			$this->last_error = $msg;
			$text = $msg;
			if(!is_null($this->fallback_text)) $text = $this->fallback_text;
			if($this->fallback_type == 'exception') {
				throw new Exception($msg);
			}
			elseif($this->fallback_type == 'auto') {
				//creates a empty image
				$this->image_from_text($text);
				$this->add_string($text);
			}
			elseif(is_string($this->fallback_type)) {
				//try to open the image
				$this->open($this->fallback_type);
				if($text) $this->add_string($text);
			}
			elseif($this->fallback_type instanceOf mImage) {
				//returns the current image
			}
			else throw new Exception($msg);
		}
	}

}