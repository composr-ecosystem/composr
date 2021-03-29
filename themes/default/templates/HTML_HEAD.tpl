{$, - Note you will need to empty the template cache manually if you change this file manually due to the way it is included - }

{$,Want a nice Google font? Try uncommenting the below}
{$,<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet" />}

{$,The character set of the page}
<meta http-equiv="Content-Type" content="text/html; charset={$CHARSET*}" />

{$,Force IE to not use compatiblity mode}
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

{$,Page title}
<title>{+START,IF_NON_PASSED,TITLE}{+START,IF_NON_EMPTY,{$HEADER_TEXT}}{$HEADER_TEXT*} &ndash; {+END}{$SITE_NAME*}{+END}{+START,IF_PASSED,TITLE}{TITLE}{+END}</title>

{$,In developer mode we totally break relative URLs so we know if they're used - we shouldn't ever use them, as they reflect path assumptions}
{+START,IF,{$NOT,{$DEV_MODE}}}{+START,IF_PASSED,TARGET}<base href="{$BASE_URL*}/{$ZONE*}" target="{TARGET*}" />{+END}{+END}
{+START,IF,{$DEV_MODE}}<base href="http://example.com/"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END} />{+END}

{$,Hints to Google etc that may be set by Composr code}
{+START,IF_PASSED_AND_TRUE,NOFOLLOW}
	<meta name="robots" content="noindex, nofollow" />
{+END}

{$,iPhone/Android/etc should know they have an optimised design heading to them}
{+START,IF,{$MOBILE}}
	{+START,IF,{$NOT,{$_GET,overlay}}}
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	{+END}
	{+START,IF,{$_GET,overlay}}
		<meta name="viewport" content="width=280, initial-scale=1, user-scalable=yes" />
	{+END}
{+END}
{+START,IF,{$NOT,{$MOBILE}}}
	{+START,IF,{$CONFIG_OPTION,fixed_width}}
		<meta name="viewport" content="width=982, user-scalable=yes" />
	{+END}
	{+START,IF,{$NOT,{$CONFIG_OPTION,fixed_width}}}
		<meta name="viewport" content="width=device-width, user-scalable=yes" />
	{+END}
{+END}

{$,Metadata for the page: standard metadata, Dublin Core metadata, Facebook Open Graph, and Composr metadata extensions [CMSCORE]}
{+START,IF,{$NEQ,{$PAGE},404}}<link rel="canonical" href="{$CANONICAL_URL*}" />{+END}
<link rel="baseurl" href="{$BASE_URL*}" />
<link rel="sitemap" href="{$BASE_URL*}/data_custom/sitemaps/index.xml" />
<meta name="description" content="{+START,IF,{$NEQ,{$METADATA,meta_description},{!NA},???}}{$METADATA*,meta_description}{+END}" />
<meta name="keywords" content="{$METADATA*,keywords}" />
{+START,COMMENT,Commented out by default to save bandwidth}
	<meta name="GENERATOR" content="{$BRAND_NAME*}" />
	<meta name="publisher" content="{$COPYRIGHT`}" />
	<meta name="author" content="{$SITE_NAME*}" />
{+END}
{+START,COMMENT,Commented out by default to save bandwidth - schema.org and HTML5 semantics is probably the best default approach for most sites}
	<link rel="schema.CMSCORE" href="http://compo.sr/cmscore.rdf" />
	{+START,IF_NON_EMPTY,{$METADATA,rating}}<meta name="CMSCORE.Rating" content="{$METADATA*,rating}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,numcomments}}<meta name="CMSCORE.NumComments" content="{$METADATA*,numcomments}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,site_newestmember}}<meta name="CMSCORE.Site_NewestMember" content="{$METADATA*,site_newestmember}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,site_nummembers}}<meta name="CMSCORE.Site_NumMembers" content="{$METADATA*,site_nummembers}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,site_bestmember}}<meta name="CMSCORE.Site_BestMember" content="{$METADATA*,site_bestmember}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,forum_numtopics}}<meta name="CMSCORE.Forum_NumTopics" content="{$METADATA*,forum_numtopics}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,forum_numposts}}<meta name="CMSCORE.Forum_NumPosts" content="{$METADATA*,forum_numposts}" />{+END}
	<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" /><link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />
	<meta name="DC.Language" content="{$LANG*}" />{+START,IF_NON_EMPTY,{$METADATA,created}}<meta name="DCTERMS.Created" content="{$METADATA*,created}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,publisher}}<meta name="DC.Publisher" content="{$METADATA*,publisher}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,created}}<meta name="DC.Creator" content="{$METADATA*,creator}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,modified}}<meta name="DCTERMS.Modified" content="{$METADATA*,modified}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,type}}<meta name="DC.Type" content="{$METADATA*,type}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,title}}<meta name="DC.Title" content="{$METADATA*,title}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,identifier}}<meta name="DC.Identifier" content="{$FIND_SCRIPT*,page_link_redirect}?id={$METADATA&*,identifier}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,description}}<meta name="DC.Description" content="{$TRIM,{$METADATA*,description}}" />{+END}
{+END}
{+START,IF_NON_EMPTY,{$METADATA,title}}<meta property="og:title" content="{$METADATA*,title}" />{+END}
{+START,IF,{$EQ,{$METADATA,type},News article}}{$,Valid types only}<meta property="og:type" content="{$REPLACE*,News article,article,{$METADATA,type}}" />{+END}
<meta property="og:url" content="{$CANONICAL_URL*}" /><meta property="og:site_name" content="{$SITE_NAME*}" />
{+START,COMMENT,Commented out by default to save bandwidth}
	Only do this if you have a real uid, not a page id... {+START,IF_NON_EMPTY,{$CONFIG_OPTION*,facebook_uid,1}}<meta property="fb:admins" content="{$CONFIG_OPTION*,facebook_uid}" />{+END}
{+END}
{+START,IF_NON_EMPTY,{$CONFIG_OPTION*,facebook_appid}}<meta property="fb:app_id" content="{$CONFIG_OPTION*,facebook_appid,1}" />{+END}
{+START,IF_NON_EMPTY,{$METADATA,meta_description}}<meta property="og:description" content="{$TRIM,{$METADATA*,meta_description}}" />{+END}
{+START,IF_NON_EMPTY,{$METADATA,image}}<meta property="og:image" content="{$METADATA*,image}" /><meta property="og:image:width" content="{$IMG_WIDTH*,{$METADATA,image}}" /><meta property="og:image:height" content="{$IMG_HEIGHT*,{$METADATA,image}}" />{$,NB: You may also want to define a image_src link tag for some social sites}{+END}{+START,IF_EMPTY,{$METADATA,image}}<meta property="og:image" content="{$IMG*,logo/standalone_logo}" /><meta property="og:image:width" content="{$IMG_WIDTH*,logo/standalone_logo}" /><meta property="og:image:height" content="{$IMG_HEIGHT*,logo/standalone_logo}" />{+END}
{+START,IF_NON_EMPTY,{$METADATA,video}}<meta property="og:video" content="{$METADATA*,video}" /><meta property="og:video:width" content="{$METADATA*,video:width}" /><meta property="og:video:height" content="{$METADATA*,video:height}" /><meta property="og:video:type" content="{$METADATA*,video:type}" />{+END}
<meta property="og:locale" content="{$PREG_REPLACE,\..*$,,{$PREG_REPLACE,\,.*$,,{$REPLACE,-,_,{!locale}}}}" />
{+START,IF,{$EQ,{$METADATA,type},News article}}
	{+START,IF_NON_EMPTY,{$METADATA,created}}<meta name="article:published_time" content="{$METADATA*,created}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,modified}}<meta name="article:modified_time" content="{$METADATA*,modified}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,category}}<meta name="article:section" content="{$METADATA*,category}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,raw_keywords}}<meta name="article:tag" content="{$METADATA*,raw_keywords}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,creator}}<meta name="article:author" content="{$METADATA*,creator}" />{+END}
{+END}
{+START,IF,{$EQ,{$METADATA,type},Profile}}
	{+START,IF_NON_EMPTY,{$METADATA,creator}}<meta name="profile:username" content="{$METADATA*,creator}" />{+END}
{+END}

{$,Define the Microformats we support}
{+START,COMMENT,Commented out by default to save bandwidth}
	<link rel="profile" href="http://www.w3.org/2003/g/data-view" />
	<link rel="profile" href="http://dublincore.org/documents/dcq-html/" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="profile" href="http://www.w3.org/2006/03/hcard" />
	<link rel="profile" href="http://microformats.org/profile/hcalendar" />
	<link rel="profile" href="http://ns.inria.fr/grddl/ogp/" />
{+END}
{$,NB: We also support standard metadata, schema.org, semantic HTML5, ARIA, OpenSearch, and CMSCORE}

{$,Favicon and app icon for site, managed as theme images}
<link rel="icon" href="{$IMG*,favicon}" type="image/x-icon" sizes="48x48 32x32 24x24 16x16" />
<link rel="apple-touch-icon" href="{$IMG*,webclipicon}?v={$IMG_MTIME%,webclipicon}" type="image/x-icon" sizes="152x152 120x120" /> {$,Used on speed dials and phone home screens and not downloaded automatically}

{$,Inclusion of search semantic data, so smart browsers can automatically allow native-browser searching of the site}
{+START,COMMENT,Commented out by default to save bandwidth}{+START,IF,{$ADDON_INSTALLED,search}}
	{+START,IF,{$EQ,{$ZONE},docs}}
		<link rel="search" type="application/opensearchdescription+xml" title="{$SITE_NAME*} {$ZONE*}" href="{$FIND_SCRIPT*,opensearch}?filter=:id=comcode_pages:search_under=docs" />
	{+END}
	{+START,IF,{$NEQ,{$ZONE},docs}}
		<link rel="search" type="application/opensearchdescription+xml" title="{$SITE_NAME*} {$ZONE*}" href="{$FIND_SCRIPT*,opensearch}?filter=" />
	{+END}
	{+START,IF_NON_EMPTY,{$METADATA,opensearch_totalresults}}<meta name="totalResults" content="{$METADATA*,opensearch_totalresults}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,opensearch_startindex}}<meta name="startIndex" content="{$METADATA*,opensearch_startindex}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,opensearch_itemsperpage}}<meta name="itemsPerPage" content="{$METADATA*,opensearch_itemsperpage}" />{+END}
{+END}{+END}

{$,Google Analytics account, if one set up}
{+START,IF_NON_EMPTY,{$CONFIG_OPTION,google_analytics}}{+START,IF,{$NOR,{$IS_STAFF},{$IS_ADMIN}}}
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create','{$TRIM;/,{$CONFIG_OPTION,google_analytics}}',{+START,IF,{$CONFIG_OPTION,long_google_cookies}}'auto'{+END}{+START,IF,{$NOT,{$CONFIG_OPTION,long_google_cookies}}}{cookieExpires:0}{+END});
		ga('send','pageview');
	</script>
{+END}{+END}

{$,Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent}
{+START,IF,{$AND,{$CONFIG_OPTION,cookie_notice},{$RUNNING_SCRIPT,index},{$IS_EMPTY,{$REFRESH}}}}
	<script>
		window.cookieconsent_options={'message':'{!COOKIE_NOTICE;/,{$SITE_NAME}}','dismiss':'{!INPUTSYSTEM_OK;}','learnMore':'{!READ_MORE;}','link':'{$PAGE_LINK;/,:privacy}','theme':'dark-top'};
	</script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
{+END}

{$,Detecting of Timezones and JavaScript support}
<script>// <![CDATA[
	{+START,IF,{$CONFIG_OPTION,detect_javascript}}
		{+START,IF,{$AND,{$EQ,,{$_GET,keep_has_js}},{$NOT,{$JS_ON}}}}
			if ((window.location.href.indexOf('upgrader.php')==-1) && (window.location.href.indexOf('webdav.php')==-1) && (window.location.search.indexOf('keep_has_js')==-1)) {$,Redirect with JS on, and then hopefully we can remove keep_has_js after one click. This code only happens if JS is marked off, no infinite loops can happen.}
				window.location=window.location.href+((window.location.search=='')?(((window.location.href.indexOf('.htm')==-1)&&(window.location.href.indexOf('.php')==-1))?(((window.location.href.substr(window.location.href.length-1)!='/')?'/':'')+'index.php?'):'?'):'&')+'keep_has_js=1{+START,IF,{$DEV_MODE}}&keep_devtest=1{+END}';
		{+END}
	{+END}
	{+START,IF,{$NOT,{$BROWSER_MATCHES,ie}}}{+START,IF,{$HAS_PRIVILEGE,sees_javascript_error_alerts}}window.take_errors=true;{+END}{+END}
	var {+START,IF,{$CONFIG_OPTION,is_on_timezone_detection}}server_timestamp={$FROM_TIMESTAMP%},{+END}cms_lang='{$LANG;/}',cms_theme='{$THEME;/}',cms_username='{$USERNAME;/}'{+START,IF,{$IS_STAFF}},cms_is_staff=true{+END};
//]]></script>

{$,JavaScript code (usually) from Composr page}
{$EXTRA_HEAD}

{$,CSS includes from Composr page}
{$CSS_TEMPCODE}

{$,jQuery fan? Just uncomment the below and start using all the jQuery plugins you love in the normal way}
{$,{$REQUIRE_JAVASCRIPT,jquery}}

{$,JavaScript includes from Composr page}
{$JS_TEMPCODE,header}
{$,LEGACY: IE8}
<!--[if lt IE 9]>
<script src="{$BASE_URL*}/data/html5.js"></script>
<![endif]-->

{$,If the page is doing a refresh include the markup for that}
{$REFRESH}

{$,Feeds}
{$FEEDS}
