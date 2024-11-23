<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    visualisation
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
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'visualisation';
        $info['parameters'] = [
            'param',
            'default_sort_column',
            'max',
            'labels',
            'labels_tooltip',
            'columns_display',
            'columns_tooltip',
            'types',
            'has_header_row',
            'guid',
            'class',
            'stylings_header',
            'stylings',
            'classes',
            'transform',
            'max_rows',
            'ignore_value',
        ];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        [$map, empty($map['param']) ? false : @filemtime(get_custom_file_base() . '/uploads/website_specific/' . filter_naughty($map['param']))]
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
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('visualisation', $error_msg)) {
            return $error_msg;
        }

        require_javascript('sortable_tables');
        require_css('sortable_tables');
        require_lang('sortable_tables');

        disable_php_memory_limit();

        $columns_display = empty($map['columns_display']) ? [] : array_map([$this, 'letters_to_numbers'], $this->parse_comma_separated($map['columns_display']));
        $columns_tooltip = empty($map['columns_tooltip']) ? [] : array_map([$this, 'letters_to_numbers'], $this->parse_comma_separated($map['columns_tooltip']));

        $labels = empty($map['labels']) ? [] : $this->parse_comma_separated($map['labels']);
        $labels = $this->set_property_list_alignment($labels, $columns_display);

        $labels_tooltip = empty($map['labels_tooltip']) ? [] : $this->parse_comma_separated($map['labels_tooltip']);
        $labels_tooltip = $this->set_property_list_alignment($labels_tooltip, $columns_display);

        $types = empty($map['types']) ? [] : $this->parse_comma_separated($map['types']);
        $types = $this->set_property_list_alignment($types, $columns_display);

        $class = empty($map['class']) ? '' : $map['class'];

        $stylings_header = empty($map['stylings_header']) ? [] : $this->parse_comma_separated($map['stylings_header']);
        $stylings_header = $this->set_property_list_alignment($stylings_header, $columns_display);

        $stylings = empty($map['stylings']) ? [] : $this->parse_comma_separated($map['stylings']);
        $stylings = $this->set_property_list_alignment($stylings, $columns_display);

        $classes = empty($map['classes']) ? [] : $this->parse_comma_separated($map['classes']);
        $classes = $this->set_property_list_alignment($classes, $columns_display);

        if (empty($map['transform'])) {
            $transform = '';
        } else {
            $transform = $this->parse_comma_separated($map['transform']);
            $transform = $this->set_property_list_alignment($transform, $columns_display);
        }

        $guid = empty($map['guid']) ? '' : $map['guid'];
        $ignore_value = empty($map['ignore_value']) ? '' : $map['ignore_value'];
        $max_rows = empty($map['max_rows']) ? null : intval($map['max_rows']);

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
                return do_template('RED_ALERT', ['_GUID' => 'bd164caaf23e58579ad89c1a5c034786', 'TEXT' => 'We only accept spreadsheet files, for security reasons.']);
            }
            $path = get_custom_file_base() . '/uploads/website_specific/' . filter_naughty($file);
            if (!is_file($path)) {
                $path = get_file_base() . '/uploads/website_specific/' . filter_naughty($file);
            }
            if (!is_file($path)) {
                return paragraph('File not found (' . escape_html($file) . ').', 'encs8t6p4oax17o84fq6uwhjcty6mo13', 'nothing-here');
            }

            $has_header_row = empty($map['has_header_row']) ? true : ($map['has_header_row'] == '1');

            // Load data
            $i = 0;
            $sheet_reader = spreadsheet_open_read($path, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
            $full_header_row = null;
            while (($row = $sheet_reader->read_row()) !== false) {
                // Process out the ignore value
                $value = mixed();
                foreach ($row as $j => $value) {
                    if ($value == $ignore_value) {
                        $row[$j] = '';
                    }
                }

                if (implode('', $row) == '') {
                    continue;
                }

                if ($has_header_row) {
                    $is_header_row = ($i == 0);

                    if ($is_header_row) {
                        $full_header_row = $row;
                    }
                } else {
                    $is_header_row = false;
                    if ($i == 0) {
                        $full_header_row = [];
                        for ($j = 0; $j < count($row); $j++) {
                            $full_header_row[] = chr($j + 65);
                        }
                    }
                }

                if (!$is_header_row) {
                    // Make sure row has the correct column count
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

                    $_rows_raw[] = array_combine($full_header_row, $row);
                }

                $row = $this->adjust_row_to_displayed_columns($row, $columns_display, $columns_tooltip);

                $_rows[] = $row;

                $i++;

                if (($max_rows !== null) && ($i == $max_rows + 1)) {
                    break;
                }
            }
            $sheet_reader->close();

            if (!isset($_rows[0])) {
                return do_template('RED_ALERT', ['_GUID' => '49cb9981981e5cb3b649f809cffe9f48', 'TEXT' => 'Empty spreadsheet file.']);
            }

            // Work out header
            if ($has_header_row) {
                $header_row = array_shift($_rows);
            } else {
                $header_row = $this->adjust_row_to_displayed_columns($full_header_row, $columns_display, $columns_tooltip);
            }

            if (count($header_row) < 2) {
                return do_template('RED_ALERT', ['_GUID' => '50c95ab14a4e59c7b3c70d4ba7b83c38', 'TEXT' => 'We expect at least two headers in the spreadsheet.']);
            }

            // Prepare initial header templating
            foreach ($header_row as $j => $_header) {
                $headers[] = [
                    'LABEL' => isset($labels[$j]) ? $labels[$j] : $_header,
                    'SORTABLE_TYPE' => isset($types[$j]) ? $types[$j] : null,
                    'FILTERABLE' => null,
                    'SEARCHABLE' => null,
                ];
            }
            foreach ($columns_tooltip as $j => $pos) {
                if (isset($full_header_row[$pos - 1])) {
                    $tooltip_headers[] = isset($labels_tooltip[$j]) ? $labels_tooltip[$j] : $full_header_row[$pos - 1];
                }
            }
        } else { // Database table...
            if (stripos($file, 'f_members') !== false) {
                return do_template('RED_ALERT', ['_GUID' => '2289bd818c545ca79e5fb2d16528bc87', 'TEXT' => 'Security filter disallows display of the ' . escape_html($file) . ' table.']);
            }

            if ($max_rows === null) {
                $max_rows = 10000; // Prevent DOS attacks on the database
            }

            $records = $GLOBALS['SITE_DB']->query_select($file, ['*'], [], '', $max_rows);
            if (empty($records)) {
                return paragraph(do_lang('NO_ENTRIES'), 'et1gqf521gjjz8yaz1ecu1x7of4nt16m', 'nothing-here');
            }
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
                if (!empty($columns_display) || !empty($columns_tooltip)) {
                    if (empty($columns_display)) {
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
                            'SORTABLE_TYPE' => isset($types[$j]) ? $types[$j] : null,
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

        // Work out data types and set automatic classnames
        foreach ($headers as $j => &$header) {
            if ($header['SORTABLE_TYPE'] === null) {
                $header['SORTABLE_TYPE'] = $this->determine_field_type($_rows, $j);
            }

            if (!empty($classes[$j])) {
                $classes[$j] .= ' ';
            } else {
                $classes[$j] = '';
            }
            $classes[$j] .= 'column-' . fix_id($header['LABEL'], true);
        }

        // Work out filterability
        $numeric_types = array_flip([
            'raw_number',

            'integer',
            'integer_comma',
            'integer_explicit_sign',
            'integer_comma_explicit_sign',

            'float',
            'float_comma',
            'float_explicit_sign',
            'float_comma_explicit_sign',

            'float_1dp',
            'float_comma_1dp',
            'float_1dp_explicit_sign',
            'float_comma_1dp_explicit_sign',
        ]);
        foreach ($headers as $j => &$header) {
            if ($header['FILTERABLE'] !== null) {
                continue; // Already known
            }

            $values_with_dupes = [];
            foreach ($_rows as &$row) {
                $values_with_dupes[] = $row[$j];
            }
            $values = array_unique($values_with_dupes);
            cms_mb_sort($values, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($values as $i => $value) {
                $values[$i] = $this->apply_formatting($values[$i], $headers[$j]['SORTABLE_TYPE']);
            }
            $too_much_to_filter = (count($values) > 20);
            $header['FILTERABLE'] = (($too_much_to_filter) || (/*No duplication*/count($values) == count($values_with_dupes)) || (isset($numeric_types[$header['SORTABLE_TYPE']]))) ? [] : $values;
            $header['SEARCHABLE'] = ($header['SORTABLE_TYPE'] == 'alphanumeric');
        }

        // Work out minimums and maximums for numeric fields
        $minimums = [];
        $maximums = [];
        foreach ($headers as $j => &$header) {
            $numeric = isset($numeric_types[$headers[$j]['SORTABLE_TYPE']]);
            $minimums[$j] = $numeric ? null : false;
            $maximums[$j] = $numeric ? null : false;
        }
        foreach ($_rows as $i => &$row) {
            foreach ($row as $j => &$value) {
                if (($minimums[$j] !== false) && (is_numeric($value))) {
                    $float_value = floatval($value);
                    if ($float_value >= 0.0) {
                        if (($minimums[$j] === null) || ($float_value < $minimums[$j])) {
                            $minimums[$j] = $float_value;
                        }
                        if (($maximums[$j] === null) || ($float_value > $maximums[$j])) {
                            $maximums[$j] = $float_value;
                        }
                    } else {
                        $minimums[$j] = false;
                        $maximums[$j] = false;
                    }
                }
            }
        }
        foreach ($minimums as $j => $minimum) {
            if (($minimum !== null) && ($minimum !== false)) {
                $classes[$j] .= ' numeric';
            }
        }

        // Create template-ready data
        $rows = new Tempcode();
        $tooltip_headers_sortable = [];
        foreach (array_keys($tooltip_headers) as $j) {
            $field_type = $this->determine_field_type($_rows_tooltip, $j);
            $tooltip_headers_sortable[] = $field_type;
        }
        foreach ($_rows as $i => &$row) {
            $percentage_fills = [];

            foreach ($row as $j => &$value) {
                $percentage_fills[$j] = null;
                if (($minimums[$j] !== false) && ($minimums[$j] !== null) && ($maximums[$j] !== null) && (is_numeric($value))) {
                    $float_value = floatval($value);
                    if ($float_value >= 0.0) {
                        $range = $maximums[$j] - $minimums[$j];
                        $offset = $float_value - $minimums[$j];
                        $percentage_fills[$j] = ($range == 0) ? 0.0 : (($offset / $range) * 100.0);
                    }
                }

                $value = $this->apply_formatting($value, $headers[$j]['SORTABLE_TYPE']);

                $_transform = is_array($transform) ? (isset($transform[$j]) ? $transform[$j] : '') : $transform;
                if ($_transform != '') {
                    $this->apply_transform($value, $_transform);
                }
            }

            $tooltip_values = [];
            foreach ($tooltip_headers as $j => &$header) {
                $tooltip_values[$header] = $this->apply_formatting($_rows_tooltip[$i][$j], $tooltip_headers_sortable[$j]);
            }

            $rows->attach(do_template('SORTABLE_TABLE_ROW', [
                '_GUID' => $guid,
                'HEADERS' => $headers,
                'VALUES' => $row,
                'MINIMUMS' => $minimums,
                'MAXIMUMS' => $maximums,
                'PERCENTAGE_FILLS' => $percentage_fills,
                'STYLINGS' => $stylings,
                'CLASSES' => $classes,
                'TOOLTIP_VALUES' => $tooltip_values,
                'RAW_DATA' => json_encode($_rows_raw[$i]),
            ]));
        }

        // Final render...

        $id = (preg_match('#^[\w_\-]+$#', $guid) != 0) ? $guid : uniqid('', false);

        $reverse_sorting = false;
        if ((!empty($map['default_sort_column'])) && (substr($map['default_sort_column'], 0, 1) == '!')) {
            $reverse_sorting = true;
            $map['default_sort_column'] = substr($map['default_sort_column'], 1);
        }
        $_default_sort_column = max(0, empty($map['default_sort_column']) ? 0 : ($this->letters_to_numbers($map['default_sort_column']) - 1));
        $default_sort_column = empty($columns_display) ? $_default_sort_column : array_search($_default_sort_column + 1, $columns_display);
        if ($default_sort_column === false) {
            $default_sort_column = 0;
        }
        $max = empty($map['max']) ? 25 : intval($map['max']);

        return do_template('SORTABLE_TABLE', [
            '_GUID' => $guid,
            'ID' => $id,
            'CLASS' => $class,
            'DEFAULT_SORT_COLUMN' => ($reverse_sorting ? '!' : '') . strval($default_sort_column),
            'MAX' => strval($max),
            'HEADERS' => $headers,
            'STYLINGS' => $stylings_header,
            'CLASSES' => $classes,
            'ROWS' => $rows,
            'NUM_ROWS' => strval(count($_rows)),
        ]);
    }

    /**
     * Adjust a row to displayed table columns.
     *
     * @param  array $row Original row
     * @param  array $columns_display List of columns to display
     * @param  array $columns_tooltip List of tooltips
     * @return array Adjusted row
     */
    protected function adjust_row_to_displayed_columns(array $row, array $columns_display, array $columns_tooltip)
    {
        if (!empty($columns_display) || !empty($columns_tooltip)) {
            if (empty($columns_display)) {
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
        return $row;
    }

    /**
     * Parse out comma-separated lists, with escaping support.
     *
     * @param  string $str Input string
     * @return array List
     */
    protected function parse_comma_separated(string $str) : array
    {
        $out = [];
        $tmp = '';
        $escaped = false;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];
            if ($escaped) {
                $tmp .= $c;
                $escaped = false;
            } elseif ($c == ',') {
                $out[] = $tmp;
                $tmp = '';
            } elseif ($c == '\\') {
                $escaped = true;
            } else {
                $tmp .= $c;
            }
        }
        $out[] = $tmp;
        return $out;
    }

    /**
     * Support for column letter syntax as an alternative to position-aligned syntax.
     *
     * @param  array $list Property list with use of both possible syntaxes.
     * @param  array $columns_display Columns displayed.
     * @return array Property list with just position-aligned syntax.
     */
    protected function set_property_list_alignment(array $list, array $columns_display) : array
    {
        $_list = empty($columns_display) ? [] : array_fill(0, count($columns_display), null);
        $i = 0;
        foreach ($list as $l) {
            $l = trim($l);

            $matches = [];
            if (preg_match('#^([A-Z\d]+)=(.*)$#i', $l, $matches) != 0) {
                $column_code = $this->letters_to_numbers($matches[1]);
                foreach ($columns_display as $column_index => $_column_code) {
                    if ($_column_code == $column_code) {
                        $_list[$column_index] = $matches[2];
                    }
                }
            } else {
                $_list[$i] = $l;
                $i++;
            }
        }
        return $_list;
    }

    /**
     * Convert column letters to normal numbers (starting from 1).
     *
     * @param  string $val Value to convert
     * @return integer Converted value
     */
    public function letters_to_numbers(string $val) : int
    {
        if (!addon_installed('visualisation')) {
            return 0;
        }

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

        return intval(str_replace($letters, $numbers, $val));
    }

    /**
     * Find a field type for a column index.
     *
     * @param  array $_rows Rows
     * @param  integer $j Column offset
     * @return string Field type
     * @set integer float date currency alphanumeric
     */
    protected function determine_field_type(array $_rows, int $j) : string
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

        if (do_lang('locale_thousands_sep') == '.') {
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
    protected function apply_formatting(string $value, string $sortable_type) : string
    {
        $numeric = false;

        if (strpos($sortable_type, '_comma') === false) {
            $decimal_separator = '.';
            $thousands_separator = ',';
        } else {
            $decimal_separator = ',';
            $thousands_separator = '.';
        }

        if (is_numeric($value)) {
            if (strpos($sortable_type, 'integer') !== false) {
                $value = number_format(floatval($value), 0, $decimal_separator, $thousands_separator);
                $numeric = true;
            }

            if (strpos($sortable_type, 'float') !== false) {
                if (strpos($sortable_type, '1dp') === false) {
                    $num_digits = 0;
                    if (strpos($value, '.') !== false) {
                        $num_digits = strlen($value) - strpos($value, '.') - 1;
                    }
                    $value = number_format(floatval($value), $num_digits, $decimal_separator, $thousands_separator);
                } else {
                    $num_digits = 1;
                    $value = number_format(floatval($value), $num_digits, $decimal_separator, $thousands_separator);
                }
                $numeric = true;
            }
        }

        if (strpos($sortable_type, '_explicit_sign') !== false) {
            if ($value == '') {
                $value = '+0';
            } elseif ((substr($value, 0, 1) != '-') && ($numeric)) {
                $value = '+' . $value;
            }
        }

        return $value;
    }

    /**
     * Apply a transform to a cell value.
     *
     * @param  string $value Value to apply transform to (passed by reference).
     * @param  ID_TEXT $transform Transform type.
     */
    protected function apply_transform(string &$value, string $transform)
    {
        switch ($transform) {
            case 'country_names':
                require_code('locations');
                $_value = find_country_name_from_iso($value);
                if ($_value !== null) {
                    $value = $_value;
                }
                break;

            case 'ucwords':
                $value = cms_mb_ucwords(cms_mb_strtolower($value));
                break;

            case 'non-numeric-italics':
                if ((!is_numeric($value)) && ($value != '')) {
                    $value = protect_from_escaping('<em>' . escape_html($value) . '</em>');
                }
                break;

            case 'flag':
                $country = $value;

                // First, see if the provided value is already a country code
                $theme_image = find_theme_image('flags_large/' . strtolower($country), true);

                if ($theme_image == '') {
                    // Not a country code, so maybe a country name?
                    require_code('locations');
                    $_country = find_iso_country_from_name($country);

                    if ($_country === null) {
                        return; // Not a country at all; exit.
                    }

                    $country = $_country;

                    $theme_image = find_theme_image('flags_large/' . strtolower($country), true);
                    if ($theme_image == '') { // Country, but we do not have a flag for it, so return the country code instead.
                        $value = protect_from_escaping(escape_html($country));
                        return;
                    }
                }

                $value = protect_from_escaping('<span class="accessibility_hidden">' . escape_html($country) . '</span>&nbsp;<img width="24" src="' . escape_html($theme_image) . '" alt="" title="' . escape_html($country) . '" />');
                break;
        }
    }
}
