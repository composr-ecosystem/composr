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

if (!addon_installed('cms_homesite')) {
    return do_template('RED_ALERT', ['_GUID' => '6117c986d2ff5acaa6c87d093e4c9b72', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite'))]);
}

$_download_page_url = build_url(['page' => 'download'], 'site');
$download_page_url = $_download_page_url->evaluate();

$_importing_tutorial_url = build_url(['page' => 'tut_importer'], 'docs');
$importing_tutorial_url = $_importing_tutorial_url->evaluate();

$feature_tree = [
    // Ways to help (using same code, bit of a hack)
    'help' => [
        'evangelism' => [
            'Evangelism (outreach)',
            [
                ['Twitter', 'Follow [url="https://twitter.com/composr_cms"]Composr[/url] on Twitter, and tweet about [url="#composr"]https://twitter.com/hashtag/composr[/url]. Answer [url="CMS questions"]twitter.com/search?q=CMS[/url].'],
                ['Facebook', 'Become a fan of Composr [url="https://www.facebook.com/composrcms"]on Facebook[/url].'],
                ['Stack Overflow', 'Answer CMS questions on [url="Stack Overflow"]http://stackoverflow.com/search?q=cms[/url].'],
                ['YouTube', 'Rate and comment on [url="http://youtube.com/c/ComposrCMSvideo"]our video tutorials[/url] on YouTube.'],
                (get_forum_type() != 'cns') ? null : ['Post about Composr', 'If you see other CMSs compared on other websites, {$COMCODE,[page="forum:topicview:browse:{$FIND_ID_VIA_LABEL,topic,Composr evangelism}"]let us know about it[/page]}!'],
                ['Tell a friend about Composr', '[page=":recommend"]Recommend Composr[/page] if a friend or your company is looking to make a website.'],
                //['Recommend ocProducts', 'Mention the ocProducts developers to help them bring in an income.'],
                ['Show our ad', 'You can advertise Composr via the [url="banner ad"]{$BRAND_BASE_URL}/uploads/website_specific/cms_homesite/banners.zip[/url] we have created.'],
                ['Self-initiatives', 'Find any opportunity to share Composr with someone. Write your own article and publish it. Talk about Composr at a conference. Be creative!'],
            ],
        ],

        'skill_based' => [
            'Skill-based',
            [
                ['Make addons', 'If you know PHP, or want to learn, [page="docs:sup_hardcore_1"]make and release some addons[/page] for the community. It takes a lot of knowledge, but [page="docs:tut_programming"]anybody can learn[/page] and it\'s fun, fulfilling and makes you more employable.'],
                ['Make themes', 'If you know [abbr="HyperText Markup Language"]HTML[/abbr]/[abbr="Cascading Style Sheets"]CSS[/abbr], or are [page="docs:tut_markup"]learning[/page], [page="docs:tut_releasing_themes"]make and release some themes[/page] for the community. With CSS you can start small and still achieve cool things.'],
                ['Translate', 'If you know another language, [url="collaborate with others on Transifex"]https://explore.transifex.com/composr/[/url] to [page="docs:tut_intl"]make a new language pack[/page].'],
                ['Use Composr for clients', 'Are you a professional website developer? Try using Composr for your projects &ndash; it provides you [page="site:features"]lots of advantages[/page] to other software, it\'s free, and we want the community and install-base to grow!'],
                ['Google Summer of Code', 'If you\'re a student and want to work on Composr for the [url="https://summerofcode.withgoogle.com/archive/"]Google Summer of Code[/url], please [page="site:tickets:ticket:ticket_type=Partnership"]contact us[/page] and we will work to try and make it happen.'],
                ['Developing automated tests', 'If you know some PHP you can help us test Composr en-masse. Write [page="docs:codebook_3"]automated tests[/page] (the latest version of the testing framework is in our public [url="git"]' . CMS_REPOS_URL . '[/url] repository).'],
                ['Contribute code', 'Help improve Composr directly by [url="{$CMS_REPOS_URL}"]contributing code on our repository[/url].'],
            ],
        ],

        'our_site' => [
            'On composr.app',
            [
                (get_forum_type() != 'cns') ? null : ['Reach out to other users', '{$COMCODE,[page="forum:forumview:browse:{$FIND_ID_VIA_LABEL,forum,Introduce yourself}"]Welcome new users[/page]} and help make sure people don\'t get lost.[html]<br />[/html]Also {$COMCODE,[page="forum:topicview:browse:{$FIND_ID_VIA_LABEL,topic,Post your location}"]put yourself on the map[/page]} so people near you can get in contact.'],
                ['Help others on the forum', 'Where you can, answer other user\'s questions.'],
                ['Hang out in the chat', 'If we have users in the [page="site:chat"]chatroom[/page] 24&times;7 then users (including yourself) are less likely to feel stuck or isolated.'],
                ['Send points', 'If you see other members doing good things, send them some points.'],
            ],
        ],

        'usability' => [
            'User experience',
            [
                ['Reporting bugs', 'Big or tiny &ndash; we will be happy if you even report typos we make as bugs.'],
                ['Reporting usability issues', 'We will be happy if you have any concrete suggestions for making reasonably common tasks even a little bit easier.'],
                ['Write tutorials', 'Post them on the forum and [url="link them into the tutorial database"]https://composr.app/forum/topicview/browse/posting-tutorials.htm[/url].'],
            ],
        ],

        'money' => [
            'Financial',
            [
                ['Support a developer on Patreon', 'The lead developer has a [url="Patreon"]https://www.patreon.com/composr[/url].'],
                ['Sponsor a feature', 'Do you want something new implemented in Composr? [page=":contact:sponsor"]Sponsor[/page] little projects listed on the [page="site:tracker"]tracker[/page].'],
            ],
        ],

        'other' => [
            'Other',
            [
                ['Supply test data for importers', 'Send an SQL-dump to help us create a Composr importer. There\'s no promise of anything, but it helps us a lot to have test data on hand should we decide to make an importer.'],
                ['Other', 'Do you have some other expertise? Do you have the ability to help the staff make business connections? There are many other ways to support [page="site:vision"]our mission[/page] &ndash; be imaginative!'],
            ],
        ],
    ],

    // Real features
    'browse' => [
        'installation' => [
            'Installation <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Installation" href="{$PAGE_LINK*,docs:tut_install}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Quick installer', 'Our self-extractor allows faster uploads and will automatically set permissions'],
                ['Wizard-based installation'],
                ['Advanced feature to scan for over 100 website-health problems'],
                ['Get your site up and running in just a few minutes'],
                null, // divider
                ['Keep your site closed to regular visitors until you\'re happy to open it'],
                ['Configures server', 'Automatically generates a <kbd>.htaccess</kbd> file for you'],
                ['Auto-detection of forum settings for easy integration'],
            ],
        ],
        'banners' => [
            'Banners <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Banners" href="http://shareddemo.composr.info/cms/index.php?page=cms_banners"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Multiple campaigns', 'Each one can specify it\'s own width-by-height (e.g. skyscraper)'],
                ['Smart banners', 'Integrate text-banners into your content via keyword detection'],
                ['Broad media compatibility', 'Image banners, HTML banners, external banner rotations, and text banners'],
                null, // divider
                ['Determine which banners display most often'],
                ['Run a cross-site banner network'],
                ['Hit-balancing support', 'A site on a banner network gets as many inbound hits as it provides outbound clicks'],
                ['Targeted advertising', 'Show different banners to different usergroups'],
                ['Track banner performance'],
                ['Use the banner system to display whole sets of sponsor logos'],
                (!is_maintained('ip_geocoding')) ? false : ['Geotargetting'],
            ],
        ],
        'search' => [
            'Search engine <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Search engine" href="http://shareddemo.composr.info/site/index.php?page=search"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Choose what is searchable'],
                ['Boolean and full-text modes'],
                ['Keyword highlighting in results'],
                ['Search boxes to integrate into your website'],
                null, // divider
                ['Logging/stats'],
                ['OpenSearch support', 'Allow users to search from inside their web browser'],
                ['Results sorting, and filtering by author and date'],
                ['Search within downloads', 'Including support for looking inside archives'],
            ],
        ],
        'newsletters' => [
            'Newsletters <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Newsletters" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_newsletter"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Automatically create newsletter issues highlighting your latest content'],
                ['Double opt-in', 'Prevent false sign-ups by asking subscribers to confirm their subscriptions'],
                ['Host multiple newsletters'],
                ['Flexible mailings', 'Send out mailings to all members, to different usergroups, or to subscribers of specific newsletters'],
                ['Welcome e-mails', 'Send multiple welcome e-mails to new users automatically, on a configurable schedule (Conversr-only)'],
                ['Bounce cleanup', 'Automatically clean out bounces from your e-mail list'],
            ],
        ],
        'featured' => [
            'Featured content <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Featured content" href="http://shareddemo.composr.info/lorem/index.php?page=start"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Random quotes', 'Put random quotes (e.g. testimonials) into your design'],
                ['Showcase popular content', 'Automatically feature links to your most popular downloads and galleries'],
                ['Tags', 'Set tags for content and display tag clouds'],
                null, // divider
                ['Recent content', 'Automatically feature links to your most recent content'],
                ['Show website statistics to your visitors'],
                ['Random content', 'Feature random content from your website, specified via a sophisticated filtering language'],
            ],
        ],
        'ecommerce' => [
            'eCommerce and subscriptions <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of eCommerce" href="http://shareddemo.composr.info/site/index.php?page=catalogues&amp;type=index&amp;id=products"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Paid membership', 'Sell access to sections of your website, or offer members privileges'],
                ['Shopping cart for running an online store'],
                ['Extendable framework', 'Programmers can easily add new product types to sell, or payment gateways'],
                null, // divider
                ['Multiple payment gateways', 'Accepts payments via PayPal, or other gateways developers may add, and manual transactions (cash/cheque)'],
                ['Invoicing support', 'Including status tracking and online payment tracking'],
                ['Basic accounting support', 'Input your incoming and outgoing transactions to get basic ledger, profit-and-loss, and cashflow charting'],
                (!is_maintained('currency')) ? false : ['Currency conversions', 'Perform automatic currency conversions within your website pages'],
            ],
        ],
        'support' => [
            'Support tickets <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of ticket system" href="http://shareddemo.composr.info/site/index.php?page=tickets"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Support ticket system', 'Users can view and reply in private tickets to staff'],
                ['Assign to individual staff', 'Includes the ability for staff members to &ldquo;take ownership&rdquo; of raised issues, and for all staff to discuss'],
                ['Allow users to e-mail in their tickets and replies'],
                ['Expanded access granting', 'Grant third party members access to individual tickets'],
                null, // divider
                ['FAQ integration', 'Automatically search FAQs before opening a ticket'],
                ['Multiple ticket types', 'Set up different kinds of support ticket, with different access levels and fine-grained ticket notification settings'],
                (!is_maintained('sms')) ? false : ['Receive SMS alerts for important tickets'],
                ['Anonymous posting', 'Allow staff to post anonymously so that customers don\'t always expect the same employee to reply'],
                ['Merging', 'If customers open multiple tickets for the same issue you can merge them'],
                ['Closing', 'Let customers close tickets that are now resolved, or do it yourself'],
                ['Filtering', 'Filter the tickets you see by status and ticket type'],
            ],
        ],
    ],
    'web20' => [
        'polls' => [
            'Polls <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Polls" href="http://shareddemo.composr.info/site/index.php?page=polls&amp;type=view&amp;id=1"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Integrate polls into your website', 'Guage visitor opinion'],
                ['Virtually cheat-proof'],
                ['Community involvement', 'Users can submit polls, and comment and rate them'],
                ['Multiple polls', 'Showcase different polls on different areas of your website'],
                ['Archive the data from unlimited polls'],
            ],
        ],
        'points' => [
            'Points system',
            [
                ['So many ways to earn points', 'From submitting different content to how active they are, you control the economy'],
                ['eCommerce integration', 'Members can buy advertising space, temporary privileges, gamble, or any other eCommerce product you configure to accept points! <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of eCommerce" href="http://shareddemo.composr.info/site/index.php?page=purchase"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Gift points', 'Allows members to reward each other using gift points instead of regular points <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Points" href="http://shareddemo.composr.info/site/index.php?page=points"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Leader board', 'Create some community competition, by showing a week-by-week who has the most points <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Leaderboard" href="http://shareddemo.composr.info/site/index.php?page=leader_board"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Ledger', 'See all of the point transactions to crack down on any abuse, and reverse any transactions as necessary'],
                ['Profiles', 'Browse through member points profiles, see what points members have received and sent, and send / escrow points with them'],
                ['Escrow', 'Send points to another member, but keep them from being received until written conditions are agreed met'],
            ],
            'A virtual economy for your members',
        ],
        'community' => [
            'Community features',
            [
                ['User content submission', 'Allow users to submit to any area of your site. Staff approval is supported <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of CMS" href="http://shareddemo.composr.info/cms/index.php?page=cms"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Public awards', 'Give public awards to your choice of &ldquo;best content&rdquo; <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Awards" href="http://shareddemo.composr.info/site/index.php?page=awards"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Per-usergroup privileges', 'Give special members access to extra features, like file storage <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Permissions" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_permissions"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Bookmarks', 'Users can bookmark their favourite pages to their account <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Bookmarks" href="http://shareddemo.composr.info/site/index.php?page=bookmarks"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Recommend-to-a-friend', 'Visitors can recommend your website to other visitors'],
                ['Users may review your content (optional)'],
            ],
        ],
        'chat' => [
            'Chatrooms and instant messaging <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Chatrooms" href="http://shareddemo.composr.info/site/index.php?page=chat"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Unlimited chatrooms', 'Each with your choice of access restrictions'],
                ['Moderation', 'Moderate messages and ban troublesome users'],
                ['Integrate shout-boxes into your website'],
                ['Instant messaging', 'Members may have IM conversations with each other, or in groups'],
                ['Site-wide IM', 'Give your members the ability to pick up conversations anywhere on your site'],
                null, // divider
                ['Sound effects', 'Members may configure their own'],
                ['Programmers can write their own chat bots'],
                ['Download chatroom logs'],
                ['Blocking', 'Choose to appear offline to certain members'],
            ],
        ],
    ],
    'content' => [
        'catalogues' => [
            'Catalogues <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Catalogues" href="http://shareddemo.composr.info/site/index.php?page=catalogues"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Flexible data control', 'Set up multiple catalogues, each with it\'s own set of fields. There are 44 kinds of field, such as short text fields, description fields, and date fields'],
                ['Multiple display modes', 'Display the contents of categories using tables, boxes, or lists'],
                null, // divider
                ['Powerful structure', 'Each catalogue contains categories which contain entries. Catalogues can have a tree structure of categories and/or work from an index'],
                ['Configurable searching', 'Choose which fields are shown on categories, and which can be used to perform searches (template searches)'],
                ['Entirely customisable', 'Full support for customising catalogue categories and entries, exactly as you want them- field by field'],
                ['Classified ads', 'Entries can automatically expire and get archived. See view reports'],
                ['Community interaction', 'Allow users to comment upon and rate entries'],
                ['Import data from spreadsheet files'],
                ['Periodic content reviews', 'Helping you ensure ongoing accuracy of your data'],
            ],
            'Think &ldquo;databases on my website&rdquo;',
        ],
        'wiki' => [
            'Wiki+ <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Wiki+" href="http://shareddemo.composr.info/site/index.php?page=wiki"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Create an encyclopaedic database for your website'],
                ['Use a tree-structure, or traditional cross-linking'],
                ['Track changes'],
                ['Display the tree structure of your whole Wiki+ (normal wiki\'s can\'t do that!)'],
                null, // divider
                ['Allow users to jump in at random pages'],
                ['Make your pages either wiki-style or topic-style'],
            ],
            'Think &ldquo;structured wikis&rdquo;',
        ],
        'calendar' => [
            'Calendar <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Calendar" href="http://shareddemo.composr.info/site/index.php?page=calendar"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Behaves like you\'d expect', 'Day/week/month/year views'],
                ['Advanced &ldquo;recurring event&rdquo; settings'],
                ['Event reminders'],
                ['Detect conflicting events'],
                null, // divider
                ['Microformats support'],
                ['Integrate a calendar month view, or an upcoming events view, onto your design'],
                ['Multiple event types'],
                ['Multiple timezones', 'Have different events in different timezones, with configurable conversion settings'],
                ['Sophisticated permissions'],
                ['Priority flagging'],
                ['Programmers can even use the calendar to schedule website cronjobs'],
                ['<abbr title="Really Simple Syndication">RSS</abbr> and Atom support', 'Export support, but also support for overlaying news feeds onto the calendar'],
                (!is_maintained('ip_geocoding')) ? false : ['Geotargetting'],
            ],
        ],
        'news' => [
            'News and blogging <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/site/index.php?page=news"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Member blogs', 'Allow members to have their own blogs <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/cms/index.php?page=cms_blogs"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['<abbr title="Really Simple Syndication">RSS</abbr> and Atom support', 'Export and import feeds'],
                ['Trackback support', 'Send and receive trackbacks'],
                ['Scheduled publishing'],
                null, // divider
                ['Ping support and <abbr title="Really Simple Syndication">RSS</abbr> Cloud support'],
                ['Multiple news categories, and filtering'],
                ['Multiple ways to integrate news into your website'],
                ['Import from RSS feeds <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/cms/index.php?page=cms_news"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                (!is_maintained('ip_geocoding')) ? false : ['Geotargetting'],
            ],
        ],
        'quizzes' => [
            'Quizzes <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Quizzes" href="http://shareddemo.composr.info/site/index.php?page=quiz"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Run a competition', 'Give members a chance to win'],
                ['Surveys', 'Gather data and find trends'],
                ['Tests', 'Allow members to take tests'],
                ['Cheat prevention', 'Settings to prevent cheating'],
            ],
        ],
        'galleries' => [
            'Galleries <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Galleries" href="http://shareddemo.composr.info/site/index.php?page=galleries"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Multimedia', 'Supports images, videos, audio, and more'],
                ['Personal galleries', 'Allow your members to create their own galleries'],
                ['Support for embedding YouTube and Vimeo videos', 'Save on bandwidth'],
                null, // divider
                ['Auto-detection of video length and resolution (most file formats)'],
                ['Full tree-structure support'],
                ['2 different display modes'],
                ['e-cards'],
                ['Slide-shows'],
                ['Automatic thumbnail generation'],
                ['Mass upload', 'Including metadata support'],
                ['Optional watermarking', 'To guard against thieving swines ;)'],
                (!is_maintained('ip_geocoding')) ? false : ['Geotargetting'],
                ['Adjustments', 'Automatic size and orientation adjustments'],
            ],
        ],
        'downloads' => [
            'Downloads/documents library <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Downloads" href="http://shareddemo.composr.info/site/index.php?page=downloads"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Clear organisation', 'Uses a tree structure for unlimited categorisation'],
                ['&lsquo;Sell&rsquo; downloads using website points'],
                ['Anti-leech protection'],
                ['Community-centred', 'Allow users to comment upon and rate downloads'],
                null, // divider
                ['Many ways to add new files', 'Upload files. Link-to existing files. Copy existing files using a live URL. Batch import links from existing file stores'],
                ['Author support', 'Assign your downloads to authors, so users can find other downloads by the same author'],
                ['Set licences', 'Make users agree to a licence before downloading'],
                ['Images', 'Show images along with your downloads (e.g. screen-shots)'],
                ['Basic file versioning support'],
            ],
        ],
        'pages' => [
            'Web pages <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Page support" href="http://shareddemo.composr.info/lorem/index.php?page=lorem"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Add unlimited pages'],
                ['<abbr title="What You See Is What You Get">WYSIWYG</abbr> editor'],
                ['Convenient edit links', 'Staff see &ldquo;edit this&rdquo; links at the bottom of every page'],
                ['PHP support', 'Upload your PHP scripts and run them inside Composr (may require adjustments to the script code)'],
                null, // divider
                ['Hierarchical page structure'],
                ['Periodic content reviews', 'Helping you ensure ongoing accuracy of your content'],
            ],
        ],
    ],
    'architecture' => [
        'debranding' => [
            'Debranding <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Debranding" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_debrand"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [],
            'Use Composr for clients and pretend <strong>you</strong> made it',
        ],
        'permissions' => [
            'Permissions <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Permissions" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_permissions"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Detailed privilege control', 'Over 180 permissions'],
                ['Control access to all your resources'],
                ['User-friendly permissions editor'],
                null, // divider
                ['Create addition access controls based on URL'],
                ['Customise your permission error messages'],
            ],
        ],
        'nav' => [
            'Structure and navigation',
            [
                ['Visually browse your site structure', 'Intuitive sitemap editor <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Sitemap Editor" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_sitemap&amp;type=sitemap"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Menu editor', 'Our user friendly editor can work with 7 different kinds of menu design (drop-downs, tree menus, pop-ups, etc) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Menus" href="http://shareddemo.composr.info/lorem/index.php?page=menus"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Zones (sub-sites)', 'Organise your pages into separate zones. Zones can have different menus, themes, permissions, and content <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Zones" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_zones"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Full structural control', 'Edit, move, and delete existing pages'],
                ['Redirects', 'Set up redirects if you move pages, or if you want pages to appear in more than one zone <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Redirects" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_redirects"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
        ],
        'extendable' => [
            'Extendable and programmable',
            [
                ['Versatile', 'You can strip down to a core system, or build up with 3rd-party addons <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Addons" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_addons"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Full <abbr title="Application Programming Interface">API</abbr> documentation <a target="_blank" class="link-exempt no-print" title="(Opens in new window) API documentation" href="{$BRAND_BASE_URL}/docs/api/"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['High coding standards', 'No PHP notices. Type-strict codebase. We use <abbr title="Model View Controller">MVC</abbr>'],
                ['Free online developer\'s guide book <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Developers Documentation" href="{$PAGE_LINK*,docs:codebook}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Custom field filters', 'For example, restrict news posts to a minimum length'],
                ['Stack traces for easy debugging'],
                ['Synchronise data between staging and live sites using Resource-fs'],
            ],
        ],
        'integration' => [
            'Integration and conversion',
            [
                ['Convert from other software', 'See our <a href="' . escape_html($importing_tutorial_url) . '">importing tutorial</a> for a list of importers'],
                ['Use an existing member system', 'See our <a href="' . escape_html($download_page_url) . '">download page</a> for a list of forum drivers'],
                ['Convert an <abbr title="HyperText Markup Language">HTML</abbr> site into Composr pages'],
                (!is_maintained('ldap')) ? false : ['LDAP support for corporate networks (<abbr title="The Composr forum">Conversr</abbr>) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Help of LDAP usage" href="{$PAGE_LINK*,docs:tut_ldap}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                (!is_maintained('httpauth')) ? false : ['HTTP authentication', 'Tie into an existing HTTP authentication-based login system (<abbr title="The Composr forum">Conversr</abbr>)'],
                ['Proxying system', 'Programmers can integrate any existing scripts using our sophisticated proxying system (which includes full cookie support)'],
                ['Minimodules and miniblocks', 'Programmers can port existing PHP code into Composr itself <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Framework documentation" href="{$PAGE_LINK*,docs:tut_framework}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
        ],
    ],
    'design' => [
        'adminzone' => [
            'Administration Zone',
            [
                ['Status overview', 'Upgrade and task notification from the Admin Zone dashboard <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Admin Zone" href="http://shareddemo.composr.info/adminzone/index.php?page=start"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Backups', 'Create and schedule full and incremental backups, local or remote <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Backups" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_backup"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Analytics', 'Website statistics rendered as charts <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Statistics" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_stats"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Conflict detection', 'Detect when two staff are trying to change the same thing at the same time'],
                ['Examine audit trails', 'See exactly who has done what and when <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Audit Trails" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_actionlog"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Commandr', 'Optional use of a powerful command-line environment (for Unix geeks). Use unix-like tools to explore and manage your database as it if was a filesystem, and perform general maintenance tasks'],
                ['Aggregate content types', 'Design complex content relationships, cloning out large structures in a single operation.'],
                null, // divider
                ['Configurable access', 'Restrict to no/partial/full access based on usergroup'],
                ['Detect broken URLs <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Cleanup Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cleanup"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Content versioning <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Page Versioning" href="http://shareddemo.composr.info/cms/index.php?page=cms_comcode_pages&amp;type=_edit&amp;page_link=:' . DEFAULT_ZONE_PAGE_NAME . '"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
        ],
        'tools' => [
            'Themeing tools',
            [
                ['Theme Wizard', 'Recolour all your <abbr title="Cascading Style Sheets">CSS</abbr> and images in just a few clicks (Composr picks the perfect complementary palette and automatically makes 100\'s of CSS and image changes) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Theme Wizard" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_themewizard"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Built-in template and <abbr title="Cascading Style Sheets">CSS</abbr> editing tools <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Theme Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin&amp;type=style"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Quick-start logo wizard <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Logo Wizard" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_themewizard&amp;type=make_logo"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Interactive CSS editor', 'Quickly identify what to change and preview'],
            ],
        ],
        'barriers' => [
            'Design without barriers',
            [
                ['Full control of your vision', 'Control hundreds of settings. Strip Composr down. Reshape features as needed'],
                ['Full templating support', 'Reskin features to look however you want them to'],
                ['No navigation assumptions', 'Replace default page and structures as required'],
                null, // divider
                ['No layout assumptions', 'Shift content between templates, totally breaking down any default layout assumptions'],
                ['Embed content entries of any type on your pages'],
            ],
        ],
        'tempcode' => [
            'Template programming language (Tempcode) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Tempcode" href="{$PAGE_LINK*,docs:tut_tempcode}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Perform computations', 'Run loops, manipulate logic, numbers, and text'],
                ['Handy effects', 'Easily create design effects like &ldquo;Zebra striping&rdquo; and tooltips &ndash; and much more'],
                ['Branching and filtering', 'Tailor output according to permissions and usergroups, as well as user options such as language selection'],
                null, // divider
                ['Include other templates, blocks, or pages, within a template'],
                ['Create and use standard boxes', 'Avoid having to copy and paste complex segments of <abbr title="eXtensible HyperText Markup Language 5">XHTML5</abbr>'],
                (!is_maintained('detect_bot') || !is_maintained('detect_mobile')) ? false : ['Easy web browser sniffing', 'Present different markup to different web browsers, detect whether JavaScript is enabled, detect bots, and detect PDAs/Smartphones'],
                ['Randomisation features'],
                ['Pull up member details with ease', 'For example, show the current users avatar or point count'],
                ['Easily pull different banner rotations into your templates'],
            ],
        ],
        'rad' => [
            '<abbr title="Rapid Application Development">RAD</abbr> and testing tools',
            [
                ['Switch users', 'Masquerade as any user using your admin login <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of SU" href="http://shareddemo.composr.info/index.php?keep_su=test"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Change theme images inline with just a few clicks'],
                ['Easily find and edit the templates used to construct any screen <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Template Tree" href="http://shareddemo.composr.info/index.php?special_page_type=tree"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Error monitoring', 'Get informed by e-mail if errors ever happen on your site'],
                null, // divider
                ['Make inline changes to content titles'],
                ['Easy text changes', 'Easily change the language strings used to build up any screen'],
                ['Easily diagnose permission configuration problems', 'Log permission checks, or interactively display them in the browser console'],
            ],
        ],
        'richmedia' => [
            'Rich media <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Comcode" href="http://shareddemo.composr.info/lorem/index.php?page=lorem"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Comcode', 'Powerful but simple content-enrichment language'],
                ['Media embedding', 'Easily integrate/attach all common video and image formats, as well as embeds for common sites such as Vimeo, YouTube, and Google Maps (just by pasting in the URL)'],
                ['Easily create cool effects', 'Create scrolling, rolling, randomisation, and hiding effects. Put content in boxes, split content across subpages. Create <abbr title="eXtensible HyperText Markup Language 5">XHTML5</abbr> overlays. Place tooltips'],
                ['Customise your content for different usergroups'],
                ['Create count-downs and hit counters'],
                ['Automatic table of contents creation for your documents'],
                ['Custom Comcode tags', 'Set up your own tags, to make it easy to maintain a sophisticated and consistent design as your site grows <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Custom Comcode" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_custom_comcode"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Embed pages within other pages'],
            ],
        ],
    ],
    'standards' => [
        'security' => [
            'Security',
            [
                ['<abbr title="Secure Socket Layer">SSL</abbr>/HTTPS support', 'Make pages of your choice run over <abbr title="Transport Layer Security">TLS</abbr> (e.g. the join and payment pages)'],
                ['Automatic detection and banning of hackers <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of IP Banning" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_ip_ban"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Punishment system', 'Warnings, probation, and silencing of members from forums/topics<br />(Conversr-only) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Punishments" href="http://shareddemo.composr.info/site/index.php?page=warnings&amp;type=add&amp;id=2"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['2-factor-authentication', 'E-mail based 2-factor-authentication security when unrecognised IP addresses are used with staff groups<br />(optional, Conversr-only)'],
                null, // divider
                ['Password strength checks', 'Enforce minimum password strengths (Conversr-only)'],
                ['Architectural approaches to combat all major exploit techniques'],
                ['Defence-in-depth', 'Multiple layers of built-in security'],
                ['<abbr title="Cross-Site scripting">XSS</abbr> protection', 'Developed using unique technology to auto-detect XSS security holes before the software gets even released'],
                (!is_maintained('cpf_encryption')) ? false : ['Encrypted Custom Profile Fields', 'Once set the CPF can\'t be read unless a key password is entered (Conversr-only, requires OpenSSL)'],
                ['Track failed logins <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Security" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_security"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['<abbr title="HyperText Markup Language">HTML</abbr> filtering'],
                ['Protection against <abbr title="Cross-Site Request-Forgery">CSRF</abbr> attacks', 'You can temporarily &lsquo;Concede&rsquo; your admin access for added protection'],
                ['Root-kit detection kit for developers'],
            ],
        ],
        'spam' => [
            'Spam protection and Moderation',
            [
                ['Configurable swear filtering <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Word Filter" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_wordfilter"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['IP address analysis', 'Audit, check, and ban <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Lookup Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_lookup"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['<abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr>'],
                (!is_maintained('stop_forum_spam')) ? false : ['Integrate with known-spammer blocklists', 'Multiple configurable levels of enforcement'],
                ['Honeypots and blackholes', 'Find and ban bots via automated traps'],
                ['Heuristics', 'Clever ways to detect and block spammers based on behaviour'],
                null, // divider
                ['Published e-mail addresses will be protected from spammers'],
                ['Protection from spammers trying to use your website for their own <abbr title="Search Engine Optimisation">SEO</abbr>'],
            ],
        ],
        'easeofuse' => [
            'Ease of use <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Ease of use" href="{$BRAND_BASE_URL}/docs/"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Professionally designed user interfaces'],
                ['<abbr title="Asynchronous JavaScript And XML">AJAX</abbr> techniques', 'Streamlined website interaction'],
                ['<abbr title="What You See Is What You Get">WYSIWYG</abbr> editing'],
                ['Tutorials', 'Over 200 written tutorials, and a growing collection of video tutorials'],
                ['Displays great on mobiles', 'Mobile browsers can be automatically detected, or the user can select the mobile version from the footer. All public website features work great on <abbr title="Quarter VGA, a mobile display size standard">QVGA</abbr> or higher.'],
                ['A consistent and fully integrated feature-set', 'Breadcrumb navigation, previews, and many other features we didn\'t have space to mention here &ndash; are all present right across Composr'],
            ],
        ],
        'performance' => [
            'Performance',
            [
                ['Highly optimised code'],
                ['Support for <abbr title="Content Delivery Networks">CDN</abbr>s'],
                null, // divider
                ['Multiple levels of caching'],
                ['Sophisticated template compiler'],
                ['Self-learning optimisation system'],
            ],
        ],
        'webstandards' => [
            'Web standards <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Web standards" href="{$PAGE_LINK*,site:vision}"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Responsive design and hi-dpi images'],
                ['True and correct <abbr title="eXtensible HyperText Markup Language 5">XHTML5</abbr> markup'],
                (!is_maintained('standard_accessibility')) ? false : ['<abbr title="Web Content Accessibility Guidelines">WCAG</abbr>, <abbr title="Authoring Tool Accessibility Guidelines">ATAG</abbr>', 'Meeting of accessibility guidelines in full'],
                ['Tableless <abbr title="Cascading Style Sheets">CSS</abbr> markup, with no hacks'],
                null, // divider
                ['Support for all major web browsers'],
                ['Inbuilt tools for checking webstandards conformance of <abbr title="eXtensible HyperText Markup Language 5">XHTML5</abbr>, <abbr title="Cascading Style Sheets">CSS</abbr>, and JavaScript'],
                (!is_maintained('standard_microformats')) ? false : ['Extra markup semantics', 'Including Dublin Core support, schema.org, Open Graph, and microformats'],
                ['Standards-based (modern <abbr title="Document Object Model">DOM</abbr> and <abbr title="Asynchronous JavaScript And XML">AJAX</abbr>, no DOM-0 or innerHTML) JavaScript'],
                ['Automatic cleanup of bad <abbr title="eXtensible HyperText Markup Language 5">XHTML5</abbr>', 'HTML outside your control (e.g. from <abbr title="Really Simple Syndication">RSS</abbr>) will be cleaned up for you'],
            ],
        ],
        'itln' => [
            'Localisation support <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Localisation" href="https://explore.transifex.com/composr/"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Translate Composr into your own language'],
                ['Host multiple languages on your website at the same time'],
                null, // divider
                (!is_maintained('multi_lang_content')) ? false : ['Translate content into multiple languages'],
                ['Custom time and date formatting'],
                ['Timezone support', 'Members may choose their own timezones'],
                ['Full utf-8 support'],
                ['Serve different theme images for different languages'],
                (!is_maintained('theme_rtl')) ? false : ['Support for right-to-left languages'],
            ],
        ],
        'seo' => [
            '<abbr title="Search Engine Optimisation">SEO</abbr> <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of SEO" href="http://shareddemo.composr.info/index.php?page=sitemap"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>',
            [
                ['Support for short URLs', 'Also textual monikers instead of numeric IDs'],
                ['Automatic site-map generation', 'Both XML Sitemaps and sitemaps for users'],
                ['Metadata', 'Meta descriptions and keywords for all content. Auto-summarisation.'],
                null, // divider
                ['Keyword density analysis for your content'],
                ['Correct use of HTTP status codes'],
                ['Content-contextualised page titles'],
                ['<abbr title="Search Engine Optimisation">SEO</abbr> via semantic and accessible markup (e.g. &lsquo;alt tags&rdquo;)'],
            ],
        ],
        'legal' => [
            'Legal',
            [
                ['Detailed GDPR auditing'],
                ['COPPA'],
                // ['Utah (US) Social Media Regulation Act'], // TODO: #5569
            ],
        ],
    ],
    'forums' => [
        'cnsmembers' => [
            'Membership',
            [
                ['Profiles', 'Browse through and search for members, and view member profiles <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Member Directory" href="http://shareddemo.composr.info/site/index.php?page=members"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Multiple usergroups', 'Members can be in an unlimited number of different usergroups. They can also &lsquo;apply&rsquo; to join new ones <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Usergroups" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_groups"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Social networking', 'Create and browse friendships'],
                ['Custom Profile Fields', 'Allow your members to add extra information which is relevant to your website (or to their subcommunity) <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Custom Profile Fields" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_customprofilefields"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Promotion system', 'Members can &lsquo;advance the ranks&rsquo; by earning points'],
                ['Private topics between 2 or more members', 'Better than the basic personal messages most forum software provides <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Private Topics" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;type=pt&amp;id=2"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Invitation-only websites', 'Existing members can invite others <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Recommendation &ndash; the demo does not have invites turned on though" href="http://shareddemo.composr.info/index.php?recommend"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Allow members to create and manage a club <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Clubs" href="http://shareddemo.composr.info/cms/index.php?page=cms_cns_groups"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Over 40 bundled avatars', 'Member\'s may also upload or link to their own <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Avatars" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab--edit--avatar"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Member signatures, photos, and personal titles <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Personal Zone" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab--edit"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Users online', 'See which members are currently online, unless they logged in as invisible <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Online Members" href="http://shareddemo.composr.info/site/index.php?page=users_online"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Account pruning', 'Find and delete unused accounts, merge duplicate accounts <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Account Pruning" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_members&amp;type=delurk"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Members may set privacy settings for individual fields <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Privacy Settings" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab--edit--privacy"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Spreadsheet files', 'Import and export members using spreadsheet files, including support for automatic creation of Custom Profile Fields and usergroups &ndash; great for migrating data <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Member Spreadsheet Import" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_members&amp;type=import_spreadsheet"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
            'Conversr-only',
        ],
        'cnsforum' => [
            'Forums',
            [
                ['The usual stuff', 'Categories, forums, topics, posts, polls <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Forum" href="http://shareddemo.composr.info/forum/index.php?page=forumview"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Forum and Topic tracking', 'Receive notifications when new posts are made'],
                ['Password-protected forums'],
                ['Full moderator control', 'Determine who may moderate what forums'],
                ['Inline personal posts', 'Whisper to members within a public topic <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Whispering" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;type=browse&amp;id=2"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Over 50 bundled emoticons', 'Also, support for batch importing new ones <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Emoticons" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_emoticons"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Multi-moderation', 'Record and perform complex routine tasks <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Multi Moderation" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_multi_moderations"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Constitutionally-suitable poll voting', 'Weighted voting and per-forum configurable setting defaults/enforcement, suitable for running autonomous organisations (similar to DAOs)'],
                ['Announcements'],
                ['Quick reply'],
                ['Post/topic moderation and validation'],
                ['Unlimited sub-forum depth'],
                ['Mass-moderation', 'Perform actions on many posts and topics at once'],
                ['Post Templates', 'Use your forum as a database for record gathering <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Post Templates" href="http://shareddemo.composr.info/adminzone/index.php?admin_cns_post_templates"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Post preview', 'Read a topics first post directly from the forum-view'],
                ['Highlight posts as &lsquo;important&rsquo; <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Highlighted Posts" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;id=3"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>', 'Your posts will be <a href="https://www.youtube.com/watch?v=5hARDXYz2io" target="_blank" title="(Opens in new window)">high as a kite by then</a>'],
            ],
            'Conversr-only',
        ],
        'tracking' => [
            'Stay on top of things',
            [
                ['Find posts made since you last visited <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of New Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Remembers your unread posts', 'Even if you frequently change computers <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Unread Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=unread"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Recent activity', 'See what topics you recently read or posted in <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Recent Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=recently_read"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                ['Unanswered topics', 'Find which topics have not yet been answered <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of Unanswered Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=unanswered"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['<abbr title="Really Simple Syndication">RSS</abbr> and Atom support <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of RSS Feeds" href="http://shareddemo.composr.info/backend.php"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
            'Conversr-only',
        ],
        'forumdrivers' => [
            'Forum integration',
            [
                ['Support for popular products', 'See our <a href="' . escape_html($download_page_url) . '">download page</a> for a list of supported forums'],
                ['Share login credentials', 'Login with the same usernames/passwords'],
                ['Share usergroups', 'Control website access based on someone\'s usergroup'],
                ['Emoticon support', 'The emoticons on your forum will also be used on your website. Your members will be happy little <a href="https://www.youtube.com/watch?v=V3fZhJN4Tdc" target="_blank" title="(Opens in new window)">hobbits</a>'],
            ],
            'If integrating a third-party product',
        ],
        'forumcontentsharing' => [
            'Content sharing',
            [
                ['Show topics on your website <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of displayed forum topics" href="http://shareddemo.composr.info/lorem/index.php?page=start"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
                null, // divider
                ['Comment integration', 'New topics appear in the &lsquo;comments&rsquo; forum as you add content to your website. Members can watch these topics so they never miss an addition to your website <a target="_blank" class="link-exempt no-print" title="(Opens in new window) Example of comment topics" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;id=website-comment-topics"><img class="inline-image-3" alt="" width="12" height="12" src="{$IMG*,icons/arrow_box/arrow_box}" /></a>'],
            ],
        ],
    ],
];

$collapsed_tree = [];
foreach ($feature_tree as $t) {
    $collapsed_tree += $t;
}

$raw = (isset($map['raw'])) && ($map['raw'] == '1');

// Columns
if (!$raw) {
    echo '<div class="feature-columns float-surrounder-hidden">' . "\n";
}
foreach (empty($map['param']) ? array_keys($collapsed_tree) : explode(',', $map['param']) as $i => $column) {
    if (!$raw) {
        echo '<div class="column column' . strval($i) . '">';
    }

    // Subsections in column
    foreach (explode('|', $column) as $subsection_code) {
        if (!isset($collapsed_tree[$subsection_code])) {
            fatal_exit('Missing: ' . $subsection_code);
        }
        $subsection = $collapsed_tree[$subsection_code];

        if ($subsection === false) { // Filtered out
            continue;
        }

        if (!$raw) {
            echo '<div class="subsection">' . "\n\n";
        }

        // Icon and title
        echo '<div class="iconAndTitle">' . "\n\n";
        $subsection_title = $subsection[0];
        require_code('tempcode_compiler');
        $subsection_title = comcode_to_tempcode('[semihtml]' . $subsection_title . '[/semihtml]', null, true);
        $subsection_title = $subsection_title->evaluate();
        $subsection_items = $subsection[1];
        $s_img = find_theme_image('cms_homesite/features/' . preg_replace('#[^\w]#', '', $subsection_code), true);
        if ($s_img != '') {
            echo '<img alt="" src="' . $s_img . '" />' . "\n\n";
        }
        if (!$raw) {
            echo '<h3>' . $subsection_title . '</h3>' . "\n\n";
            echo '</div>' . "\n\n";
        }

        // Subsection caption, if there is one
        if (array_key_exists(2, $subsection)) {
            $subsection_caption = $subsection[2];
        } else {
            $subsection_caption = '';
        }
        if (!cms_empty_safe($subsection_caption)) {
            echo '<p class="subsectionCaption">' . $subsection_caption . '.</p>';
        }

        // List
        if (!empty($subsection_items)) {
            echo '<div><ul class="main">';
            $see_more = false;
            foreach ($subsection_items as $item) {
                if ($item === false) { // Filtered out
                    continue;
                }

                if ($item === null) { // Divider
                    echo '</ul></div>' . "\n\n";
                    $see_more = true;
                    echo '<div class="moreE"><ul class="more">';
                } else {
                    $item[0] = comcode_to_tempcode('[semihtml]' . $item[0] . '[/semihtml]', null, true);
                    $item[0] = $item[0]->evaluate();
                    if (array_key_exists(1, $item)) {
                        $item[1] = comcode_to_tempcode('[semihtml]' . $item[1] . '[/semihtml]', null, true);
                        $item[1] = $item[1]->evaluate();
                    }

                    echo '<li>';
                    echo '<span class="itemTitle">' . $item[0] . '</span>';
                    if (array_key_exists(1, $item)) {
                        if ((strpos($item[1], 'icons/arrow_box/arrow_box') === false) && (substr($item[1], -1) != '!') && (substr($item[1], -1) != '?') && (substr($item[1], -1) != '.')) {
                            $item[1] .= '.';
                        }
                        echo '<span class="itemDescription">' . $item[1] . '</span>';
                    }
                    echo '</li>';
                }
            }
            echo '</ul></div>';
            if ($see_more) {
                echo '<p class="button"><a class="seemore" href="#!" onclick="toggle_seemore(this); return false;">See more</a></p>'/*."\n\n"*/;
            }
        }

        echo '</div>' . "\n\n";
    }

    if (!$raw) {
        echo '</div>' . "\n\n";
    }
}
if (!$raw) {
    echo '</div>' . "\n\n";
}
