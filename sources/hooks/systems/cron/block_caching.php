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
 * Hook class.
 */
class Hook_cron_block_caching
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (get_value('cron_block_caching_careful_contexts') === '1') {
            $request_contexts = $GLOBALS['SITE_DB']->query('SELECT DISTINCT c_theme,c_lang FROM ' . get_table_prefix() . 'cron_caching_requests');
            if (empty($request_contexts)) {
                return;
            }

            $langs = find_all_langs();
            require_code('themes2');
            $themes = find_all_themes();

            // Remove anything tied to a context that no longer exists
            foreach ($request_contexts as $i => $request_context) {
                $lang = $request_context['c_lang'];
                $theme = $request_context['c_theme'];

                if ((!isset($langs[$lang])) || (!isset($themes[$theme]))) {
                    unset($request_contexts[$i]);
                    $GLOBALS['SITE_DB']->query_delete('cron_caching_requests', array('c_theme' => $theme, 'c_lang' => $lang));
                }
            }

            if ((get_param_string('keep_lang', null) === null) || (get_param_string('keep_theme', null) === null)) {
                // We need to recurse into a new call for each context (language and theme combination)
                $done_something = false;
                $more_to_do = false;
                foreach ($request_contexts as $request_context) {
                    $lang = $request_context['c_lang'];
                    $theme = $request_context['c_theme'];

                    if (($theme != $GLOBALS['FORUM_DRIVER']->get_theme()) || ($lang != user_lang())) {
                        $where = array('c_theme' => $theme, 'c_lang' => $lang);
                        $count = $GLOBALS['SITE_DB']->query_select_value('cron_caching_requests', 'COUNT(*)', $where);
                        if ($count > 0) {
                            $url = get_base_url() . '/data/cron_bridge.php?limit_hook=block_caching&keep_lang=' . urlencode($lang) . '&keep_theme=' . urlencode($theme) . '&force=1';
                            http_download_file($url, null, false, false, 'Composr', null, null, null, null, null, null, null, null, 180.0);
                            $done_something = true;
                        }
                    } else {
                        $more_to_do = true;
                    }
                }

                // Force re-loading of values that we use to mark progress (as above calls probably resulted in changes happening)
                if ($done_something) {
                    global $VALUE_OPTIONS_CACHE;
                    $VALUE_OPTIONS_CACHE = $GLOBALS['SITE_DB']->query_select('values', array('*'));
                    $VALUE_OPTIONS_CACHE = list_to_map('the_name', $VALUE_OPTIONS_CACHE);
                }

                if (!$more_to_do) {
                    return;
                }
            }

            $where = array('c_theme' => $GLOBALS['FORUM_DRIVER']->get_theme(), 'c_lang' => user_lang());
        } else {
            $where = array();
        }

        // Execute all in current context...

        global $LANGS_REQUESTED, $LANGS_REQUESTED, $TIMEZONE_MEMBER_CACHE, $JAVASCRIPTS, $CSSS, $REQUIRED_ALL_LANG, $DO_NOT_CACHE_THIS;

        $GLOBALS['NO_QUERY_LIMIT'] = true;

        $requests = $GLOBALS['SITE_DB']->query_select('cron_caching_requests', array('*'), $where);
        foreach ($requests as $request) {
            $codename = $request['c_codename'];
            $map = @unserialize($request['c_map']);
            if (!is_array($map)) {
                $map = array();
            }

            list($object, $new_security_scope) = do_block_hunt_file($codename, $map);

            if ($new_security_scope) {
                _solemnly_enter();
            }

            if (is_object($object)) {
                $backup_langs_requested = $LANGS_REQUESTED;
                $backup_required_all_lang = $REQUIRED_ALL_LANG;
                get_users_timezone();
                $backup_timezone = $TIMEZONE_MEMBER_CACHE[get_member()];
                $TIMEZONE_MEMBER_CACHE[get_member()] = $request['c_timezone'];
                $LANGS_REQUESTED = array();
                $REQUIRED_ALL_LANG = array();
                push_output_state(false, true);
                $DO_NOT_CACHE_THIS = false;
                $cache = $object->run($map);
                $TIMEZONE_MEMBER_CACHE[get_member()] = $backup_timezone;
                $cache->handle_symbol_preprocessing();
                if (!$DO_NOT_CACHE_THIS) {
                    if (method_exists($object, 'caching_environment')) {
                        $info = $object->caching_environment();
                    } else {
                        $info = array();
                        $info['cache_on'] = 'array($map)';
                        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
                        $info['ttl'] = 60 * 24;
                    }
                    $ttl = $info['ttl'];

                    $cache_identifier = do_block_get_cache_identifier($info['cache_on'], $map);

                    require_code('caches2');
                    if ($request['c_store_as_tempcode'] == 1) {
                        $cache = make_string_tempcode($cache->evaluate());
                    }
                    put_into_cache(
                        $codename,
                        $ttl,
                        $cache_identifier,
                        $request['c_staff_status'],
                        $request['c_member'],
                        $request['c_groups'],
                        $request['c_is_bot'],
                        $request['c_timezone'],
                        $cache,
                        array_keys($LANGS_REQUESTED),
                        array_keys($JAVASCRIPTS),
                        array_keys($CSSS),
                        true,
                        $request['c_theme'],
                        $request['c_lang']
                    );
                }
                $LANGS_REQUESTED += $backup_langs_requested;
                $REQUIRED_ALL_LANG += $backup_required_all_lang;
                restore_output_state(false, true);
            }

            if ($new_security_scope) {
                _solemnly_leave();
            }

            $GLOBALS['SITE_DB']->query_delete('cron_caching_requests', $request);
        }
    }
}
