<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('cms_homesite_tracker') || !addon_installed('cms_homesite')) {
    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('82e2c878e1c55dfa835b2234cc823a30')));
}

// Do not allow reports from Guests due to excessive spam
if (is_guest()) {
    access_denied('NOT_AS_GUEST');
}

require_code('decision_tree');
require_code('mantis');
require_code('cms_homesite');
require_lang('decision_tree');

global $BASE_URL;
$BASE_URL = get_custom_base_url();

// Get tracker categories
$_categories = collapse_2d_complexity('id', 'name', $GLOBALS['SITE_DB']->query('SELECT id,name FROM mantis_category_table WHERE status=0 ORDER BY name'));
$categories = array_unique($_categories);

// Submit?
$type = get_param_string('type', 'browse');
if ($type == 'submit') {
    require_code('version2');

    // Required
    $project = post_param_string('project');
    $_severity = explode(':', post_param_string('severity'));
    $severity = $_severity[0];
    $summary = post_param_string('summary');
    $description = post_param_string('description');

    // Optional
    $_category = post_param_string('category', '[All Projects] General');
    $category_aux = post_param_string('category_aux', '');
    $version = get_version_dotted__from_anything(post_param_string('version', ''));
    $steps_to_reproduce = post_param_string('steps_to_reproduce', '');
    $additional_information = post_param_string('additional_information', '');
    $search = post_param_integer('search', 0);
    $search_tutorials = post_param_integer('search_tutorials', 0);
    $remote_access = post_param_integer('remote_access', 0);

    // Map project values from the form to their ID in Mantis
    $projects = [
        'Core software / bundled addons / default theme' => '1',
        'Downloadable (non-bundled) addons or themes' => '4',
        'Content in a Documentation / Tutorial' => '7',
        'Composr website' => '3',
        'Custom code' => null,
    ];

    // Map severities to their integer value
    $severities = [
        'Feature-request' => '10',
        'Trivial-bug' => '20',
        'Minor-bug' => '50',
        'Major-bug' => '60',
        'Security-hole' => '95'
    ];

    // Set security reports to private
    $view_state = ($severities[$severity] == '95') ? '50' : '10';

    // Get category ID
    // TODO: Need to add back in ORDER BY id once we figure out why it's failing sql_compat and how to fix it
    $category = array_search($_category, $_categories);
    if (($category === false) || ($category === '')) {
        $_category = $GLOBALS['SITE_DB']->query('SELECT id FROM mantis_category_table WHERE status=0 LIMIT 1');
        $category = $_category[0]['id'];
    }

    // Add category aux to summary if applicable
    if ($category_aux != '') {
        $summary = $category_aux . ': ' . $summary;
    }

    // Add confirmation tick boxes if applicable
    if ($search == 1) {
        if ($additional_information != '') {
            $additional_information .= "\n\n";
        }
        $additional_information .= 'I confirm I searched the tracker for an existing issue.';
    }
    if ($search_tutorials == 1) {
        if ($additional_information != '') {
            $additional_information .= "\n\n";
        }
        $additional_information .= 'I confirm I searched the tutorials for an existing one.';
    }
    if ($remote_access == 1) {
        if ($additional_information != '') {
            $additional_information .= "\n\n";
        }
        $additional_information .= 'I grant permission for the core developers to investigate this issue remotely on my site, in accordance with the server access policies ( ' . $BASE_URL . '/server-access.htm ), via the FTP credentials provided on my member profile (developers: you must exchange an e-mail contact with the user and send a digital copy [not just a link] of the server access policy for them to agree to via e-mail before accessing the server).';
    }

    // Create the tracker issue
    $tracker_id = create_tracker_issue($version, $summary, $description, $additional_information, $severities[$severity], strval($category), $projects[$project], '0', $steps_to_reproduce, '100', '10', '10', $view_state);
    create_tracker_post($tracker_id, 'Automated message: This issue was created using the Report Issue Wizard on the Composr homesite.');

    // Inform the member it has been done with a redirect to it.
    $decision_tree = [
        'submit' => [
            'title' => 'Issue Submitted',
            'text' => 'Thank you for submitting an issue! Your issue is [url="#' . strval($tracker_id) . '"]' . $BASE_URL . '/tracker/view.php?id=' . strval($tracker_id) . '[/url] on the tracker. You can click the issue number to be directed to it. Be sure to save or bookmark the page for future reference.' . "\n\n" . 'If you have any screenshots or relevant files to attach to the issue (such as errors and stack traces), you can do so in a follow-up comment on the issue.',
        ]
    ];

    $ob = new DecisionTree($decision_tree, 'submit');
} else {
    $decision_tree = [
        'start' => [
            'title' => 'Report an Issue or Feature / Suggestion',
            'text' => 'Thank you for taking the time to report an issue or a feature / suggestion for Composr CMS. Your feedback is what helps improve Composr CMS and make it the best software it can be for everyone. This wizard will guide you through the process of making an issue. If you prefer, you can make an issue directly on the tracker at ' . $BASE_URL . '/tracker/ instead. This wizard aims to simplify the process by asking questions specific to your selections.' . "\n\n" . 'Please refer to the relevant section of the [page="docs:tut-software-feedback"]providing feedback tutorial[/page] for guidance on making an effective report / issue. At any time, click the question mark next to a field for guidance on what to fill out.',
            'form_method' => 'POST',
            'questions' => [
                'search' => [
                    'label' => 'Searched the tracker for existing issues?',
                    'description' => 'Did you already search the tracker to see if your issue was already reported by someone else? You can do so from your Admin Zone dashboard in the version block (there is a link to view reported issues), or at ' . $BASE_URL . '/tracker/view_all_bug_page.php (make sure you have "All Projects" selected). We encourage you do so, but we do not require it especially if the interface is overwhelming.',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'project' => [
                    'label' => 'Choose relevant component',
                    'description' => 'Which of these best describes the component of the issue you are reporting or the request you are making?',
                    'type' => 'list',
                    'default' => 'Core software / bundled addons / default theme',
                    'default_list' => [
                        'Core software / bundled addons / default theme',
                        'Downloadable (non-bundled) addons or themes',
                        'Content in a Documentation / Tutorial',
                        'Composr website',
                        'Custom code',
                    ],
                    'options' => 'widget=radio',
                    'required' => true,
                ],
            ],
            'next' => [
                // Parameter, Value, Target
                ['project', 'Custom code', 'custom_code'],
                ['project', 'Core software / bundled addons / default theme', 'core_category'],
                ['project', 'Content in a Documentation / Tutorial', 'doc_issue'],
                ['project', 'Downloadable (non-bundled) addons or themes', 'nb_issue'],
                ['project', 'Composr website', 'site_issue'],
            ],
        ],

        'custom_code' => [
            'title' => 'Issues for custom code are not supported',
            'text' => 'We apologize, but the Composr issue tracker is not for reporting issues with custom code which is not part of the core Composr software or a non-bundled addon. Please consider utilising one of the available support options at ' . $BASE_URL . '/support.htm to get help.',
        ],

        'core_category' => [
            'expects_parameters' => [
                'project'
            ],
            'title' => 'Basic Issue Information (Core software)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'questions' => [
                'category' => [
                    'label' => 'Addon / Category',
                    'description' => 'Choose the relevant addon / category for this issue. If you do not know, you can make a best guess; developers can always correct this later. Or, you can use "General / Uncategorised".',
                    'type' => 'list',
                    'default' => '',
                    'default_list' => $categories,
                    'options' => '',
                    'required' => true,
                ],
                'version' => [
                    'label' => 'Composr version',
                    'description' => 'Please specify what version of Composr you are running (this can be found in your Admin Zone dashboard).',
                    'type' => 'short_text',
                    'default' => get_latest_version_dotted(),
                    'options' => '',
                    'required' => false,
                ],
                'severity' => [
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => [
                        'Feature-request: For feature requests and suggestions on improving Composr',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the software',
                        'Minor-bug: For issues that affect software operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features unusable or cause corruption to the site',
                        'Security-hole: For reporting security vulnerabilities in the software'
                    ],
                    'options' => 'widget=radio',
                    'required' => true,
                ],
            ],
            'next' => [
                // Parameter, Value, Target
                ['severity', 'Feature-request: For feature requests and suggestions on improving Composr', 'feature'],
                ['severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the software', 'bug'],
                ['severity', 'Minor-bug: For issues that affect software operation but not to the point entire features are unusable', 'bug'],
                ['severity', 'Major-bug: For issues that render entire features unusable or cause corruption to the site', 'bug'],
                ['severity', 'Security-hole: For reporting security vulnerabilities in the software', 'security'],
            ],
        ],

        'nb_issue' => [
            'expects_parameters' => [
                'project'
            ],
            'title' => 'Basic Issue Information (Non-bundled addons)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'notice' => [
              'Please do not use the issue tracker to report issues with addons or themes which were not downloaded from your Admin Zone\'s Addons page or from the Addons / Themes pages on the Composr homesite. If you need support for an addon independently distributed by someone else, please see ' . $BASE_URL . '/support.htm .'
            ],
            'questions' => [
                'category_aux' => [
                    'label' => 'Addon / Theme',
                    'description' => 'If applicable, please specify the name of the non-bundled addon or theme relevant to your issue.',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
                'version' => [
                    'label' => 'Composr version',
                    'description' => 'Please specify what version of Composr you are running (this can be found in your Admin Zone dashboard).',
                    'type' => 'short_text',
                    'default' => get_latest_version_dotted(),
                    'options' => '',
                    'required' => false,
                ],
                'severity' => [
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => [
                        'Feature-request: For feature requests and suggestions on improving a non-bundled addon',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the addon',
                        'Minor-bug: For issues that affect addon operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features / addons unusable or cause corruption to the site',
                        'Security-hole: For reporting security vulnerabilities in an addon'
                    ],
                    'options' => 'widget=radio',
                    'required' => true,
                ],
            ],
            'next' => [
                // Parameter, Value, Target
                ['severity', 'Feature-request: For feature requests and suggestions on improving a non-bundled addon', 'feature'],
                ['severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the addon', 'bug'],
                ['severity', 'Minor-bug: For issues that affect addon operation but not to the point entire features are unusable', 'bug'],
                ['severity', 'Major-bug: For issues that render entire features / addons unusable or cause corruption to the site', 'bug'],
                ['severity', 'Security-hole: For reporting security vulnerabilities in an addon', 'security'],
            ],
        ],

        'site_issue' => [
            'expects_parameters' => [
                'project'
            ],
            'title' => 'Basic Issue Information (Composr homesite)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'questions' => [
                'severity' => [
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => [
                        'Feature-request: For feature requests and suggestions on improving the Composr site',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the Composr site',
                        'Minor-bug: For issues that affect the Composr site\'s operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features unusable or cause corruption to the Composr site',
                        'Security-hole: For reporting security vulnerabilities in the Composr site'
                    ],
                    'options' => 'widget=radio',
                    'required' => true,
                ],
            ],
            'next' => [
                // Parameter, Value, Target
                ['severity', 'Feature-request: For feature requests and suggestions on improving the Composr site', 'feature'],
                ['severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the Composr site', 'bug'],
                ['severity', 'Minor-bug: For issues that affect the Composr site\'s operation but not to the point entire features are unusable', 'bug'],
                ['severity', 'Major-bug: For issues that render entire features unusable or cause corruption to the Composr site', 'bug'],
                ['severity', 'Security-hole: For reporting security vulnerabilities in the Composr site', 'security'],
            ],
        ],

        'feature' => [
            'expects_parameters' => [
                'project',
                'severity'
            ],
            'title' => 'Feature / Request Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your feature / request. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => [
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ],
            'warn' => [
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ],
            'form_method' => 'POST',
            'questions' => [
                'summary' => [
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your feature / request in one concise sentence; this will help developers quickly understand your request at a glance. For example, you might say "Implement support for the Doggy Biscuits API".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Describe your feature / request',
                    'description' => 'Elaborate your feature / request in more details here. What would you like to see implemented? How should it be implemented? What should it do? etc.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'additional_information' => [
                    'label' => 'How will this benefit Composr? + Additional Info',
                    'description' => 'Please provide any additional information about your request here. For example, you can elaborate on why you believe this feature / request will improve the overall Composr software for everyone.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
            ],
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(['page' => '_SELF', 'type' => 'submit']),
        ],

        'bug' => [
            'expects_parameters' => [
                'project',
                'severity'
            ],
            'title' => 'Bug Report Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your bug / issue report. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => [
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ],
            'warn' => [
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ],
            'form_method' => 'POST',
            'questions' => [
                'summary' => [
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state the bug in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "Uploading an entry to a member gallery triggers a critical error".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Explain the bug / issue',
                    'description' => 'Elaborate on the bug / issue in more details here. What did you attempt to do? What did you expect to happen? What actually happened? What error messages did you get?',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'steps_to_reproduce' => [
                    'label' => 'How do you reproduce the bug / issue?',
                    'description' => 'If possible / applicable, please explain how one can reproduce this bug / issue. Please list steps in sequential order and note any special details (such as config options that need to be set).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
                'additional_information' => [
                    'label' => 'Additional Info / Workarounds / Server Environment',
                    'description' => 'Please provide any additional information about the bug / issue here. For example, you can provide relevant non-sensitive details about your server environment... PHP version, web server and version, RAM/CPU, etc (not relevant if reporting a Composr site issue). Or if you found a workaround, you can mention it here.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
                'remote_access' => [
                    'label' => 'Allow remote access to my site for investigation (see ?)',
                    'description' => 'If this is an issue you think would need the developers to investigate remotely on your server, tick this box. By doing so, you agree to the [page="_SEARCH:server_access"]server access policy[/page]. And you should ensure, immediately after submitting your issue, that your FTP credentials on your member profile are up-to-date (they are encrypted so only active core developers can see them).',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
            ],
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(['page' => '_SELF', 'type' => 'submit']),
        ],

        'security' => [
            'expects_parameters' => [
                'project',
                'severity'
            ],
            'title' => 'Security Vulnerability Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your security vulnerability report. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => [
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ],
            'notice' => [
                'Security issues will be reported to the tracker privately; only you (assuming you are logged in) and the core developers will see the issue.'
            ],
            'warn' => [
                'Please follow responsible practices for disclosing security vulnerabilities, located at ' . $BASE_URL . '/docs/tut-software-feedback.htm#title__46 . Do not publicly disclose the vulnerability anywhere until a confirmed patch has been released by the Composr core developer team.',
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ],
            'form_method' => 'POST',
            'questions' => [
                'summary' => [
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state the security vulnerability in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "XSS injection vulnerability on the tickets creation page".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Explain the vulnerability',
                    'description' => 'Elaborate on the security vulnerability in more details here. What did you attempt to do? What did you expect to happen? What actually happened? What error messages did you get? How did the vulnerability affect the stability of your site?',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'steps_to_reproduce' => [
                    'label' => 'How do you expose / exploit this vulnerability?',
                    'description' => 'Please explain how one can reproduce / verify this security vulnerability. Please list steps in sequential order and note any special details (such as config options that need to be set or that one must gain access to a privileged account first). This field is required for security vulnerability reports.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'additional_information' => [
                    'label' => 'Additional Info / Workarounds / Server Environment',
                    'description' => 'Please provide any additional information about the vulnerability here. For example, you can provide relevant non-sensitive details about your server environment... PHP version, web server and version, RAM/CPU, etc (not relevant if reporting a vulnerability with the Composr site). Or you can provide workarounds to negate the security hole until it is patched.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
                'remote_access' => [
                    'label' => 'Allow remote access to my site for investigation (see ?)',
                    'description' => 'If this is an issue you think would need the developers to investigate remotely on your server, tick this box. By doing so, you agree to the [page="_SEARCH:server_access"]server access policy[/page]. And you should ensure, immediately after submitting your issue, that your FTP credentials on your member profile are up-to-date (they are encrypted so only active core developers can see them).',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
            ],
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(['page' => '_SELF', 'type' => 'submit']),
        ],

        'doc_issue' => [
            'expects_parameters' => [
                'project'
            ],
            'title' => 'Basic Issue Information (Documentation)',
            'text' => 'Step 2 of 3: I will now ask a few quick questions so I can best guide you to the next screen for your issue.',
            'form_method' => 'POST',
            'questions' => [
                'category_aux' => [
                    'label' => 'Name of Tutorial Page',
                    'description' => 'Please specify the name of the tutorial page relevant to your issue. If you are suggesting a new tutorial, please provide a name for your tutorial.',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
                'severity' => [
                    'label' => 'Issue Type',
                    'description' => 'Please choose the type of the issue you are reporting. Note that for tutorial pages, major bugs and security holes do not apply. If there is a bug with the tutorial system itself, it should be reported under Downloadable (non-bundled) addons or themes.',
                    'type' => 'list',
                    'default' => '',
                    'default_list' => [
                        'Feature-request: For suggesting new official tutorials or additions to existing ones',
                        'Trivial-bug: For reporting typos or inaccuracies in existing tutorials',
                        'Minor-bug: For reporting issues with tutorials not rendering properly',
                    ],
                    'options' => 'widget=radio',
                    'required' => true,
                ],
            ],
            'next' => [
                // Parameter, Value, Target
                ['severity', 'Feature-request: For suggesting new official tutorials or additions to existing ones', 'doc_new'],
                ['severity', 'Trivial-bug: For reporting typos or inaccuracies in existing tutorials', 'doc_fix'],
                ['severity', 'Minor-bug: For reporting issues with tutorials not rendering properly', 'bug'],
            ],
        ],

        'doc_new' => [
            'expects_parameters' => [
                'project',
                'severity',
            ],
            'title' => 'Suggest a New Tutorial',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your new tutorial suggestion. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => [
                'Did you know? You can create your own off-site tutorials and link them to the tutorial index. Just go to ' . $BASE_URL . '/docs/tutorials.htm and scroll down to "Need better information?"',
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ],
            'warn' => [
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ],
            'form_method' => 'POST',
            'questions' => [
                'search_tutorials' => [
                    'label' => 'Did you search for an existing tutorial?',
                    'description' => 'Please tick this box to confirm you already searched the tutorials at ' . $BASE_URL . '/docs/tutorials.htm and did not find a tutorial that exists pertaining to what you are about to suggest.',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'summary' => [
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your new tutorial request in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "New documentation on how to set-up LiteSpeed".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Description of Tutorial / Additions',
                    'description' => 'Elaborate on what information you would like to see in this new tutorial.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'additional_information' => [
                    'label' => 'How will this benefit the community?',
                    'description' => 'Please let us know why you feel this new tutorial will benefit the Composr community at large (why it should be an official tutorial instead of one you can make yourself off-site and add via off-site links).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
            ],
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(['page' => '_SELF', 'type' => 'submit']),
        ],

        'doc_fix' => [
            'expects_parameters' => [
                'project',
                'severity',
            ],
            'title' => 'Report an Error in a Tutorial',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your tutorial issue. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => [
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ],
            'warn' => [
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ],
            'form_method' => 'POST',
            'questions' => [
                'summary' => [
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your issue in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "Minimum supported PHP version indicated in tutorial is wrong".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Explain the tutorial error(s) and their corrections',
                    'description' => 'Explain what error(s) you found in the tutorial and to what they should be corrected (if you can find the correct information).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ],
                'additional_information' => [
                    'label' => 'Additional Information',
                    'description' => 'Please provide any additional information you have pertaining to this issue, if applicable.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ],
            ],
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(['page' => '_SELF', 'type' => 'submit']),
        ],
    ];

    $ob = new DecisionTree($decision_tree, 'start');
}

$tpl = $ob->run();
$tpl->evaluate_echo();
