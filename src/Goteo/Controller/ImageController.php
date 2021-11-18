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

use Goteo\Core\Controller;
use Goteo\Library\Cacher;
use Goteo\Model;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller {

    const CACHE_MAX_AGE_DAYS = 60 * 60 * 24 * 30; // 30 DAYS

    static private $predefinedSizes = array(
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
        if (self::$predefinedSizes[$params]) {
            $params = self::$predefinedSizes[$params];
        }

        if (preg_match('/(\d+)x(\d+)([c]?)/', $params, $matches)) {
            $width = $matches[1];
            $height = $matches[2];
            $crop = ($matches[3] == 'c');
        } else {
            $width = 192;
            $height = 20;
            $crop = false;
        }

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
            $response->setMaxAge(self::CACHE_MAX_AGE_DAYS);
            $response->setPublic();
        }

        return $response;
    }

    public function oldIndexAction($id, $width = 200, $height = 200, $crop = false) {
        return new RedirectResponse('/img/' . $width . 'x' . $height . ($crop ? 'c' : '') . '/' . $id);
    }
}
