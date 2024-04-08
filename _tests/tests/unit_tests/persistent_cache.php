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

/*EXTRA FUNCTIONS: sleep*/

/**
 * Composr test case class (unit testing).
 */
class persistent_cache_test_set extends cms_test_case
{
    public function testConsistentSetGet()
    {
        if (get_param_integer('live_test', 0) == 1) {
            global $PERSISTENT_CACHE;
            $cache = $PERSISTENT_CACHE;
        } else {
            require_code('persistent_caching/filesystem');
            $cache = new Persistent_caching_filesystem();
        }

        // Test value lifetimes
        // --------------------

        $values = ['foobar', '', false, null, str_repeat('x', 1024 * 1024 * 10)]; // TODO: fails on 32MB max limit because we're copying to global and also making a serialised copy
        foreach ($values as $value) {
            // Set
            $cache->set('test', $value);

            // Get (check correct set)
            $got = $cache->get('test');
            $this->assertTrue($got === $value);

            // Delete
            $cache->delete('test');

            // Get (check correct delete)
            $got = $cache->get('test');
            $this->assertTrue($got === null);
        }

        // Test flushing
        // -------------

        // Flush
        $cache->set('test', 'foobar');
        $cache->flush();

        // Get (check correct flush)
        $got = $cache->get('test');
        $this->assertTrue($got === null);

        // Test expiry
        // -----------

        // Set
        $cache->set('test', 'foobar', 0, 1);

        // Get (not expired)
        $got = $cache->get('test');
        $this->assertTrue($got === 'foobar');

        // Set
        $cache->set('test', 'foobar', 0, 1);

        // Delay
        sleep(3);

        // Get (expired)
        $got = $cache->get('test');
        $this->assertTrue($got == 'foobar');

        // Min cache date
        // --------------

        $time = time();

        // Set
        $cache->set('test', 'foobar', 0, 1);

        // Get (over min)
        $got = $cache->get('test', $time - 1);
        $this->assertTrue($got === 'foobar');

        // Set
        $cache->set('test', 'foobar', 0, 1);

        // Get (under min)
        $got = $cache->get('test', $time + 1);
        $this->assertTrue($got === null);
    }
}
