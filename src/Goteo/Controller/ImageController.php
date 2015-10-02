<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Goteo\Core\Error;
use Goteo\Library\Cacher;
use Goteo\Model;

class ImageController extends \Goteo\Core\Controller {
    //some predefined sizes
    static private $sizes = array(
            'icon' => '16x16',
            'tiny' => '32x32',
            'thumb' => '56x56',
            'small' => '128x128',
            'medium' => '192x192',
            'large' => '512x512',
            'big' => '1024x1024',
            'iconc' => '16x16c',
            'tinyc' => '32x32c',
            'thumbc' => '56x56c',
            'mediumc' => '192x192c',
            'largec' => '512x512c',
            'bigc' => '1024x1024c'
        );

    public function indexAction($params = '', $filename = '') {
        //check if predefined size
        if(self::$sizes[$params]) {
            $params = self::$sizes[$params];
        }
        //  $width = 200, $height = 200, $crop = false
        if (preg_match('/(\d+)x(\d+)([c]?)/', $params, $matches)) {

            $width = $matches[1];
            $height = $matches[2];
            $crop = ($matches[3] == 'c');
        }
        else {
            $width = 192;
            $height = 20;
            $crop = false;
        }
        // die("{$width}  {$height} {$crop} {$filename}");

        $image = new Model\Image;
        $image->setCache(new Cacher());
        $image->name = $filename;
        $data = $image->display($width, $height, $crop);
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $data);
        $length = strlen($data);

        $response = new Response($data, Response::HTTP_OK, [ 'Content-Type' => $mime, 'Content-Length' => $length ]);

        if($image->error === 'not_found') {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else {
            // Cache-Control
            // 30days (60sec * 60min * 24hours * 30days)
            $response->setMaxAge(2592000);
            $response->setPublic();
        }

        return $response;

    }

    public function oldIndexAction($id, $width = 200, $height = 200, $crop = false) {
        return new RedirectResponse('/img/' . $width . 'x' . $height . ($crop ? 'c' : '') . '/' . $id);
    }
}

