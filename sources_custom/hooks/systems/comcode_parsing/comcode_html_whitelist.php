<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    comcode_html_whitelist
 */

/**
 * Hook class.
 */
class Hook_comcode_parsing_comcode_html_whitelist
{
    /**
     * Get a list of allowed HTML sequences.
     *
     * @return array List of allowed HTML sequences
     */
    public function get_allowed_html_seqs()
    {
        if (!addon_installed('comcode_html_whitelist')) {
            return [];
        }

        $allowed_html_seqs = [];

        require_code('textfiles');
        $whitelists = explode("\n", read_text_file('comcode_whitelist', null, true));
        foreach ($whitelists as $w) {
            if (trim($w) != '') {
                if ($w[0] != '/') {
                    $w = preg_quote($w, '#');
                } else {
                    $w = substr($w, 1, strlen($w) - 2);
                }
                $allowed_html_seqs[] = $w;
            }
        }
        return $allowed_html_seqs;
    }

    /**
     * Find if some Comcode tag sequence in the parsing stream is white-listed.
     *
     * @param  string $comcode_portion The chunk of Comcode
     * @return boolean Whether it is
     *
     * @ignore
     */
    public function comcode_white_listed($comcode_portion)
    {
        if (!addon_installed('comcode_html_whitelist')) {
            return false;
        }

        require_code('textfiles');
        static $whitelists = null;
        if ($whitelists === null) {
            $whitelists = explode("\n", read_text_file('comcode_whitelist'));
        }

        if (in_array($comcode_portion, $whitelists)) {
            return true;
        }
        foreach ($whitelists as $whitelist) {
            if ((substr($whitelist, 0, 1) == '/') && (substr($whitelist, -1) == '/') && (preg_match($whitelist, $comcode_portion) != 0)) {
                return true;
            }
        }

        return false;
    }
}
