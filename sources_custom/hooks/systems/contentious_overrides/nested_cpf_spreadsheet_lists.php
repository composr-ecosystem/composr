<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_spreadsheet_lists
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_nested_cpf_spreadsheet_lists
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (($template_name != 'global') || ($suffix != '.js')) {
            return;
        }

        if (!addon_installed('nested_cpf_spreadsheet_lists')) {
            return;
        }

        if ($GLOBALS['IN_MINIKERNEL_VERSION']) {
            return;
        }

        require_code('nested_spreadsheet');
        $spreadsheet_structure = get_nested_spreadsheet_structure();

        // Sanitisation to protect any data not destined to be available in the form
        $spreadsheet_headings_used = [];
        foreach ($spreadsheet_structure['cpf_fields'] as $spreadsheet_field) {
            $spreadsheet_headings_used[$spreadsheet_field['spreadsheet_heading']] = 1;
            $spreadsheet_headings_used[$spreadsheet_field['spreadsheet_parent_heading']] = 1;
        }
        foreach ($spreadsheet_structure['spreadsheet_files'] as $spreadsheet_filename => $spreadsheet_file) {
            foreach ($spreadsheet_file['data'] as $i => $row) {
                foreach (array_keys($row) as $spreadsheet_heading) {
                    if ($spreadsheet_heading == 'deprecated') {
                        continue;
                    }
                    if (!isset($spreadsheet_headings_used[$spreadsheet_heading])) {
                        unset($spreadsheet_structure['spreadsheet_files'][$spreadsheet_filename]['data'][$i][$spreadsheet_heading]);
                    }
                }
            }
        }

        // Output JavaScript
        $data .= <<<'PHP'
        (function ($cms, $dom){
            'use strict';

            /** @type {Object} */
            window.nestedCsvStructure =
PHP;
        $data .= json_encode($spreadsheet_structure);
        $data .= <<<'PHP'
            ;

            $dom.ready.then(function () {
                var forms = document.getElementsByTagName('form');

                for (var i = 0; i < forms.length; i++) {
                    injectFormSelectChainingForm(forms[i]);
                }
            });

            function injectFormSelectChainingForm(form) {
                var cpfFields = window.nestedCsvStructure.cpf_fields;
                for (var i in cpfFields) {
                    var cpfField = cpfFields[i];
                    if (cpfField.possible_fields === undefined) { // Is not part of list
                        continue;
                    }

                    var element = findCpfFieldElement(form, cpfField);
                    if (element) {
                        injectFormSelectChainingElement(element, cpfField, true);
                    }
                }
            }

            function findCpfFieldElement(form, cpfField) {
                for (var i = 0; i < form.elements.length; i++) {

                    if (form.elements[i].localName === 'select') {

                        for (var j = 0; j < cpfField.possible_fields.length; j++) {

                            if ((form.elements[i].name !== undefined) && (cpfField.possible_fields[j] == form.elements[i].name.replace('[]', ''))) {
                                return form.elements[i];
                            }
                        }
                    }
                }

                return null;
            }

            function injectFormSelectChainingElement(selectEl, cpfField, initialRun) {
                var cpfFields = window.nestedCsvStructure.cpf_fields;

                var changesMadeAlready = true;

                if (cpfField.spreadsheet_parent_heading !== null)  { // We need to look at parent to filter possibilities, if we have one
                    var currentValue = $dom.value(selectEl);

                    $dom.empty(selectEl);  // Wipe list contents
                    var option;

                    var parentCpfFieldElement = findCpfFieldElement(selectEl.form, cpfFields[cpfField.spreadsheet_parent_heading]);
                    var currentParentValue = $dom.value(parentCpfFieldElement);
                    if (currentParentValue.length === 0) { // Parent unset, so this is
                        option = document.createElement('option');
                        selectEl.add(option, null);
                        $dom.html(option, '{!SELECT_OTHER_FIRST,xxx}'.replace(/xxx/g, cpfFields[cpfField.spreadsheet_parent_heading].label));
                        option.value = '';
                    } else { // Parent is set, so we need to filter possibilities
                        // Work out available (filtered) possibilities
                        var spreadsheetData = window.nestedCsvStructure.spreadsheet_files[cpfField.spreadsheet_parent_filename].data;
                        var possibilities = [];
                        for (var i = 0; i < spreadsheetData.length; i++) { // This is going through parent table. Note that the parent table must contain both the child and parent IDs, as essentially it is a linker table. Field names are defined as unique across all spreadsheet files, so you don't need to use the same actual spreadsheet file as the parent field was drawn from.

                            for (var j = 0; j < currentParentValue.length; j++) {

                                if (spreadsheetData[i][cpfField.spreadsheet_parent_heading] == currentParentValue[j]) {
                                    if ((spreadsheetData[i]['deprecated'] === undefined) || (spreadsheetData[i]['deprecated'] == '0') || (window.handle_spreadsheet_deprecation === undefined) || (!window.window.handle_spreadsheet_deprecation)) {

                                        if (spreadsheetData[i][cpfField.spreadsheet_heading] === undefined) {
                                            $cms.inform('Configured linker table does not include child field');
                                        }
                                        possibilities.push(spreadsheetData[i][cpfField.spreadsheet_heading]);
                                    }
                                }
                            }
                        }
                        if (cpfField.spreadsheet_parent_filename != cpfField.spreadsheet_filename) {
                            spreadsheetData = window.nestedCsvStructure.spreadsheet_files[cpfField.spreadsheet_filename].data;
                            for (var i = 0; i < spreadsheetData.length; i++) {
                                if ((spreadsheetData[i]['deprecated'] !== undefined) && (spreadsheetData[i]['deprecated'] == '1') && (window.handle_spreadsheet_deprecation !== undefined) && (window.window.handle_spreadsheet_deprecation)) {
                                    for (var j = 0; j < possibilities.length; j++) {
                                        if (possibilities[j] == spreadsheetData[i][cpfField.spreadsheet_heading]) {
                                            possibilities[j] = null; // Deprecated, so remove
                                        }
                                    }
                                }
                            }
                        }
                        possibilities.sort();

                        // Add possibilities, selecting one if it matches old selection (i.e. continuity maintained)
                        if (!selectEl.multiple) {
                            option = document.createElement('option');
                            selectEl.add(option, null);
                            $dom.html(option, '{!PLEASE_SELECT}');
                            option.value = '';
                        }
                        var previousOne = null;
                        for (var i = 0; i < possibilities.length; i++) {
                            if (possibilities[i] === null) {
                                continue;
                            }

                            if (previousOne != possibilities[i]) { // don't allow dupes (which we know are sequential due to sorting)
                                // not a dupe
                                option = document.createElement('option');
                                selectEl.add(option, null);
                                $dom.html(option, escape_html(possibilities[i]));
                                option.value = possibilities[i];
                                if (currentValue.length == 0) {
                                    if (selectEl.multiple) { // Pre-select all, if multiple input
                                        option.selected = true;
                                    }
                                } else {
                                    for (var j = 0; j < currentValue.length; j++) {
                                        if (possibilities[i] == currentValue[j]) option.selected = true;
                                    }
                                }
                                previousOne = possibilities[i];
                            }
                        }
                        if (!selectEl.multiple) {
                            if (selectEl.options.length == 2) { // Only one thing to select, so may as well auto-select it
                                selectEl.selectedIndex = 1;
                            }
                        }
                    }

                    changesMadeAlready = true;
                } else {
                    changesMadeAlready = false;
                }

                if (initialRun) { // This may effectively be called on non-initial runs, but it would be due to the list filter changes causing a selection change that propagates
                    var allRefreshFunctions = [];

                    $cms.inform('Looking for children of ' + cpfField.spreadsheet_heading + '...');

                    for (var i in cpfFields) {
                        var childCpfField = cpfFields[i], refreshFunction, childCpfFieldElement;

                        if (childCpfField.spreadsheet_parent_heading == cpfField.spreadsheet_heading) {
                            $cms.inform(' ' + cpfField.spreadsheet_heading + ' has child ' + childCpfField.spreadsheet_heading);

                            childCpfFieldElement = findCpfFieldElement(selectEl.form, childCpfField);

                            refreshFunction = function (childCpfFieldElement, childCpfField) {
                                return function () {
                                    $cms.inform('UPDATING: ' + childCpfField.spreadsheet_heading);

                                    if (childCpfFieldElement) {
                                        injectFormSelectChainingElement(childCpfFieldElement, childCpfField, false);
                                    }
                                };
                            }(childCpfFieldElement, childCpfField);

                            allRefreshFunctions.push(refreshFunction);
                        }
                    }

                    selectEl.onchange = function () {
                        for (var i = 0; i < allRefreshFunctions.length; i++) {
                            allRefreshFunctions[i]();
                        }
                    };
                } else {
                    $dom.trigger(selectEl, 'change');  // Cascade
                }
            }

        }(window.$cms || (window.$cms = {}), window.$dom));
PHP;
    }
}
