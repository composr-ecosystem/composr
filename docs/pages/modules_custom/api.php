<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Module page class.
 */
class Module_api
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_tutorials';
        return $info;
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'browse' => ['tutorials:API_DOC_TITLE', 'help'],
        ];
    }

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        require_lang('tutorials');

        $type = get_param_string('type', '');
        $id = get_param_string('id', '');

        if ($type == '') {
            breadcrumb_set_self(do_lang_tempcode('API_DOC_TITLE'));
        } elseif ($id == '') {
            breadcrumb_set_parents([['_SELF:_SELF', do_lang_tempcode('API_DOC_TITLE')]]);
            breadcrumb_set_self(do_lang_tempcode('API_DOC_CLASS_TITLE', escape_html($type)));
        } else {
            breadcrumb_set_parents([['_SELF:_SELF', do_lang_tempcode('API_DOC_TITLE')], ['_SELF:_SELF:' . filter_naughty_harsh($type), do_lang_tempcode('API_DOC_CLASS_TITLE', escape_html($type))]]);
            breadcrumb_set_self(do_lang_tempcode('API_DOC_FUNCTION_TITLE', escape_html($type), escape_html($id)));
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $type = get_param_string('type', '');
        $id = get_param_string('id', '');

        if ($type == '') {
            return $this->api_index();
        }
        if ($id == '') {
            return $this->api_class($type);
        }
        return $this->api_function($type, $id);
    }

    /**
     * The UI for the API documentation index
     *
     * @return Tempcode The UI
     */
    public function api_index() : object
    {
        require_code('files2');
        require_code('templates');
        require_code('templates_results_table');

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 100);

        $header_row = results_header_row([do_lang_tempcode('NAME')]);

        // Get all available classes (directories)
        $api_path = get_file_base() . '/data_custom/modules/api';
        $directories = get_directory_contents($api_path, '', IGNORE_ACCESS_CONTROLLERS, false, false);

        $rows = new Tempcode();
        foreach ($directories as $i => $directory) {
            if ($i < $start) {
                continue;
            }
            if ($i >= ($start + $max)) {
                continue;
            }

            $class_link = hyperlink(build_url(['page' => 'api', 'type' => $directory], get_module_zone('api')), $directory, false, true);
            $rows->attach(results_entry([$class_link], false));
        }

        $results_table = results_table(do_lang_tempcode('API_DOC_TITLE'), $start, 'start', $max, 'max', count($directories), $header_row, $rows);

        return do_template('TUTORIAL_API_INDEX', [
            '_GUID' => 'd76c1cb2617a5ac29eeb1371788cb0e5',
            'TITLE' => get_screen_title(do_lang('API_DOC_TITLE'), false),
            'CLASS_LINKS' => $results_table,
        ]);
    }

    public function api_class(string $class) : object
    {
        require_code('files');
        require_code('files2');
        require_code('templates');
        require_code('templates_results_table');

        $api_path = get_file_base() . '/data_custom/modules/api';

        // Class definitions
        if ($class == '__global') {
            $class_definitions = null;
        } else {
            $definitions_path = $api_path . '/' . filter_naughty_harsh($class) . '/___class_definitions.bin';
            if (!is_file($definitions_path)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'class'));
            }
            $__class_definitions = cms_file_get_contents_safe($definitions_path);
            if ($__class_definitions === false) {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('81f63bcb6c325b4bb8b13470bbb40e2a')));
            }
            $_class_definitions = unserialize($__class_definitions);
            if ($_class_definitions === false) {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('8fc14b9e6e645e59950383e357a26153')));
            }

            $class_definitions = [];
            foreach ($_class_definitions as $path => $class_data) {
                $class_implements = [];
                foreach ($class_data['implements'] as $implements) {
                    $class_implements[] = hyperlink(build_url(['page' => 'api', 'type' => $implements], get_module_zone('api')), $implements, false, true);
                }

                $class_extends = null;
                if (array_key_exists('extends', $class_data) && ($class_data['extends'] !== null)) {
                    $class_extends = hyperlink(build_url(['page' => 'api', 'type' => $class_data['extends']], get_module_zone('api')), $class_data['extends'], false, true);
                }

                $class_definitions[] = [
                    'PATH' => $path,
                    'IS_ABSTRACT' => ((array_key_exists('is_abstract', $class_data)) && ($class_data['is_abstract'] === true)) ? do_lang('YES') : do_lang('NO'),
                    'IMPLEMENTS' => $class_implements,
                    'TRAITS' => (array_key_exists('traits', $class_data) && is_array($class_data['traits'])) ? $class_data['traits'] : null,
                    'EXTENDS' => $class_extends,
                    'TYPE' => (array_key_exists('type', $class_data)) ? $class_data['type'] : do_lang('UNKNOWN'),
                    'PACKAGE' => (array_key_exists('package', $class_data)) ? $class_data['package'] : do_lang('UNKNOWN'),
                ];
            }
        }

        // Class functions (and links)
        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 100);

        $header_row = results_header_row([do_lang_tempcode('NAME')]);

        $functions = get_directory_contents($api_path . '/' . $class, '', IGNORE_ACCESS_CONTROLLERS, false, true, ['bin']);
        $rows = new Tempcode();
        foreach ($functions as $i => $function) {
            if ($function == '___class_definitions.bin') {
                continue;
            }
            if ($i < $start) {
                continue;
            }
            if ($i >= ($start + $max)) {
                continue;
            }

            $function_parsed = str_replace('.bin', '', $function);
            $function_link = hyperlink(build_url(['page' => 'api', 'type' => $class, 'id' => $function_parsed], get_module_zone('api')), $function_parsed, false, true);
            $rows->attach(results_entry([$function_link], false));
        }

        $results_table = results_table(do_lang_tempcode('API_DOC_CLASS_FUNCTIONS'), $start, 'start', $max, 'max', count($functions) - 1, $header_row, $rows);

        return do_template('TUTORIAL_API_CLASS', [
            '_GUID' => '93ee8a49a157577db90853401648c0bd',
            'TITLE' => get_screen_title('tutorials:API_DOC_CLASS_TITLE', true, [escape_html($class)]),
            'CLASS_DEFINITIONS' => $class_definitions,
            'CLASS_FUNCTIONS' => $results_table,
        ]);
    }

    public function api_function(string $class, string $function) : object
    {
        require_code('files');
        require_code('files2');
        require_code('templates');
        require_code('templates_results_table');

        // Function definitions
        $api_path = get_file_base() . '/data_custom/modules/api';
        $functions_path = $api_path . '/' . filter_naughty_harsh($class) . '/' . filter_naughty_harsh($function) . '.bin';
        if (!is_file($functions_path)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'function'));
        }
        $__function_definitions = cms_file_get_contents_safe($functions_path);
        if ($__function_definitions === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('eec2ce6e04d7546792b9331fbff15aa7')));
        }
        $_function_definitions = unserialize($__function_definitions);
        if ($_function_definitions === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('2d54c094b3c75ff8b4c9089263a8be1f')));
        }

        $function_definitions = [];
        $i = 0;
        foreach ($_function_definitions as $path => $function_data) {
            $parameters = null;
            if (array_key_exists('parameters', $function_data) && (count($function_data['parameters']) > 0)) {
                $header = [
                    do_lang_tempcode('NAME'),
                    do_lang_tempcode('TYPE'),
                    do_lang_tempcode('DEFAULT'),
                    do_lang_tempcode('SET'),
                    do_lang_tempcode('API_DOC_RANGE'),
                    do_lang_tempcode('DESCRIPTION'),
                ];
                $header_row = results_header_row($header);

                $rows = new Tempcode();
                foreach ($function_data['parameters'] as $parameter) {
                    // We need to convey the default value in such a way we can differentiate between a literal value and something else
                    if (array_key_exists('default', $parameter)) {
                        if ($parameter['default'] === false) {
                            $param_default = do_lang_tempcode('API_DOC_FALSE');
                        } elseif ($parameter['default'] === true) {
                            $param_default = do_lang_tempcode('API_DOC_TRUE');
                        } elseif ($parameter['default'] === null) {
                            $param_default = do_lang_tempcode('API_DOC_NULL');
                        } elseif (is_array($parameter['default'])) {
                            $param_default = protect_from_escaping(json_encode($parameter['default'], JSON_PRETTY_PRINT));
                        } elseif (is_object($parameter['default'])) {
                            $param_default = protect_from_escaping('<em>Object</em>');
                        } else {
                            $param_default = protect_from_escaping(escape_html(strval($parameter['default'])));
                        }
                        if ($param_default == '') {
                            $param_default = do_lang_tempcode('API_DOC_BLANK');
                        }
                    } else {
                        $param_default = do_lang_tempcode('API_DOC_REQUIRED_PARAMETER');
                    }

                    if (array_key_exists('set', $function_data)) {
                        $param_set = escape_html($parameter['set']);
                    } else {
                        $param_set = '<em>N/A</em>';
                    }

                    if (array_key_exists('range', $function_data)) {
                        $param_range = escape_html($parameter['range']);
                    } else {
                        $param_range = '<em>N/A</em>';
                    }

                    $map = [
                        escape_html('$' . $parameter['phpdoc_name']),
                        $this->type_tooltip($parameter['type']),
                        $param_default,
                        $param_set,
                        $param_range,
                        (array_key_exists('description', $function_data)) ? escape_html($parameter['description']) : do_lang('NA'),
                    ];

                    $rows->attach(results_entry($map, false));
                }

                $parameters = results_table(do_lang_tempcode('API_DOC_PARAMETERS'), get_param_integer('param_' . strval($i) . '_start', 0), 'param_' . strval($i) . '_start', get_param_integer('param_' . strval($i) . '_max', 0), 'param_' . strval($i) . '_max', count($function_data['parameters']), $header_row, $rows);
            }

            $map = [
                'PATH' => $path,
                'DESCRIPTION' => (array_key_exists('description', $function_data)) ? $function_data['description'] : null,
                'RETURN_TYPE' => (array_key_exists('php_return_type', $function_data)) ? $this->type_tooltip($function_data['php_return_type'], $function_data['php_return_type_nullable']) : null,
                'FLAGS' => (array_key_exists('flags', $function_data)) ? $function_data['flags'] : null,
                'IS_STATIC' => (array_key_exists('is_static', $function_data)) && ($function_data['is_static'] === true) ? do_lang('YES') : do_lang('NO'),
                'IS_ABSTRACT' => ((array_key_exists('is_abstract', $function_data)) && ($function_data['is_abstract'] === true)) ? do_lang('YES') : do_lang('NO'),
                'IS_FINAL' => ((array_key_exists('is_final', $function_data)) && ($function_data['is_final'] === true)) ? do_lang('YES') : do_lang('NO'),
                'VISIBILITY' => (array_key_exists('visibility', $function_data)) ? $function_data['visibility'] : do_lang('UNKNOWN'),
                'PARAMETERS' => $parameters,
            ];

            if (array_key_exists('return', $function_data) && ($function_data['return'] !== null)) {
                if (array_key_exists('set', $function_data['return'])) {
                    $param_set = escape_html($function_data['return']['set']);
                } else {
                    $param_set = '<em>N/A</em>';
                }

                if (array_key_exists('range', $function_data['return'])) {
                    $param_range = escape_html($function_data['return']['range']);
                } else {
                    $param_range = '<em>N/A</em>';
                }

                $map['RETURN_TYPE_CMS'] = (array_key_exists('type', $function_data['return']) ? $this->type_tooltip($function_data['return']['type']) : do_lang('UNKNOWN'));
                $map['RETURN_SET'] = $param_set;
                $map['RETURN_RANGE'] = $param_range;
                $map['RETURN_DESCRIPTION'] = (array_key_exists('description', $function_data['return']) ? escape_html($function_data['return']['description']) : null);
            } else {
                $map['RETURN_TYPE_CMS'] = null;
                $map['RETURN_SET'] = null;
                $map['RETURN_RANGE'] = null;
                $map['RETURN_DESCRIPTION'] = null;
            }

            $function_definitions[] = $map;

            $i++;
        }

        return do_template('TUTORIAL_API_FUNCTION', [
            '_GUID' => '45af1c58bdfc58e19e46403f5ca14cc7',
            'TITLE' => get_screen_title('tutorials:API_DOC_FUNCTION_TITLE', true, [escape_html($class), escape_html($function)]),
            'FUNCTION_DEFINITIONS' => $function_definitions,
        ]);
    }

    /**
     * Create an abbr tag explaining the given type.
     *
     * @param  ?ID_TEXT $type The type (null: none)
     * @param  ?boolean $nullable Whether this can be null (null: find out from the type)
     * @return string The HTML
     */
    protected function type_tooltip(?string $type, ?bool $nullable = null) : string
    {
        if ($type === null) {
            return '<em>N/A</em>';
        }

        $abbr_title = do_lang('API_DOC_TYPE__' . str_replace(['?', '~', '*'], ['', '', ''], $type), null, null, null, null, false);
        if ($abbr_title === null) {
            return $type;
        }

        $ret = '<abbr title="' . $abbr_title;
        if (($nullable === true) || (($nullable === null) && strpos($type, '?') !== false)) {
            $ret .= do_lang('API_DOC_TYPE_NULLABLE');
        }
        if (strpos($type, '~') !== false) {
            $ret .= do_lang('API_DOC_TYPE_FALSEABLE');
        }
        $ret .= '">' . $type . '</abbr>';
        return $ret;
    }
}
