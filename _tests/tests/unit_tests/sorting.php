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

/**
 * Composr test case class (unit testing).
 */
class sorting_test_set extends cms_test_case
{
    public function testSortMapsByMultipleParameters()
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

    public function testSortMapsByIncludingNulls()
    {
        $arr = [
            [1],
            [3],
            [2],
        ];

        // Ascending
        sort_maps_by($arr, 0);
        $expected = [
            [1],
            [2],
            [3],
        ];
        $this->assertTrue($arr == $expected);

        // Descending
        sort_maps_by($arr, '!0');
        $expected = [
            [3],
            [2],
            [1],
        ];
        $this->assertTrue($arr == $expected);

        // Now with nulls...

        $arr = [
            [null],
            [3],
            [2],
        ];

        // Ascending
        sort_maps_by($arr, 0);
        $expected = [
            [null],
            [2],
            [3],
        ];
        $this->assertTrue($arr == $expected);

        // Descending
        sort_maps_by($arr, '!0');
        $expected = [
            [3],
            [2],
            [null],
        ];
        $this->assertTrue($arr == $expected);
    }
}
