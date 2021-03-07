{$REQUIRE_JAVASCRIPT,core_themeing}

<li data-tpl="themeScreenPreview">
	{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}

	{+START,IF_NON_EMPTY,{URL}}<a title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" class="js-link-click-open-template-preview-window" href="{URL*}&amp;keep_wide_high=1" target="_blank">{+END}<span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{SCREEN*}</span>{+START,IF_NON_EMPTY,{URL}}</a>{+END}

	<ul class="horizontal-links horiz-field-sep">
		<li>
			{+START,IF_NON_EMPTY,{URL}}<a class="js-link-click-open-mobile-template-preview-window" title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" href="{URL*}&amp;keep_wide_high=1&amp;keep_mobile=1" target="_blank"><span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{!MOBILE_VERSION}</span></a>{+END}
		</li>

		{+START,IF,{$NOT,{PLAIN_TEXT}}}
			<li>
				<a target="_blank" title="Validate {!LINK_NEW_WINDOW}" href="{URL*}&amp;validate=cms">Validate</a>
			</li>

			<li>
				<a target="_blank" title="Validate @ W3C {!LINK_NEW_WINDOW}" href="{URL*}&amp;validate=w3c">Validate @ W3C</a>
			</li>
		{+END}
	</ul>

	{+START,IF_NON_EMPTY,{LIST}}
		<div class="mini-indent">
			<ul class="meta-details-list">{LIST*}</ul>
		</div>
	{+END}
</li>
