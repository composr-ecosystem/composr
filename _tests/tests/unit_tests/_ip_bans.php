<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// Underscored as it could cause other tests to fail if run when/if this test bans local IP

/**
 * Composr test case class (unit testing).
 */
class _ip_bans_test_set extends cms_test_case
{
    public function testIPBans()
    {
        require_code('failure');

        // Put something in spam_check_exclusions that isn't relevant to ensure no problems from that
        set_option('spam_check_exclusions', '12.34.56.78');

        $future_timestamp = time() + 600;
        $past_timestamp = time() - 600;

        // Repeat for .htaccess and not
        foreach ([true, false] as $force_db) {
            // Positive bans...

            // Test adding a positive ban (add, then banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip();
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Test removing the positive ban (remove, then not banned)
            remove_ip_ban($ip);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Test adding a positive wildcard ban (add, then banned, remove, then not banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            $result = add_ip_ban($wildcarded_ip, '', null, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($wildcarded_ip);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Test adding a positive temporary ban (add, then banned, remove, then not banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip();
            $result = add_ip_ban($ip, '', $future_timestamp, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === $future_timestamp);
            remove_ip_ban($ip);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Test adding a positive expired temporary ban (add, then not banned, remove, then also not banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip();
            $result = add_ip_ban($ip, '', $past_timestamp, true, false);
            //$this->assertTrue($result); Actually we only test expiry in ip_banned, to reduce code weight
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Test adding a positive wildcard temporary ban (add, then banned, remove, then not banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip();
            $result = add_ip_ban($wildcarded_ip, '', $future_timestamp, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === $future_timestamp);
            remove_ip_ban($wildcarded_ip);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);

            // Negative bans...

            // Test adding a negative ban (add, not banned, add a positive, still not banned)
            list($ip, $wildcarded_ip) = $this->generate_test_ip();
            $result = add_ip_ban($ip, '', null, false/*negative ban*/, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue(!$result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);

            // Test removing the negative ban (remove, add positive ban on top, then banned) - then cleanup
            remove_ip_ban($ip);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);

            // Test adding a negative wildcard ban (add, then not banned, add a positive, still not banned) - then cleanup
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            $result = add_ip_ban($wildcarded_ip, '', null, false/*negative ban*/, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue(!$result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);
            remove_ip_ban($wildcarded_ip);

            // Test adding a negative temporary ban (add, then not banned, add a positive, still not banned) - then cleanup
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            $result = add_ip_ban($ip, '', $future_timestamp, false/*negative ban*/, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === $future_timestamp);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue(!$result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === $future_timestamp);
            remove_ip_ban($ip);

            // Test adding a negative expired temporary ban (add, then not banned, add a positive, is banned) - then cleanup
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            $result = add_ip_ban($ip, '', $past_timestamp, false/*negative ban*/, false);
            //$this->assertTrue($result); Actually we only test expiry in ip_banned, to reduce code weight
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);

            // Test adding a negative wildcard temporary ban (add, then not banned, add a positive, still not banned) - then cleanup
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            $result = add_ip_ban($wildcarded_ip, '', $future_timestamp, false/*negative ban*/, false);
            $this->assertTrue($result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === $future_timestamp);
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue(!$result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === $future_timestamp);
            remove_ip_ban($wildcarded_ip);
            remove_ip_ban($ip);

            // Miscellaneous...

            // Test adding a positive ban against something listed in spam_check_exclusions (add, then not banned) - then cleanup
            list($ip, $wildcarded_ip) = $this->generate_test_ip(true);
            set_option('spam_check_exclusions', $ip);
            $result = add_ip_ban($ip, '', null, true, false);
            //$this->assertTrue(!$result); Actually we do let it be added, as the check is meant to be on ip_banned only (allows retroactive setting of spam_check_exclusions, and less code)
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);
            $result = add_ip_ban($wildcarded_ip, '', null, true, false);
            //$this->assertTrue(!$result); Ditto previous comment
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === true);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($wildcarded_ip);
            remove_ip_ban($ip);

            // Test adding a positive ban against a localhost IP (add, then not banned)
            $ip = '127.0.0.1';
            $result = add_ip_ban($ip, '', null, true, false);
            //$this->assertTrue(!$result); Actually we do let it be added, as the check is meant to be on ip_banned only (allows retroactive bug-fixing, and less code)
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);

            // Test adding a positive ban against an invalid IP (add, then not banned)
            $ip = '123';
            $result = add_ip_ban($ip, '', null, true, false);
            $this->assertTrue(!$result);
            $is_unbannable = null;
            $ban_until = null;
            $this->assertTrue(!ip_banned($ip, $force_db, false, $is_unbannable, $ban_until, false));
            $this->assertTrue($is_unbannable === false);
            $this->assertTrue($ban_until === null);
            remove_ip_ban($ip);
        }

        set_option('spam_check_exclusions', '');
    }

    protected function generate_test_ip()
    {
        do {
            $components = [];
            for ($i = 0; $i < 4; $i++) {
                $components[] = strval(mt_rand(0, 255));
            }
            $ip = implode('.', $components);
        } while ($ip == '12.34.56.78' || $ip == '127.0.0.1');

        $components[3] = '*';
        $ip_wildcarded = implode('.', $components);

        return [$ip, $ip_wildcarded];
    }
}
