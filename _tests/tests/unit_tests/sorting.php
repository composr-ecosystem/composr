<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class sorting_test_set extends cms_test_case
{
    public function testSortMapsBy()
    {
        $results = [
            ['a' => 1, 'b' => 1, 'expected' => 1],
            ['a' => 1, 'b' => 2, 'expected' => 2],
            ['a' => 2, 'b' => 2, 'expected' => 4],
            ['a' => 2, 'b' => 1, 'expected' => 3],
        ];

        $expected = [
            ['a' => 1, 'b' => 1, 'expected' => 1],
            ['a' => 1, 'b' => 2, 'expected' => 2],
            ['a' => 2, 'b' => 1, 'expected' => 3],
            ['a' => 2, 'b' => 2, 'expected' => 4],
        ];

        sort_maps_by($results, 'a,b');

        $this->assertTrue($results == $expected);

        $results = [
            ['a' => 1, 'b' => 1, 'expected' => 1],
            ['a' => 1, 'b' => 2, 'expected' => 2],
            ['a' => 2, 'b' => 2, 'expected' => 4],
            ['a' => 2, 'b' => 1, 'expected' => 3],
        ];

        $expected = [
            ['a' => 2, 'b' => 2, 'expected' => 4],
            ['a' => 2, 'b' => 1, 'expected' => 3],
            ['a' => 1, 'b' => 2, 'expected' => 2],
            ['a' => 1, 'b' => 1, 'expected' => 1],
        ];

        sort_maps_by($results, '!a,!b');

        $this->assertTrue($results == $expected);
    }
}
