<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Block class.
 */
class Block_main_include_module
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'strip_title', 'only_if_permissions', 'leave_page_and_zone', 'merge_parameters', 'use_http_status', 'use_metadata', 'use_attached_messages', 'use_breadcrumbs', 'use_refreshes', 'use_helper_panel');
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        // Settings
        $strip_title = array_key_exists('strip_title', $map) ? intval($map['strip_title']) : 1;
        $only_if_permissions = array_key_exists('only_if_permissions', $map) ? intval($map['only_if_permissions']) : 1;
        $leave_page_and_zone = array_key_exists('leave_page_and_zone', $map) ? ($map['leave_page_and_zone'] == '1') : false;
        $merge_parameters = array_key_exists('merge_parameters', $map) ? ($map['merge_parameters'] == '1') : false;

        $use_http_status = array_key_exists('use_http_status', $map) ? ($map['use_http_status'] == '1') : false;
        $use_metadata = array_key_exists('use_metadata', $map) ? ($map['use_metadata'] == '1') : false;
        $use_attached_messages = array_key_exists('use_attached_messages', $map) ? ($map['use_attached_messages'] == '1') : false;
        $use_breadcrumbs = array_key_exists('use_breadcrumbs', $map) ? ($map['use_breadcrumbs'] == '1') : false;
        $use_refreshes = array_key_exists('use_refreshes', $map) ? ($map['use_refreshes'] == '1') : false;
        $use_helper_panel = array_key_exists('use_helper_panel', $map) ? ($map['use_helper_panel'] == '1') : false;

        // Find out what we're virtualising
        $param = array_key_exists('param', $map) ? $map['param'] : '';
        if ($param == '') {
            return new Tempcode();
        }
        list($zone, $attributes,) = page_link_decode($param);
        if (!array_key_exists('page', $attributes)) {
            return new Tempcode();
        }
        if ($zone == '_SEARCH') {
            $zone = get_page_zone($attributes['page'], false);
        } elseif ($zone == '_SELF') {
            $zone = get_zone_name();
        }
        if (is_null($zone)) {
            return new Tempcode();
        }
        foreach ($_GET as $key => $val) {
            if ((substr($key, 0, 5) == 'keep_') || ($merge_parameters)) {
                $_GET[$key] = @get_magic_quotes_gpc() ? addslashes($val) : $val;
            }
        }

        // Check permissions
        if (($only_if_permissions == 1) && (!has_actual_page_access(get_member(), $attributes['page'], $zone))) {
            return new Tempcode();
        }

        require_code('urls2');

        // Setup virtual environment
        global $SKIP_TITLING;
        if ($strip_title == 1) {
            $prior_skip_titling = $SKIP_TITLING;
            $SKIP_TITLING = true;
        }
        $new_zone = $leave_page_and_zone ? get_zone_name() : $zone;
        list($old_get, $old_zone, $old_current_script) = set_execution_context(
            ($leave_page_and_zone ? array('page' => $attributes['page']) : array()) + $attributes,
            $new_zone
        );
        global $IS_VIRTUALISED_REQUEST;
        $IS_VIRTUALISED_REQUEST = true;
        push_output_state();

        // Do it!
        process_url_monikers($attributes['page'], false);
        $ret = request_page($attributes['page'], false, $zone, null, true);
        $ret->handle_symbol_preprocessing();
        $_out = $ret->evaluate(); // So things are evaluated in the right context

        // Get things back to prior state
        set_execution_context(
            $old_get,
            $old_zone,
            $old_current_script,
            false
        );
        $keep = array(
            'EXTRA_HEAD',
            'EXTRA_FOOT',
            'JAVASCRIPT',
            'JAVASCRIPTS',
            'CSSS',
        );
        if ($use_http_status) {
            $keep += array(
                'HTTP_STATUS_CODE',
            );
        }
        if ($use_metadata) {
            $keep += array(
                'METADATA',
                'SEO_KEYWORDS',
                'SEO_DESCRIPTION',
                'DISPLAYED_TITLE',
                'SHORT_TITLE',
                'FORCE_SET_TITLE',
                'FEED_URL',
                'FEED_URL_2',
            );
        }
        if ($use_attached_messages) {
            $keep += array(
                'ATTACHED_MESSAGES',
                'ATTACHED_MESSAGES_RAW',
            );
        }
        if ($use_breadcrumbs) {
            $keep += array(
                'BREADCRUMBS',
                'BREADCRUMB_SET_PARENTS',
                'BREADCRUMB_SET_SELF',
            );
        }
        if ($use_refreshes) {
            $keep += array(
                'REFRESH_URL',
                'FORCE_META_REFRESH',
                'QUICK_REDIRECT',
            );
        }
        if ($use_helper_panel) {
            $keep += array(
                'HELPER_PANEL_TEXT',
                'HELPER_PANEL_TUTORIAL',
            );
        }
        restore_output_state(false, true, $keep);
        $IS_VIRTUALISED_REQUEST = false;
        if ($strip_title == 1) {
            $SKIP_TITLING = $prior_skip_titling;
        }

        // More replacing, if _SELF wasn't used within the module
        if ($leave_page_and_zone) {
            $url_from = static_evaluate_tempcode(build_url(array('page' => $attributes['page']), $zone, null, false, false, true));
            if (substr($url_from, -4) == '.htm') {
                $url_from = substr($url_from, 0, strlen($url_from) - 4);
            }
            $url_to = static_evaluate_tempcode(build_url(array('page' => get_page_name()), get_zone_name(), null, false, false, true));
            if (substr($url_to, -4) == '.htm') {
                $url_to = substr($url_to, 0, strlen($url_to) - 4);
            }
            if (strpos($_out, $attributes['page']) !== false) {
                $_out = str_replace($url_from, $url_to, $_out);
            }
        }

        $out = make_string_tempcode($_out);

        // Done
        return $out;
    }
}
