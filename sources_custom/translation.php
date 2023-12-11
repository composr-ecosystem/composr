<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2018

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    translation
 */

/**
 * Translate some text.
 * Powered by Google Translate.
 *
 * @param  string $text The text to translate
 * @param  integer $context A TRANS_TEXT_CONTEXT_* constant
 * @param  ?LANGUAGE_NAME $from Source language (null: autodetect)
 * @param  ?LANGUAGE_NAME $to Destination language (null: current user's language)
 * @return ?string Translated text (null: some kind of error)
 */
function translate_text($text, $context = 0, $from = null, $to = null)
{
    if ($text == '') {
        return '';
    }

    if (get_option('google_translate_api_key') == '') {
        return null;
    }

    if ($to === null) {
        $to = user_lang();
    }

    if ($to === $from) {
        return null;
    }

    $_to = _get_google_lang_code($to);
    if ($_to === null) {
        return null;
    }
    if ($from !== null) {
        $_from = _get_google_lang_code($from);
        if ($_from === null) {
            return null;
        }
    } else {
        $_from = null;
        if (($to === 'EN') && (preg_match('#^[\x00-\x7F]*$#', ($context == TRANS_TEXT_CONTEXT_plain) ? $text : html_entity_decode($text, ENT_QUOTES, 'utf-8')) != 0)) {
            return null; // Looks like it's already in English (no other languages work well in ASCII): don't waste money on translation
        }
    }

    $cache_map = array(
        't_lang_from' => ($from === null) ? '' : $from,
        't_lang_to' => $to,
        't_text' => $text,
        't_context' => $context,
    );
    $text_result = $GLOBALS['SITE_DB']->query_select_value_if_there('translation_cache', 't_text_result', $cache_map);

    if ($text_result === null) {
        $url = 'https://translation.googleapis.com/language/translate/v2';
        $request = array(
            'q' => $text,
            'source' => $_from,
            'target' => $_to,
            'format' => ($context == TRANS_TEXT_CONTEXT_plain) ? 'text' : 'html',
        );

        $result = _google_translate_api_request($url, $request);

        if ($result === null) {
            return null;
        }
        if (!isset($result['data']['translations'][0]['translatedText'])) {
            return null;
        }

        $text_result = $result['data']['translations'][0]['translatedText'];

        $GLOBALS['SITE_DB']->query_insert('translation_cache', $cache_map + array('t_text_result' => $text_result));
    }

    if (trim($text) == trim($text_result)) {
        return null;
    }

    $ret = '';
    switch ($context) {
        case TRANS_TEXT_CONTEXT_plain:
            $ret = $text_result;
            break;

        case TRANS_TEXT_CONTEXT_html_block:
        case TRANS_TEXT_CONTEXT_html_inline:
            $tag = ($context == TRANS_TEXT_CONTEXT_html_block) ? 'div' : 'span';
            $ret = '<' . $tag . ' lang="' . $_to . '-x-mtfrom-' . $_from . '">' . $text_result . '</' . $tag . '>';
            if ($context == TRANS_TEXT_CONTEXT_html_block) {
                $ret .= '
                <div>
                    <a href="http://translate.google.com/" target="_blank" title="Powered by Google Translate ' . do_lang('LINK_NEW_WINDOW') . '"><img src="' . escape_html(find_theme_image('google_translate')) . '" alt="" /></a>
                </div>
                ';
            }
            break;
    }

    return $ret;
}

/**
 * Convert a standard language codename to a google code.
 *
 * @param  LANGUAGE_NAME $in The code to convert
 * @return string The converted code (null: none found)
 */
function _get_google_lang_code($in)
{
    $url = 'https://translation.googleapis.com/language/translate/v2/languages';
    $ret = str_replace('_', '-', strtolower($in));

    // Now check the language actually exists...

    require_code('files2');
    $result = unserialize(cache_and_carry('_google_translate_api_request', array($url, null)));

    if ($result === null) {
        return null;
    }
    if (!isset($result['data']['languages'])) {
        return null;
    }

    foreach ($result['data']['languages'] as $lang) {
        if ($lang['language'] == $ret) {
            return $ret;
        }
    }

    return null;
}

/**
 * Call a Google translate API.
 *
 * @param  URLPATH $url API URL to call
 * @param  ?array $request Request data (null: GET request)
 * @return ?array Result (null: some kind of error)
 */
function _google_translate_api_request($url, $request = null)
{
    $key = get_option('google_translate_api_key');
    $url .= '?key=' . urlencode($key);

    if ($request === null) {
        $_request = null;
    } else {
        $_request = json_encode($request);
    }

    $_result = http_download_file($url, null, false, false, 'Composr', ($_request === null) ? null : array($_request), null, null, null, null, null, null, null, 0.5, ($request !== null), null, null, ($request === null) ? 'GET' : 'POST', 'application/json');

    $result = @json_decode($_result, true);
    if ($result === false) {
        return null;
    }
    return $result;
}
