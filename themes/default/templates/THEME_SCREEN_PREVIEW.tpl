{$REQUIRE_JAVASCRIPT,core_themeing}

<div data-tpl="themeScreenPreview">
	{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}

	{+START,IF_NON_EMPTY,{SCREEN_PREVIEW_URL}}
		<a title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" class="js-link-click-open-template-preview-window" href="{SCREEN_PREVIEW_URL*}" target="_blank"><span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{SCREEN*}</span></a>
	{+END}
	{+START,IF_EMPTY,{SCREEN_PREVIEW_URL}}
		<span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{SCREEN*}</span>
	{+END}

	<ul class="horizontal-links horiz-field-sep">
		{+START,IF_NON_EMPTY,{MOBILE_SCREEN_PREVIEW_URL}}
			<li>
				<a class="js-link-click-open-mobile-template-preview-window" title="{SCREEN*} {!LINK_NEW_WINDOW}" data-click-pd="1" href="{MOBILE_SCREEN_PREVIEW_URL*}" target="_blank"><span {+START,IF_NON_EMPTY,{COLOR}} style="color: {COLOR*}"{+END}>{!MOBILE_VERSION}</span></a>
			</li>
		{+END}

		{+START,IF_NON_EMPTY,{CMS_VALIDATION_URL}}
			<li>
				<a target="_blank" title="Validate {!LINK_NEW_WINDOW}" href="{CMS_VALIDATION_URL*}">Validate</a>
			</li>
		{+END}

		{+START,IF_NON_EMPTY,{W3C_VALIDATION_URL}}
			<li>
				<a target="_blank" title="Validate @ W3C {!LINK_NEW_WINDOW}" href="{W3C_VALIDATION_URL*}">Validate @ W3C</a>
			</li>
		{+END}
	</ul>

	{+START,IF_NON_EMPTY,{LIST}}
		<div class="mini-indent">
			<ul class="meta-details-list">{LIST*}</ul>
		</div>
	{+END}
</div>
