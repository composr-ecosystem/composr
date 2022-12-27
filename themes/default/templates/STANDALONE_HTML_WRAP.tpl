{$,This template is used for things like iframes used for previewing or for creating independent navigation areas in the site, or popups / overlays}
<!DOCTYPE html>

{$SET,page_link_privacy,{$PAGE_LINK,:privacy}}

{$SET,is_preview,{$RUNNING_SCRIPT,preview}}
<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr} data-view="Global" data-view-params="{+START,PARAMS_JSON,page_link_privacy}{_*}{+END}" data-tpl="standaloneHtmlWrap" data-tpl-params="{+START,PARAMS_JSON,is_preview}{_*}{+END}">
	<head>
		{+START,INCLUDE,HTML_HEAD}{+END}
	</head>

	<body class="{+START,IF_PASSED_AND_TRUE,FRAME}frame {+END}{+START,IF_PASSED_AND_TRUE,POPUP}popup-spacer {+END}{+START,IF_PASSED_AND_TRUE,OPENS_BELOW}opens-below {+END}website-body global-middle-faux{+START,IF_PASSED,CSS} {CSS*}{+END}" id="standalone-html-wrap" itemscope="itemscope" itemtype="http://schema.org/WebPage">
		{+START,IF_NON_PASSED_OR_FALSE,POPUP}
			{+START,IF_NON_EMPTY,{$MESSAGES_TOP}}
				<div class="global-messages">
					{$MESSAGES_TOP}
				</div>
			{+END}
		{+END}

		{CONTENT}

		{$JS_TEMPCODE}

		{+START,IF_NON_PASSED,POPUP}
			{$EXTRA_FOOT}
		{+END}

		{$,extra_footer_standalone_goes_here}
	</body>
</html>
