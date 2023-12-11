<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    calculatr
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_javascript('checking');

$message = $map['message'];
$equation = $map['equation'];
$equation = str_replace('math.', 'Math.', strtolower($equation)); // Name fields come out lower case, so equation needs to be
echo '<form onsubmit="event.returnValue=false; return false;" action="#" method="post">';
foreach ($map as $key => $val) {
    $key = strtolower($key); // Firefox forces this, but we'll force it too just in case of browser inconsistency
    if (($key != 'equation') && ($key != 'block') && ($key != 'message') && ($key != 'cache')) {
        echo '<p>
            <input class="input_integer_required right" size="6" type="text" id="' . escape_html($key) . '" name="' . escape_html($key) . '" value="" />
            <label class="field_title" for="' . escape_html($key) . '">' . escape_html($val) . '</label>
        </p>';
    }
}
$uniqid = str_replace('.', '_', uniqid('', true));
echo '
    <script>
        function calculate_sum_' . $uniqid . '(elements)
        {
            var equation=\'' . $equation . '\';
            for (var i=0;i<elements.length;i++)
            {
                if (elements[i].name!=\'\')
                    this[elements[i].name]=elements[i].value;
            }
            window.eval(\'var ret=\'+equation);
            return Math.round(ret);
        }
    </script>
    <p class="proceed_button">
        <input onclick="if (check_form(this.form)) window.fauxmodal_alert(\'' . $message . '\'.replace(\'xxx\',calculate_sum_' . $uniqid . '(this.form.elements))); return false;" class="buttons__calculate button_screen_item" type="submit" value="Calculate" />
    </p>
</form>
';
