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
        raise_php_memory_limit();
        $values = ['foobar', '', false, null, str_repeat('x', 1024 * 1024 * 10)]; // TODO: fails at standard 32MB memory. Is that a problem?
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

    public function testMainCache()
    {
        cms_extend_time_limit(75);

        require_code('tempcode');
        require_code('lorem');
        require_code('caches');
        require_code('caches2');

        $tempcode_empty = new Tempcode();
        $tempcode_not_empty = do_lorem_template('PARAGRAPH', [
            'TEXT' => lorem_sentence_html(),
            'CLASS' => lorem_phrase(),
        ]);

        // Try strings, integers, floats, arrays, objects, and Tempcode.
        $values = [uniqid('', false), '', mt_rand(0, 1000000), pi(), ['foo' => 'bar'], new dummy_class(), $tempcode_empty, $tempcode_not_empty];

        // Set cache entries
        foreach ($values as $key => $value) {
            set_cache_entry('test', 1, strval($key), $value, CACHE_AGAINST_DEFAULT, [], [], [], ($key >= (count($values) - 2)));
        }

        // Every 16 seconds for 4 iterations, try to get the value (and we expect it to fetch)
        for ($i = 0; $i < 4; $i++) {
            foreach ($values as $key => $value) {
                $ret = get_cache_entry('test', strval($key), CACHE_AGAINST_DEFAULT, 1, ($key >= (count($values) - 2)));
                $this->assertTrue(($ret !== null), 'Expected cache key ' . strval($key) . ' to return a value but it did not.');

                if ($ret !== null) {
                    if ($key >= (count($values) - 2)) { // Tempcode
                        $this->assertTrue(($ret->evaluate() == $value->evaluate()), 'Expected cache key '. strval($key) . ' to be ' . $value->evaluate() . ' but instead it was ' . $ret->evaluate());
                    } else {
                        $this->assertTrue((serialize($ret) == serialize($value)), 'Expected cache key '. strval($key) . ' to be ' . serialize($value) . ' but instead it was ' . serialize($ret));
                    }
                }
            }
            sleep(16);
        }

        // Now, over a minute passed, so we expect cache misses
        foreach ($values as $key => $value) {
            $ret = get_cache_entry('test', strval($key), CACHE_AGAINST_DEFAULT, 1);
            $this->assertTrue(($ret === null), 'Expected cache key ' . strval($key) . ' to have expired, but it did not.');
        }
    }
}

class dummy_class
{
    protected $some_value;
    public function __construct()
    {
        $this->some_value = 0;
    }
}
