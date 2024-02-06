<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
class Mx_join extends Module_join
{
    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        return parent::pre_run();
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        if (addon_installed('external_db_login') && get_forum_type() == 'cns') {
            $redirect_url = get_value('external_join_url', null, true);
            if (!empty($redirect_url)) {
                return redirect_screen(null, $redirect_url);
            }
        }

        return parent::run();
    }
}
