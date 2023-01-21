<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    imap
 */

function init__imap()
{
    if (addon_installed('imap')) {
        require_code('developer_tools');

        destrictify();
        require_code('imap/vendor/autoload');
        restrictify();
    }
}
