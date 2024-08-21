(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.adminPushBugfix = function adminPushBugfix(params, container) {
        var update_automatic_category = function update_automatic_category()
        {
            var i;
// See if we can match all the selected files to a particular category
            var fixed_files = [];
            var fixed_files_e = document.getElementById('fixed_files');
            var file_addons = Array.from(params.gitFound);
            var category_title = null;

            for (i = 0; i < fixed_files_e.options.length; i++) {
                if (fixed_files_e.options[i].selected) {
                    fixed_files.push(fixed_files_e.options[i].value);
                }
            }
            for (i = 0; i < fixed_files.length; i++) {
                let filename = fixed_files[i];
                if ((typeof file_addons[filename] != 'undefined') && (file_addons[filename] !== null)) {
                    if (category_title === null) {
                        category_title = file_addons[filename]; // Nice match to a bundled addon
                    } else if ((file_addons[filename] !== category_title) && (!file_addons[filename].match(/^core(_.*)?$/))) {
                        category_title = 'core'; // Conflict with something other than core, so bump it back to core as a generalisation
                        break; // ... and stop trying
                    }
                }
            }
            if (category_title === null) {
                category_title = 'General'; // Must be from non-bundled addon
            }

            // Find some special general matches
            let is_all_tests = true;
            let is_all_documentation = true;
            let is_all_build_tools = true;
            for (i = 0; i < fixed_files.length; i++) {
                let filename = fixed_files[i];
                if (!filename.match(/^_tests\//)) {
                    is_all_tests = false;
                }
                if (!filename.match(/^docs\//)) {
                    is_all_documentation = false;
                }
                if (!['sources_custom/make_release.php', 'adminzone/pages/modules_custom/admin_make_release.php', 'adminzone/pages/modules_custom/admin_push_bugfix.php'].includes(filename)) {
                    is_all_build_tools = false;
                }

            }
            let correct_general_project = '4';
            if (is_all_tests) {
                correct_general_project = '9';
            }
            if (is_all_documentation) {
                correct_general_project = '7';
            }
            if (is_all_build_tools) {
                correct_general_project = '8';
            }

            // Now select that category
            // TODO: Does not work
            let category_e = document.getElementById('category');
            for (i = 0; i < category_e.options.length; i++) {
                if (category_e.options[i].text === category_title) {
                    category_e.selectedIndex = i;
                    break;
                }
            }

            // Now select the corresponding project
            // TODO: does not work
            let project_e = document.getElementById('project');
            for (i = 0; i < project_e.options.length; i++) {
                if (((project_e.options[i].value === params.defaultProjectId) && (category_title !== 'General')) || ((project_e.options[i].value === correct_general_project) && (category_title === 'General'))) {
                    project_e.selectedIndex = i;
                    break;
                }
            }
        }

        $dom.on(container, 'load', '', function (e, btn) {
            update_automatic_category();
        });
    }
}(window.$cms, window.$util, window.$dom));
