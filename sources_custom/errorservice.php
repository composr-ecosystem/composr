<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Handler for compo.sr error message web service.
 */
function get_problem_match()
{
    header('Content-type: text/plain; charset=' . get_charset());

    $version = get_param_string('version');
    $error_message = get_param_string('error_message', false, true);

    $output = get_problem_match_nearest($error_message);
    if (!is_null($output)) {
        echo $output;
    }
}

/**
 * Find a match for a problem in the database.
 *
 * @param  string $error_message The error that occurred
 * @return ?string The full Comcode (null: not found)
 */
function get_problem_match_nearest($error_message)
{
    // Find matches. Stored in a CSV file.
    $matches = array();
    $myfile = fopen(get_custom_file_base() . '/uploads/website_specific/compo.sr/errorservice.csv', 'rb');
    fgetcsv($myfile); // Skip header row
    while (($row = fgetcsv($myfile)) !== false) {
        list($message, $summary, $how, $solution) = $row;

        $assembled = $summary . "\n\n[title=\"2\"]How did this happen?[/title]\n\n" . $how . "\n\n[title=\"2\"]How do I fix it?[/title]\n\n" . $solution;

        // Possible rebranding
        $brand = get_param_string('product');
        if (($brand != 'Composr') && ($brand != '')) {
            $brand_base_url = get_param_string('product_site', '');
            if ($brand_base_url != '') {
                $assembled = str_replace('Composr', $brand, $assembled);
                $assembled = str_replace('ocProducts', 'The developers', $assembled);
                $assembled = str_replace(get_brand_base_url(), $brand_base_url, $assembled);
            }
        }

        $regexp = str_replace('xxx', '.*', preg_quote($message, '#'));
        if (preg_match('#' . $regexp . '#', $error_message) != 0) {
            $matches[$message] = $assembled;
        }
    }
    fclose($myfile);

    // No matches
    if (count($matches) == 0) {
        return null;
    }

    // Sort by how good the match is (string length)
    uksort($matches, '_strlen_sort');

    // Return best-match result
    $_output = array_pop($matches);
    $output = comcode_to_tempcode($_output, $GLOBALS['FORUM_DRIVER']->get_guest_id());
    return $output->evaluate();
}
