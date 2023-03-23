<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_endpoint_content_commandr_fs
{
    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('composr_mobile_sdk')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!addon_installed('commandr')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('commandr')));
        }

        require_lang('composr_mobile_sdk');

        if (!has_actual_page_access(get_member(), 'admin_commandr')) {
            access_denied('PAGE_ACCESS');
        }

        if ($type === null) {
            warn_exit(do_lang_tempcode('NO_PARAMETER_SENT', escape_html('type')));
        }

        if ($id === null) {
            $id = '';
        }

        require_code('commandr_fs');
        $commandr_fs = new Commandr_fs();

        $data = [
            'message' => strip_html(do_lang('SUCCESS')),
        ];

        $path_arr = $commandr_fs->_pwd_to_array('/' . $id);

        $is_file = $commandr_fs->_is_file($path_arr);
        $is_dir = $commandr_fs->_is_dir($path_arr);
        $exists = $is_file || $is_dir;
        /*if ($is_dir) {    Actually it's best to force being explicit, allows us to then serve directory listings (no ambiguity)
            require_code('resource_fs');
            $path_arr[] = RESOURCE_FS_SPECIAL_DIRECTORY_FILE;
        }*/

        switch ($type) {
            case 'add':
                if (!$exists) {
                    $test = $commandr_fs->write_file($path_arr, post_param_string('data'));
                    if (!$test) {
                        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
                    }
                } else {
                    warn_exit(do_lang_tempcode('ALREADY_EXISTS', escape_html($id)));
                }
                break;

            case 'edit':
                if ($exists) {
                    $test = $commandr_fs->write_file($path_arr, post_param_string('data'));
                    if (!$test) {
                        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
                    }
                } else {
                    warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($id)));
                }
                break;

            case 'delete':
                if ($exists) {
                    $test = $commandr_fs->remove_file($path_arr);
                    if (!$test) {
                        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
                    }
                } else {
                    warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($id)));
                }
                break;

            case 'search':
                // Search format is <resource-type>/<resource-id> (e.g. download/10) OR <guid>
                require_code('resource_fs');
                if (strpos($id, '/') === false) {
                    $details = $GLOBALS['SITE_DB']->query_select('alternative_ids', ['*'], [
                        'resource_guid' => $id,
                    ], '', 1);
                    if (!array_key_exists(0, $details)) {
                        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
                    }
                    $resource_type = $details[0]['resource_type'];
                    $resource_id = $details[0]['resource_id'];
                } else {
                    list($resource_type, $resource_id) = explode('/', $id, 2);
                }
                $id = find_commandr_fs_filename_via_id($resource_type, $resource_id, true);
                if ($id === null) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
                }
                $fs_hook = convert_composr_type_codes('content_type', $resource_type, 'commandr_filesystem_hook');
                $id = 'var/' . $fs_hook . '/' . $id;
                $path_arr = $commandr_fs->_pwd_to_array('/' . $id);
                $is_file = $commandr_fs->_is_file($path_arr);
                $is_dir = $commandr_fs->_is_dir($path_arr);
                $exists = $is_file || $is_dir;
                // no break

            case 'view':
                if ($exists) {
                    if ($is_dir) {
                        $data = $commandr_fs->listing($path_arr);
                    } else {
                        $__data = $commandr_fs->read_file($path_arr);
                        if ($__data !== false) {
                            $_data = @json_decode($__data, true);
                            if (is_array($_data)) {
                                $data = $_data;
                            } else {
                                $data = [
                                    'data' => $__data,
                                ];
                            }
                        } else {
                            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
                        }
                    }
                } else {
                    warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($id)));
                }
                break;

            default:
                warn_exit(do_lang_tempcode('INVALID_ENDPOINT_TYPE'));
        }

        return $data;
    }
}
