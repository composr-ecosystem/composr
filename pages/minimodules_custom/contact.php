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

$error_msg = new Tempcode();
if (!addon_installed__messaged('cms_homesite', $error_msg)) {
    return $error_msg;
}

require_code('locations');

$disclaimer = 'It is your responsibility to ensure confidence in the chosen provider. The Composr core team does the matching service as a part of the Composr CMS stewardship role and don\'t charge a commission for the service &ndash; so are not in any way commercially responsible for the implementation, or for developer training. We do encourage third-party companies to give back to the Composr CMS project by contributing code improvements made for projects, and we do often make ourselves available to the developer for implementation of certain parts of a referred project.' . "\n\n" . 'Be aware that third-party developers have no special control over the core team\'s development and maintenance priorities.';

$extra_support_inform = [];
$extra_support_notice = [];
$extra_support_warn = [];

/*
$credits_available = intval(get_cms_cpf('support_credits'));
if ($credits_available == 0) {
    $extra_support_notice[] = 'You do not currently have any support credits. You will need to purchase some credits before your ticket can be fully answered, although we\'ll of course reply to confirm reply cost and confirmation that we can provide an answer.';
}
*/

$extra_brief_details = [];
if (is_guest()) {
    $extra_brief_details['job_role'] = [
        'label' => 'Your e-mail address',
        'description' => 'You\'re not logged in to composr.app so please enter your e-mail address so we can contact you.',
        'type' => 'short_text',
        'default' => $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()),
        'options' => '',
        'required' => true,
    ];
}

if (is_guest()) {
    $type = get_param_string('type', 'start');
    if ($type == 'support' || $type == 'upgrade' || $type == 'installation' || $type == 'sponsor') {
        access_denied('NOT_AS_GUEST');
    } else {
        $join_url = $GLOBALS['FORUM_DRIVER']->join_url(true);
        if (!is_object($join_url)) {
            $join_url = make_string_tempcode($join_url);
        }
        $login_url = build_url(['page' => 'login', 'type' => 'browse', 'redirect' => protect_url_parameter(SELF_REDIRECT)], get_module_zone('login'));
        $please_log_in = 'You are not logged in. We advise <a href="' . escape_html($join_url->evaluate()) . '">joining</a> then <a href="' . escape_html($login_url->evaluate()) . '">logging in</a> to make best use of the ticket system.';
        attach_message(protect_from_escaping($please_log_in), 'notice');
    }
}

require_code('decision_tree');

$decision_tree = [
    'start' => [
        'title' => 'Contact request',
        'warn' => [
          'Note that Composr is a community project. It is better to inquire about most issues within the community (such as the forums) than to inquire directly with the core developers. This wizard can help guide you to the right place.'
        ],
        'text' => 'Thanks for getting in touch. To process your message efficiently we need to ask you some questions.',
        'form_method' => 'GET',
        'questions' => [
            'service_class' => [
                'label' => 'Service class',
                'description' => 'What are you looking for?',
                'type' => 'list',
                'default' => 'Other',
                'default_list' => [
                    'Professional services',
                    'Other (free services)',
                ],
                'options' => 'widget=radio',
                'required' => true,
            ],
        ],
        'next' => [
            //    Parameter         Value                                   Target
            ['service_class',  'Other (free services)',                'free'],
            ['service_class',  'Professional services',                'paid'],
        ],
    ],

    'free' => [
        'title' => 'Free support options',
        'text' => "The options below are types of requests that do not necessitate hiring a third-party developer. For informal community support, choose the forum or chatroom.",
        'notice' => [
            //    Parameter             Value                               Warning
            ['free_service_type',  'Report a bug',                     'Please only report bugs that look to be genuine bugs in the Composr CMS code (and not bugs that exist in your own custom code). You will be redirected to the report issue wizard which will guide you on reporting a bug to us.' . "\n\n" . 'If you have a [i]very high urgency[/i] to get a bug fixed, or if you want a hotfix deployed and tested for you individually, this is not something that can be offered for free. Consider hiring a developer or going through this form again under Professional services.'],
            ['free_service_type',  'Send some general feedback',       'Your feedback is greatly appreciated. While you can proceed, it is recommended to instead share your thoughts in the forums rather than privately with the core team. That way, the community as a whole can collectively give input and insight. There is no guarantee you will receive a response. But the core team does review every feedback submitted.'],
        ],
        'previous' => 'start',
        'form_method' => 'GET',
        'questions' => [
            'free_service_type' => [
                'label' => 'Service',
                'description' => 'What would you like to do?',
                'type' => 'list',
                'default' => 'Report an issue or request a feature',
                'default_list' => [
                    'Go to the community chatroom',
                    'Go to the community forum',
                    'Report an issue or request a feature',
                    'Send some general feedback',
                    'Contribute some code',
                    'Make a partnership enquiry',
                ],
                'options' => 'widget=radio',
                'required' => true,
            ],
        ],
        'next' => [
            //    Parameter             Value                                   Target
            ['free_service_type',  'Go to the community chatroom',         build_url(['page' => 'chat'], get_module_zone('chat'))],
            ['free_service_type',  'Go to the community forum',            build_url(['page' => ''], 'forum')],
            ['free_service_type',  'Report an issue or request a feature', build_url(['page' => 'report-issue'], '')],
            ['free_service_type',  'Send some general feedback',           build_url(['page' => 'tickets', 'type' => 'ticket', 'ticket_type' => 'Feedback'], get_module_zone('tickets'))],
            ['free_service_type',  'Contribute some code',                 'contribute_code'],
            ['free_service_type',  'Make a partnership enquiry',           build_url(['page' => 'tickets', 'type' => 'ticket', 'ticket_type' => 'Partnership'], get_module_zone('tickets'))],
        ],
    ],

    'contribute_code' => [
        'title' => 'Contribute code',
        'text' => "Thanks, that's fantastic!

There are 3 ways to contribute code to Composr:
[list=\"1\"]
[*] Make a [url=\"merge request\" target=\"_blank\"]https://docs.gitlab.com/ee/user/project/merge_requests/creating_merge_requests.html[/url] on [url=\"GitLab\" target=\"_blank\"]" . CMS_REPOS_URL . "[/url]. It is also a good idea to [url=\"create an issue on the tracker\" target=\"_blank\"]" . get_base_url() . "/tracker/bug_report_page.php[/url] to reference your changes and merge request.
[*] Post a patch [url=\"in an issue on the tracker\" target=\"_blank\"]" . get_base_url() . "/tracker/bug_report_page.php[/url].
[/list]
For contributions to any of the bundled addons we may need a standard dual-copyright agreement signing. We'll get in touch regarding that if necessary.

Also ask us if you want to be listed as one of the [page=\"site:stars\"]Composr developers[/page].",
        'previous' => 'free',
    ],

    'paid' => [
        'title' => 'Professional services',
        'text' => 'Great! We wish you luck on your project.' . "\n\n" . 'As of Composr version 11, there are no longer any central companies or individuals, and so we do not offer a stewardship to match you with a developer. Instead, by clicking proceed, you will be directed to a list of Composr Partners which are companies and individuals offering Composr-specific services. It is up to you to decide on and contact one of them, explain your needs / requirements / budget / timeframe, and negotiate a contract with them. We make no guarantees as to the reliability of those listed on the directory. If you believe a listing is not appropriate, please use the Report This link on it.',
        'previous' => 'start',
        'next' => build_url(['page' => 'partners'], ''),
        'form_method' => 'GET',
    ],
];

$ob = new DecisionTree($decision_tree, 'start');
$tpl = $ob->run();
$tpl->evaluate_echo();
