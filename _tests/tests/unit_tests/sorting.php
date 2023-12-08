<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
        $arr = array(
            array(1),
            array(3),
            array(2),
        );

        // Ascending
        sort_maps_by($arr, 0);
        $expected = array(
            array(1),
            array(2),
            array(3),
        );
        $this->assertTrue($arr == $expected);

        // Descending
        sort_maps_by($arr, '!0');
        $expected = array(
            array(3),
            array(2),
            array(1),
        );
        $this->assertTrue($arr == $expected);

        // Now with nulls...

        $arr = array(
            array(null),
            array(3),
            array(2),
        );

        // Ascending
        sort_maps_by($arr, 0);
        $expected = array(
            array(null),
            array(2),
            array(3),
        );
        $this->assertTrue($arr == $expected);

        // Descending
        sort_maps_by($arr, '!0');
        $expected = array(
            array(3),
            array(2),
            array(null),
        );
        $this->assertTrue($arr == $expected);
    }
}
