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
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'api_classes',
            'api_functions',
            'api_function_params',
            'api_functions_fulltext_index',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        // NB: We do not need upgrade code for this module because all the data can be re-compiled, so we just drop what we have and start over
        $this->uninstall();

        $GLOBALS['SITE_DB']->create_table('api_classes', [
            'id' => '*AUTO',
            'c_name' => 'ID_TEXT',
            'c_source_url' => 'URLPATH',
            'c_is_abstract' => 'BINARY',
            'c_implements' => 'LONG_TEXT', // Comma-delimited list
            'c_traits' => 'LONG_TEXT', // Comma-delimited list
            'c_extends' => 'ID_TEXT',
            'c_package' => 'ID_TEXT',
            'c_type' => 'MINIID_TEXT',
            'c_comment' => 'BINARY',

            'c_edit_date' => 'TIME',
        ]);

        $GLOBALS['SITE_DB']->create_index('api_classes', 'by_package', ['c_package', 'c_name']);

        $GLOBALS['SITE_DB']->create_table('api_functions', [
            'id' => '*AUTO',
            'class_id' => 'AUTO_LINK',
            'class_name' => 'ID_TEXT', // Needed for more efficient class searching instead of using the api_classes table directly
            'f_name' => 'ID_TEXT',
            'f_php_return_type' => 'ID_TEXT',
            'f_php_return_type_nullable' => 'BINARY',
            'f_description' => 'LONG_TEXT',
            'f_flags' => 'LONG_TEXT', // Comma-delimited list
            'f_is_static' => 'BINARY',
            'f_is_abstract' => 'BINARY',
            'f_is_final' => 'BINARY',
            'f_visibility' => 'MINIID_TEXT',
            'f_return_type' => 'ID_TEXT', // Blank: none
            'f_return_description' => 'LONG_TEXT',
            'f_return_set' => 'SHORT_TEXT',
            'f_return_range' => 'SHORT_TEXT',

            'f_edit_date' => 'TIME',
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions', 'by_class_name', ['class_name', 'f_name']);
        $GLOBALS['SITE_DB']->create_index('api_functions', 'by_class_id', ['class_id', 'f_name']);

        $GLOBALS['SITE_DB']->create_table('api_function_params', [
            'id' => '*AUTO',
            'function_id' => 'AUTO_LINK',
            'p_name' => 'ID_TEXT',
            'p_php_type' => 'ID_TEXT',
            'p_php_type_nullable' => 'BINARY',
            'p_type' => 'ID_TEXT',
            'p_set' => 'SHORT_TEXT',
            'p_range' => 'SHORT_TEXT',
            'p_ref' => 'BINARY',
            'p_is_variadic' => 'BINARY',
            'p_default' => 'SERIAL', // Only way we can get a properly typed default (blank: no default)
            'p_description' => 'LONG_TEXT',
        ]);

        $GLOBALS['SITE_DB']->create_index('api_function_params', 'by_function_id', ['function_id', 'p_name']);

        $GLOBALS['SITE_DB']->create_table('api_functions_fulltext_index', [
            'i_f_id' => '*AUTO_LINK',

            'i_lang' => '*LANGUAGE_NAME',
            'i_ngram' => '*INTEGER',
            'i_ac' => '*INTEGER',

            'i_occurrence_rate' => 'REAL',

            // De-normalised stuff from main content tables for any major filters that shape the results provided
            //  (other stuff will come in via join back to the main content table)
            'i_add_time' => 'TIME',
            'i_f_name' => 'ID_TEXT',
            'i_c_id' => 'AUTO_LINK',
            'i_submitter' => 'MEMBER',
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'content_id', [ // Used for clean-outs and potentially optimising some JOINs if query planner decides to start at the content table
            'i_f_id',
        ]);

        /* TODO: too long
        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_f_name',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);
        */

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_2', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_3', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_4', [
            'i_lang',
            'i_ngram',
            'i_f_name',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_5', [
            'i_lang',
            'i_ngram',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_6', [
            'i_lang',
            'i_ngram',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_7', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_8', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_f_name',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_9', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_10', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_11', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_f_name',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_12', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_13', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_14', [
            'i_lang',
            'i_ngram',
            'i_f_name',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_15', [
            'i_lang',
            'i_ngram',
            'i_f_name',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_16', [
            'i_lang',
            'i_ngram',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_17', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_f_name',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_18', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_19', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_20', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_f_name',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_21', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_f_name',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_22', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_23', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_f_name',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_24', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_f_name',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_25', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_26', [
            'i_lang',
            'i_ngram',
            'i_f_name',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_27', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_f_name',
            'i_c_id',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_28', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_f_name',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_29', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_add_time',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_30', [
            'i_lang',
            'i_ngram',
            'i_ac',
            'i_f_name',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_31', [
            'i_lang',
            'i_ngram',
            'i_add_time',
            'i_f_name',
            'i_c_id',
            'i_submitter',
            'i_occurrence_rate', // For sorting
        ]);

        $GLOBALS['SITE_DB']->create_index('api_functions_fulltext_index', 'main_32', [
            'i_lang',
            'i_ngram',
            'i_occurrence_rate', // For sorting
        ]);
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
        require_code('templates');
        require_code('templates_results_table');

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 100);

        // Prepare Filtercode
        require_code('filtercode');
        $active_filters = get_params_filtercode();

        // Build WHERE query from Filtercode
        $where = [];
        $end = '';
        list($extra_join, $end) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($active_filters), null, 'api_classes');

        $header_row = results_header_row([do_lang_tempcode('NAME')]);

        // Get all available distinct classes
        $count_rows = $GLOBALS['SITE_DB']->query_select_value('api_classes r', 'COUNT(DISTINCT r.c_name)', [], $end);
        $classes = $GLOBALS['SITE_DB']->query_select('api_classes r', ['DISTINCT r.c_name'], [], $end . ' ORDER BY r.c_name', $max, $start);

        $rows = new Tempcode();
        foreach ($classes as $class) {
            $class_link = hyperlink(build_url(['page' => 'api', 'type' => $class['c_name']], get_module_zone('api')), $class['c_name'], false, true);
            $rows->attach(results_entry([$class_link], false));
        }

        $filtercode = [
            'c_name<c_name_op><c_name>',
            'c_source_url<c_source_url_op><c_source_url>',
            'c_is_abstract=<c_is_abstract>',
            'c_implements<c_implements_op><c_implements>',
            'c_traits<c_traits_op><c_traits>',
            'c_extends<c_extends_op><c_extends>',
            'c_package=<c_package>',
            'c_type=<c_type>',
            'c_comment=<c_comment>',
        ];
        $filtercode_labels = [
            'c_name=' . do_lang('API_DOC_CLASS_NAME'),
            'c_source_url=' . do_lang('API_DOC_SOURCE_FILE'),
            'c_is_abstract=' . do_lang('API_DOC_IS_ABSTRACT'),
            'c_implements=' . do_lang('API_DOC_IMPLEMENTS'),
            'c_traits=' . do_lang('API_DOC_TRAITS'),
            'c_extends=' . do_lang('API_DOC_EXTENDS'),
            'c_package=' . do_lang('API_DOC_PACKAGE'),
            'c_type=' . do_lang('TYPE'),
            'c_comment=' . do_lang('COMMENT'),
        ];
        $filtercode_types = [
            'c_package=list',
            'c_type=list'
        ];

        $results_table = results_table(do_lang_tempcode('API_DOC_TITLE'), $start, 'start', $max, 'max', $count_rows, $header_row, $rows);

        $filtercode_box = do_block('main_content_filtering', [
            'param' => implode(',', $filtercode),
            'table' => 'api_classes',
            'labels' => implode(',', $filtercode_labels),
            'types' => implode(',', $filtercode_types),
        ]);

        return do_template('TUTORIAL_API_INDEX', [
            '_GUID' => 'd76c1cb2617a5ac29eeb1371788cb0e5',
            'TITLE' => get_screen_title(do_lang('API_DOC_TITLE'), false),
            'CLASS_LINKS' => $results_table,
            'FILTERCODE_BOX' => $filtercode_box,
        ]);
    }

    public function api_class(string $class) : object
    {
        require_code('templates');
        require_code('templates_results_table');

        // Class definitions
        if ($class == '__global') {
            $class_definitions = null; // Every file has a global class, so let's not list them all
        } else {
            $rows = $GLOBALS['SITE_DB']->query_select('api_classes', ['*'], ['c_name' => $class], ' ORDER BY c_name');
            if (count($rows) == 0) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE', escape_html('api_classes')));
            }

            $class_definitions = [];
            foreach ($rows as $row) {
                $class_implements = [];
                foreach (explode(',', $row['c_implements']) as $implements) {
                    if ($implements == '') {
                        continue;
                    }
                    $class_implements[] = hyperlink(build_url(['page' => 'api', 'type' => $implements], get_module_zone('api')), $implements, false, true);
                }

                $class_traits = [];
                foreach (explode(',', $row['c_traits']) as $trait) {
                    if ($trait == '') {
                        continue;
                    }
                    $class_traits[] = $trait;
                }

                $class_extends = null;
                if ($row['c_extends'] != '') {
                    $class_extends = hyperlink(build_url(['page' => 'api', 'type' => $row['c_extends']], get_module_zone('api')), $row['c_extends'], false, true);
                }

                $class_definitions[] = [
                    'PATH' => $row['c_source_url'],
                    'IS_ABSTRACT' => ($row['c_is_abstract'] == 1) ? do_lang('YES') : do_lang('NO'),
                    'IMPLEMENTS' => $class_implements,
                    'TRAITS' => $class_traits,
                    'EXTENDS' => $class_extends,
                    'TYPE' => $row['c_type'],
                    'PACKAGE' => $row['c_package'],
                ];
            }
        }

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 100);

        // Prepare Filtercode
        require_code('filtercode');
        $active_filters = get_params_filtercode();

        // Build WHERE query from Filtercode
        $where = [];
        $end = '';
        list($extra_join, $end) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($active_filters), null, 'api_functions');

        // Get all available distinct functions from this class
        $count_rows = $GLOBALS['SITE_DB']->query_select_value('api_functions r', 'COUNT(DISTINCT r.f_name)', ['class_name' => $class], $end);
        $functions = $GLOBALS['SITE_DB']->query_select('api_functions r', ['DISTINCT r.f_name'], ['class_name' => $class], $end . ' ORDER BY r.f_name', $max, $start);

        $rows = new Tempcode();
        foreach ($functions as $function) {
            $function_link = hyperlink(build_url(['page' => 'api', 'type' => $class, 'id' => $function['f_name']], get_module_zone('api')), $function['f_name'], false, true);
            $rows->attach(results_entry([$function_link], false));
        }

        $header_row = results_header_row([do_lang_tempcode('NAME')]);

        $filtercode = [
            'f_name<f_name_op><f_name>',
            'f_description<f_description_op><f_description>',
            'f_flags<f_flags_op><f_flags>',
            'f_is_static=<f_is_static>',
            'f_is_abstract=<f_is_abstract>',
            'f_is_final=<f_is_final>',
            'f_visibility=<f_visibility>',
            'f_return_type=<f_return_type>',
        ];
        $filtercode_labels = [
            'f_name=' . do_lang('NAME'),
            'f_description=' . do_lang('DESCRIPTION'),
            'f_flags=' . do_lang('API_DOC_FLAGS'),
            'f_is_static=' . do_lang('API_DOC_IS_STATIC'),
            'f_is_abstract=' . do_lang('API_DOC_IS_ABSTRACT'),
            'f_is_final=' . do_lang('API_DOC_IS_FINAL'),
            'f_visibility=' . do_lang('API_DOC_VISIBILITY'),
            'f_return_type=' . do_lang('API_DOC_RETURN'),
        ];
        $filtercode_types = [
            'f_visibility=list',
            'f_return_type=list',
        ];

        $results_table = results_table(do_lang_tempcode('API_DOC_CLASS_FUNCTIONS'), $start, 'start', $max, 'max', $count_rows, $header_row, $rows);

        $filtercode_box = do_block('main_content_filtering', [
            'param' => implode(',', $filtercode),
            'table' => 'api_functions',
            'labels' => implode(',', $filtercode_labels),
            'types' => implode(',', $filtercode_types),
        ]);

        return do_template('TUTORIAL_API_CLASS', [
            '_GUID' => '93ee8a49a157577db90853401648c0bd',
            'TITLE' => get_screen_title('tutorials:API_DOC_CLASS_TITLE', true, [escape_html($class)]),
            'CLASS_DEFINITIONS' => $class_definitions,
            'CLASS_FUNCTIONS' => $results_table,
            'FILTERCODE_BOX' => $filtercode_box,
        ]);
    }

    public function api_function(string $class, string $function) : object
    {
        require_code('tutorials');
        require_code('templates');
        require_code('templates_results_table');

        $db_functions = $GLOBALS['SITE_DB']->query_select('api_functions', ['*'], ['class_name' => $class, 'f_name' => $function], ' ORDER BY f_name');
        if (count($db_functions) == 0) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', escape_html('api_function')));
        }

        $function_definitions = [];
        $i = 0;
        foreach ($db_functions as $db_function) {
            $function_definitions[] = prepare_api_function_for_render($db_function, $i);
        }

        return do_template('TUTORIAL_API_FUNCTION', [
            '_GUID' => '45af1c58bdfc58e19e46403f5ca14cc7',
            'TITLE' => get_screen_title('tutorials:API_DOC_FUNCTION_TITLE', true, [escape_html($class), escape_html($function)]),
            'FUNCTION_DEFINITIONS' => $function_definitions,
        ]);
    }
}
