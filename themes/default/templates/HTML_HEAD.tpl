{$, - Note you will need to empty the template cache manually if you change this file manually due to the way it is included - }

{$,The character set of the page}
<meta http-equiv="Content-Type" content="text/html; charset={$CHARSET*}" />

{$,Page title}
<title>{+START,IF_NON_PASSED,TITLE}{+START,IF_NON_EMPTY,{$HEADER_TEXT}}{$HEADER_TEXT*} &ndash; {+END}{$SITE_NAME*}{+END}{+START,IF_PASSED,TITLE}{TITLE}{+END}</title>

{$,In developer mode we totally break relative URLs so we know if they're used - we shouldn't ever use them, as they reflect path assumptions}
{+START,IF,{$NOT,{$DEV_MODE}}}{+START,IF_PASSED,TARGET}<base href="{$BASE_URL*}/{$ZONE*}" target="{TARGET*}" />{+END}{+END}

{$,Hints to Google etc that may be set by Composr code}
{+START,IF_PASSED_AND_TRUE,NOINDEX}
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
{+START,IF,{$DESKTOP}}
	{+START,IF,{$THEME_OPTION,fixed_width}}
		<meta name="viewport" content="width=982, user-scalable=yes" />
	{+END}
	{+START,IF,{$NOT,{$THEME_OPTION,fixed_width}}}
		<meta name="viewport" content="width=device-width, user-scalable=yes" />
	{+END}
{+END}

{$,Metadata for the page: standard metadata, Dublin Core metadata, Facebook Open Graph, and Composr metadata extensions [CMSCORE]}
{+START,IF,{$NEQ,{$PAGE},404}}<link rel="canonical" href="{$CANONICAL_URL*}" />{+END}
{$TRANSLATION_LINKS}
<link rel="baseurl" href="{$BASE_URL*}" />
<link rel="sitemap" href="{$BASE_URL*}/data_custom/sitemaps/index.xml" />
<meta id="cms-symbol-data" name="cms-symbol-data" content="{$SYMBOL_DATA_AS_JSON*}" />
{+START,COMMENT,Commented out by default to save bandwidth}
	<meta name="GENERATOR" content="{$BRAND_NAME*}" />
	<meta name="publisher" content="{$COPYRIGHT`}" />
	<meta name="author" content="{$SITE_NAME*}" />
{+END}
{+START,COMMENT,Commented out by default to save bandwidth - schema.org and HTML5 semantics is probably the best default approach for most sites}
	<link rel="schema.CMSCORE" href="https://composr.app/cmscore.rdf" />
	{+START,IF_NON_EMPTY,{$METADATA,rating}}<meta name="CMSCORE.Rating" content="{$METADATA*,rating}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,numcomments}}<meta name="CMSCORE.NumComments" content="{$METADATA*,numcomments}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,site_newestmember}}<meta name="CMSCORE.Site_NewestMember" content="{$METADATA*,site_newestmember}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,site_nummembers}}<meta name="CMSCORE.Site_NumMembers" content="{$METADATA*,site_nummembers}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,forum_numtopics}}<meta name="CMSCORE.Forum_NumTopics" content="{$METADATA*,forum_numtopics}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,forum_numposts}}<meta name="CMSCORE.Forum_NumPosts" content="{$METADATA*,forum_numposts}" />{+END}
	<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" /><link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />
	<meta name="DC.Language" content="{$METADATA*,lang}" />{+START,IF_NON_EMPTY,{$METADATA,created}}<meta name="DCTERMS.Created" content="{$METADATA*,created}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,publisher}}<meta name="DC.Publisher" content="{$METADATA*,publisher}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,created}}<meta name="DC.Creator" content="{$METADATA*,creator}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,modified}}<meta name="DCTERMS.Modified" content="{$METADATA*,modified}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,type}}<meta name="DC.Type" content="{$METADATA*,type}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,title}}<meta name="DC.Title" content="{$METADATA*,title}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,identifier}}<meta name="DC.Identifier" content="{$FIND_SCRIPT*,page_link_redirect}?id={$METADATA&*,identifier}" />{+END}
	{+START,IF_NON_EMPTY,{$METADATA,description}}<meta name="DC.Description" content="{$TRIM,{$METADATA*,description}}" />{+END}
{+END}
{+START,IF_NON_EMPTY,{$METADATA,title}}<meta property="og:title" content="{$METADATA*,title}" />{+END}
<meta property="og:type" content="{$?,{$EQ,{$METADATA,type},News article},article,website}" />
<meta property="og:url" content="{$CANONICAL_URL*}" />
<meta property="og:site_name" content="{$SITE_NAME*}" />
{+START,COMMENT,Commented out by default to save bandwidth}
	Only do this if you have a real uid, not a page id... {+START,IF_NON_EMPTY,{$CONFIG_OPTION*,facebook_uid,1}}<meta property="fb:admins" content="{$CONFIG_OPTION*,facebook_uid,1}" />{+END}
{+END}
{+START,IF_NON_EMPTY,{$CONFIG_OPTION*,facebook_appid}}<meta property="fb:app_id" content="{$CONFIG_OPTION*,facebook_appid,1}" />{+END}
{+START,IF_NON_EMPTY,{$METADATA,meta_description}}<meta property="og:description" name="description" content="{+START,IF,{$NEQ,{$METADATA,meta_description},{!NA},???}}{$TRIM,{$METADATA*,meta_description}}{+END}" />{+END}
<meta name="description" content="{+START,IF,{$NEQ,{$METADATA,meta_description},{!NA},???}}{$METADATA*,meta_description}{+END}" />
<meta name="keywords" content="{$METADATA*,keywords}" />
{+START,IF_NON_EMPTY,{$METADATA,image}}
	<meta property="og:image" content="{$METADATA*,image}" />
	<meta property="og:image:width" content="{$IMG_WIDTH*,{$METADATA,image}}" />
	<meta property="og:image:height" content="{$IMG_HEIGHT*,{$METADATA,image}}" />
	{$,NB: You may also want to define a image_src link tag for some social sites}
{+END}
{+START,IF_EMPTY,{$METADATA,image}}
	{$SET,fallback_image,{$IMG,logo/standalone_logo}}
	<meta property="og:image" content="{$GET*,fallback_image}" />
	<meta property="og:image:width" content="{$IMG_WIDTH*,{$GET,fallback_image}}" />
	<meta property="og:image:height" content="{$IMG_HEIGHT*,{$GET,fallback_image}}" />
{+END}
{+START,IF,{$EQ,{$METADATA,type},Image}}
	<meta property="twitter:card" content="summary_large_image" />
{+END}
{+START,IF,{$NEQ,{$METADATA,type},Image}}
	<meta property="twitter:card" content="summary" />
{+END}
{+START,IF_NON_EMPTY,{$METADATA,video}}
	<meta property="og:video" content="{$METADATA*,video}" />
	<meta property="og:video:width" content="{$METADATA*,video:width}" />
	<meta property="og:video:height" content="{$METADATA*,video:height}" />
	<meta property="og:video:type" content="{$METADATA*,video:type}" />
{+END}
{$SET,og_locale,{$PREG_REPLACE,\..*$,,{$PREG_REPLACE,\,.*$,,{$REPLACE,-,_,{!locale}}}}}{+START,IF,{$NEQ,{$GET,og_locale},en_US}}<meta property="og:locale" content="{$GET,og_locale}" />{+END}
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
	<link rel="profile" href="https://www.dublincore.org/specifications/dublin-core/dcq-html/" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="profile" href="http://www.w3.org/2006/03/hcard" />
	<link rel="profile" href="http://microformats.org/profile/hcalendar" />
	<link rel="profile" href="http://ns.inria.fr/grddl/ogp/" />
{+END}
{$,NB: We also support standard metadata, schema.org, semantic HTML5, ARIA, OpenSearch, and CMSCORE}

{$,Favicon and app icon for site, managed as theme images}
<link rel="icon" href="{$FAVICON*}" type="image/x-icon" sizes="48x48 32x32 24x24 16x16" />
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

{$,CSS includes from Composr page}
{$CSS_TEMPCODE}

{$,Load polyfills}
{+START,INCLUDE,HTML_HEAD_POLYFILLS}FROM={$BASE_URL}/data/polyfills{+END}

{$,JavaScript code (usually) from Composr page}
{$EXTRA_HEAD}

{$,Google fonts}
{+START,IF_NON_EMPTY,{$CONFIG_OPTION,google_fonts}}{+START,IF,{$NOT,{$CONFIG_OPTION,google_fonts_delayed_load}}}
	<link href="//fonts.googleapis.com/css?family={+START,LOOP,={$CONFIG_OPTION,google_fonts}}{+START,IF_NON_EMPTY,{_loop_key}}|{+END}{_loop_var&*}{+END}:300,300i,400,400i,500,500i&amp;display=swap" rel="stylesheet" referrerpolicy="no-referrer" {$CSP_NONCE_HTML} />
{+END}{+END}

{$,If the page is doing a refresh include the markup for that}
{$REFRESH}

{$,Feeds}
{$FEEDS}

{$,Tab colour, particularly Android-Chrome}
<meta name="theme-color" content="{$THEME_OPTION*,themewizard_built_with_seed}" />
