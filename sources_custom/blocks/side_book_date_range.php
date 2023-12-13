<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

// TODO (can optionally take a filter of what bookables to allow choosing from) - for date ranges

class Block_side_book_date_range
{
    public function run()
    {
        return new Tempcode();
    }
}
