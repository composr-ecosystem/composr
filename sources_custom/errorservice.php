<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

/**
 * Handler for compo.sr error message web service.
 */
function get_problem_match_script()
{
    if (!addon_installed('composr_homesite')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
    }

    header('Content-Type: text/plain; charset=' . get_charset());

    $version = get_param_string('version');
    $error_message = get_param_string('error_message', false, INPUT_FILTER_GET_COMPLEX);

    $output = get_problem_match_nearest($error_message);
    if ($output !== null) {
        echo $output;
    }
}

/**
 * Find a match for a problem in the database.
 *
 * @param  string $error_message The error that occurred
 * @return ?string The full Comcode (null: not found)
 */
function get_problem_match_nearest(string $error_message) : ?string
{
    require_code('files_spreadsheets_read');

    // Find matches. Stored in a spreadsheet file.
    $matches = [];
    $sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/uploads/website_specific/compo.sr/errorservice.csv');
    while (($row = $sheet_reader->read_row()) !== false) {
        $message = $row['Message'];
        $summary = $row['Summary'];
        $how = $row['How did this happen?'];
        $solution = $row['How do I fix it?'];

        $assembled = $summary . "\n\n[title=\"2\"]How did this happen?[/title]\n\n" . $how . "\n\n[title=\"2\"]How do I fix it?[/title]\n\n" . $solution;

        // Possible rebranding
        $brand = get_param_string('product');
        if (($brand != 'Composr') && ($brand != '')) {
            $brand_base_url = get_param_string('product_site', '');
            if ($brand_base_url != '') {
                $assembled = str_replace('Composr', $brand, $assembled);
                $assembled = str_replace('ocProducts', 'Core Development Team', $assembled);
                $assembled = str_replace(get_brand_base_url(), $brand_base_url, $assembled);
            }
        }

        $regexp = str_replace('xxx', '.*', preg_quote($message, '#'));
        if (preg_match('#' . $regexp . '#', $error_message) != 0) {
            $matches[$message] = $assembled;
        }
    }
    $sheet_reader->close();

    // No matches
    if (empty($matches)) {
        return null;
    }

    // Sort by how good the match is (string length)
    uksort($matches, '_strlen_sort');

    // Return best-match result
    $_output = array_pop($matches);
    $output = comcode_to_tempcode($_output, $GLOBALS['FORUM_DRIVER']->get_guest_id());
    return $output->evaluate();
}
