<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite_support_credits') || !addon_installed('composr_homesite')) {
    warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
}

require_code('decision_tree');
require_code('mantis');
require_code('composr_homesite');
require_lang('decision_tree');

global $BASE_URL;
$BASE_URL = get_custom_base_url();

// Get tracker categories
$_categories = collapse_2d_complexity('id', 'name', $GLOBALS['SITE_DB']->query('SELECT id,name FROM mantis_category_table WHERE status=0 ORDER BY name'));
$categories = array_unique($_categories);

// Submit?
$type = get_param_string('type', 'browse');
if ($type == 'submit') {
    // Required
    $project = post_param_string('project');
    $_severity = explode(':', post_param_string('severity'));
    $severity = $_severity[0];
    $summary = post_param_string('summary');
    $description = post_param_string('description');

    // Optional
    $_category = post_param_string('category', '[All Projects] General');
    $category_aux = post_param_string('category_aux', '');
    $version = post_param_string('version', '');
    $steps_to_reproduce = post_param_string('steps_to_reproduce', '');
    $additional_information = post_param_string('additional_information', '');
    $search = post_param_integer('search', 0);
    $search_tutorials = post_param_integer('search_tutorials', 0);

    // Map project values from the form to their ID in Mantis
    $projects = array(
        'Core software / bundled addons / default theme' => '1',
        'Downloadable (non-bundled) addons or themes' => '4',
        'Documentation / Tutorials' => '7',
        'Composr website' => '3',
        'Custom code' => null,
    );

    // Map severities to their integer value
    $severities = array(
        'Feature-request' => '10',
        'Trivial-bug' => '20',
        'Minor-bug' => '50',
        'Major-bug' => '60',
        'Security-hole' => '95'
    );

    // Set security reports to private
    $view_state = ($severities[$severity] == '95') ? '50' : '10';

    // Get category ID
    $category = array_search($_category, $_categories);
    if (($category === false) || ($category === '')) {
        $_category = $GLOBALS['SITE_DB']->query('SELECT id FROM mantis_category_table WHERE status=0 ORDER BY id LIMIT 1');
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

    // Create the tracker issue
    $tracker_id = create_tracker_issue($version, $summary, $description, $additional_information, $severities[$severity], strval($category), $projects[$project], '0', $steps_to_reproduce, '100', '10', '10', $view_state);
    create_tracker_post($tracker_id, 'Automated message: This issue was created using the Report Issue Wizard on the Composr homesite.');

    // Inform the member it has been done with a redirect to it.
    $decision_tree = array(
        'submit' => array(
            'title' => 'Issue Submitted',
            'text' => 'Thank you for submitting an issue! Your issue is [url="#' . strval($tracker_id) . '"]' . $BASE_URL . '/tracker/view.php?id=' . strval($tracker_id) . '[/url] on the tracker. You can click the issue number to be directed to it. If you have any screenshots or relevant files to attach to the issue (such as errors and stack traces), you can do so in a follow-up comment on the issue.',
        )
    );

    $ob = new DecisionTree($decision_tree, 'submit');
} else {

    // Invite guests to log in or join
    if (is_guest()) {
        $join_url = $GLOBALS['FORUM_DRIVER']->join_url();
        if (!is_object($join_url)) {
            $join_url = make_string_tempcode($join_url);
        }
        $login_url = build_url(array('page' => 'login', 'type' => 'browse', 'redirect' => get_self_url(true)), get_module_zone('login'));
        $please_log_in = 'You are not logged in. We advise <a href="' . escape_html($join_url->evaluate()) . '">joining</a> then <a href="' . escape_html($login_url->evaluate()) . '">logging in</a> so you receive notifications when developers follow up with your issue. This also allows you to receive credit for reporting the issue.';
        attach_message(protect_from_escaping($please_log_in), 'notice');
    }

    $decision_tree = array(
        'start' => array(
            'title' => 'Report an Issue or Feature / Suggestion',
            'text' => 'Thank you for taking the time to report an issue or a feature / suggestion for Composr CMS. Your feedback is what helps improve Composr CMS and make it the best software it can be for everyone. This wizard will guide you through the process of making an issue. If you prefer, you can make an issue directly on the tracker at ' . $BASE_URL . '/tracker/ instead. This wizard aims to simplify the process by asking questions specific to your selections.',
            'form_method' => 'POST',
            'questions' => array(
                'search' => array(
                    'label' => 'Searched the tracker for existing issues?',
                    'description' => 'Did you already search the tracker to see if your issue was already reported by someone else? You can do so at ' . $BASE_URL . '/tracker/view_all_bug_page.php (make sure you have "All Projects" selected). We encourage you do so, but we do not require it especially if the interface is overwhelming.',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'project' => array(
                    'label' => 'Choose relevant component',
                    'description' => 'Which of these best describes the component of the issue you are reporting or the request you are making?',
                    'type' => 'list',
                    'default' => 'Core software / bundled addons / default theme',
                    'default_list' => array(
                        'Core software / bundled addons / default theme',
                        'Downloadable (non-bundled) addons or themes',
                        'Documentation / Tutorials',
                        'Composr website',
                        'Custom code',
                    ),
                    'options' => 'widget=radio',
                    'required' => true,
                ),
            ),
            'next' => array(
                // Parameter, Value, Target
                array('project', 'Custom code', 'custom_code'),
                array('project', 'Core software / bundled addons / default theme', 'core_category'),
                array('project', 'Documentation / Tutorials', 'doc_issue'),
                array('project', 'Downloadable (non-bundled) addons or themes', 'nb_issue'),
                array('project', 'Composr website', 'site_issue'),
            ),
        ),

        'custom_code' => array(
            'title' => 'Issues for custom code are not supported',
            'text' => 'We apologize, but the Composr issue tracker is not for reporting issues with custom code which is not part of the core Composr software or a non-bundled addon. Please consider utilising one of the available support options at ' . $BASE_URL . '/support.htm to get help.',
        ),

        'core_category' => array(
            'expects_parameters' => array(
                'project'
            ),
            'title' => 'Basic Issue Information (Core software)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'questions' => array(
                'category' => array(
                    'label' => 'Addon / Category',
                    'description' => 'Choose the relevant addon / category for this issue. If you do not know, you can make a best guess; developers can always correct this later. Or, you can use "General / Uncategorised".',
                    'type' => 'list',
                    'default' => '',
                    'default_list' => $categories,
                    'options' => '',
                    'required' => true,
                ),
                'version' => array(
                    'label' => 'Composr version',
                    'description' => 'Please specify what version of Composr you are running (this can be found in your Admin Zone dashboard). Blank this field if using an unofficial release, such as an alpha or beta version.',
                    'type' => 'short_text',
                    'default' => get_latest_version_dotted(),
                    'options' => '',
                    'required' => false,
                ),
                'severity' => array(
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => array(
                        'Feature-request: For feature requests and suggestions on improving Composr',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the software',
                        'Minor-bug: For issues that affect software operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features unusable or cause corruption to the site',
                        'Security-hole: For reporting security vulnerabilities in the software'
                    ),
                    'options' => 'widget=radio',
                    'required' => true,
                ),
            ),
            'next' => array(
                // Parameter, Value, Target
                array('severity', 'Feature-request: For feature requests and suggestions on improving Composr', 'feature'),
                array('severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the software', 'bug'),
                array('severity', 'Minor-bug: For issues that affect software operation but not to the point entire features are unusable', 'bug'),
                array('severity', 'Major-bug: For issues that render entire features unusable or cause corruption to the site', 'bug'),
                array('severity', 'Security-hole: For reporting security vulnerabilities in the software', 'security'),
            ),
        ),

        'nb_issue' => array(
            'expects_parameters' => array(
                'project'
            ),
            'title' => 'Basic Issue Information (Non-bundled addons)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'notice' => array(
              'Please do not use the issue tracker to report issues with addons or themes which were not downloaded from Composr\'s Addon management screen on the Admin Zone or from the Addons / Themes pages on the Composr homesite. If you need support for an addon independently distributed by someone else, please see ' . $BASE_URL . '/support.htm .'
            ),
            'questions' => array(
                'category_aux' => array(
                    'label' => 'Addon / Theme',
                    'description' => 'If applicable, please specify the name of the non-bundled addon or theme relevant to your issue.',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
                'version' => array(
                    'label' => 'Composr version',
                    'description' => 'Please specify what version of Composr you are running (this can be found in your Admin Zone dashboard). Blank this field if using an unofficial release, such as an alpha or beta version.',
                    'type' => 'short_text',
                    'default' => get_latest_version_dotted(),
                    'options' => '',
                    'required' => false,
                ),
                'severity' => array(
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => array(
                        'Feature-request: For feature requests and suggestions on improving a non-bundled addon',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the addon',
                        'Minor-bug: For issues that affect addon operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features / addons unusable or cause corruption to the site',
                        'Security-hole: For reporting security vulnerabilities in an addon'
                    ),
                    'options' => 'widget=radio',
                    'required' => true,
                ),
            ),
            'next' => array(
                // Parameter, Value, Target
                array('severity', 'Feature-request: For feature requests and suggestions on improving a non-bundled addon', 'feature'),
                array('severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the addon', 'bug'),
                array('severity', 'Minor-bug: For issues that affect addon operation but not to the point entire features are unusable', 'bug'),
                array('severity', 'Major-bug: For issues that render entire features / addons unusable or cause corruption to the site', 'bug'),
                array('severity', 'Security-hole: For reporting security vulnerabilities in an addon', 'security'),
            ),
        ),

        'site_issue' => array(
            'expects_parameters' => array(
                'project'
            ),
            'title' => 'Basic Issue Information (Composr homesite)',
            'text' => 'Step 2 of 3: Please provide the following basic information about your issue.',
            'form_method' => 'POST',
            'questions' => array(
                'severity' => array(
                    'label' => 'Severity / Issue Type',
                    'description' => 'Please choose the type / severity of the issue you are reporting.',
                    'type' => 'list',
                    'default' => 'Feature / Request',
                    'default_list' => array(
                        'Feature-request: For feature requests and suggestions on improving the Composr site',
                        'Trivial-bug: For typos and other issues that do not affect the operation of the Composr site',
                        'Minor-bug: For issues that affect the Composr site\'s operation but not to the point entire features are unusable',
                        'Major-bug: For issues that render entire features unusable or cause corruption to the Composr site',
                        'Security-hole: For reporting security vulnerabilities in the Composr site'
                    ),
                    'options' => 'widget=radio',
                    'required' => true,
                ),
            ),
            'next' => array(
                // Parameter, Value, Target
                array('severity', 'Feature-request: For feature requests and suggestions on improving the Composr site', 'feature'),
                array('severity', 'Trivial-bug: For typos and other issues that do not affect the operation of the Composr site', 'bug'),
                array('severity', 'Minor-bug: For issues that affect the Composr site\'s operation but not to the point entire features are unusable', 'bug'),
                array('severity', 'Major-bug: For issues that render entire features unusable or cause corruption to the Composr site', 'bug'),
                array('severity', 'Security-hole: For reporting security vulnerabilities in the Composr site', 'security'),
            ),
        ),

        'feature' => array(
            'expects_parameters' => array(
                'project',
                'severity'
            ),
            'title' => 'Feature / Request Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your feature / request. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => array(
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ),
            'warn' => array(
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ),
            'form_method' => 'POST',
            'questions' => array(
                'summary' => array(
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your feature / request in one concise sentence; this will help developers quickly understand your request at a glance. For example, you might say "Implement support for the Doggy Biscuits API".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'description' => array(
                    'label' => 'Describe your feature / request',
                    'description' => 'Elaborate your feature / request in more details here. What would you like to see implemented? How should it be implemented? What should it do? etc.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'additional_information' => array(
                    'label' => 'How will this benefit Composr? + Additional Info',
                    'description' => 'Please provide any additional information about your request here. For example, you can elaborate on why you believe this feature / request will improve the overall Composr software for everyone.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
            ),
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(array('page' => '_SELF', 'type' => 'submit'), '_SEARCH'),
        ),

        'bug' => array(
            'expects_parameters' => array(
                'project',
                'severity'
            ),
            'title' => 'Bug Report Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your bug / issue report. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => array(
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ),
            'warn' => array(
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ),
            'form_method' => 'POST',
            'questions' => array(
                'summary' => array(
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state the bug in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "Uploading an entry to a member gallery triggers a critical error".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'description' => array(
                    'label' => 'Explain the bug / issue',
                    'description' => 'Elaborate on the bug / issue in more details here. What did you attempt to do? What did you expect to happen? What actually happened? What error messages did you get?',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'steps_to_reproduce' => array(
                    'label' => 'How do you reproduce the bug / issue?',
                    'description' => 'If possible / applicable, please explain how one can reproduce this bug / issue. Please list steps in sequential order and note any special details (such as config options that need to be set).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
                'additional_information' => array(
                    'label' => 'Additional Info / Server Environment',
                    'description' => 'Please provide any additional information about the bug / issue here. For example, you can provide relevant details about your server environment... PHP version, web server and version, RAM/CPU, etc (not relevant if reporting a Composr site issue).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
            ),
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(array('page' => '_SELF', 'type' => 'submit'), '_SEARCH'),
        ),

        'security' => array(
            'expects_parameters' => array(
                'project',
                'severity'
            ),
            'title' => 'Security Vulnerability Details',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your security vulnerability report. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => array(
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ),
            'notice' => array(
                'Security issues will be reported to the tracker privately; only you (assuming you are logged in) and the core developers will see the issue.'
            ),
            'warn' => array(
                'Please follow responsible practices for disclosing security vulnerabilities, located at ' . $BASE_URL . '/docs/tut-software-feedback.htm#title__46 . Do not publicly disclose the vulnerability anywhere until a confirmed patch has been released by the Composr core developer team.',
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ),
            'form_method' => 'POST',
            'questions' => array(
                'summary' => array(
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state the security vulnerability in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "XSS injection vulnerability on the tickets creation page".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'description' => array(
                    'label' => 'Explain the vulnerability',
                    'description' => 'Elaborate on the security vulnerability in more details here. What did you attempt to do? What did you expect to happen? What actually happened? What error messages did you get? How did the vulnerability affect the stability of your site?',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'steps_to_reproduce' => array(
                    'label' => 'How do you expose / exploit this vulnerability?',
                    'description' => 'Please explain how one can reproduce / verify this security vulnerability. Please list steps in sequential order and note any special details (such as config options that need to be set or that one must gain access to a privileged account first). This field is required for security vulnerability reports.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'additional_information' => array(
                    'label' => 'Additional Info / Server Environment',
                    'description' => 'Please provide any additional information about the vulnerability here. For example, you can provide relevant details about your server environment... PHP version, web server and version, RAM/CPU, etc (not relevant if reporting a vulnerability with the Composr site).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
            ),
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(array('page' => '_SELF', 'type' => 'submit'), '_SEARCH'),
        ),

        'doc_issue' => array(
            'expects_parameters' => array(
                'project'
            ),
            'title' => 'Basic Issue Information (Documentation)',
            'text' => 'Step 2 of 3: I will now ask a few quick questions so I can best guide you to the next screen for your issue.',
            'form_method' => 'POST',
            'notice' => array(
                ''
            ),
            'questions' => array(
                'category_aux' => array(
                    'label' => 'Name of Tutorial Page',
                    'description' => 'Please specify the name of the tutorial page relevant to your issue. If you are suggesting a new tutorial, please provide a name for your tutorial.',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
                'severity' => array(
                    'label' => 'Issue Type',
                    'description' => 'Please choose the type of the issue you are reporting.',
                    'type' => 'list',
                    'default' => '',
                    'default_list' => array(
                        'Feature-request: For suggesting new official tutorials or additions to existing ones',
                        'Trivial-bug: For reporting typos or inaccuracies in existing tutorials',
                        'Minor-bug: For reporting issues with tutorials not rendering properly',
                    ),
                    'options' => 'widget=radio',
                    'required' => true,
                ),
            ),
            'next' => array(
                // Parameter, Value, Target
                array('severity', 'Feature-request: For suggesting new official tutorials or additions to existing ones', 'doc_new'),
                array('severity', 'Trivial-bug: For reporting typos or inaccuracies in existing tutorials', 'doc_fix'),
                array('severity', 'Minor-bug: For reporting issues with tutorials not rendering properly', 'bug'),
            ),
        ),

        'doc_new' => array(
            'expects_parameters' => array(
                'project',
                'severity',
            ),
            'title' => 'Suggest a New Tutorial',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your new tutorial suggestion. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => array(
                'Did you know? You can create your own off-site tutorials and link them to the tutorial index. Just go to ' . $BASE_URL . '/docs/tutorials.htm and scroll down to "Need better information?"',
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ),
            'warn' => array(
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ),
            'form_method' => 'POST',
            'questions' => array(
                'search_tutorials' => array(
                    'label' => 'Did you search for an existing tutorial?',
                    'description' => 'Please tick this box to confirm you already searched the tutorials at ' . $BASE_URL . '/docs/tutorials.htm and did not find a tutorial that exists pertaining to what you are about to suggest.',
                    'type' => 'tick',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'summary' => array(
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your new tutorial request in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "New documentation on how to set-up LiteSpeed".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'description' => array(
                    'label' => 'Description of Tutorial / Additions',
                    'description' => 'Elaborate on what information you would like to see in this new tutorial.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'additional_information' => array(
                    'label' => 'How will this benefit the community?',
                    'description' => 'Please let us know why you feel this new tutorial will benefit the Composr community at large (why it should be an official tutorial instead of one you can make yourself off-site and add via off-site links).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
            ),
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(array('page' => '_SELF', 'type' => 'submit'), '_SEARCH'),
        ),

        'doc_fix' => array(
            'expects_parameters' => array(
                'project',
                'severity',
            ),
            'title' => 'Report an Error in a Tutorial',
            'text' => 'Step 3 of 3: I will now ask you a few questions about your new tutorial issue. If you need help understanding what to put in a field, click the ? icon.',
            'inform' => array(
                'An issue will be created after you proceed from this screen. You can then include relevant uploads / files in a follow-up comment on the issue.'
            ),
            'warn' => array(
                'Do not ever submit account credentials or other secrets or keys in an issue.'
            ),
            'form_method' => 'POST',
            'questions' => array(
                'summary' => array(
                    'label' => 'Concise sentence / summary',
                    'description' => 'Please state your issue in one concise sentence; this will help developers quickly understand your issue at a glance. For example, you might say "Minimum supported PHP version indicated in tutorial is wrong".',
                    'type' => 'short_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'description' => array(
                    'label' => 'Explain the tutorial error(s) and their corrections',
                    'description' => 'Explain what error(s) you found in the tutorial and to what they should be corrected (if you can find the correct information).',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => true,
                ),
                'additional_information' => array(
                    'label' => 'Additional Information',
                    'description' => 'Please provide any additional information you have pertaining to this issue, if applicable.',
                    'type' => 'long_text',
                    'default' => '',
                    'options' => '',
                    'required' => false,
                ),
            ),
            'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
            'next' => build_url(array('page' => '_SELF', 'type' => 'submit'), '_SEARCH'),
        ),
    );

    $ob = new DecisionTree($decision_tree, 'start');
}

$tpl = $ob->run();
$tpl->evaluate_echo();
