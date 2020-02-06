<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Get the Tempcode for a bank of tick (check) boxes.
 *
 * @param  array $options A list of tuples: (prettyname, name, value, description)
 * @param  mixed $description A description for this input field
 * @param  ?integer $_tabindex The tab index of the field (null: not specified)
 * @param  mixed $_pretty_name A human intelligible name for this input field (blank: use default)
 * @param  boolean $simple_style Whether to place each tick (check) on a new line
 * @param  ?ID_TEXT $custom_name Name for custom value to be entered to (null: no custom value allowed)
 * @param  ?string $custom_value Value for custom value (null: no custom value known)
 * @return Tempcode The input field
 */
function form_input_various_ticks($options, $description, $_tabindex = null, $_pretty_name = '', $simple_style = false, $custom_name = null, $custom_value = null)
{
    if (!addon_installed('workflows') || !addon_installed('unvalidated')) {
        return non_overridden__form_input_various_ticks($options, $description, $_tabindex, $_pretty_name, $simple_style, $custom_name, $custom_value);
    }

    if (empty($options)) {
        return new Tempcode();
    }

    $options = array_values($options);

    if ($_tabindex === null) {
        $tabindex = get_form_field_tabindex(null);
    } else {
        $_tabindex++;
        $tabindex = $_tabindex;
    }

    if ((is_string($_pretty_name)) && ($_pretty_name == '')) {
        $_pretty_name = do_lang_tempcode('OPTIONS');
    }

    $input = new Tempcode();

    if (count($options[0]) != 3) {
        $options = [[$options, null, new Tempcode()]];
    }
    foreach ($options as $_option) {
        $out = [];
        foreach ($_option[0] as $option) {
            // $disabled has been added to the API, so we must emulate the
            // previous behaviour if it isn't supplied (ie. $disabled == '0')
            if (count($option) == 4) {
                list($pretty_name, $name, $value, $_description) = $option;
                $disabled = '0';
            } elseif (count($option) == 5) {
                list($pretty_name, $name, $value, $_description, $_disabled) = $option;
                $disabled = $_disabled ? '1' : '0';
            } else {
                fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
            }

            $value = (filter_form_field_default($name, $value ? '1' : '0') == '1');

            $out[] = ['CHECKED' => $value, 'TABINDEX' => strval($tabindex), 'NAME' => $name, 'PRETTY_NAME' => $pretty_name, 'DESCRIPTION' => $_description, 'DISABLED' => $disabled];
        }

        if (empty($custom_value)) {
            $custom_value = [''];
        }

        $input->attach(do_template('FORM_SCREEN_INPUT_VARIOUS_TICKS', [
            '_GUID' => 'e6be7f9668020bc2ba5d112300ceba4c',
            'CUSTOM_ACCEPT_MULTIPLE' => is_array($custom_value),
            'CUSTOM_NAME' => $custom_name,
            'CUSTOM_VALUE' => $custom_value,
            'SECTION_TITLE' => $_option[2],
            'EXPANDED' => $_option[1],
            'SIMPLE_STYLE' => $simple_style,
            'SIBLINGS' => strval(count($out)),
            'OUT' => $out,
        ]));
    }
    return _form_input('', $_pretty_name, $description, $input, false, false, $tabindex);
}
