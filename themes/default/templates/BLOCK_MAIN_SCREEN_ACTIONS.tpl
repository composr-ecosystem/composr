<aside class="screen_actions_outer box"><nav class="screen_actions box_inner">
	<div class="print"><a class="link_exempt" rel="print nofollow" target="_blank" title="{!PRINT_THIS_SCREEN} {!LINK_NEW_WINDOW}" href="{PRINT_URL*}"><span>{!PRINT_THIS_SCREEN}</span></a></div>
	<div class="recommend"><a onclick="return open_link_as_overlay(this);" class="link_exempt" rel="nofollow" target="_blank" title="{!RECOMMEND_LINK} {!LINK_NEW_WINDOW}" href="{RECOMMEND_URL*}"><span>{!RECOMMEND_LINK}</span></a></div>
	{+START,IF,{$BROWSER_MATCHES,ie}}
		<div class="favorites"><a class="link_exempt" title="{!ADD_TO_FAVORITES}" href="#" onclick="window.external.AddFavorite(window.location.href,document.title); return false;"><span>{!ADD_TO_FAVORITES}</span></a></div>
	{+END}
	<div class="facebook"><a class="link_exempt" target="_blank" title="{!ADD_TO_FACEBOOK} {!LINK_NEW_WINDOW}" href="https://www.facebook.com/sharer.php?u={EASY_SELF_URL*}"><span>{!ADD_TO_FACEBOOK}</span></a></div>
	<div class="twitter"><a class="link_exempt" target="_blank" title="{!ADD_TO_TWITTER} {!LINK_NEW_WINDOW}" onclick="this.setAttribute('href','http://twitter.com/share?count=horizontal&amp;counturl={EASY_SELF_URL;*}&amp;original_referer={EASY_SELF_URL;*}&amp;text='+window.encodeURIComponent(document.title)+'&amp;url={EASY_SELF_URL;*}');" href="http://twitter.com/home?status=RT%20{EASY_SELF_URL*}"><span>{!ADD_TO_TWITTER}</span></a></div>
	<div class="digg"><a class="link_exempt" target="_blank" title="{!ADD_TO_DIGG} {!LINK_NEW_WINDOW}" href="http://digg.com/submit?phase=2&amp;url={EASY_SELF_URL*}"><span>{!ADD_TO_DIGG}</span></a></div>
	{+START,IF,{$ADDON_INSTALLED,bookmarks}}
		{$,<div class="favorites"><a class="link_exempt" href="{$FIND_SCRIPT*,bookmarks}?no_redirect=1&amp;type=add&amp;url={$SELF_URL&*}&amp;title={TITLE&*}" onclick="window.faux_open(maintain_theme_in_link(this.getAttribute('href')),'','width=600,height=500,status=yes,resizable=yes,scrollbars=yes'); return false;"><span>{!BOOKMARK_THIS}</span></a></div>}
	{+END}
</nav></aside>
