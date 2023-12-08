<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_csv_lists
 */

/*EXTRA FUNCTIONS: json_encode*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('nested_csv');
$csv_structure = get_nested_csv_structure();

// Sanitisation to protect any data not destined to be available in the form
$csv_headings_used = array();
foreach ($csv_structure['cpf_fields'] as $csv_field) {
    $csv_headings_used[$csv_field['csv_heading']] = 1;
    $csv_headings_used[$csv_field['csv_parent_heading']] = 1;
}
foreach ($csv_structure['csv_files'] as $csv_filename => $csv_file) {
    foreach ($csv_file['data'] as $i => $row) {
        foreach (array_keys($row) as $csv_heading) {
            if ($csv_heading == 'deprecated') {
                continue;
            }
            if (!isset($csv_headings_used[$csv_heading])) {
                unset($csv_structure['csv_files'][$csv_filename]['data'][$i][$csv_heading]);
            }
        }
    }
}

require_code('json');

// Output JavaScript
echo "
    window.nested_csv_structure=" . json_encode($csv_structure) . ";

    add_event_listener_abstract(window,'load',function() {
        var forms=document.getElementsByTagName('form');
        for (var i=0;i<forms.length;i++)
        {
            inject_form_select_chaining__form(forms[i]);
        }
    });

    function inject_form_select_chaining__form(form)
    {
        var cpf_fields=window.nested_csv_structure.cpf_fields;
        for (var i in cpf_fields)
        {
            var cpf_field=cpf_fields[i];
            if (typeof cpf_field.possible_fields=='undefined') continue; // Is not part of list

            var element=find_cpf_field_element(form,cpf_field);
            if (element) inject_form_select_chaining__element(element,cpf_field,true);
        }
    }

    function find_cpf_field_element(form,cpf_field)
    {
        for (var i=0;i<form.elements.length;i++)
        {
            if (form.elements[i].nodeName.toLowerCase()=='select')
            {
                    for (var j=0;j<cpf_field.possible_fields.length;j++)
                    {
                            if ((typeof form.elements[i].name!='undefined') && (cpf_field.possible_fields[j]==form.elements[i].name.replace('[]','')))
                            {
                                        return form.elements[i];
                            }
                    }
            }
        }

        return null;
    }

    function find_selected_on_list(list)
    {
        var out=[];
        for (var i=0;i<list.options.length;i++)
        {
            if (list.options[i].selected) out.push(list.options[i].value);
        }
        return out;
    }

    function inject_form_select_chaining__element(element,cpf_field,initial_run)
    {
        var cpf_fields=window.nested_csv_structure.cpf_fields;

        var changes_made_already=true;

        if (cpf_field.csv_parent_heading!==null) // We need to look at parent to filter possibilities, if we have one
        {
            var current_value=find_selected_on_list(element);

            element.innerHTML=''; // Wipe list contents
            var option;

            var parent_cpf_field_element=find_cpf_field_element(element.form,cpf_fields[cpf_field.csv_parent_heading]);
            var current_parent_value=find_selected_on_list(parent_cpf_field_element);
            if (current_parent_value.length==0) // Parent unset, so this is
            {
                    option=document.createElement('option');
                    element.add(option,null);
                    set_inner_html(option,'" . addslashes(do_lang('SELECT_OTHER_FIRST', 'xxx')) . "'.replace(/xxx/g,cpf_fields[cpf_field.csv_parent_heading].label));
                    option.value='';
            } else // Parent is set, so we need to filter possibilities
            {
                    // Work out available (filtered) possibilities
                    var csv_data=window.nested_csv_structure.csv_files[cpf_field.csv_parent_filename].data;
                    var possibilities=[];
                    for (var i=0;i<csv_data.length;i++) // This is going through parent table. Note that the parent table must contain both the child and parent IDs, as essentially it is a linker table. Field names are defined as unique across all CSV files, so you don't need to use the same actual CSV file as the parent field was drawn from.
                    {
                            for (var j=0;j<current_parent_value.length;j++)
                            {
                                        if (csv_data[i][cpf_field.csv_parent_heading]==current_parent_value[j])
                                        {
                                                        if ((typeof csv_data[i]['deprecated']=='undefined') || (csv_data[i]['deprecated']=='0') || (typeof window.handle_csv_deprecation=='undefined') || (!window.window.handle_csv_deprecation))
                                                        {
                                                                            if (typeof csv_data[i][cpf_field.csv_heading]=='undefined')
                                                                            {
                                                                                                        console.log('Configured linker table does not include child field');
                                                                            }
                                                                            possibilities.push(csv_data[i][cpf_field.csv_heading]);
                                                        }
                                        }
                            }
                    }
                    if (cpf_field.csv_parent_filename!=cpf_field.csv_filename)
                    {
                            csv_data=window.nested_csv_structure.csv_files[cpf_field.csv_filename].data;
                            for (var i=0;i<csv_data.length;i++)
                            {
                                        if ((typeof csv_data[i]['deprecated']!='undefined') && (csv_data[i]['deprecated']=='1') && (typeof window.handle_csv_deprecation!='undefined') && (window.window.handle_csv_deprecation))
                                        {
                                                        for (var j=0;j<possibilities.length;j++)
                                                        {
                                                                            if (possibilities[j]==csv_data[i][cpf_field.csv_heading])
                                                                            {
                                                                                                        possibilities[j]=null; // Deprecated, so remove
                                                                            }
                                                        }
                                        }
                            }
                    }
                    possibilities.sort();

                    // Add possibilities, selecting one if it matches old selection (i.e. continuity maintained)
                    if (!element.multiple)
                    {
                            option=document.createElement('option');
                            element.add(option,null);
                            set_inner_html(option,'" . addslashes(do_lang('PLEASE_SELECT')) . "');
                            option.value='';
                    }
                    var previous_one=null;
                    for (var i=0;i<possibilities.length;i++)
                    {
                            if (possibilities[i]===null) continue;

                            if (previous_one!=possibilities[i]) // don't allow dupes (which we know are sequential due to sorting)
                            { // not a dupe
                                        option=document.createElement('option');
                                        element.add(option,null);
                                        set_inner_html(option,escape_html(possibilities[i]));
                                        option.value=possibilities[i];
                                        if (current_value.length==0)
                                        {
                                                        if (element.multiple) // Pre-select all, if multiple input
                                                        {
                                                                            option.selected=true;
                                                        }
                                        } else
                                        {
                                                        for (var j=0;j<current_value.length;j++)
                                                        {
                                                                            if (possibilities[i]==current_value[j]) option.selected=true;
                                                        }
                                        }
                                        previous_one=possibilities[i];
                            }
                    }
                    if (!element.multiple)
                    {
                            if (element.options.length==2) element.selectedIndex=1; // Only one thing to select, so may as well auto-select it
                    }
            }

            changes_made_already=true;
        } else
        {
            changes_made_already=false;
        }

        if (initial_run) // This may effectively be called on non-initial runs, but it would be due to the list filter changes causing a selection change that propagates
        {
            var all_refresh_functions=[];

            if (typeof window.console!='undefined')
                    console.log('Looking for children of '+cpf_field.csv_heading+'...');

            for (var i in cpf_fields)
            {
                    var child_cpf_field=cpf_fields[i],refresh_function,child_cpf_field_element;

                    if (child_cpf_field.csv_parent_heading==cpf_field.csv_heading)
                    {
                            if (typeof window.console!='undefined')
                                        console.log(' '+cpf_field.csv_heading+' has child '+child_cpf_field.csv_heading);

                            child_cpf_field_element=find_cpf_field_element(element.form,child_cpf_field);

                            refresh_function=function(child_cpf_field_element,child_cpf_field) { return function() {
                                        if (typeof window.console!='undefined')
                                                        console.log('UPDATING: '+child_cpf_field.csv_heading);

                                        if (child_cpf_field_element)
                                                        inject_form_select_chaining__element(child_cpf_field_element,child_cpf_field,false);
                            }; }(child_cpf_field_element,child_cpf_field);

                            all_refresh_functions.push(refresh_function);
                    }
            }

            element.onchange=function() {
                    for (var i=0;i<all_refresh_functions.length;i++)
                    {
                            all_refresh_functions[i]();
                    }
            };
        } else
        {
            element.onchange(); // Cascade
        }
    }
";
