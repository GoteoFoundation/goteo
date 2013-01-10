<?php

namespace Goteo\Library {

    use Goteo\Model;

    /*
     * Clase para cosas de actividad en redes sociales
     * en principio, para saccar el buz de una convocatoria (recibiendo el query de busqueda)
     */
    class Buzz {

        /**
         * Metodo para obtener buzz de twitter
         * @param string $query; busqueda en twitter sin codificar
         * @param bool $matchusers; si buscamos los autores entre los usuarios de goteo
         * @return mixed items
         */
        public static function getTweets( $query , $matchusers = false) {

            $list = array();
            
            $url = "http://search.twitter.com/search.json?q=" . urlencode( $query );
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_USERAGENT, 'Goteo.org Buzz Getter');

            $result = curl_exec( $curl );
            curl_close( $curl );
            $return =json_decode( $result );

            // parsear los resultados para devolver un array simple
            foreach ($return->results as $item) {

                $the_author = $item->from_user;
                $the_user = $item->from_user_name;
                $the_avatar = $item->profile_image_url;
                $the_profile = 'https://twitter.com/'.$the_author;
                $twitter_user = $item->from_user;
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
                        $the_profile = 'http://goteo.org/user/profile/'.$the_author;
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


            return $list;
        }
        
    }
}