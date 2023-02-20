<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

header('Location: /support.htm');
exit();

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

/*
Notes:
 - Lists are ordered in cost order where applicable
*/

require_code('locations');

$disclaimer = 'Be aware that once the work is referred, it is your responsibility to ensure confidence in the chosen provider. ocProducts do the matching service as a part of the Composr CMS stewardship role and don\'t charge a commission for the service &ndash; so are not in any way commercially responsible for the implementation, or for developer training. We do encourage third-party companies to give back to the Composr CMS project by contributing code improvements made for projects though, and we do often make ourselves available to the developer for implementation of certain parts of a referred project.' . "\n\n" . 'Be aware that third-party developers have no special control over the ocProducts development and maintenance priorities.';

$extra_support_inform = array();
$extra_support_notice = array();
$extra_support_warn = array();
$credits_available = intval(get_cms_cpf('support_credits'));
if ($credits_available == 0) {
    $extra_support_notice[] = 'You do not currently have any support credits. You will need to purchase some credits before your ticket can be fully answered, although we\'ll of course reply to confirm reply cost and confirmation that we can provide an answer.';
}

$extra_brief_details = array();
if (is_guest()) {
    $extra_brief_details['job_role'] = array(
        'label' => 'Your e-mail address',
        'description' => 'You\'re not logged in to compo.sr so please enter your e-mail address so we can contact you.',
        'type' => 'short_text',
        'default' => $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()),
        'options' => '',
        'required' => true,
    );
}

if (is_guest()) {
    $type = get_param_string('type', 'start');
    if ($type == 'support' || $type == 'upgrade' || $type == 'installation' || $type == 'sponsor') {
        access_denied('NOT_AS_GUEST');
    } else {
        $join_url = $GLOBALS['FORUM_DRIVER']->join_url();
        if (!is_object($join_url)) {
            $join_url = make_string_tempcode($join_url);
        }
        $login_url = build_url(array('page' => 'login', 'type' => 'browse', 'redirect' => get_self_url(true)), get_module_zone('login'));
        $please_log_in = 'You are not logged in. We advise <a href="' . escape_html($join_url->evaluate()) . '">joining</a> then <a href="' . escape_html($login_url->evaluate()) . '">logging in</a> to make best use of the ticket system.';
        attach_message(protect_from_escaping($please_log_in), 'notice');
    }
}

require_code('decision_tree');

$decision_tree = array(
    'start' => array(
        'title' => 'Contact request',
        'text' => 'Thanks for getting in touch. To process your message efficiently we need to ask you some questions.',
        'form_method' => 'GET',
        'questions' => array(
            'service_class' => array(
                'label' => 'Service class',
                'description' => 'What service class are you looking for?',
                'type' => 'list',
                'default' => 'Professional services',
                'default_list' => array(
                    'Free options',
                    'Professional services',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
        ),
        'next' => array(
            //    Parameter         Value                                   Target
            array('service_class',  'Free options',                         'free'),
            array('service_class',  'Professional services',                'paid'),
        ),
    ),

    'free' => array(
        'title' => 'Free options',
        'text' => "The options below are kinds of request ocProducts can take in without requiring support credits.\n\nWe regret ocProducts cannot provide official 1-on-1 support or consultancy, due to the free status of Composr. For informal support (i.e. no guarantees, honour system) choose the forum or chatroom.",
        'notice' => array(
            //    Parameter             Value                               Warning
            array('free_service_type',  'Report a bug',                     'Usually we get bugs fixed within a couple of days. Please only report bugs that look to be genuine bugs in the Composr CMS code. You will be taken through to the tracker where you\'ll also see a link to read additional advice about how to make a good bug report.' . "\n\n" . 'If you have a [i]very high urgency[/i] to get a bug fixed, or if you want a hotfix deployed and tested for you individually, you may want to consider putting it through as a paid support question.'),
            array('free_service_type',  'Send some general feedback',       'We really appreciate your feedback. The feedback will be considered carefully, and if appropriate our plans will be adapted for it. Please be aware that while we\'ll usually find time to reply, due to time constraints we usually can only reply with a few words rather than direct explanations.'),
        ),
        'previous' => 'start',
        'form_method' => 'GET',
        'questions' => array(
            'free_service_type' => array(
                'label' => 'Service',
                'description' => 'What would you like to do?',
                'type' => 'list',
                'default' => 'Go to the community chatroom',
                'default_list' => array(
                    'Go to the community chatroom',
                    'Go to the community forum',
                    'Report a bug',
                    'Report a usability/UX issue',
                    'Report a security hole',
                    'Request a feature',
                    'Send some documentation feedback',
                    'Send some general feedback',
                    'Contribute some code',
                    'Make a partnership enquiry',
                    'Apply for a job with ocProducts Ltd',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
        ),
        'next' => array(
            //    Parameter             Value                                   Target
            array('free_service_type',  'Go to the community chatroom',         build_url(array('page' => 'chat'), '_SEARCH')),
            array('free_service_type',  'Go to the community forum',            build_url(array('page' => ''), 'forum')),
            array('free_service_type',  'Report a bug',                         get_base_url() . '/tracker/bug_report_page.php?severity=50'),
            array('free_service_type',  'Report a usability/UX issue',          get_base_url() . '/tracker/bug_report_page.php?severity=50'),
            array('free_service_type',  'Report a security hole',               get_base_url() . '/tracker/bug_report_page.php?view_state=50&severity=80'),
            array('free_service_type',  'Report inappropriate listed site',     'report_community_site'),
            array('free_service_type',  'Request a feature',                    get_base_url() . '/tracker/bug_report_page.php?severity=10'),
            array('free_service_type',  'Send some documentation feedback',     get_base_url() . '/tracker/set_project.php?project_id=7'),
            array('free_service_type',  'Send some general feedback',           build_url(array('page' => 'tickets', 'type' => 'ticket', 'ticket_type' => 'Feedback'), '_SEARCH')),
            array('free_service_type',  'Contribute some code',                 'contribute_code'),
            array('free_service_type',  'Make a partnership enquiry',           build_url(array('page' => 'tickets', 'type' => 'ticket', 'ticket_type' => 'Partnership'), '_SEARCH')),
            array('free_service_type',  'Apply for a job with ocProducts Ltd',  'job'),
        ),
    ),

    'report_community_site' => array(
        'title' => 'Report inappropriate listed site',
        'text' => 'Report a [page="site:community_sites"]listed community website[/page] as inappropriate.',
        'previous' => 'free',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'Reporting a listed community website',
        ),
        'questions' => array(
            'site_name' => array(
                'label' => 'Website name',
                'description' => 'The given name of the listed website.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'problem' => array(
                'label' => 'Problem',
                'description' => 'Why you are reporting the site.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Report community site'), '_SEARCH'),
    ),

    'contribute_code' => array(
        'title' => 'Contribute code',
        'text' => "Thanks, that's fantastic!

There are 3 ways to contribute code to Composr:
[list=\"1\"]
[*] Make a [url=\"merge request\" target=\"_blank\"]https://docs.gitlab.com/ee/gitlab-basics/add-merge-request.html[/url] on [url=\"GitLab\" target=\"_blank\"]" . COMPOSR_REPOS_URL . "[/url]. It is also a good idea to [url=\"create an issue on the tracker\" target=\"_blank\"]" . get_base_url() . "/tracker/bug_report_page.php[/url] to reference your changes and merge request.
[*] Post a patch [url=\"in an issue on the tracker\" target=\"_blank\"]" . get_base_url() . "/tracker/bug_report_page.php[/url].
[*] [page=\"site:tickets:ticket:ticket_type=Partnership\"]Request direct git write-access[/page].\nWe can only provide write-access to Git when someone shows a serious commitment to the project in the form of high-quality code, because we need to be careful with security and quality.
[/list]
For contributions to any of the bundled addons we may need a standard dual-copyright agreement signing. We'll get in touch regarding that if necessary.

Ask us if you wanted to be listed as one of the [page=\"site:stars\"]Composr developers[/page].",
        'previous' => 'free',
    ),

    'job' => array(
        'title' => 'Apply for a job with ocProducts Ltd',
        'text' => 'We are always on the lookout for very skilled people. We may hire people from anywhere in the world to work from home, if you are very good at what you do and your English spelling/grammar/fluency is excellent.',
        'previous' => 'free',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'Applying for a job',
        ),
        'questions' => array(
            'job_role' => array(
                'label' => 'Job role',
                'description' => 'What job role would you like with ocProducts?',
                'type' => 'short_text',
                'default' => 'Full stack developer',
                'options' => '',
                'required' => true,
            ),
            'cover_letter' => array(
                'label' => 'Cover letter',
                'description' => 'Paste in your cover letter. i.e. your interests, you requirements, and how you think you can contribute to ocProducts/Composr.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'cv' => array(
                'label' => 'CV',
                'description' => 'Attach your CV (aka resum&eacute;)',
                'type' => 'upload',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Job'), '_SEARCH'),
    ),

    'paid' => array(
        'title' => 'Professional services',
        'text' => 'We have a few different ways of doing business, depending on your needs.',
        'previous' => 'start',
        'form_method' => 'GET',
        'questions' => array(
            'commercial_service_type' => array(
                'label' => 'Service',
                'description' => 'What would you like to do?',
                'type' => 'list',
                'default' => 'Technical support (single question support / very quick jobs)',
                'default_list' => array(
                    'Request an installation',
                    'Hire for a project',
                    'Hire someone for ongoing work (secondment)',
                    'Give advance notice of support needs',
                    'Technical support (single question support / very quick jobs)',
                    'Sponsor Composr enhancements',
                    'Request a website upgrade',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
        ),
        'next' => array(
            //    Parameter                     Value                                                                   Target
            array('commercial_service_type',    'Request an installation',                                              'installation'),
            array('commercial_service_type',    'Hire for a project',                                                   'project'),
            array('commercial_service_type',    'Hire someone for ongoing work (secondment)',                           'secondment'),
            array('commercial_service_type',    'Give advance notice of support needs',                                 'support_notice'),
            array('commercial_service_type',    'Technical support (single question support / very quick jobs)',        'support'),
            array('commercial_service_type',    'Sponsor Composr enhancements',                                         'sponsor'),
            array('commercial_service_type',    'Request a website upgrade',                                            'upgrade'),
        ),
    ),

    'project' => array(
        'title' => 'Hire for a project - step 1 of 6',
        'text' => 'Exciting! We will now be asking a lot of questions to try and get a clear picture for what you\'re looking for.' . "\n\n" . 'Apologies if the questions don\'t quite apply (e.g. if the project is not a full website implementation). Rest assured that a real human with common sense will read over whatever you fill in.',
        'notice' => array(
            //    Parameter             Value                                                                                           Warning
            array('ideal_developer',    'ocProducts (the Composr CMS sponsoring company)',                                              'That\'s cool. ocProducts is run by founding developers of Composr CMS, with work done to a very high standard by foremost Composr experts. Communication is generally done via e-mail due to the balancing we need to do between Composr CMS stewardship, and commercial work.' . "\n\n" . 'You should expect we will charge around the same as a high-quality established UK/US agency. If you need lower costs, or physical meetings, it may be best to choose a local agency or freelancer, especially if you are not based in a &ldquo;Western&rdquo; country.' . "\n\n" . 'We cannot guarantee availability of ocProducts staff for any particular project because we may sometimes be very busy or not be a good match. Sometimes we may have a waiting period as we have to clear back-logs or hire additional staff (top developers are hard to find). In such a case we\'ll try and match you with a local developer.'),
            array('ideal_developer',    'A local agency that ocProducts will help you pick',                                            'That\'s cool. We will try and find an appropriate company in your country.' . "\n\n" . $disclaimer),
            array('ideal_developer',    'A local freelancer that ocProducts will help you pick (lower cost, more basic, less formal)',  'That\'s cool. We will try and find someone appropriate in your country. Just be aware that a freelancer usually won\'t be able to offer the full reliability than an established agency can. It\'s a trade-off of service breadth/reliability vs. cost.' . "\n\n" . $disclaimer),
            array('ideal_developer',    'Specifically Chris Graham, Composr lead developer',                                            'Chris is the ocProducts CEO, so has limited availability and a higher cost. Work is charged on a strict hourly basis (without advance quotes), for the work done in the hours he is available. If you\'re happy to pay top dollar for the world\'s foremost expert in Composr CMS then this is the option for you.'),
        ),
        'previous' => 'paid',
        'form_method' => 'POST',
        'questions' => array(
            'title' => array(
                'label' => 'Project&nbsp;title / domain&nbsp;name',
                'description' => 'The title for your project. Include the domain name if you have one already.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'description' => array(
                'label' => 'Project description',
                'description' => 'A description of the project, as detailed as reasonably possible to reduce the chance of surprises. If you have a specification prepared already then you can keep this short and attach the specification below. What are your objectives that will define a successful project? What is the intended audience for your project? Describe a typical user visiting your website. What is their motivation for visiting?',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'specification' => array(
                'label' => 'Specification',
                'description' => 'Attach your detailed specification, if you have one. If your specification covers further answers then you don\'t need to bother filling them in.',
                'type' => 'upload',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            /*'materials' => array(     Too much to upload this now, don't want to risk 'crashes' for very large files
                'label' => 'Project materials',
                'description' => 'Attach any materials you need worked with, e.g. logos, brand guidelines, or policy documents.',
                'type' => 'upload_multi',
                'default' => '',
                'options' => '',
                'required' => false,
            ),*/
            'materials' => array(
                'label' => 'Pre-existing project materials',
                'description' => 'What project materials already exist? You can attach them to your support ticket later.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Logo',
                    'Brand guidelines',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'country' => array(
                'label' => 'Your country & nearest&nbsp;city',
                'description' => 'What country do you work in? Knowing the nearest city will help also.',
                'type' => 'short_text',
                'default' => find_country_name_from_iso(get_country()),
                'options' => '',
                'required' => true,
            ),
            'budget' => array(
                'label' => 'Budget',
                'description' => 'What is your budget? You can give a range if you like, but the developer will need an indication so that the developer can assess feasibility and approach before a full professional analysis.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'composr_project' => array(
                'label' => 'Composr project?',
                'description' => 'Is this project to be implemented using Composr CMS?',
                'type' => 'list',
                'default' => 'Yes',
                'default_list' => array(
                    'Yes',
                    'No',
                    'Unsure',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'ideal_developer' => array(
                'label' => 'Desired developer',
                'description' => 'Who do you want to develop the work?',
                'type' => 'list',
                'default' => '',
                'default_list' => array(
                    'Unsure',
                    'A local freelancer that ocProducts will help you pick (lower cost, more basic, less formal)',
                    'A local agency that ocProducts will help you pick',
                    'ocProducts (the Composr CMS sponsoring company)',
                    'Specifically Chris Graham, Composr lead developer',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'contact_methods' => array(
                'label' => 'Contact methods',
                'description' => 'How would you like your assigned project manager to be contactable?',
                'type' => 'list_multi',
                'default' => 'E-mail',
                'default_list' => array(
                    'Skype text chat',
                    'Phone calls',
                    'E-mail',
                    'Out-of-hours contact',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'planning' => array(
                'label' => 'Planning to be done by developer',
                'description' => 'What planning would you like done for you? Anything you don\'t select is likely still important, so you\'d want to get it done on your own end.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'General market research (market size, demographics, etc)',
                    'Detailed review of competitors',
                    'Business plan consulting',
                    'Market surveying',
                    'Domain name research and purchase',
                    'Review of your chosen webhost (compatibility testing)',
                    'We write a detailed specification for/with you',
                    'Engineering diagrams ([acronym title="Unified Modelling Language"]UML[/acronym] use case diagrams, class diagrams, etc)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'number_of_stakeholders' => array(
                'label' => 'Number of stakeholders',
                'description' => 'How many stakeholders/partners will the developer need to work with? This includes your relevant colleagues, partners, committee members, active investors, and other interested parties who will need to be consulted.',
                'type' => 'list',
                'default' => '2-3',
                'default_list' => array(
                    'Just me',
                    '2-3',
                    '4-6',
                    '7+',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'involvement_level' => array(
                'label' => 'Your involvement',
                'description' => 'What level of involvement do you expect to have during development? The answer to this question will affect the proposed development methodology and charging structure.',
                'type' => 'list',
                'default' => 'Basic involvement (assess work at milestones)',
                'default_list' => array(
                    'Minimal involvement (just make it!)',
                    'Basic involvement (assess work at milestones)',
                    'Ongoing involvement (frequent demos as things are developed)',
                    'Part of the team (work as one unified team)',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'response_time' => array(
                'label' => 'Expected response time to e-mails',
                'description' => 'What is your expected response time by the developer to your e-mails?',
                'type' => 'list',
                'default' => 'Within 2-3 business days',
                'default_list' => array(
                    'Immediately',
                    'Within a few hours',
                    'Within 1 business day',
                    'Within 2-3 business days',
                    'Within a week',
                    'Within 2 weeks',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'terms_and_conditions' => array(
                'label' => 'Your past experience',
                'description' => 'Have you done a web/engineering project before with a professional company? If not you\'ll want someone to step through things like how copyright assignment works, contingency budgeting, scope agreements, working processes, and problem/defect resolution.',
                'type' => 'list',
                'default' => 'No, but I\'ll read over terms and conditions or a contract carefully myself',
                'default_list' => array(
                    'Yes, I have past experience',
                    'No, but I\'ll read over a contract carefully myself',
                    'No, I\'d like someone to read through a contract with me',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'client_background' => array(
                'label' => 'Your background',
                'description' => 'Knowing your background will help the developer know how to best work with you.',
                'type' => 'list',
                'default' => '',
                'default_list' => array(
                    'Designer',
                    'Developer',
                    'IT specialist',
                    'Manager',
                    'Entrepreneur',
                    'Other',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
        ) + $extra_brief_details,
        'next' => 'project_qs_development',
    ),

    'project_qs_development' => array(
        'title' => 'Hire for a project - step 2 of 6',
        'text' => 'Now it\'s time for some questions about your site development/functionality.',
        'previous' => 'project',
        'form_method' => 'POST',
        'questions' => array(
            'features' => array(
                'label' => 'Composr features',
                'description' => 'What Composr features do you want? Leave blank if it is not a Composr CMS project.',
                'type' => 'list_multi',
                'default' => 'News/Blogs' . "\n" . 'Page editing',
                'default_list' => array(
                    'Calendar / Upcoming events',
                    'Catalogues (custom databases)',
                    'Chatrooms',
                    'Contact us page with address, phone number, possibly a map',
                    'Content comments and rating by users',
                    'Downloads database (hosted downloads/documents)',
                    'Forums',
                    'Galleries (Photos & Videos)',
                    'Live chat system (i.e. sales chat)',
                    'News/Blogs',
                    'Page editing',
                    'Polls',
                    'Store finder (or similar)',
                    'Support tickets',
                    'Surveys',
                    'wiki',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'features_free' => array(
                'label' => 'Other Composr features',
                'description' => 'Are there any other particular standard [page="_SEARCH:features" external="1"]Composr CMS features[/page] that attracted your attention? Leave blank if it is not a Composr CMS project.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            'mobile' => array(
                'label' => 'Mobile options',
                'description' => 'Mobile is very important, but there are many approaches to it. Tick (Check) which ones you want.',
                'type' => 'list_multi',
                'default' => 'Separate optimised mobile/desktop modes' . "\n" . 'Basic responsive design',
                'default_list' => array(
                    'Separate optimised mobile/desktop modes',
                    'Basic responsive design',
                    'Enhanced responsive design (Twitter Bootstrap system)',
                    'Extensive testing across different smartphones and tablets',
                    'Full Android app',
                    'Full iOS app',
                    'Full Windows Phone app',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'advanced_requirements' => array(
                'label' => 'Advanced requirements',
                'description' => 'Tick (Check) which advanced implementation requirements you have.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Significant custom programming for tailored functionality',
                    'Scalable architecture for ultra-high usage',
                    'Integration with existing digital systems',
                    'Support for multiple languages, including site and content translation',
                    'Basic eCommerce support (selling things online)',
                    'Advanced eCommerce support: self-hosted credit card processing',
                    'Usergroup subscriptions (selling access to parts/features of the website)',
                    'Let your users host their own customised sites under yours',
                    'Advanced intranet support (e.g. LDAP login or automatic Kerberos login)',
                    'Bookings system (e.g. events, hotels, taxis, ...)',
                    'Automatic video transcoding (take videos in any format and make them web-friendly)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'social_media' => array(
                'label' => 'Social media integration',
                'description' => 'Tick (Check) which social media integration options you need.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Login to website using Facebook account',
                    'Login to website using a variety of different accounts (Google, Twitter, etc)',
                    'Automatic syndication of content to Twitter and/or Facebook',
                    'Automatic syndication of content to other services',
                    'Custom Facebook app (integration functionality directly onto Facebook)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
        ),
        'next' => 'project_qs_design',
        'expects_parameters' => array(
            'title',
            'description',
            'country',
            'budget',
            'composr_project',
            'ideal_developer',
            'description',
            'client_background',
            'response_time',
            'terms_and_conditions',
        ),
    ),

    'project_qs_design' => array(
        'title' => 'Hire for a project - step 3 of 6',
        'text' => 'Now it\'s time for some questions about your site design.',
        'previous' => 'project_qs_development',
        'form_method' => 'POST',
        'questions' => array(
            'visual_appeal' => array(
                'label' => 'Visual appeal',
                'description' => 'How important is the visual appearance to you?',
                'type' => 'list',
                'default' => 'The design should be attractive, but simple and quick',
                'default_list' => array(
                    'The design needs to be neat, but isn\'t important beyond this',
                    'The design should be attractive, but simple and quick',
                    'The design must be on par with other good web sites',
                    'The design must equal top sites (house-hold brands, for example), and stun visitors',
                    'The design must look better than current top sites and contest design awards',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'feel' => array(
                'label' => 'Design feel',
                'description' => 'Describe how a visitor should perceive your website? (e.g. prestigious, friendly, corporate, fun, modern, innovative, etc).',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            'front_page' => array(
                'label' => 'Front page design',
                'description' => 'How would you like your front page to be?',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Short headline and description laid on top of an edge-to-edge image or video',
                    'Image/video/text slider (multiple animating slides, to catch user attention)',
                    'Medium-length copy',
                    'News or other dynamic content',
                    'Syndicated industry news from other website(s)',
                    'Animated images that come alive as you scroll the page',
                    'Infinite scrolling of content (more content loads from the server as you scroll down)',
                    'Prominent custom testimonials',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'global_layout' => array(
                'label' => 'Global layout',
                'description' => 'The global layout is the header/footer, essentially. What would you like it to be like?',
                'type' => 'list_multi',
                'default' => 'Drop-down/hamburger menus' . "\n" . 'Prominent search box',
                'default_list' => array(
                    'Banner advertising',
                    'Drop-down/hamburger menus',
                    'Prominent search box',
                    'Prominent member signup',
                    'Separate newsletter signup (separate to membership)',
                    'Prominent notifications/private-messages',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'design_direction' => array(
                'label' => 'Design direction',
                'description' => 'Here are a few questions that will give the developer an idea of what kind of design you have in mind.',
                'type' => 'list_multi',
                'default' => 'Social media sharing buttons',
                'default_list' => array(
                    'Social media sharing buttons',
                    'Automatic share/subscribe/signup requests (e.g. in an overlay after a timer/scrolling)',
                    'Columns for page content (like a newspaper or magazine)',
                    'Drop-caps effect (traditional looking big letters to start paragraphs)',
                    'Fancy arrangement of content on pages (e.g. tabbed interfaces, accordions, or other FX)',
                    'Use a non-standard font to achieve rich typography',
                    'Customised scrollbar or colour of highlighted text',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'general_design' => array(
                'label' => 'General design work',
                'description' => 'Apart from the web design, what do you need doing?',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Logo work',
                    'Creation of brand guidelines document',
                    'Video production for an intro video',
                    'Creation of advertising material (banners, flyers, business cards, etc)',
                    'Design social media cover art (e.g. for a Facebook page)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'website_design' => array(
                'label' => 'Website design work',
                'description' => 'What broad web design work needs doing?',
                'type' => 'list_multi',
                'default' => 'Full custom design (rather than using a pre-existing Composr theme)',
                'default_list' => array(
                    'Full custom design (rather than using a pre-existing Composr theme)',
                    'Extensive design work (more than 5 screen designs)',
                    'Extensive Composr redesigning (if you don\'t want to use the default signup forms, etc)',
                    'Copywriting (i.e. the developer researches and writes the website text)',
                    'Extensive site help (e.g. a video, an interactive walk-through, or a long guide)',
                    'Custom 404 page (fancy designed page shown if users access a broken link)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'icons' => array(
                'label' => 'Icon work',
                'description' => 'Apart from icons that come with your page designs, is there additional icon work you need?',
                'type' => 'list_multi',
                'default' => 'Favicons and mobile launcher icons (the icon shown in browser toolbars, bookmarks, etc)',
                'default_list' => array(
                    'Retina icons (high-dpi icons so things look amazing on high-res screens)',
                    'Favicons and mobile launcher icons (the icon shown in browser toolbars, bookmarks, etc)',
                    'Custom rank set for forum',
                    'Custom emoticons for forum (e.g. ones relating to your market)',
                    'Custom list bullet icon',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'emails' => array(
                'label' => 'E-mail work',
                'description' => 'It\'s easy to forget that e-mails need designing to. What needs doing?',
                'type' => 'list_multi',
                'default' => 'Custom e-mail design (basic sent e-mails)',
                'default_list' => array(
                    'Custom e-mail design (basic sent e-mails)',
                    'Custom e-mail design (newsletters)',
                    'Copywriting for automatic welcome e-mails to users',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
        ),
        'next' => 'project_qs_testing_and_training',
        'expects_parameters' => array(
            'title',
            'description',
            'country',
            'budget',
            'composr_project',
            'ideal_developer',
            'description',
            'client_background',
            'response_time',
            'terms_and_conditions',
        ),
    ),

    'project_qs_testing_and_training' => array(
        'title' => 'Hire for a project - step 4 of 6',
        'text' => 'Now it\'s time for some questions about testing and training.',
        'previous' => 'project_qs_design',
        'form_method' => 'POST',
        'questions' => array(
            'testing' => array(
                'label' => 'Enhanced testing/tuning',
                'description' => 'What special testing and tuning do you want doing? Naturally your site will be tested, but it is sometimes good to do a lot more extensive testing than just ensuring that things work.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Detailed security review by a different senior engineer',
                    'Detailed full code review by a different senior engineer',
                    'Page load speed optimisation',
                    'Tuned styling for printing pages',
                    'Usability lab sessions',
                    'Formal HTML/CSS/ATAG standards compliance testing',
                    'Screen-reader testing (to help blind users)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'training' => array(
                'label' => 'Training approach',
                'description' => 'How would you like to be trained? You may opt for none of this, if your website is relatively simple.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Written training guide with screenshots',
                    'Series of video tutorials',
                    'In-person training (up to 1 day)',
                    'Staff secondment (multiple days)',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
        ),
        'next' => 'project_qs_deployment',
        'expects_parameters' => array(
            'title',
            'description',
            'country',
            'budget',
            'composr_project',
            'ideal_developer',
            'description',
            'client_background',
            'response_time',
            'visual_appeal',
            'terms_and_conditions',
        ),
    ),

    'project_qs_deployment' => array(
        'title' => 'Hire for a project - step 5 of 6',
        'text' => 'Now it\'s time for some questions about your site deployment.',
        'previous' => 'project_qs_testing_and_training',
        'form_method' => 'POST',
        'questions' => array(
            'launch_date' => array(
                'label' => 'Launch date',
                'description' => 'When is your intended launch date? Be aware that developers are likely not available for immediate work, setting a short deadline could lead to a rushed development, and there will need to be plenty of time for testing -- so please be realistic.',
                'type' => 'list',
                'default' => 'Unsure',
                'default_list' => array(
                    'Around a month',
                    '1-3 months',
                    '4-7 months',
                    '8-11 months',
                    'A year or more',
                    'Unsure',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'deployment' => array(
                'label' => 'Enhanced deployment options',
                'description' => 'Apart from uploading the finished website, you may need other things doing.',
                'type' => 'list_multi',
                'default' => 'Set up Google Analytics' . "\n" . 'SSL certificate setup (the padlock icon in the browser toolbar)' . "\n" . 'Setting up of automatic downtime alerts ("uptime monitoring")',
                'default_list' => array(
                    'Set up Google Analytics',
                    'Set up Google Adsense (so you can make money from displaying adverts)',
                    'Search Engine and Directory submission',
                    'SSL certificate setup (the padlock icon in the browser toolbar)',
                    'Setting up of social media accounts (Twitter, Facebook, etc)',
                    'Setting up of business on Google/Bing/Apple Maps',
                    'Setting up of automatic downtime alerts ("uptime monitoring")',
                    'On-site (i.e. in person) deployment',
                    'Maintain a separate staging/testing website',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'migration' => array(
                'label' => 'Migration',
                'description' => 'You may have an existing project that needs to be migrated over to the new project.',
                'type' => 'list_multi',
                'default' => '',
                'default_list' => array(
                    'Migrate legacy content into the new Composr solution',
                    'Migrate legacy functionality into the new Composr solution',
                    'Import third party software\'s database into your Composr database',
                    'Set up redirects for pages of an old site',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'access_details' => array(
                'label' => 'Access details',
                'description' => 'We may need access details to your hosting, such as FTP access, or control panel access (login URL, username, password). Provide what you have if you already have hosting and think we might need these details. If you saved your details into your [page="site:members:view" external="1"]compo.sr member profile[/page], you can just say that. Server access is subject to our [page="_SEARCH:server_access" external="1"]server access policy[/page].',
                'type' => 'long_text',
                'default' => '(I do not yet have hosting, provide advice or hosting for me.)',
                'options' => '',
                'required' => false,
            ),
            'expected_traffic_levels' => array(
                'label' => 'Expected traffic levels',
                'description' => 'How many visitors do you expect on a peak day? This will help with hosting recommendations.',
                'type' => 'list',
                'default' => 'Under 1,000 visitors',
                'default_list' => array(
                    'Unsure',
                    'Under 1,000 visitors', // ~ shared hosting
                    'Under 5,000 visitors', // ~ 1 moderate server
                    'Larger numbers', // Multiple servers, e.g. EC2
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
        ),
        'next' => 'project_qs_maintenance',
        'expects_parameters' => array(
            'title',
            'description',
            'country',
            'budget',
            'composr_project',
            'ideal_developer',
            'description',
            'client_background',
            'response_time',
            'visual_appeal',
            'terms_and_conditions',
        ),
    ),

    'project_qs_maintenance' => array(
        'title' => 'Hire for a project - step 6 of 6',
        'text' => 'It is usually not a good idea to think of a web project as something that is finished when launched. To stay competitive, and to achieve results, you may need ongoing testing and tuning of your website.',
        'previous' => 'project_qs_deployment',
        'form_method' => 'POST',
        'questions' => array(
            'maintenance' => array(
                'label' => 'Regular/routine maintenance',
                'description' => 'What maintenance do you need doing?',
                'type' => 'list_multi',
                'default' => 'Priority patching for security vulnerabilities' . "\n" . 'Developer hours each month for unspecified maintenance and support' . "\n" . 'Security audit/review of logs and system software',
                'default_list' => array(
                    'Priority patching for security vulnerabilities',
                    'Developer hours each month for unspecified maintenance and support',
                    'Security audit/review of logs and system software',
                    'Review of competitors',
                    'On-site (i.e. in person) visits',
                    'Routine testing and tuning as new web browser versions come out',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
            'goals_optimisation' => array(
                'label' => 'Optimising performance',
                'description' => 'Once a website is live you can start measuring performance by analysing usage patterns.',
                'type' => 'list_multi',
                'default' => 'Regular SEO performance reviews (i.e. Google optimisation)' . "\n" . 'Regular analytics reviews (analyse growth and identify stumbling blocks)',
                'default_list' => array(
                    'Regular SEO performance reviews (i.e. Google optimisation)',
                    'Regular analytics reviews (analyse growth and identify stumbling blocks)',
                    'Regular multivariate testing (automatic testing of different site variations)',
                    'Regular page speed / server load testing',
                ),
                'options' => 'widget=vertical_checkboxes',
                'required' => false,
            ),
        ),
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Project'), '_SEARCH'),
        'expects_parameters' => array(
            'title',
            'description',
            'country',
            'budget',
            'composr_project',
            'ideal_developer',
            'description',
            'client_background',
            'response_time',
            'visual_appeal',
            'terms_and_conditions',
            'launch_date',
        ),
    ),

    'installation' => array(
        'title' => 'Request an installation',
        'text' => 'We will install Composr for you by setting up the databases, uploading the files, and so on. We will not charge credits for more than an hours time if your server is not faulty, meets our [page="docs:tut_webhosting" external="1"]minimum requirements[/page], and the given access details work.' . "\n\n" . 'Please be aware that this isn\'t a full site build service, just a Composr CMS installation service.',
        'inform' => array(
            'Did you know that some webhosts can install Composr CMS for you, automatically and free? Composr is published via Installatron, Softaculous, Bitnami, Microsoft Web Platform, and APS. Many hosts have one of these installation systems.',
        ),
        'previous' => 'paid',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'New Composr installation request',
        ),
        'questions' => array(
            'access_details' => array(
                'label' => 'Access details',
                'description' => 'We need access details to your hosting, such as FTP access, or control panel access (login URL, username, password). Provide what you have. Server access is subject to our [page="_SEARCH:server_access" external="1"]server access policy[/page].',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'url' => array(
                'label' => 'Installation URL',
                'description' => 'On what URL would you like Composr installed? Leave blank if you just want to install on the root of your main domain name.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            'password' => array(
                'label' => 'Password to use',
                'description' => 'What would you like your admin password to be set to?',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'notes' => array(
                'label' => 'Notes',
                'description' => 'Any notes that you may have such as special or non-default requirements.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Installation'), '_SEARCH'),
    ),

    'secondment' => array(
        'title' => 'Hire someone for ongoing work (secondment)',
        'text' => 'We will try and match one of an employee with your needs. Secondment is a great option for ongoing work, or if you want the confidence and convenience of having a named person on hand to tackle random or urgent things.',
        'previous' => 'paid',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'Secondment request',
        ),
        'questions' => array(
            'hours_per_week' => array(
                'label' => 'Hours per week',
                'description' => 'How many hours per week will be required. This will be a fixed number, incorporated into an ongoing work agreement. You don\'t need to hire all an employees hours, but if you don\'t be aware that they will need to be juggling commitments so won\'t always be available.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'work_period' => array(
                'label' => 'Work period',
                'description' => 'How long the work will run for.',
                'type' => 'list',
                'default' => 'Permanent',
                'default_list' => array(
                    'Permanent',
                    'Unknown',
                    '4 months (approx)',
                    '6 months (approx)',
                    '9 months (approx)',
                    '1 year (approx)',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'notes' => array(
                'label' => 'Description of work',
                'description' => 'A description of the work you have.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'phone_needed' => array(
                'label' => 'Phone contact needed?',
                'description' => 'Some of our developers hate phones, work odd hours, or aren\'t great at spoken English. If you need phone contact please tick (check) here.',
                'type' => 'tick',
                'default' => '0',
                'options' => '',
                'required' => true,
            ),
            'travel_needed' => array(
                'label' => 'Travel needed?',
                'description' => 'Will the employee be expected to travel to you?',
                'type' => 'tick',
                'default' => '0',
                'options' => '',
                'required' => true,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Secondment'), '_SEARCH'),
    ),

    'support_notice' => array(
        'title' => 'Give advance notice of support needs',
        'text' => 'If time is a factor and you are expecting to put through some complex support queries, or a stream of support queries, let us know via this form as far in advance as possible. This helps us arrange staff availability (we have to schedule primary staff assignments weeks or months in advance, and it can get extremely hectic).',
        'previous' => 'paid',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'Advance notice of support needs',
        ),
        'questions' => array(
            'hours_per_week' => array(
                'label' => 'Hours per week',
                'description' => 'How many hours per week will be required (approximately).',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            'work_period' => array(
                'label' => 'Work period',
                'description' => 'How long the support will run for.',
                'type' => 'list',
                'default' => 'Permanent',
                'default_list' => array(
                    'Permanent',
                    'Unknown',
                    '4 months (approx)',
                    '6 months (approx)',
                    '9 months (approx)',
                    '1 year (approx)',
                ),
                'options' => 'widget=radio',
                'required' => false,
            ),
            'notes' => array(
                'label' => 'Description of support',
                'description' => 'A description of the support needed (to help us ensure availability of someone with the necessary skills).',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Professional support'), '_SEARCH'),
    ),

    'support' => array(
        'title' => 'Technical support <span class="associated_details">(single&nbsp;question&nbsp;support&nbsp;/&nbsp;very&nbsp;quick&nbsp;jobs)</span>',
        'text' => 'Great, let\'s get to it!' . "\n\n" . 'Please do make sure you are entering single-questions only (or at least related-questions); we need to be able to assign the ticket to a particular person with particular experience in the area you are asking about.' . "\n\n" . 'Please make sure your ticket needs to be completely self-explanatory. If it is necessary to reference another ticket please ensure that the exact title/URL of the ticket is given. Different members of staff may work on different tickets so we won\'t necessarily know the details relating to other tickets.' . "\n\n" . 'A professional ticket usually will be charged/due-for-payment only if you agree to the quote we give. Exceptions are if you tell us to work immediately without a quote, or if the quote would be the minimum for the ticket priority selected.' . "\n\n" . 'If you sometimes need things more urgent than the reply times available, you will need to go back and look into our secondment option so that you have someone permanently and directly assigned to you (as we don\'t always have someone available to jump on very urgent tickets; we need ongoing commitments to guarantee staffing of very skilled developers).' . "\n\n" . 'If you already have a ticket for this work/issue please don\'t create a new ticket. It is important to stay within one ticket to ensure the assigned staff member is aware of past progress on that ticket.',
        'notice' => array_merge($extra_support_notice, array(
            //    Parameter         Value                                                   Warning
            array('response_time',  'Back-burner reply (approximately two-business-weeks)', 'Be aware that all follow-ups in the support ticket will only be read on back-burner basis, no matter the urgency of them. This means a ticket with follow-ups will often take an even longer time to process, and if you do mention urgent things in your replies, we may not see it until too late.'),
        )),
        'warn' => array_merge($extra_support_warn, array(
        )),
        'previous' => 'paid',
        'form_method' => 'POST',
        'questions' => array(
            'title' => array(
                'label' => 'Brief title',
                'description' => 'A title to identify your support ticket. Choose something short and descriptive.',
                'type' => 'short_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'question' => array(
                'label' => 'Question',
                'description' => 'What\'s your full question or quick job? Please be careful with your English spelling and grammar, make sure you are clear and unambiguous, and try your best to use accurate terminology. You will save money if we can easily read through your ticket.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => true,
            ),
            'response_time' => array(
                'label' => 'Response time',
                'description' => 'Expected response time. This is not guaranteed, but we\'ll try and meet it. Most customers will want our regular reply response time, which provides the service level that most customers would expect from us. A back-burner reply, or late regular reply, uses [page="_SEARCH:professional_support" external="1"]fewer support credits[/page].',
                'type' => 'list',
                'default' => 'Regular reply (maximum two-business-days)',
                'default_list' => array(
                    'Regular reply (maximum two-business-days)',
                    'Back-burner reply (approximately two-business-weeks)',
                    'Sponsor a detailed tutorial (Regular reply time)',
                    'Sponsor a detailed tutorial (Back-burner reply time)',
                ),
                'options' => 'widget=radio',
                'required' => true,
            ),
            'access_details' => array(
                'label' => 'Access details',
                'description' => 'We may need access details to your hosting, such as FTP access, or control panel access (login URL, username, password). Provide what you have if you think we might. If you saved your details into your [page="site:members:view" external="1"]compo.sr member profile[/page], you can just say that. Server access is subject to our [page="_SEARCH:server_access" external="1"]server access policy[/page].',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Professional support'), '_SEARCH'),
    ),

    'sponsor' => array(
        'title' => 'Sponsor Composr enhancements',
        'text' =>
            'Wow, thanks! Composr moves forward when people sponsor new functionality, or when new functionality is developed as part of a wider commercial project. If you want to see something integrated cleanly into Composr\'s open roadmap, and don\'t need a custom deployment, feature sponsorship is a great way to do it. Sponsored features are developed at ocProduct\'s back-burner hourly rate.' . "\n\n" .
            'To sponsor something you should:[list="1"]' . "\n\n" .
            '[*] Go to the [url="tracker"]' . get_base_url() . '/tracker[/url].' . "\n\n" .
            '[*] Find the feature you want to sponsor on the tracker, which usually will have a number of hours filled in for it already. If the issue is not yet on the tracker, you\'ll need to add it, mention that you\'ll sponsor it, and wait for the hours field to be updated. If it is on the tracker already but has no hours set yet, similarly reply to say that you\'d like to sponsor it and wait.' . "\n\n" .
            '[*] Bring up your desire to sponsor as a note on the tracker issue.' . "\n\n" .
            '[/list]' . "\n\n" .
            'A developer will then need to agree to accept sponsorship, after reviewing the accuracy of the issue and their own availability. ocProducts will then need to open the issue for sponsorship and assign the issue.' . "\n\n" .
            'Then:[list="1"]' . "\n\n" .
            '[*] Fill in the sponsor form on the tracker issue.' . "\n\n" .
            '[*] The developer may or may not work for ocProducts. If not ocProducts then payment would be negotiated and made in private, e.g. using PayPal. If it is an ocProducts developer, you should [page="_SEARCH:professional_support" external="1"]purchase some support credits[/page] to back up the amount. You can only buy support credits in certain bundles, although you can work out what set of bundles to get to get the exact right number of credits. If this is too bothersome, you can pay directly via PayPal to payment@ocproducts.com with a note to turn it into support credits (include your username).' . "\n\n" .
            '[/list]' . "\n\n" .
            'If you don\'t know what you want to sponsor but just want to support Composr, you could consider [url="Chris\'s Patreon"]https://www.patreon.com/composr[/url], or sponsoring any other individual developer.' . "\n\n" .
            'We do need to explain some possible caveats of sponsorship...' . "\n\n" .
            'Please be aware that not all sponsorships can be accepted. The developers/community needs to feel a feature is right for Composr before we can add it, regardless of sponsorship.' . "\n\n" .
            'Features could potentially be changed or even removed from Composr at a later date, if the developers decide it is the best action for the product at that particular point in time, even if they were originally sponsored.' . "\n\n" .
            'Sponsorships aren\'t usually acted on immediately, as a developer has to be free. If time is critical, please discuss this on the tracker issue. Sponsorships can be cancelled if you change your mind (assuming work has not already started).' . "\n\n" .
            'Sponsored code goes into the next version of Composr. Here are some things to consider regarding the development methodology:[list]
                [*] The developers won\'t usually do direct business analysis or maintain awareness of your operation -- development is purely focused around the features being created.
                [*] All features will be made as generic, not focused specifically to your own needs. They may therefore not be optimal to you.
                [*] Development is done in the open, nothing is private or proprietary. Other users could get the new code quicker than you if they jump on it, especially if they are programmers.
                [*] As work goes into the next version of Composr, it may not be compatible with your current site until your site is upgraded (which can\'t happen until the next version is out). If this is a concern then discuss it in the tracker issue [i]in advance of any work starting[/i]; sometimes it can be arranged to provide a patch for a particular existing version also &ndash; or, the work can be done as a project instead at a higher cost, and merged into Composr later by the developers separately.
                [*] Patches can be incompatible with each other. For example, if you applied 2 patches for sponsored functionality, it is possible that they could conflict.
                [*] ... or, get overwritten when you apply patch upgrades.
                [*] ... or, hot fixes.
                [*] ... So if you patch immediately then you\'ll end up with a temporary "fork" of Composr and will have to skip subsequent patch releases, until the next major release comes that incorporates your sponsored features.
            [/list]' . "\n\n" .
            'Often none of the above matters much. Sponsorship works particularly well for medium-term improvements that are nice and generic. A big advantage to sponsorship is that the code will usually be maintained for free indefinitely -- which definitely wouldn\'t be the case for a project.',
        'previous' => 'paid',
    ),

    'upgrade' => array(
        'title' => 'Request a website upgrade',
        'text' => 'New toys for everyone.',
        'previous' => 'paid',
        'form_method' => 'POST',
        'hidden' => array(
            'title' => 'Composr upgrade request',
        ),
        'questions' => array(
            'from' => array(
                'label' => 'Upgrading from?',
                'description' => 'What version are you upgrading from?',
                'type' => 'short_text',
                'default' => 'Previous major version',
                'options' => '',
                'required' => true,
            ),
            'to' => array(
                'label' => 'Upgrading to?',
                'description' => 'What version are you upgrading to?',
                'type' => 'short_text',
                'default' => 'Current major version',
                'options' => '',
                'required' => true,
            ),
            'access_details' => array(
                'label' => 'Access details',
                'description' => 'We need access details to your hosting, such as FTP access, or control panel access (login URL, username, password). Provide what you have. If you saved your details into your [page="site:members:view" external="1"]compo.sr member profile[/page], you can just say that. Server access is subject to our [page="_SEARCH:server_access" external="1"]server access policy[/page].',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
            'custom_theme' => array(
                'label' => 'Custom theme to upgrade?',
                'description' => 'Is there a highly-custom theme that needs to be upgraded? Don\'t tick (check) this if you just used the theme wizard to make your theme.',
                'type' => 'tick',
                'default' => '0',
                'options' => '',
                'required' => true,
            ),
            'custom_code' => array(
                'label' => 'Custom code to upgrade?',
                'description' => 'Is there custom code (code not a part of the bundled Composr) that needs to keep working?',
                'type' => 'tick',
                'default' => '0',
                'options' => '',
                'required' => true,
            ),
            'notes' => array(
                'label' => 'Notes',
                'description' => 'Any notes you may have.',
                'type' => 'long_text',
                'default' => '',
                'options' => '',
                'required' => false,
            ),
        ) + $extra_brief_details,
        'needs_captcha' => ((addon_installed('captcha')) && (get_option('captcha_on_feedback') == '1') && (use_captcha())),
        'next' => build_url(array('page' => 'tickets', 'type' => 'post', 'ticket_type' => 'Upgrade'), '_SEARCH'),
    ),
);

$ob = new DecisionTree($decision_tree, 'start');
$tpl = $ob->run();
$tpl->evaluate_echo();
