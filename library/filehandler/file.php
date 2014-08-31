<?php

namespace Goteo\Library\FileHandler {

    require_once "library/aws/S3.php"; //AWS SDK normal
    use \S3;

    class File {

        /**
         * Constructor
         */
        private function __construct() {

        }

        public static function factory($extra = array()) {

            if (defined("FILE_HANDLER") && FILE_HANDLER == 's3'
                && defined("AWS_SECRET") && defined("AWS_KEY")) {

                S3::setExceptions();

                if (!isset($extra['bucket'])) {
                    $extra['bucket'] = AWS_S3_BUCKET_STATIC;
                }
                if (!isset($extra['path'])) {
                    $extra['path'] = '';
                }
                $obj = new S3File(AWS_KEY, AWS_SECRET, $extra['bucket'], $extra['path']);

            } else {

                if (isset($extra['path'])) {
                    $path = $extra['path'];
                    if (substr($path, -1) != DIRECTORY_SEPARATOR) {
                        $path .= DIRECTORY_SEPARATOR;
                    }
                } else {
                    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR. 'data' . DIRECTORY_SEPARATOR;
                }

                $obj = new LocalFile($extra['path']);

            }

            if (isset($extra['error_mode'])) {
                $obj->error_mode($extra['error_mode']);
            }

            return $obj;
        }
    }

}
