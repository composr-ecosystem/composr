{$REQUIRE_JAVASCRIPT,core_rich_media}

<div class="comcode-supported block-desktop" data-tpl="comcodeMessage" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}">
	<input type="hidden" name="comcode__{NAME*}" value="1" />

	<ul class="horizontal-links horiz-field-sep">
		{+START,IF,{$SHOW_DOCS}}{+START,IF_PASSED,URL}
			<li>
				<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{URL*}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
			</li>
		{+END}{+END}
		<li>
			<a rel="nofollow" class="link-exempt js-link-click-open-emoticon-chooser-window" data-click-pd="1" title="{!EMOTICONS_POPUP} {!LINK_NEW_WINDOW}" target="_blank" href="{$FIND_SCRIPT*,emoticons}?field_name={NAME*}{$KEEP*,0,1}">{+START,INCLUDE,ICON}NAME=editor/insert_emoticons{+END}</a>
		</li>

		{+START,IF,{W}}
			<li>
				<a rel="nofollow" id="toggle-wysiwyg-{NAME*}" href="#!" class="js-click-toggle-wysiwyg" title="{!comcode:ENABLE_WYSIWYG}"><abbr title="{!comcode:TOGGLE_WYSIWYG_2}">{+START,INCLUDE,ICON}NAME=editor/wysiwyg_on{+END}</abbr></a>
			</li>
		{+END}
	</ul>
</div>
