<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_trackback_iotds
{
    /**
     * Run function for trackback hooks. They see if content of an ID relating to this content has trackback enabled.
     *
     * @param  ID_TEXT $id The ID
     * @return boolean Whether trackback is enabled
     */
    public function run($id)
    {
        if (!addon_installed('iotds')) {
            return false;
        }

        $rows = $GLOBALS['SITE_DB']->query_select('iotd', ['allow_trackbacks'], ['id' => intval($id)], '', 1);
        if (!array_key_exists(0, $rows)) {
            return false;
        }
        return $rows[0]['allow_trackbacks'] == 1;
    }
}
