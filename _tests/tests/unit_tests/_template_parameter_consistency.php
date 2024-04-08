<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _template_parameter_consistency_test_set extends cms_test_case
{
    public function testConsistency()
    {
        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $template_instances = [];

        $included_templates = [
            // Used in the <head> by default
            'CSS_NEED' => true,
            'JAVASCRIPT_NEED' => true,
            'RSS_HEADER' => true,
        ];

        $filter = get_param_string('filter', null);

        require_code('files2');
        require_code('lorem');

        $templates_with_exotic_params = [
            // Shared templates with more-exotic extra parameters we do not expect to be universally used
            'SIMPLE_PREVIEW_BOX',
            'BLOCK_NO_ENTRIES',

            // Complex map buildup that we can't auto-scan
            'MEDIA_IMAGE_WEBSAFE',
            'CNS_POST_BOX',
        ];

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        sort($files);
        foreach ($files as $path) {
            if (($filter !== null) && (strpos($path, $filter) === false)) {
                continue;
            }

            $this->scan_file($path, $template_instances);
        }

        $template_files = get_directory_contents(get_file_base() . '/themes', 'themes', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['tpl', 'xml', 'txt', 'css', 'js']);
        foreach ($template_files as $path) {
            $c = file_get_contents(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#\{\+START,INCLUDE,(\w+)\}#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $template = $matches[1][$i];
                $included_templates[$template] = true;
            }
        }

        if ($this->debug) {
            @var_dump($template_instances);
        }

        // Check that for each we have properly detected both a live and a preview instance
        foreach ($template_instances as $template => $instances) {
            $found_preview = false;
            $found_live = false;
            foreach (array_keys($instances) as $context) {
                if ($this->is_preview_template_context($context)) {
                    $found_preview = true;
                } else {
                    $found_live = true;
                }
            }

            $template_path = $this->find_template_path($template);
            $custom_template = ($template_path !== null) && (strpos($template_path, '_custom/') !== false);
            if (!$custom_template) {
                //$this->assertTrue($found_live, 'Could not find live use of ' . $template); Dynamic code may do weird things, too many false positives with this check
                if ((!array_key_exists($template, $included_templates)) && (!in_array($template, ['_base', '_colours', 'global', 'installer', 'COMCODE_REAL_TABLE_ROW_START', 'POST']))) {
                    $this->assertTrue($found_preview, 'Could not find preview of ' . $template);
                }
            }
        }

        // Check consistency
        foreach ($template_instances as $template => $instances) {
            $template_path = $this->find_template_path($template);
            if ($template_path === null) {
                $this->assertTrue(false, 'Could not find referenced template, ' . $template);
                continue;
            }
            $template_c = file_get_contents($template_path);

            $all_parameters = [];
            $preview_parameters = null;
            $num_preview_instances = 0;
            foreach ($instances as $context => $parameters) {
                $all_parameters += $parameters;
                if ($this->is_preview_template_context($context)) {
                    if ($preview_parameters === null) {
                        $preview_parameters = [];
                    }
                    $num_preview_instances++;
                    foreach ($parameters as $parameter => $is_null) {
                        if (!$is_null) {
                            if (!array_key_exists($parameter, $preview_parameters)) {
                                $preview_parameters[$parameter] = 0;
                            }
                            $preview_parameters[$parameter]++;
                        }
                    }
                }
            }
            if ($preview_parameters !== null) {
                foreach ($preview_parameters as $parameter => $count) {
                    if ($count < $num_preview_instances) {
                        unset($preview_parameters[$parameter]);
                    } else {
                        $preview_parameters[$parameter] = false;
                    }
                }
            }

            ksort($instances);

            foreach ($instances as $context => $parameters) {
                $missing_parameters = [];

                if ($this->is_preview_template_context($context)) {
                    $this->check_template_call($parameters, array_keys($all_parameters), $template, $template_c, $templates_with_exotic_params, $missing_parameters);
                } else {
                    if ($preview_parameters !== null) {
                        $this->check_template_call($parameters, array_keys($preview_parameters), $template, $template_c, $templates_with_exotic_params, $missing_parameters);
                    }
                }

                if (($this->debug) && (!empty($missing_parameters))) {
                    list($path, $line) = explode(':', $context);
                    echo '<a onclick="navigator.clipboard.writeText(\'' . escape_html(str_replace('\'', '\\\'', implode(',\n', $missing_parameters))) . ',\n\');" title="' . escape_html(implode(',', $missing_parameters)) . '" href="txmt://open?url=file://' . escape_html(get_file_base() . '/' . $path) . '&line=' . strval($line) . '">' . escape_html($template) . '</a><br />';
                } else {
                    $this->assertTrue(empty($missing_parameters), $context . ' - Missing ' . implode(',', $missing_parameters) . ', from ' . $template);
                }
            }
        }

        if ($this->debug) {
            var_dump($template_instances);
        }
    }

    protected function check_template_call($parameters, $known_parameters, $template, $template_c, $templates_with_exotic_params, &$missing_parameters)
    {
        foreach ($known_parameters as $parameter) {
            $ok = (array_key_exists($parameter, $parameters)) ||
                ($parameter == '_GUID') ||
                (in_array($template, $templates_with_exotic_params)) ||
                (strpos($template_c, '{+START,IF_PASSED,' . $parameter . '}') !== false) ||
                (strpos($template_c, '{+START,IF_PASSED_AND_TRUE,' . $parameter . '}') !== false);

            // Exceptions
            $exceptions = [
                ['FORM_SCREEN_FIELD_SPACER', 'SECTION_HIDDEN'],
                ['BUTTON_SCREEN_ITEM', 'ONCLICK_CALL_FUNCTIONS'],
                ['BUTTON_SCREEN_ITEM', 'ONMOUSEDOWN_CALL_FUNCTIONS'],
                ['PAGINATION_SCREEN', 'CATALOGUE'],
                ['PAGINATION_SCREEN', 'SUBMIT_URL'],
            ];
            if (in_array([$template, $parameter], $exceptions)) {
                $ok = true;
            }

            if (!$ok) {
                $missing_parameters[] = "'" . $parameter . "' => TODO()";
            }
        }
    }

    protected function find_template_path($template)
    {
        $places = [
            'templates' => 'tpl',
            'text' => 'txt',
            'xml' => 'xml',
            'javascript' => 'js',
            'css' => 'css',
        ];
        foreach ($places as $subpath => $ext) {
            foreach (['', '_custom'] as $suffix) {
                $template_path = get_file_base() . '/themes/default/' . $subpath . $suffix . '/' . $template . '.' . $ext;
                if (is_file($template_path)) {
                    return $template_path;
                }
            }
        }
        return null;
    }

    protected function is_preview_template_context($context)
    {
        return (strpos($context, '/addon_registry/') !== false) || (strpos($context, 'sources/lorem.php') !== false);
    }

    protected function scan_file($path, &$template_instances)
    {
        $c = file_get_contents(get_file_base() . '/' . $path);

        // No-parameter format
        $matches = [];
        $num_matches = preg_match_all('#do(_lorem|_notification)?_template\(\'(\w+)\'\)#U', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $context = $path . ':' . strval(substr_count($c, "\n", 0, strpos($c, $matches[0][$i])) + 1);

            $template = $matches[2][$i];

            if (($this->only !== null) && ($this->only != $template)) {
                continue;
            }

            $parameters = [];
            $template_instances[$template][$context] = $parameters;
        }

        // Single-line format
        $matches = [];
        $num_matches = preg_match_all('#do(_lorem|_notification)?_template\(\'(\w+)\', \[(.*)\](, null, false, null|\))#U', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $context = $path . ':' . strval(substr_count($c, "\n", 0, strpos($c, $matches[0][$i])) + 1);

            $template = $matches[2][$i];

            if (($this->only !== null) && ($this->only != $template)) {
                continue;
            }

            $_parameters = $matches[3][$i];
            $matches_2 = [];
            $parameters = [];
            $num_matches_2 = preg_match_all('#\'([^\']+)\' => (null)?#m', $_parameters, $matches_2);
            for ($j = 0; $j < $num_matches_2; $j++) {
                $parameter = $matches_2[1][$j];
                if (cms_strtoupper_ascii($parameter) == $parameter) {
                    $parameters[$parameter] = ($matches_2[2][$j] == 'null');
                }
            }

            if (!isset($template_instances[$template])) {
                $template_instances[$template] = [];
            }
            $template_instances[$template][$context] = $parameters;
        }

        // Multi-line format
        $matches = [];
        $num_matches = preg_match_all('#do(_lorem|_notification)?_template\(\'(\w+)\', \[\n(([^\n]*\n)*)\s*\](, null, false, null|\)| \+ )#Us', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $context = $path . ':' . strval(substr_count($c, "\n", 0, strpos($c, $matches[0][$i])) + 1);

            $template = $matches[2][$i];

            if (($this->only !== null) && ($this->only != $template)) {
                continue;
            }

            $_parameters = $matches[3][$i];
            $matches_2 = [];
            $parameters = [];
            $num_matches_2 = preg_match_all('#^\s*\'([^\']+)\' => (null)?#m', $_parameters, $matches_2);
            for ($j = 0; $j < $num_matches_2; $j++) {
                $parameter = $matches_2[1][$j];
                if (cms_strtoupper_ascii($parameter) == $parameter) {
                    $parameters[$parameter] = ($matches_2[2][$j] == 'null');
                }
            }

            if (!isset($template_instances[$template])) {
                $template_instances[$template] = [];
            }
            $template_instances[$template][$context] = $parameters;
        }
    }
}
