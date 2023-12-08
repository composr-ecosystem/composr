<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    crosswordr
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_css('crossword');
require_code('php-crossword/php_crossword.class');

$id = isset($map['param']) ? $map['param'] : '';
$cols = array_key_exists('cols', $map) ? intval($map['cols']) : 15;
$rows = array_key_exists('rows', $map) ? intval($map['rows']) : 15;
$max_words = array_key_exists('max_words', $map) ? intval($map['max_words']) : 15;

$cache_id = $id . '_' . strval($cols) . '_' . strval($rows) . '_' . strval($max_words);
$cached = (isset($map['cache']) && $map['cache'] == '0') ? null : get_cache_entry('main_crossword', $cache_id, CACHE_AGAINST_NOTHING_SPECIAL);
if (is_null($cached)) {
    $pc = new PHP_Crossword($rows, $cols);

    $pc->setMaxWords($max_words);

    $charset = get_charset();

    $success = $pc->generate();

    if (!$success) {
        return paragraph('Sorry, unable to generate demo crossword - try with more area or less words.', '', 'red_alert');
    }

    $params = array(
        'colors' => 0,
        'fillflag' => 0,
        'cellflag' => ''
    );

    $html = $pc->getHTML($params);
    $words = $pc->getWords();

    require_code('caches2');
    put_into_cache('main_crossword', 60 * 24 * 5000, $cache_id, null, null, '', null, '', array($html, $words));
} else {
    list($html, $words) = $cached;
}

echo '<div class="float_surrounder crossword">';

echo <<<END
<table class="questionTable results_table" border="0" cellpadding="4">
<tr>
    <th>Num.</th>
    <th>Question</th>
</tr>
END;

$word_hints = '';
foreach ($words as $key => $word) {
    $_key = escape_html(strval($key + 1));
    $_question = escape_html($word['question']);
    $_word = $word['word'];
    if ($word_hints != '') {
        $word_hints .= ', ';
    }
    $word_hints .= strval($key + 1) . '=' . $_word;
    echo <<<END
<tr>
    <td>{$_key}.</td>
    <td>{$_question}</td>
</tr>
END;
}

echo <<<END
</table>
END;

echo $html;

echo '</div>';

if ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
    attach_message('As you are staff you can see that the answers are as follows: ' . $word_hints, 'inform');
}
