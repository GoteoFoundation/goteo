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
    // This is logarithmic! May be incremented as computer power increments.
    private const ENCRYPTION_ROUNDS = 12;
    private string $hashed_password = '';
    private bool $sha1_encoding = false;
    private bool $bcrypt_encoding = false;

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

    static private function getContext(): Context
    {
        $context = new Context;
        $context->addConfig('bcrypt', array ('rounds' => self::ENCRYPTION_ROUNDS));
        return $context;
    }

    public function isPasswordValid($check_password): bool
    {
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
    public function isSecure(): bool
    {
        if(!$this->sha1_encoding) {
            // Configure a context to use bcrypt with a specific number of rounds
            $outdated = static::getContext()->needsUpdate($this->hashed_password);
            return !$outdated;
        }
        return false;
    }

    /**
     * @param  string $raw Plain password or SHA-1 encoded password
     */
    static public function encode(string $raw): string
    {
         // $raw will be already be encoded with SHA1
         // This sometimes ensures compatibility with already encoded $raw password
        if(!self::isSHA1($raw)) $raw = sha1($raw);

        return static::getContext()->hash($raw);
    }

    static function isBlowfish(string $str): bool
    {
        return strpos($str, '$2a$') === 0;
    }

    static function isSHA1(string $str): bool
    {
        return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
    }

    static function isOldBcrypt(string $str): bool
    {
        return strpos($str, '$1$') === 0;
    }

}
