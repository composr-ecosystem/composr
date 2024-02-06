<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Module page class.
 */
class Module_tutorials
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_tutorials';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'tutorials_external',
            'tutorials_external_tags',
            'tutorials_tags',
            'tutorials_internal',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        $GLOBALS['SITE_DB']->create_table('tutorials_external', [
            'id' => '*AUTO',
            't_url' => 'URLPATH',
            't_title' => 'SHORT_TEXT',
            't_summary' => 'LONG_TEXT',
            't_icon' => 'ID_TEXT',
            't_media_type' => 'ID_TEXT', // document|video|audio|slideshow|book
            't_difficulty_level' => 'ID_TEXT', // novice|regular|expert
            't_pinned' => 'BINARY',
            't_author' => 'ID_TEXT',
            't_submitter' => 'MEMBER',
            't_views' => 'INTEGER',
            't_add_date' => 'TIME',
            't_edit_date' => 'TIME',
        ]);

        $GLOBALS['SITE_DB']->create_index('tutorials_external', '#t_title', ['t_title']);
        $GLOBALS['SITE_DB']->create_index('tutorials_external', '#t_summary', ['t_summary']);

        $GLOBALS['SITE_DB']->create_table('tutorials_external_tags', [
            't_id' => '*AUTO_LINK',
            't_tag' => '*ID_TEXT',
        ]);

        $GLOBALS['SITE_DB']->create_table('tutorials_internal', [
            't_page_name' => '*ID_TEXT',
            't_views' => 'INTEGER',
        ]);

        // Insert default external tutorials
        $external_tutorials = [
            // YouTube videos
            [
                'title' => 'Installing ocPortal with Softaculous in cPanel',
                'url' => 'https://www.youtube.com/watch?v=GnEqFPMUQmw',
                'author' => 'NixiHost',
                'difficulty_level' => 'novice',
                'summary' => 'A demonstration of how to easily install ocPortal (now Composr) on a hosting account that has Softaculous. Produced by a webhost that does (NixiHost).',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Installation'],
            ],
            [
                'title' => 'Implementing a "Shopping discounts" feature',
                'url' => 'https://www.youtube.com/watch?v=ucTaHpd3ObA',
                'author' => 'Chris Graham (ocProducts)',
                'difficulty_level' => 'expert',
                'summary' => 'A live programming tutorial, implementing a new "discounts" feature into the shopping catalogues. Shows you how coding for Composr gets done.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Development', 'PHP', 'Catalogues', 'eCommerce'],
            ],
            [
                'title' => 'Using diff tools to upgrade an ocPortal theme',
                'url' => 'https://www.youtube.com/watch?v=0M8KWaM3bwk',
                'author' => 'Chris Graham (ocProducts)',
                'difficulty_level' => 'regular',
                'summary' => 'How to use a diff tool to find changes made in a theme, or upgrade the files in a theme.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Maintenance', 'Upgrading'],
            ],
            [
                'title' => 'Changing the ocPortal header image',
                'url' => 'https://www.youtube.com/watch?v=-5MZOTdxFcU',
                'author' => 'Chris Graham (ocProducts)',
                'difficulty_level' => 'novice',
                'summary' => 'A live themeing tutorial showing how to change the page header. Note that this was for an older version so is very outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Themes 201 - building a totally new design',
                'url' => 'https://www.youtube.com/watch?v=e8ArVn-Jb8Q',
                'author' => 'Allen Ellis (ocProducts)',
                'difficulty_level' => 'expert',
                'summary' => 'A live themeing tutorial showing how to build a totally new design. Note that this was for an older version so is very outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Themes 101 - replacing the header',
                'url' => 'https://www.youtube.com/watch?v=0uEwY8DAYPs',
                'author' => 'Allen Ellis (ocProducts)',
                'difficulty_level' => 'regular',
                'summary' => 'A live themeing tutorial showing how to change the header. Note that this was for an older version so is quite outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Custom Comcode tags',
                'url' => 'https://www.youtube.com/watch?v=dBuBiYuGE0E',
                'author' => 'Allen Ellis (ocProducts)',
                'difficulty_level' => 'novice',
                'summary' => 'How to extend Comcode using Custom Comcode tags.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Content'],
            ],
            [
                'title' => 'SU and the if_in_group tag',
                'url' => 'https://www.youtube.com/watch?v=J6OGys3s6v8',
                'author' => 'Allen Ellis (ocProducts)',
                'difficulty_level' => 'novice',
                'summary' => 'How to switch users under your admin login, and how to customise page contents based on usergroup membership.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Structure and navigation', 'Content', 'Security', 'Members'],
            ],
            [
                'title' => 'Embedding a Google map',
                'url' => 'https://www.youtube.com/watch?v=T3o7mj0OU4Q',
                'author' => 'Allen Ellis (ocProducts)',
                'difficulty_level' => 'novice',
                'summary' => 'Tutorial showing how to embed an HTML widget (Google Maps in this case) into a Comcode page.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Content'],
            ],
            [
                'title' => 'Changing the login block in the header',
                'url' => 'https://youtu.be/0uWdYq_YCeU',
                'author' => 'Chris Graham (ocProducts)',
                'difficulty_level' => 'regular',
                'summary' => 'Themeing tutorial, putting the login block into the header. Note that this was for an older version so is quite outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'A social site in under 40 minutes',
                'url' => 'https://youtu.be/Bv6MwqHhxvg',
                'author' => 'Chris Graham (ocProducts)',
                'difficulty_level' => 'novice',
                'summary' => 'This video was originally made for our old ocportal.com front page. Note that this was for an older version so is quite outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Configuration'],
            ],
            [
                'title' => 'Installing ocPortal, part 1',
                'url' => 'https://youtu.be/bniWVljBbF4',
                'author' => 'Dr Keith Maynard',
                'difficulty_level' => 'novice',
                'summary' => 'A tutorial on how to install. Covers downloading and uploading the installer. Note that this was for an older version so is quite outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Installation'],
            ],
            [
                'title' => 'Installing ocPortal, part 2',
                'url' => 'https://youtu.be/bniWVljBbF4',
                'author' => 'Dr Keith Maynard',
                'difficulty_level' => 'novice',
                'summary' => 'A tutorial on how to install. Covers running the installer. Note that this was for an older version so is quite outdated.',
                'icon' => 'icons/spare/video2',
                'media_type' => 'video',
                'tags' => ['Installation'],
            ],

            // Books
            [
                'title' => 'The 22 Immutable Laws Of Branding',
                'url' => 'https://www.amazon.co.uk/gp/product/1861976054/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1861976054&linkCode=as2&tag=ocportal-21&linkId=UYNVBEDIBTERUFIR',
                'author' => 'Al Ries, Laura Ries',
                'difficulty_level' => 'novice',
                'summary' => 'Everyone knows that building your product or service into a bona fide brand is the only way to stand out in today\'s insanely crowded marketplace. The only question is how do you do it?',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'The 22 Immutable Laws of Marketing',
                'url' => 'https://www.amazon.co.uk/gp/product/B00118WX46/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=B00118WX46&linkCode=as2&tag=ocportal-21&linkId=2QUVBUQ6WAQ3H3KU',
                'author' => 'Al Ries, Jack Trout',
                'difficulty_level' => 'novice',
                'summary' => 'Rules for certain successes in the world of marketing. Combining a wide-ranging historical overview with a keen eye for the future, the authors bring to light 22 superlative tools and innovative techniques for the international marketplace.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'About Face 3: The Essentials of Interaction Design',
                'url' => 'https://www.amazon.co.uk/gp/product/0470084111/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0470084111&linkCode=as2&tag=ocportal-21&linkId=3X2DR2DIVMIDYDOH',
                'author' => 'Alan Cooper',
                'difficulty_level' => 'expert',
                'summary' => 'Effective and practical tools you need to design great desktop applications, Web 2.0 sites, and mobile devices.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'After Image: Mind-Altering Marketing',
                'url' => 'https://www.amazon.co.uk/gp/product/1861976402/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1861976402&linkCode=as2&tag=ocportal-21&linkId=JZS3OYMCYB2IGYCP',
                'author' => 'John Grant',
                'difficulty_level' => 'novice',
                'summary' => 'This pioneering book draws from the latest findings in business theory, cognitive neuroscience and social research, to provide a new direction and system for marketing.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'The Brand Innovation Manifesto - How to Build Brands, Redefine Markets and Defy Conventions',
                'url' => 'https://www.amazon.co.uk/gp/product/B000QEOWF4/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=B000QEOWF4&linkCode=as2&tag=ocportal-21&linkId=H47YY5C6UVK3OT2X',
                'author' => 'John Grant',
                'difficulty_level' => 'novice',
                'summary' => 'The days of the image brands are over, and \'new marketing\' has gone mainstream. The world\'s biggest companies are pursuing a post-advertising strategy, moving away from advertising and investing in leading edge alternatives.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Business Adventures: Twelve Classic Tales from the World of Wall Street',
                'url' => 'https://www.amazon.co.uk/gp/product/1473610389/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1473610389&linkCode=as2&tag=ocportal-21&linkId=NJ4YFVCGYQJBBR5E',
                'author' => 'John Brooks',
                'difficulty_level' => 'novice',
                'summary' => 'What do the $350 million Ford Motor Company disaster known as the Edsel, the fast and incredible rise of Xerox, and the unbelievable scandals at General Electric and Texas Gulf Sulphur have in common? Each is an example of how an iconic company was defined by a particular moment of fame or notoriety.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Communicating Design: Developing Web Site Documentation for Design and Planning',
                'url' => 'https://www.amazon.co.uk/gp/product/B0045U9W4G/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=B0045U9W4G&linkCode=as2&tag=ocportal-21&linkId=SM33YMGY2AUHREH2',
                'author' => 'Dan M. Brown',
                'difficulty_level' => 'expert',
                'summary' => 'Successful web design teams depend on clear communication between developers and their clients -- and among members of the development team. Wireframes, site maps, flow charts, and other design diagrams establish a common language so designers and project teams can capture ideas, track progress, and keep their stakeholders informed.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Crossing the Chasm: Marketing and Selling Disruptive Products to Mainstream Customers',
                'url' => 'https://www.amazon.co.uk/gp/product/0062292986/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0062292986&linkCode=as2&tag=ocportal-21&linkId=CMGULMOB4NTNE4UA',
                'author' => 'Geoffrey A. Moore',
                'difficulty_level' => 'novice',
                'summary' => 'The bible for bringing cutting-edge products to larger markets. Geoffrey A. Moore shows that in the Technology Adoption Life Cycle-which begins with innovators and moves to early adopters. early majority. late majority. and laggards-there is a vast chasm between the early adopters and the early majority',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'CSS3: The Missing Manual',
                'url' => 'https://www.amazon.co.uk/gp/product/1491918055/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1491918055&linkCode=as2&tag=ocportal-21',
                'author' => 'David Sawyer McFarland',
                'difficulty_level' => 'regular',
                'summary' => 'CSS3 lets you create professional-looking websites, but learning its finer points can be tricky -- even for seasoned web developers. This Missing Manual shows you how to take your HTML and CSS skills to the next level, with valuable tips, tricks, and step-by-step instructions.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['CSS', 'Design & Themeing', 'Web standards & Accessibility'],
            ],
            [
                'title' => 'The Design of Everyday Things',
                'url' => 'https://www.amazon.co.uk/gp/product/0262525674/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0262525674&linkCode=as2&tag=ocportal-21&linkId=A5UNMPCK6NTCEHGS',
                'author' => 'Donald A. Norman',
                'difficulty_level' => 'novice',
                'summary' => 'Even the smartest among us can feel inept as we try to figure out the shower control in a hotel or attempt to navigate an unfamiliar television set or stove. When The Design of Everyday Things was published in 1988, cognitive scientist Don Norman provocatively proposed that the fault lies not in ourselves, but in design that ignores the needs and psychology of people.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'The Design of Sites: Patterns for Creating Winning Websites',
                'url' => 'https://www.amazon.co.uk/gp/product/0131345559/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0131345559&linkCode=as2&tag=ocportal-21&linkId=SGLZEXDQNYPVN4GZ',
                'author' => 'Douglas K. van Duyne',
                'difficulty_level' => 'regular',
                'summary' => 'The definitive reference for the principles, patterns, methodologies, and best practices underlying exceptional Web design. If you are involved in the creation of dynamic Web sites, this book will give you all the necessary tools and techniques to create effortless end-user Web experiences, improve customer satisfaction, and achieve a balanced approach to Web design.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Designed for Use: Create Usable Interfaces for Applications and the Web',
                'url' => 'https://www.amazon.co.uk/gp/product/1680501607/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1680501607&linkCode=as2&tag=ocportal-21&linkId=U6LCMOS5H7D4T3GW',
                'author' => 'Lukas Mathis',
                'difficulty_level' => 'regular',
                'summary' => 'Explanations of how to make usability the cornerstone of every point in your design process, walking you through the necessary steps to plan the design for an application or website, test it, and get usage data after the design is complete. He shows you how to focus your design process on the most important thing: helping people get things done, easily and efficiently.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Differentiate or Die: Survival in Our Era of Killer Competition',
                'url' => 'https://www.amazon.co.uk/gp/product/0470223391/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0470223391&linkCode=as2&tag=ocportal-21&linkId=SS2PC3POAQLNO6O7',
                'author' => 'Jack Trout',
                'difficulty_level' => 'novice',
                'summary' => 'Differentiate or Die shows you how to differentiate your products, services, and business in order to dominate the competition. Veteran marketing guru Jack Trout uses real-world examples and his own unique insight to show you how to bind customers to your products for long-term success and loyalty.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Don\'t Make Me Think: A Common Sense Approach to Web Usability',
                'url' => 'https://www.amazon.co.uk/gp/product/0321965515/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0321965515&linkCode=as2&tag=ocportal-21&linkId=ADNLKEXVUR4JY3PQ',
                'author' => 'Steve Krug',
                'difficulty_level' => 'novice',
                'summary' => 'Since Don\'t Make Me Think was first published in 2000, hundreds of thousands of Web designers and developers have relied on usability guru Steve Krug\'s guide to help them understand the principles of intuitive navigation and information design. Witty, commonsensical, and eminently practical, it\'s one of the best-loved and most recommended books on the subject.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Emotional Design: Why We Love (or Hate) Everyday Things',
                'url' => 'https://www.amazon.co.uk/gp/product/0465051367/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0465051367&linkCode=as2&tag=ocportal-21&linkId=I6GCGTKQ62IQABAX',
                'author' => 'Donald A. Norman',
                'difficulty_level' => 'novice',
                'summary' => 'Did you ever wonder why cheap wine tastes better in fancy glasses? Why sales of Macintosh computers soared when Apple introduced the colorful iMac? New research on emotion and cognition has shown that attractive things really do work better, as Donald Norman amply demonstrates in this fascinating book.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Founders at Work: Stories of Startups\' Early Days',
                'url' => 'https://www.amazon.co.uk/gp/product/1430210788/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1430210788&linkCode=as2&tag=ocportal-21&linkId=FTWOWC2ABEMCAUG4',
                'author' => 'Jessica Livingston',
                'difficulty_level' => 'novice',
                'summary' => 'Founders at Work: Stories of Startups\' Early Days is a collection of interviews with founders of famous technology companies about what happened in the very earliest days. These people are celebrities now. What was it like when they were just a couple friends with an idea?',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Graphic Design School: The Principles and Practices of Graphic Design',
                'url' => 'https://www.amazon.co.uk/gp/product/0500285268/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0500285268&linkCode=as2&tag=ocportal-21&linkId=KANZGUT3NPV4NGRK',
                'author' => 'David Dabner',
                'difficulty_level' => 'novice',
                'summary' => 'Packed with practical guidance and beautifully illustrated throughout, Graphic Design School provides a solid foundation for the design student as well as offering a back-to-basics tool for more advanced designers in search of solutions to graphic problems.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'HTML & CSS: Design and Build Web Sites',
                'url' => 'https://www.amazon.co.uk/gp/product/1118008189/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1118008189&linkCode=as2&tag=ocportal-21&linkId=7LKAKZALMFMFERIM',
                'author' => 'Jon Duckett',
                'difficulty_level' => 'regular',
                'summary' => 'A full-color introduction to the basics of HTML and CSS.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['CSS', 'Design & Themeing', 'Web standards & Accessibility'],
            ],
            [
                'title' => 'HTML5: The Missing Manual',
                'url' => 'https://www.amazon.co.uk/gp/product/1449363261/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1449363261&linkCode=as2&tag=ocportal-21&linkId=VOOKS2AQ2C72NCJT',
                'author' => 'Matthew MacDonald',
                'difficulty_level' => 'regular',
                'summary' => 'HTML5 is more than a markup language -- it\'s a collection of several independent web standards. Fortunately, this expanded guide covers everything you need in one convenient place.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing', 'Web standards & Accessibility'],
            ],
            [
                'title' => 'JavaScript & jQuery',
                'url' => 'https://www.amazon.co.uk/gp/product/1449399029/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1449399029&linkCode=as2&tag=ocportal-21&linkId=TVOGSEXJPJ4566WO',
                'author' => 'David Sawyer McFarland',
                'difficulty_level' => 'expert',
                'summary' => 'JavaScript lets you supercharge your web pages with animation, interactivity, and visual effects, but learning the language isn\'t easy. This fully updated and expanded guide takes you step-by-step through JavaScript basics, then shows you how to save time and effort with jQuery.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['JavaScript', 'Design & Themeing', 'Web standards & Accessibility'],
            ],
            [
                'title' => 'JavaScript: The Definitive Guide',
                'url' => 'https://www.amazon.co.uk/gp/product/0596805527/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0596805527&linkCode=as2&tag=ocportal-21&linkId=QPOU6OLWQFCKA2GR',
                'author' => 'David Flanagan',
                'difficulty_level' => 'expert',
                'summary' => 'A programmer\'s guide and comprehensive reference to the core language and to the client-side JavaScript APIs defined by web browsers.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['JavaScript', 'Design & Themeing', 'Web standards & Accessibility'],
            ],
            [
                'title' => 'The Laws of Simplicity (Simplicity: Design, Technology, Business, Life)',
                'url' => 'https://www.amazon.co.uk/gp/product/0262134721/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0262134721&linkCode=as2&tag=ocportal-21&linkId=V5IMBI2TVYEXEVCP',
                'author' => 'John Maeda',
                'difficulty_level' => 'novice',
                'summary' => 'Finally, we are learning that simplicity equals sanity. But sometimes we find ourselves caught up in the simplicity paradox: we want something that\'s simple and easy to use, but also does all the complex things we might ever want it to do.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'The New Marketing Manifesto: The 12 Rules for Building Successful Brands in the 21st Century',
                'url' => 'https://www.amazon.co.uk/gp/product/1587990245/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1587990245&linkCode=as2&tag=ocportal-21&linkId=YWKVHKTBWY6M4Z3N',
                'author' => 'John Grant',
                'difficulty_level' => 'novice',
                'summary' => 'Marketing is a vital function of modern business. It plays a key role in the future success of every company - large and small. But the social world around us is changing rapidly. People\'s wants, needs and beliefs no longer conform to the rigid and predictable \'types\' of yesterday that enabled easy targeting.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Ogilvy on Advertising',
                'url' => 'https://www.amazon.co.uk/gp/product/1853756156/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1853756156&linkCode=as2&tag=ocportal-21&linkId=Z3ENOYYNMQJRETDM',
                'author' => 'David Ogilvy',
                'difficulty_level' => 'novice',
                'summary' => 'This is the definitive guide to advertising from the most influential and successful adman of all time - David Ogilvy - who founded an agency which is now an international giant.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'PHP & MySQL: The Missing Manual',
                'url' => 'https://www.amazon.co.uk/gp/product/1449325572/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1449325572&linkCode=as2&tag=ocportal-21&linkId=NWIM4TFQSEDUYYUV',
                'author' => 'Brett McLaughlin',
                'difficulty_level' => 'expert',
                'summary' => 'If you can build websites with CSS and JavaScript, this book takes you to the next level -- creating dynamic, database-driven websites with PHP and MySQL.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['PHP', 'Development'],
            ],
            [
                'title' => 'Positioning: The Battle for Your Mind',
                'url' => 'https://www.amazon.co.uk/gp/product/0071373586/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0071373586&linkCode=as2&tag=ocportal-21&linkId=YNTM3KPP5RRHCZX5',
                'author' => 'Al Ries',
                'difficulty_level' => 'novice',
                'summary' => 'The first book to deal with the problems of communicating to a skeptical, media-blitzed public, Positioning describes a revolutionary approach to creating a "position" in a prospective customer\'s mind-one that reflects a company\'s own strengths and weaknesses as well as those of its competitors.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Rules For Revolutionaries: The Capitalist Manifesto for Creating and Marketing New Products',
                'url' => 'https://www.amazon.co.uk/gp/product/088730995X/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=088730995X&linkCode=as2&tag=ocportal-21&linkId=AYLIO3WNB5NMONPW',
                'author' => 'Guy Kawasaki',
                'difficulty_level' => 'novice',
                'summary' => 'Guy Kawasaki, CEO of garage.com and former chief evangelist of Apple Computer, Inc., presents his manifesto for world-changing innovation, using his battle-tested lessons to help revolutionaries become visionaries.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'The E-Myth Revisited: Why Most Small Businesses Don\'t Work and What to Do About It',
                'url' => 'https://www.amazon.co.uk/gp/product/0887307280/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0887307280&linkCode=as2&tag=ocportal-21&linkId=JH5H3VUXOG3UFCOB',
                'author' => 'Michael E. Gerber',
                'difficulty_level' => 'novice',
                'summary' => 'E-Myth \\ \'e-,\'mith\\ n 1: the entrepreneurial myth: the myth that most people who start small businesses are entrepreneurs 2: the fatal assumption that an individual who understands the technical work of a business can successfully run a business that does that technical work',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Universal Principles of Design',
                'url' => 'https://www.amazon.co.uk/gp/product/1592535879/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1592535879&linkCode=as2&tag=ocportal-21&linkId=NZ7MPXZWEEKOAGOS',
                'author' => 'William Lidwell',
                'difficulty_level' => 'regular',
                'summary' => '115 Ways to Enhance Usability, Influence Perception, Increase Appeal, Make Better Design Decisions and Teach Through Design. The first comprehensive, cross-disciplinary encyclopedia of design.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Design & Themeing'],
            ],
            [
                'title' => 'Venture Deals: Be Smarter Than Your Lawyer and Venture Capitalist',
                'url' => 'https://www.amazon.co.uk/gp/product/1118443616/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1118443616&linkCode=as2&tag=ocportal-21&linkId=7CQVE2VZEJIWQ643',
                'author' => 'Brad Feld, Jason Mendelson',
                'difficulty_level' => 'regular',
                'summary' => 'As each new generation of entrepreneurs emerges, there is a renewed interest in how venture capital deals come together.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'Small Business Start-Up Workbook: A Step-by-step Guide to Starting the Business You\'ve Dreamed of',
                'url' => 'https://www.amazon.co.uk/gp/product/1845280385/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=1845280385&linkCode=as2&tag=ocportal-21&linkId=KW4ONRLRZPK3PMHO',
                'author' => 'Cheryl D. Rickman',
                'difficulty_level' => 'novice',
                'summary' => 'A modern approach to self-employment and business start-up. Packed with real-life case studies and practical exercises, checklists and worksheets, it provides a step-by-step guide to researching and formulating your business ideas, planning the right marketing strategies, and managing a team that will drive your vision forward with you.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
            [
                'title' => 'How to Win Friends and Influence People',
                'url' => 'https://www.amazon.co.uk/gp/product/0091906814/ref=as_li_tl?ie=UTF8&camp=1634&creative=6738&creativeASIN=0091906814&linkCode=as2&tag=ocportal-21&linkId=H6MT4SCD2LS2MUFN',
                'author' => 'Dale Carnegie',
                'difficulty_level' => 'novice',
                'summary' => 'Millions of people around the world have improved their lives based on the teachings of Dale Carnegie. In How to Win Friends and Influence People, he offers practical advice and techniques. His advice has stood the test of time and will teach you how to persuade people to follow your way of thinking, enable you to win new clients and customers and become a better speaker.',
                'icon' => 'icons/spare/book',
                'media_type' => 'book',
                'tags' => ['Advice & Guidance'],
            ],
        ];
        foreach ($external_tutorials as $external_tutorial) {
            $id = $GLOBALS['SITE_DB']->query_insert('tutorials_external', [
                't_url' => $external_tutorial['url'],
                't_title' => $external_tutorial['title'],
                't_summary' => $external_tutorial['summary'],
                't_icon' => $external_tutorial['icon'],
                't_media_type' => $external_tutorial['media_type'],
                't_difficulty_level' => $external_tutorial['difficulty_level'],
                't_pinned' => 0,
                't_author' => $external_tutorial['author'],
                't_submitter' => $GLOBALS['FORUM_DRIVER']->get_guest_id(),
                't_views' => 0,
                't_add_date' => time() - 60 * 60 * 24 * 365,
                't_edit_date' => time() - 60 * 60 * 24 * 365,
            ], true);

            foreach ($external_tutorial['tags'] as $tag) {
                $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', [
                    't_id' => $id,
                    't_tag' => $tag,
                ]);
            }
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'browse' => ['tutorials:TUTORIALS', 'help'],
        ];
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('composr_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
        }
        if (!addon_installed('composr_homesite_support_credits')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite_support_credits')));
        }
        if (!addon_installed('composr_release_build')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_release_build')));
        }

        require_code('tutorials');
        require_css('tutorials');

        $type = get_param_string('type', 'browse');
        if ($type == 'view') {
            return $this->view();
        }

        return $this->browse();
    }

    /**
     * Browse tutorials.
     *
     * @return Tempcode The result of execution
     */
    public function browse() : object
    {
        $title = get_screen_title(protect_from_escaping('Tutorials &ndash; Learning Composr'), false);

        $tag = get_param_string('type', 'Installation', INPUT_FILTER_GET_COMPLEX); // $type, essentially

        $tags = list_tutorial_tags(true);

        $tutorials = list_tutorials_by('likes', ($tag == '') ? null : $tag);
        $_tutorials = templatify_tutorial_list($tutorials);

        return do_template('TUTORIAL_INDEX_SCREEN', [
            '_GUID' => '4569ab28e8959d9556dbb6d73c0e834a',
            'TITLE' => $title,
            'TAGS' => $tags,
            'SELECTED_TAG' => $tag,
            'TUTORIALS' => $_tutorials,
        ]);
    }

    /**
     * Redirect to external tutorial.
     *
     * @return Tempcode The result of execution
     */
    public function view() : object
    {
        $id = get_param_integer('id');

        $tutorial_rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', ['*'], ['id' => $id], '', 1);
        if (!array_key_exists(0, $tutorial_rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'tutorial'));
        }

        $tutorial_row = $tutorial_rows[0];

        $title = get_screen_title(protect_from_escaping($tutorial_row['t_title']), false);

        $url = $tutorial_row['t_url'];
        return redirect_screen($title, $url, do_lang_tempcode('SUCCESS'));
    }
}
