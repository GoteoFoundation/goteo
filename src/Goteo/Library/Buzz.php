<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library {

    use Goteo\Model;

    /*
     * Clase para cosas de actividad en redes sociales
     * en principio, para saccar el buz de una convocatoria (recibiendo el query de busqueda)
     */
    class Buzz {

        protected static
		$twitter_id = OAUTH_TWITTER_ID,
		$twitter_secret = OAUTH_TWITTER_SECRET;

        /**
         * Metodo para obtener buzz de twitter
         * @param string $query; busqueda en twitter sin codificar
         * @param bool $matchusers; si buscamos los autores entre los usuarios de goteo
         * @return mixed items
         */
        public static function getTweets( $query , $matchusers = false) {

            $list = array();

            $doReq = true;
            // primero miramos si tenemos cache y de cuando es
            $filename = 'buzz_'.md5($query).'.json';
            // echo "BUZZ[$filename]";die;
            $cache = new Cacher('buzz');
            $data_file = $cache->getFile($filename);;
            if (file_exists($data_file)) {
                $fMod = date ("Y-m-d H:i:s", filemtime($data_file));
                $fNow = date ("Y-m-d H:i:s");
                $fMin = ($fNow - $fMod) / 60;
                if ($fMin < 31) {
                    // leemos el archivo de datos
                    $result = \file_get_contents($data_file);
                    $doReq = false;
                }
            }

            // si nos piden que limpiemos la cche, forzamos la consulta
            if (isset($_GET['clrbuzzcache'])) $doReq = true;

            if ($doReq) {
                // autenticación (application-only)
                if (empty(self::$twitter_id) || empty(self::$twitter_secret)) {
                    throw new Exception("Faltan credenciales para twitter, OAUTH_TWITTER_ID y OAUTH_TWITTER_SECRET en config.php");
                }
                $credentials = base64_encode(rawurlencode(self::$twitter_id).':'.rawurlencode(self::$twitter_secret));
                $grantstr = "grant_type=client_credentials";

                // solicitar el bearer token
                $authUrl = "https://api.twitter.com/oauth2/token";
                $cAuth = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt( $cAuth, CURLOPT_URL, $authUrl);

                curl_setopt( $cAuth, CURLOPT_POST, 1);
                curl_setopt( $cAuth, CURLOPT_HTTPHEADER, array(
                    'Authorization: Basic '.$credentials,
                    'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                    'Content-Length: '.strlen($grantstr))
                );
                curl_setopt( $cAuth, CURLOPT_POSTFIELDS, $grantstr);

                // verificar SSL
                curl_setopt( $cAuth, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt( $cAuth, CURLOPT_SSL_VERIFYHOST, true);
                curl_setopt( $cAuth, CURLOPT_CAINFO, '/usr/share/ncat/ca-bundle.crt');

                curl_setopt( $cAuth, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $cAuth, CURLOPT_USERAGENT, 'Goteo.org Buzz Getter');

                $authRes = curl_exec( $cAuth );
                /*
                echo '<hr />'.$authRes.'<hr />';
                if(!curl_errno($cAuth)) {
                    $authInfo = curl_getinfo($cAuth);
                    die(\trace($authInfo));
                }
                 */
                curl_close( $cAuth );

                // parsear respuesta
                $authRet =json_decode($authRes);
                if ($authRet->token_type != 'bearer' || empty($authRet->access_token)) {
                    return null;
                }

                // petición
                $url = "https://api.twitter.com/1.1/search/tweets.json?q=" . rawurlencode( $query );

//                echo $url.'<hr />';

                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_URL, $url );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $curl, CURLOPT_USERAGENT, 'Goteo.org Buzz Getter');
                curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
                    'Authorization: Bearer '.$authRet->access_token,
                    'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
                ));

                $result = curl_exec( $curl );
                curl_close( $curl );

                // guardamos ahora cache de resultado
                \file_put_contents($data_file, $result);
            }

            if (!empty($result)) {
                $return =json_decode($result);

                // parsear los resultados para devolver un array simple
                if(is_array($return->statuses)) {
                    foreach ($return->statuses as $item) {
                        // echo \trace($item).'<hr />';
                        $the_author = $item->user->screen_name;
                        $the_user = $item->user->name;
                        $the_avatar = str_replace('http://', 'https://', $item->user->profile_image_url);
                        $the_profile = 'https://twitter.com/'.$the_author;
                        $twitter_user = $the_author;
                        $gUser = false;

                        if ($matchusers) {
                            $sql = "SELECT id, name, avatar FROM user WHERE twitter LIKE :author";
                            $uQuery = Model\User::query($sql, array(':author' => "%{$the_author}%"));
                            if ($user = $uQuery->fetchObject()) {
                                $the_author = $user->id;
                                $the_user = $user->name;
                                if (!empty($user->avatar)) {
                                    $image = Model\Image::get($user->avatar);
                                    if ($image instanceof Model\Image) {
                                        $the_avatar = $image->getLink(48, 48, true);
                                    }
                                }
                                $the_profile = SITE_URL.'/user/profile/'.$the_author;
                                $gUser = true;
                            } else {
                                $gUser = false;
                            }
                        }


                        $list[] = (object) array(
                            'date' => $item->created_at,
                            'author' => $the_author,
                            'user' => $the_user,
                            'avatar' => $the_avatar,
                            'profile' => $the_profile,
                            'text' => $item->text,
                            'es_usuario' => $gUser,
                            'twitter_user' => $twitter_user
                        );
                    }
                }
            }


            return $list;
        }

    }
}
