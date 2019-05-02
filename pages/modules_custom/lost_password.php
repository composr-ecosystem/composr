<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    external_db_login
 */

/*FORCE_ORIGINAL_LOAD_FIRST*/

/**
 * Module page class.
 */
class Mx_lost_password extends Module_lost_password
{
    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $GLOBALS['OUTPUT_STREAMING'] = false;
        parent::pre_run();
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run()
    {
        if (addon_installed('external_db_login') && get_forum_type() == 'cns') {
            $redirect_url = get_value('external_lost_password_url', null, true);
            if (!empty($redirect_url)) {
                return redirect_screen(null, $redirect_url);
            }
        }

        return parent::run();
    }
}
