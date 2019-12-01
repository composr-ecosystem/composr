<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sortable_tables
 */

/**
 * Block class.
 */
class Block_main_sortable_table
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info()
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = ['param', 'default_sort_column', 'max', 'labels', 'labels_tooltip', 'columns_display', 'columns_tooltip', 'guid', 'transform', 'stylings', 'classes'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        $map
PHP;
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT;
        $info['ttl'] = 60 * 60 * 24 * 365 * 5;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('sortable_tables', $error_msg)) {
            return $error_msg;
        }

        require_javascript('sortable_tables');
        require_css('sortable_tables');
        require_lang('sortable_tables');

        disable_php_memory_limit();

        $letters = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];
        $numbers = [
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
        ];
        if (!@cms_empty_safe($map['columns_display'])) {
            $map['columns_display'] = str_replace($letters, $numbers, $map['columns_display']);
        }
        if (!@cms_empty_safe($map['columns_tooltip'])) {
            $map['columns_tooltip'] = str_replace($letters, $numbers, $map['columns_tooltip']);
        }

        $labels = @cms_empty_safe($map['labels']) ? [] : array_map('trim', explode(',', $map['labels']));
        $labels_tooltip = @cms_empty_safe($map['labels_tooltip']) ? [] : array_map('trim', explode(',', $map['labels_tooltip']));
        $columns_display = @cms_empty_safe($map['columns_display']) ? [] : array_map('intval', array_map('trim', explode(',', $map['columns_display'])));
        $columns_tooltip = @cms_empty_safe($map['columns_tooltip']) ? [] : array_map('intval', array_map('trim', explode(',', $map['columns_tooltip'])));

        $stylings = empty($map['stylings']) ? [] : array_map('trim', explode(',', $map['stylings']));
        $classes = empty($map['classes']) ? [] : array_map('trim', explode(',', $map['classes']));

        if (empty($map['transform'])) {
            $transform = [];
        } else {
            $transform = array_map('trim', explode(',', $map['transform']));
        }

        $guid = empty($map['guid']) ? '' : $map['guid'];

        // What will we be reading?
        $file = empty($map['param']) ? 'example.csv' : $map['param'];

        $headers = [];
        $_rows = [];
        $tooltip_headers = [];
        $_rows_tooltip = [];
        $_rows_raw = [];

        // Spreadsheet file
        if (strpos($file, '.') !== false) {
            require_code('files_spreadsheets_read');

            // Find/validate path
            if (!is_spreadsheet_readable($file)) {
                return do_template('RED_ALERT', ['_GUID' => '9kalmcrafmbg3hi4162gursqzdf6q43j', 'TEXT' => 'We only accept spreadsheet files, for security reasons.']);
            }
            $path = get_custom_file_base() . '/uploads/website_specific/' . filter_naughty($file);
            if (!is_file($path)) {
                $path = get_custom_file_base() . '/uploads/website_specific/' . filter_naughty($file);
            }
            if (!is_file($path)) {
                return paragraph('File not found (' . escape_html($file) . ').', 'encs8t6p4oax17o84fq6uwhjcty6mo13', 'nothing-here');
            }

            // Load data
            $i = 0;
            $sheet_reader = spreadsheet_open_read($path, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
            $full_header_row = null;
            while (($row = $sheet_reader->read_row()) !== false) {
                if ($i != 0) {
                    // Make sure row has the right column count
                    for ($j = count($row); $j < count($full_header_row); $j++) { // Too few? Pad.
                        $row[$j] = '';
                    }
                    for ($j = count($full_header_row); $j < count($row); $j++) { // Too many? Truncate.
                        unset($row[$j]);
                    }

                    // Get tooltip columns
                    $row_tooltip = [];
                    foreach ($columns_tooltip as $pos) {
                        if (isset($row[$pos - 1])) {
                            $row_tooltip[] = $row[$pos - 1];
                        }
                    }
                    $_rows_tooltip[] = $row_tooltip;
                }

                if ($i == 0) {
                    $full_header_row = $row;
                }

                if ($i != 0) {
                    $_rows_raw[] = array_combine($full_header_row, $row);
                }

                // Filter to displayed table columns
                if ($columns_display != [] || $columns_tooltip != []) {
                    if ($columns_display == []) {
                        foreach ($row as $key => $val) {
                            if (in_array($key + 1, $columns_tooltip)) {
                                unset($row[$key]);
                            }
                        }
                        $row = array_values($row);
                    } else {
                        $row_new = [];
                        foreach ($columns_display as $pos) {
                            if (isset($row[$pos - 1])) {
                                $row_new[] = $row[$pos - 1];
                            }
                        }
                        $row = $row_new;
                    }
                }

                if (implode('', $row) == '') {
                    continue;
                }

                foreach ($row as $j => $val) {
                    foreach ($transform as $_transform) {
                        switch ($_transform) {
                            case 'ucwords':
                                $val = cms_mb_ucwords($val);
                                break;

                            case 'non-numeric-italics':
                                if (!is_numeric($val)) {
                                    $row[$j] = protect_from_escaping('<em>' . escape_html($val) . '</em>');
                                }
                                break;
                        }
                    }
                }

                $_rows[] = $row;

                $i++;
            }
            $sheet_reader->close();

            // Work out header
            if (isset($_rows[0])) {
                $header_row = array_shift($_rows);

                if (count($header_row) < 2) {
                    return do_template('RED_ALERT', ['_GUID' => '37odjvkieql3atnq0mjb9yves78wc6yo', 'TEXT' => 'We expect at least two headers in the spreadsheet.']);
                }
            } else {
                return do_template('RED_ALERT', ['_GUID' => '006kvk6di5j0d4x1h83eb90k8vbbx03m', 'TEXT' => 'Empty spreadsheet file.']);
            }

            // Prepare initial header templating
            foreach ($header_row as $j => $_header) {
                $headers[] = [
                    'LABEL' => isset($labels[$j]) ? $labels[$j] : $_header,
                    'SORTABLE_TYPE' => null,
                    'FILTERABLE' => null,
                    'SEARCHABLE' => null,
                ];
            }
            foreach ($columns_tooltip as $j => $pos) {
                if (isset($full_header_row[$pos - 1])) {
                    $tooltip_headers[] = isset($labels_tooltip[$j]) ? $labels_tooltip[$j] : $full_header_row[$pos - 1];
                }
            }
        } else {
            // Database table...

            if (stripos($file, 'f_members') !== false) {
                return do_template('RED_ALERT', ['_GUID' => '71d6xnfv3fnomqo2jo4xqlm8n98xngwk', 'TEXT' => 'Security filter disallows display of the ' . escape_html($file) . ' table.']);
            }

            $records = $GLOBALS['SITE_DB']->query_select($file, ['*']);
            if (empty($records)) {
                return paragraph(do_lang('NO_ENTRIES'), 'et1gqf521gjjz8yaz1ecu1x7of4nt16m', 'nothing-here');
            }
            $header_row = [];
            foreach ($records as $i => $record) {
                // Get tooltip columns
                $row_tooltip = [];
                $j = 0;
                $keys = array_keys($record);
                $values = array_values($record);
                foreach ($columns_tooltip as $pos) {
                    if (isset($values[$pos - 1])) {
                        $row_tooltip[$keys[$pos - 1]] = $values[$pos - 1];
                    }
                }
                $_rows_tooltip[] = @array_map('strval', array_values($row_tooltip));

                $_rows_raw[] = $record;

                // Filter to displayed table columns
                if ($columns_display != [] || $columns_tooltip != []) {
                    if ($columns_display == []) {
                        foreach (array_keys($record) as $j => $key) {
                            if (in_array($j + 1, $columns_tooltip)) {
                                unset($record[$key]);
                            }
                        }
                    } else {
                        $record_new = [];
                        foreach ($columns_display as $pos) {
                            if (isset($values[$pos - 1])) {
                                $record_new[$keys[$pos - 1]] = $values[$pos - 1];
                            }
                        }
                        $record = $record_new;
                    }

                    $row = array_values($record);
                }
                $_rows[] = @array_map('strval', array_values($record));

                if ($i == 0) {
                    $prefixes = [];
                    foreach (array_keys($record) as $key) {
                        $prefixes[] = (strpos($key, '_') === false) ? '' : (preg_replace('#_.*$#s', '', $key) . '_');
                    }
                    $prefixes = array_count_values($prefixes);
                    asort($prefixes);
                    $prefix = '';
                    if (count($prefixes) > count($record) - 3) {
                        $prefix = key($prefixes);
                    }

                    foreach (array_keys($record) as $j => $key) {
                        $headers[] = [
                            'LABEL' => isset($labels[$j]) ? $labels[$j] : titleify(preg_replace('#^' . preg_quote($prefix, '#') . '#', '', $key)),
                            'SORTABLE_TYPE' => null,
                            'FILTERABLE' => null,
                            'SEARCHABLE' => null,
                        ];
                    }

                    foreach (array_keys($row_tooltip) as $j => $key) {
                        $tooltip_headers[] = isset($labels_tooltip[$j]) ? $labels_tooltip[$j] : titleify(preg_replace('#^' . preg_quote($prefix, '#') . '#', '', $key));
                    }
                }
            }
        }

        // Work out data types
        foreach ($headers as $j => &$header) {
            if ($header['SORTABLE_TYPE'] !== null) {
                continue; // Already known
            }

            $header['SORTABLE_TYPE'] = $this->determine_field_type($_rows, $j);
        }

        // Work out filterability
        foreach ($headers as $j => &$header) {
            if ($header['FILTERABLE'] !== null) {
                continue; // Already known
            }

            $values = [];
            foreach ($_rows as &$row) {
                $values[] = $row[$j];
            }
            $values = array_unique($values);
            cms_mb_sort($values, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($values as $i => $value) {
                $values[$i] = $this->apply_formatting($values[$i], $headers[$j]['SORTABLE_TYPE']);
            }
            $too_much_to_filter = (count($values) > 20);
            $header['FILTERABLE'] = ($too_much_to_filter) ? [] : $values;
            $header['SEARCHABLE'] = ($too_much_to_filter) && ($header['SORTABLE_TYPE'] == 'alphanumeric');
        }

        // Create template-ready data
        $rows = new Tempcode();
        $tooltip_headers_sortable = [];
        foreach (array_keys($tooltip_headers) as $j) {
            $field_type = $this->determine_field_type($_rows_tooltip, $j);
            $tooltip_headers_sortable[] = $field_type;
        }
        foreach ($_rows as $i => &$row) {
            foreach ($row as $j => &$value) {
                $value = $this->apply_formatting($value, $headers[$j]['SORTABLE_TYPE']);
            }

            $tooltip_values = [];
            foreach ($tooltip_headers as $j => &$header) {
                $tooltip_values[$header] = $this->apply_formatting($_rows_tooltip[$i][$j], $tooltip_headers_sortable[$j]);
            }

            $rows->attach(do_template('SORTABLE_TABLE_ROW', [
                '_GUID' => $guid,
                'HEADERS' => $headers,
                'VALUES' => $row,
                'STYLINGS' => $stylings,
                'CLASSES' => $classes,
                'TOOLTIP_VALUES' => $tooltip_values,
                'RAW_DATA' => json_encode($_rows_raw[$i]),
            ]));
        }

        // Final render...

        $id = (preg_match('#^[\w_]+$#', $guid) != 0) ? $guid : uniqid('', false);

        $_default_sort_column = max(0, empty($map['default_sort_column']) ? 0 : (intval(str_replace($letters, $numbers, $map['default_sort_column'])) - 1));
        $default_sort_column = ($columns_display == []) ? $_default_sort_column : array_search($_default_sort_column + 1, $columns_display);
        if ($default_sort_column === false) {
            $default_sort_column = 0;
        }
        $max = empty($map['max']) ? 20 : intval($map['max']);

        return do_template('SORTABLE_TABLE', [
            '_GUID' => $guid,
            'ID' => $id,
            'DEFAULT_SORT_COLUMN' => strval($default_sort_column),
            'MAX' => strval($max),
            'HEADERS' => $headers,
            'ROWS' => $rows,
            'NUM_ROWS' => strval(count($_rows)),
        ]);
    }

    /**
     * Find a field type for a column index.
     *
     * @param  array $_rows Rows
     * @param  integer $j Column offset
     * @return string Field type
     * @set integer float date currency alphanumeric
     */
    public function determine_field_type($_rows, $j)
    {
        $sortable_type = null;
        foreach ($_rows as $row) {
            if ($row[$j] != '') {
                if ((is_numeric($row[$j])) && (strpos($row[$j], '.') === false)) {
                    if ($sortable_type === null) {
                        $sortable_type = 'integer';
                    } else {
                        if ($sortable_type != 'integer' && $sortable_type != 'float'/*an integer value can also fit a float*/) {
                            $sortable_type = null;
                            break;
                        }
                    }
                    continue;
                }

                if ((is_numeric($row[$j])) && (strpos($row[$j], '.') !== false)) {
                    if (($sortable_type === null) || ($sortable_type == 'integer'/*an integer value may upgrade to a float*/)) {
                        $sortable_type = 'float';
                    } else {
                        if ($sortable_type != 'float') {
                            $sortable_type = null;
                            break;
                        }
                    }
                    continue;
                }

                if ((preg_match('#^\d\d\d\d-\d\d-\d\d$#', $row[$j]) != 0) || (preg_match('#^\d\d-\d\d-\d\d\d\d$#', $row[$j]) != 0)) {
                    if ($sortable_type === null) {
                        $sortable_type = 'date';
                    } else {
                        if ($sortable_type != 'date') {
                            $sortable_type = null;
                            break;
                        }
                    }
                    continue;
                }

                if (addon_installed('ecommerce')) {
                    require_code('ecommerce');
                    if (preg_match('#^' . preg_quote(ecommerce_get_currency_symbol(), '#') . '#', $row[$j]) != 0) {
                        if ($sortable_type === null) {
                            $sortable_type = 'currency';
                        } else {
                            if ($sortable_type != 'currency') {
                                $sortable_type = null;
                                break;
                            }
                        }
                        continue;
                    }
                }

                // No pattern matched, has to be alphanumeric
                $sortable_type = null;
                break;
            }
        }

        $locale = localeconv();
        if ($locale['thousands_sep'] == '.') {
            if ($sortable_type === 'integer') {
                $sortable_type = 'integer_comma';
            }
            if ($sortable_type === 'float') {
                $sortable_type = 'float_comma';
            }
        }

        return ($sortable_type === null) ? 'alphanumeric' : $sortable_type;
    }

    /**
     * Apply formatting to a cell value.
     *
     * @param  string $value Value to apply formatting to
     * @param  ID_TEXT $sortable_type Sortable type
     * @set integer float date currency alphanumeric
     * @return string Formatted value
     */
    public function apply_formatting($value, $sortable_type)
    {
        if (($sortable_type == 'integer') && (is_numeric($value))) {
            $value = integer_format(intval($value));
        }

        if (($sortable_type == 'float') && (is_numeric($value))) {
            $num_digits = 0;
            if (strpos($value, '.') !== false) {
                $num_digits = strlen($value) - strpos($value, '.') - 1;
            }
            $value = float_format(floatval($value), $num_digits);
        }

        if (($sortable_type == 'integer_comma') && (is_numeric($value))) {
            $value = number_format(intval($value), 0, ',', '.');
        }

        if (($sortable_type == 'float_comma') && (is_numeric($value))) {
            $num_digits = 0;
            if (strpos($value, ',') !== false) {
                $num_digits = strlen($value) - strpos($value, ',') - 1;
            }
            $value = number_format(floatval($value), $num_digits, ',', '.');
        }

        return $value;
    }
}
