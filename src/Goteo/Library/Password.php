<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library;

// Using this library to compatibilize with python's passlib
use PHPassLib\Application\Context;

class Password {
    // This is logarithmic!
    // This value may be incremented as computer power increments
    static private $cost = 12;

    private $hashed_password = '';
    private $defaultEncoder;
    private $sha1_encoding = false;
    private $bcrypt_encoding = false;
    private $salt = null;

    function __construct($hashed_password) {
        // Old database passwords are encoded in plain SHA-1
        if(self::isSHA1($hashed_password)) {
            $this->sha1_encoding = true;
        }
        if(self::isOldBcrypt($hashed_password)) {
            $this->bcrypt_encoding = true;
        }

        $this->hashed_password = $hashed_password;
    }

    static private function getContext() {
        $context = new Context;
        $context->addConfig('bcrypt', array ('rounds' => self::$cost));
        return $context;
    }

    public function isPasswordValid($check_password) {
        if(!self::isSHA1($check_password)) {
            // For compatibility, all passwords will be pre-encoded with a SHA-1 algorithm
            $compare_password = sha1($check_password);
        }
        if($this->bcrypt_encoding) {
            // For compatibility with the github version
            return $this->hashed_password === crypt($check_password, $this->hashed_password);
        }

        if($this->sha1_encoding) {
            return $compare_password === $this->hashed_password;
        }

        return password_verify($compare_password, $this->hashed_password);
    }

    /**
     * Simple verification if a password hash is secure enough
     * @return boolean false if is SHA-1 or plain
     */
    public function isSecure() {
        if(!$this->sha1_encoding) {
            // Configure a context to use bcrypt with a specific number of rounds
            $outdated = static::getContext()->needsUpdate($this->hashed_password);
            return !$outdated;
        }
        return false;
    }

    /**
     * Encodes the password according to some security rules
     * @param  string $pass Plain password or SHA-1 encoded password
     * @return string       Encoded password
     */
    static public function encode($raw) {
         // $raw will be preencode with SHA1
         // This ensures sometimes compatibility with already encoded $raw password
        if(!self::isSHA1($raw)) $raw = sha1($raw);

        // $encoded = password_hash($raw, PASSWORD_DEFAULT, ['cost' => self::$cost]);
        $encoded = static::getContext()->hash($raw);
        // die("[$raw] [$encoded]");
        return $encoded;
    }

    /**
     * Verifies a password is using Blowfish algorithm
     */
    static function isBlowfish($str) {
        return strpos($str, '$2a$') === 0;
    }
    /*
     * Verifies if a string is a SHA-1
     */
    static function isSHA1($str) {
        return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
    }

    static function isOldBcrypt($str) {
        // $hashed = (version_compare(phpversion(), '5.5.0', '>=')) ? password_hash($this->password, PASSWORD_BCRYPT) : crypt($this->password);

        return strpos($str, '$1$') === 0;
    }

}
