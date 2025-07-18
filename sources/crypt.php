<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__crypt()
{
    if (!defined('SALT_MD5PASSWORD')) {
        define('SALT_MD5PASSWORD', 0);
        define('PASSWORD_SALT', 1);
    }

    /**
     * A Compatibility library with PHP 5.5's simplified password hashing API.
     *
     * @author Anthony Ferrara <ircmaxell@php.net>
     * @license http://www.opensource.org/licenses/mit-license.html MIT License
     * @copyright 2012 The Authors
     */

    if ((!defined('PASSWORD_DEFAULT')) && (version_compare(PHP_VERSION, '5.3.7') >= 0)) { // http://compo.sr/tracker/view.php?id=2011
        define('PASSWORD_BCRYPT', 1);
        define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

        /**
         * Hash the password using the specified algorithm
         *
         * @param  string $password The password to hash
         * @param  integer $algo The algorithm to use (Defined by PASSWORD_* constants)
         * @param  array $options The options for the algorithm to use
         * @return ~string The hashed password (false: error)
         */
        function password_hash($password, $algo, $options)
        {
            if (!is_integer($algo)) {
                trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
                return false;
            }
            $result_length = 0;
            switch ($algo) {
                case PASSWORD_BCRYPT: // Blowfish
                    // Note that this is a C constant, but not exposed to PHP, so we don't define it here.
                    $cost = 10;
                    if (isset($options['cost'])) {
                        $cost = $options['cost'];
                        if ($cost < 4 || $cost > 31) {
                            trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                            return false;
                        }
                    }
                    // The length of salt to generate
                    $raw_salt_len = 16;
                    // The length required in the final serialization
                    $required_salt_len = 22;
                    $hash_format = sprintf("$2y$%02d$", $cost);
                    // The expected length of the final crypt() output
                    $result_length = 60;
                    break;
                default:
                    trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                    return false;
            }
            $salt_requires_encoding = false;
            if (isset($options['salt'])) {
                $salt = $options['salt'];
                if (_crypt_strlen($salt) < $required_salt_len) {
                    trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", _crypt_strlen($salt), $required_salt_len), E_USER_WARNING);
                    return false;
                } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                    $salt_requires_encoding = true;
                }
            } else {
                $buffer = '';
                $buffer_valid = false;
                if ((function_exists('mcrypt_create_iv')) && (!defined('PHALANGER'))) {
                    $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
                    if ($buffer !== false) {
                        $buffer_valid = true;
                    }
                }
                if ((function_exists('openssl_random_pseudo_bytes')) && (!$buffer_valid) && (get_value('disable_openssl') !== '1')) {
                    $buffer = openssl_random_pseudo_bytes($raw_salt_len);
                    if ($buffer !== false) {
                        $buffer_valid = true;
                    }
                }
                if (!$buffer_valid && @is_readable('/dev/urandom')) {
                    $f = fopen('/dev/urandom', 'r');
                    $read = _crypt_strlen($buffer);
                    while ($read < $raw_salt_len) {
                        $buffer .= fread($f, $raw_salt_len - $read);
                        $read = _crypt_strlen($buffer);
                    }
                    fclose($f);
                    if ($read >= $raw_salt_len) {
                        $buffer_valid = true;
                    }
                }
                if (!$buffer_valid || _crypt_strlen($buffer) < $raw_salt_len) {
                    $bl = _crypt_strlen($buffer);
                    for ($i = 0; $i < $raw_salt_len; $i++) {
                        if ($i < $bl) {
                            $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                        } else {
                            $buffer .= chr(mt_rand(0, 255));
                        }
                    }
                }
                $salt = $buffer;
                $salt_requires_encoding = true;
            }
            if ($salt_requires_encoding) {
                // encode string with the Base64 variant used by crypt
                $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
                $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

                $base64_string = base64_encode($salt);
                $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
            }
            $salt = _crypt_substr($salt, 0, $required_salt_len);

            $hash = $hash_format . $salt;

            $ret = crypt($password, $hash);

            if (!is_string($ret) || _crypt_strlen($ret) == 0 || _crypt_strlen($ret) != $result_length) {
                return false;
            }

            return $ret;
        }

        /**
         * Verify a password against a hash using a timing attack resistant approach
         *
         * @param  string $password The password to verify
         * @param  string $hash The hash to verify against
         * @return boolean If the password matches the hash
         */
        function password_verify($password, $hash)
        {
            $ret = crypt($password, $hash/*Used to get crypt-salt via looking at that of existing hash*/);
            if (!is_string($ret) || _crypt_strlen($ret) != _crypt_strlen($hash) || _crypt_strlen($ret) == 0 || _crypt_strlen($ret) <= 13/*Just salt returned back*/) {
                return false;
            }

            $status = 0;
            for ($i = 0; $i < _crypt_strlen($ret); $i++) {
                $status |= (ord($ret[$i]) ^ ord($hash[$i]));
            }
            return $status === 0;
        }

        /**
         * Count the number of bytes in a string
         *
         * We cannot simply use strlen() for this, because it might be overwritten by the mbstring extension.
         * In this case, strlen() will count the number of *characters* based on the internal encoding. A
         * sequence of bytes might be regarded as a single multibyte character.
         *
         * @param  string $binary_string The input string
         * @return integer The number of bytes
         * @ignore
         */
        function _crypt_strlen($binary_string)
        {
            if (function_exists('mb_strlen')) {
                return mb_strlen($binary_string, '8bit');
            }
            return strlen($binary_string);
        }

        /**
         * Get a substring based on byte limits
         *
         * @see _strlen()
         *
         * @param string $binary_string The input string
         * @param integer $start Start
         * @param integer $length Length
         * @return string The substring
         * @ignore
         */
        function _crypt_substr($binary_string, $start, $length)
        {
            if (function_exists('mb_substr')) {
                return mb_substr($binary_string, $start, $length, '8bit');
            }
            return substr($binary_string, $start, $length);
        }
    }
}

/**
 * Do a hashing, with support for our "ratcheting up" algorithm (i.e. lets the admin increase the complexity over the time, as CPU speeds get faster).
 *
 * @param  SHORT_TEXT $password The password in plain text
 * @param  SHORT_TEXT $salt The salt
 * @param  integer $legacy_style Legacy hashing style to fallback to
 * @return SHORT_TEXT The salted&hashed password
 */
function ratchet_hash($password, $salt, $legacy_style = 0)
{
    if (function_exists('password_hash')) {
        // NB: We don't pass the salt separately, we let password_hash generate its own internal salt also (that builds into the hash). So it is double salted.
        $ratchet = max(4, min(31, intval(get_option('crypt_ratchet'))));
        return password_hash($salt . md5($password), PASSWORD_BCRYPT, array('cost' => $ratchet));
    }

    // Fallback for old versions of PHP
    if ($legacy_style == PASSWORD_SALT) {
        return md5($password . $salt);
    }
    return md5($salt . md5($password));
}

/**
 * Verify a password is correct by comparison of the hashed version.
 *
 * @param  SHORT_TEXT $password The password in plain text
 * @param  SHORT_TEXT $salt The salt
 * @param  SHORT_TEXT $pass_hash_salted The prior salted&hashed password, which will also include the algorithm/ratcheting level (unless it's old style, in which case we use non-ratcheted md5)
 * @param  integer $legacy_style Legacy hashing style to fallback to
 * @return boolean Whether the password if verified
 */
function ratchet_hash_verify($password, $salt, $pass_hash_salted, $legacy_style = 0)
{
    if ((function_exists('password_verify')) && (preg_match('#^\w+$#', $pass_hash_salted) == 0)) {
        return password_verify($salt . md5($password), $pass_hash_salted);
    }

    // Old-style md5'd password
    if ($legacy_style == PASSWORD_SALT) {
        return (md5($password . $salt) == $pass_hash_salted);
    }
    return (md5($salt . md5($password)) == $pass_hash_salted);
}

/**
 * Get a decent randomised salt.
 *
 * @return ID_TEXT The salt
 */
function produce_salt()
{
    // md5 used in all the below so that we get nice ASCII characters

    if ((function_exists('openssl_random_pseudo_bytes')) && (get_value('disable_openssl') !== '1')) {
        $u = substr(md5(openssl_random_pseudo_bytes(13)), 0, 13);
    } elseif (function_exists('password_hash')) { // password_hash will include a randomised component
        $ratchet = max(4, min(31, intval(get_option('crypt_ratchet'))));
        return substr(md5(password_hash(uniqid('', true), PASSWORD_BCRYPT, array('cost' => $ratchet))), 0, 13);
    } else {
        $u = substr(md5(uniqid(strval(get_secure_random_number()), true)), 0, 13);
    }
    return $u;
}

/**
 * Get the site-wide salt. It should be something hard for a hacker to get, so we depend on data gathered both from the database and file-system.
 *
 * @return ID_TEXT The salt
 */
function get_site_salt()
{
    $site_salt = get_value('site_salt');
    if ($site_salt === null) {
        $site_salt = produce_salt();
        set_value('site_salt', $site_salt);
    }
    //global $SITE_INFO; This is unstable on some sites, as the array can be prepopulated on the fly
    //$site_salt.=serialize($SITE_INFO);
    return md5($site_salt);
}

/**
 * Get a randomised password.
 *
 * @return string The randomised password
 */
function get_rand_password()
{
    return produce_salt();
}

/**
 * Get a secure random number, the best this PHP version can do.
 *
 * @return integer The randomised number
 */
function get_secure_random_number()
{
    // TODO: #3046 in tracker
    // 2147483647 is from MySQL limit http://dev.mysql.com/doc/refman/5.6/en/integer-types.html ; PHP_INT_MAX is higher on 64bit machines
    if ((function_exists('openssl_random_pseudo_bytes')) && (get_value('disable_openssl') !== '1')) {
        $code = intval(2147483647 * (hexdec(bin2hex(openssl_random_pseudo_bytes(4))) / 0xffffffff));
        if ($code < 0) {
            $code = -$code;
        }
    } elseif (function_exists('password_hash')) { // password_hash will include a randomised component
        $ratchet = max(4, min(31, intval(get_option('crypt_ratchet'))));
        $hash = password_hash(uniqid('', true), PASSWORD_BCRYPT, array('cost' => $ratchet));
        return crc32($hash);
    } else {
        $code = mt_rand(0, min(2147483647, mt_getrandmax()));
    }
    return $code;
}

/**
 * Calculate a reasonable cryptographic ratchet based on the server's CPU speed.
 *
 * @param  float $target_time The ratchet should not exceed this amount of time in seconds when calculating
 * @param  integer $minimum_cost The minimum allowed ratchet; must be between 4 and 31
 * @return ?integer The suggested ratchet to use (null: password_hash is not supported)
 */
function calculate_reasonable_ratchet($target_time = 0.1, $minimum_cost = 4)
{
    if (!function_exists('password_hash')) {
        return null;
    }

    $cost = ($minimum_cost - 1);

    // Costs < 4 are not supported. This will be increased by 1 in the first iteration.
    if ($cost < 3) {
        $cost = 3;
    }

    do {
        $cost++;
        if ($cost > 31) { // Costs > 31 are not supported
            break;
        }
        $start = microtime(true);
        password_hash('test', PASSWORD_BCRYPT, array('cost' => $cost));
        $end = microtime(true);
        $elapsed_time = $end - $start;
    } while ($elapsed_time < $target_time);

    return ($cost - 1); // We don't want to use the cost that exceeded our target time; use the one below it.
}
