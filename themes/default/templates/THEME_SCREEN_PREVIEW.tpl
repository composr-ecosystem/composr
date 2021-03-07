{$REQUIRE_JAVASCRIPT,core_themeing}

<li data-tpl="themeScreenPreview">
	{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}

	{+START,IF_NON_EMPTY,{URL}}<a title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" class="js-link-click-open-template-preview-window" href="{URL*}&amp;keep_wide_high=1" target="_blank">{+END}<span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{SCREEN*}</span>{+START,IF_NON_EMPTY,{URL}}</a>{+END}

	{+START,IF_NON_EMPTY,{URL}}<a class="associated-link horiz-field-sep js-link-click-open-mobile-template-preview-window" title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" href="{URL*}&amp;keep_wide_high=1&amp;keep_mobile=1" target="_blank"><span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{!MOBILE_VERSION}</span></a>{+END}

	{+START,IF_NON_EMPTY,{LIST}}
		<div class="mini-indent">
			<ul class="meta-details-list">{LIST*}</ul>
		</div>
	{+END}
</li>
