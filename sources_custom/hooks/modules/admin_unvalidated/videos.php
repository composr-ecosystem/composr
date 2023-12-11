<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Hook class.
 */
class Hook_unvalidated_videos
{
    /**
     * Find details on the unvalidated hook.
     *
     * @return ?array Map of hook info (null: hook is disabled).
     */
    public function info()
    {
        if (!module_installed('galleries')) {
            return null;
        }

        require_lang('galleries');

        $info = array();
        $info['db_table'] = 'videos';
        $info['db_identifier'] = 'id';
        $info['db_validated'] = 'validated';
        $info['db_title'] = 'description';
        $info['db_title_dereference'] = true;
        $info['db_add_date'] = 'add_date';
        $info['db_edit_date'] = 'edit_date';
        $info['edit_module'] = 'cms_galleries';
        $info['edit_type'] = '_edit_other';
        $info['view_module'] = 'galleries';
        $info['view_type'] = 'video';
        $info['edit_identifier'] = 'id';
        $info['title'] = do_lang_tempcode('VIDEOS');
        $info['uses_workflow'] = true;

        return $info;
    }
}
