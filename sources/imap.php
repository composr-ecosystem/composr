<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    core_imap
 */

/**
 * Initialise third-party IMAP code.
 */
function init__imap()
{
    require_code('developer_tools');

    destrictify();
    require_code('imap/vendor/autoload');
    restrictify();
}
