<?php

namespace Goteo\Application;

class Config {
    static protected $config;

    static function factory(array $config) {
        self::$config = $config;
        self::setConstants();
    }

    /**
     * Compatibility constants
     */
    static function setConstants() {
        // foreach(self::$config as $name => $value) {
        //     echo "$name => " . print_r($value, 1)."\n";
        // };die;
        define('GOTEO_MAINTENANCE', self::get('maintenance', true));
        define('GOTEO_SESSION_TIME', self::get('session.time', true));
        define('GOTEO_MISC_SECRET', self::get('secret', true));
        define('GOTEO_ENV', self::get('env', true));
        define('GOTEO_NODE', self::get('node', true));
        define('GOTEO_FEE', self::get('fee', true));

        define('GOTEO_META_TITLE', self::get('meta.title', true));
        define('GOTEO_META_DESCRIPTION', self::get('meta.description', true));
        define('GOTEO_META_KEYWORDS', self::get('meta.keywords', true));
        define('GOTEO_META_AUTHOR', self::get('meta.author', true));
        define('GOTEO_META_COPYRIGHT', self::get('meta.copyright', true));

        define('AWS_KEY', self::get('filesystem.aws.key'));
        define('AWS_SECRET', self::get('filesystem.aws.secret'));
        define('AWS_REGION', self::get('filesystem.aws.region'));

        define('GOTEO_DB_DRIVER', self::get('db.driver', true));
        define('GOTEO_DB_HOST', self::get('db.host', true));
        define('GOTEO_DB_PORT', self::get('db.port', true));
        define('GOTEO_DB_CHARSET', self::get('db.charset', true));
        define('GOTEO_DB_SCHEMA', self::get('db.database', true));
        define('GOTEO_DB_USERNAME', self::get('db.username', true));
        define('GOTEO_DB_PASSWORD', self::get('db.password', true));

        if(self::get('db.replica.host'))     define('GOTEO_DB_READ_REPLICA_HOST', self::get('db.replica.host'));
        if(('db.replica.port'))              define('GOTEO_DB_READ_REPLICA_PORT', self::get('db.replica.port'));
        if(self::get('db.replica.username')) define('GOTEO_DB_READ_REPLICA_USERNAME', self::get('db.replica.username'));
        if(self::get('db.replica.password')) define('GOTEO_DB_READ_REPLICA_PASSWORD', self::get('db.replica.password'));

        define('SQL_CACHE_DRIVER', self::get('db.cache.driver'));
        define('SQL_CACHE_TIME', self::get('db.cache.time'));
        define('SQL_CACHE_LONG_TIME', self::get('db.cache.long_time'));

        define('GOTEO_MAIL_FROM', self::get('mail.transport.from'));
        define('GOTEO_MAIL_NAME', self::get('mail.transport.name'));
        define('GOTEO_MAIL_TYPE', self::get('mail.transport.type'));
        define('GOTEO_MAIL_SMTP_AUTH', self::get('mail.transport.smtp'));
        define('GOTEO_MAIL_SMTP_SECURE', self::get('mail.transport.smtp.secure'));
        define('GOTEO_MAIL_SMTP_HOST', self::get('mail.transport.smtp.host'));
        define('GOTEO_MAIL_SMTP_PORT', self::get('mail.transport.smtp.port'));
        define('GOTEO_MAIL_SMTP_USERNAME', self::get('mail.transport.smtp.username'));
        define('GOTEO_MAIL_SMTP_PASSWORD', self::get('mail.transport.smtp.password'));
        define('GOTEO_MAIL', self::get('mail.mail'));
        define('GOTEO_CONTACT_MAIL', self::get('mail.contact'));
        define('GOTEO_MANAGER_MAIL', self::get('mail.manager'));
        define('GOTEO_FAIL_MAIL', self::get('mail.fail'));
        define('GOTEO_LOG_MAIL', self::get('mail.log'));
        define('GOTEO_MAIL_QUOTA', self::get('mail.quota.total'));
        define('GOTEO_MAIL_SENDER_QUOTA', self::get('mail.quota.sender'));
        define('AWS_SNS_CLIENT_ID', self::get('mail.sns.client_id'));
        define('AWS_SNS_REGION', self::get('mail.sns.region'));
        define('AWS_SNS_BOUNCES_TOPIC', self::get('mail.sns.bounces_topic'));
        define('AWS_SNS_COMPLAINTS_TOPIC', self::get('mail.sns.complaints_topic'));
        define('GOTEO_DEFAULT_LANG', self::get('lang'));

        define('GOTEO_URL', self::get('url.main'));
        define('SRC_URL', self::get('url.assets'));

        if(self::get('url.data')) define('GOTEO_DATA_URL', self::get('url.data'));

        define('GOTEO_SSL', self::get('ssl'));
        define('FILE_HANDLER', self::get('filesystem.handler'));
        define('AWS_S3_BUCKET_STATIC', self::get('filesystem.bucket.static'));
        define('AWS_S3_BUCKET_MAIL', self::get('filesystem.bucket.mail'));
        define('AWS_S3_BUCKET_DOCUMENT', self::get('filesystem.bucket.document'));
        define('AWS_S3_BUCKET_PRESS', self::get('filesystem.bucket.press'));
        define('CRON_PARAM', self::get('cron.param'));
        define('CRON_VALUE', self::get('cron.value'));
        define('PP_CONFIG_PATH', GOTEO_PATH . 'config/');
        define('PAYPAL_REDIRECT_URL', self::get('paypal.redirect_url'));
        define('TPV_MERCHANT_CODE', self::get('tpv.merchant_code'));
        define('TPV_REDIRECT_URL', self::get('tpv.redirect_url'));
        define('TPV_ENCRYPT_KEY', self::get('tpv.encrypt_key'));
        define('OAUTH_FACEBOOK_ID', self::get('oauth.facebook.id'));
        define('OAUTH_FACEBOOK_SECRET', self::get('oauth.facebook.secret'));
        define('OAUTH_TWITTER_ID', self::get('oauth.twitter.id'));
        define('OAUTH_TWITTER_SECRET', self::get('oauth.twitter.secret'));
        define('OAUTH_LINKEDIN_ID', self::get('oauth.linkedin.id'));
        define('OAUTH_LINKEDIN_SECRET', self::get('oauth.linkedin.secret'));
        define('RECAPTCHA_PUBLIC_KEY', self::get('recaptcha.public'));
        define('RECAPTCHA_PRIVATE_KEY', self::get('recaptcha.private'));
    }

    /**
     * Return a value
     * @param  string $name ex: filesystem.handler
     *                          filesystem.bucket      => array
     *                          filesystem.bucket.mail => string
     * @param  string $strick throws a Exception on fail
     * @return [type]       [description]
     */
    static function get($name, $strict = false) {
        $part = strtok($name, '.');
        if(array_key_exists($part, self::$config)) {
            $ret = self::$config[$part];
            while($part = strtok('.')) {
                if(is_array($ret) && array_key_exists($part, $ret)) {
                    $ret = $ret[$part];
                    // echo "[$part]";
                }
                elseif($strict) {
                    throw new Config\ConfigException("Config var [$name] not found!", 1);
                }
            }
            return $ret;
        }
        elseif($strict) {
            throw new Config\ConfigException("Config var [$name] not found!", 1);
        }
        return null;
    }

    static function isNode() {
        return NODE_ID !== GOTEO_NODE;
    }

}
