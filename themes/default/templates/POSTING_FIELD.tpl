{$, Template uses auto-complete}
{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,jquery_autocomplete}
{$REQUIRE_CSS,autocomplete}

{$SET,id,{$RAND}}
{$SET,init_drag_drop,0}

<tr class="form-table-field-spacer" id="field-{$GET*,id}-label">
	{+START,SET,posting_field}
		{+START,IF_PASSED,POST_COMMENT}
			{+START,IF_NON_EMPTY,{POST_COMMENT}}
				<p class="h2"><label for="{NAME*}">{POST_COMMENT*}</label></p>

				<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{POST_COMMENT*}}" />
			{+END}
		{+END}
		{+START,IF_NON_PASSED,POST_COMMENT}
			<span class="field-name">
				<label class="accessibility-hidden" for="{NAME*}">{!TEXT}</label>
			</span>

			<span id="required-readable-marker--{$?,{$IS_EMPTY,{NAME*}},{$RAND},{NAME*}}" style="display: {$?,{REQUIRED*},inline,none}"><span class="required-star">*</span> <span class="accessibility-hidden">{!REQUIRED}</span></span>
		{+END}

		{+START,INCLUDE,FORM_SCREEN_FIELD_DESCRIPTION}{+END}

		<input type="hidden" name="comcode__{NAME*}" value="1" />
		{HIDDEN_FIELDS}

		{+START,IF,{$OR,{$IN_STR,{CLASS},wysiwyg},{$AND,{$MATCH_KEY_MATCH,_WILD:cms_comcode_pages},{$SHOW_DOCS}}}}
			<div class="comcode-supported posting-form-main-comcode-button">
				<ul class="horizontal-links horiz-field-sep associated-links-block-group">
					{+START,IF,{$SHOW_DOCS}}{+START,IF_PASSED,COMCODE_URL}
						{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:cms_comcode_pages}}}
							<li>
								<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{COMCODE_URL*}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
							</li>
						{+END}
						{+START,IF,{$MATCH_KEY_MATCH,_WILD:cms_comcode_pages}}
							<li><a class="link-exempt" title="{!FULL_COMCODE_TUTORIAL} {!LINK_NEW_WINDOW}" target="_blank" href="{$TUTORIAL_URL*,tut_comcode}">{!FULL_COMCODE_TUTORIAL}</a></li>
							<li><a class="link-exempt" title="{!FULL_BLOCK_TUTORIAL} {!LINK_NEW_WINDOW}" target="_blank" href="{$TUTORIAL_URL*,tut_adv_comcode_pages}">{!FULL_BLOCK_TUTORIAL}</a></li>
						{+END}
						<li>
							<a rel="nofollow" class="link-exempt js-link-click-open-field-emoticon-chooser-window" title="{!EMOTICONS_POPUP} {!LINK_NEW_WINDOW}" target="_blank" href="{$FIND_SCRIPT*,emoticons}?field_name={NAME*}{$KEEP*,0,1}" data-click-pd="1">{+START,INCLUDE,ICON}NAME=editor/insert_emoticons{+END}</a>
						</li>
					{+END}{+END}
					{+START,IF,{$IN_STR,{CLASS},wysiwyg}}
						<li>
							<a id="toggle-wysiwyg-{NAME*}" href="#!" class="js-click-toggle-wysiwyg" title="{!comcode:ENABLE_WYSIWYG}"><abbr title="{!TOGGLE_WYSIWYG_2}">{+START,INCLUDE,ICON}NAME=editor/wysiwyg_on{+END}</abbr></a>
						</li>
					{+END}
				</ul>
			</div>
		{+END}
	{+END}
	{+START,IF_NON_EMPTY,{$TRIM,{$GET,posting_field}}}
		<th colspan="2" class="table-heading-cell{+START,IF,{REQUIRED}} required{+END}">
			{$GET,posting_field}
		</th>
	{+END}
</tr>
<tr class="field-input" id="field-{$GET*,id}-input">
	<td class="{+START,IF,{REQUIRED}} required{+END} form-table-huge-field" colspan="2">
		{+START,IF_PASSED,DEFAULT_PARSED}
			<textarea cols="1" rows="1" style="display: none" readonly="readonly" disabled="disabled" name="{NAME*}_parsed">{DEFAULT_PARSED*}</textarea>
		{+END}

		<div class="clearfix">
			<div role="toolbar" class="clearfix post-options-wrap">
				<div id="post-special-options2--{NAME*}" style="display: none">
					{COMCODE_EDITOR_SMALL}
				</div>
				<div id="post-special-options--{NAME*}">
					{COMCODE_EDITOR}
				</div>
			</div>

			<div id="container-for-{NAME*}" class="container-for-wysiwyg">
				<textarea data-textarea-auto-height="" accesskey="x" class="{CLASS*}{+START,IF,{REQUIRED}} posting-required{+END} form-control form-control-wide posting-field-textarea" tabindex="{TABINDEX_PF*}" id="{NAME*}" name="{NAME*}" cols="70" rows="17">{POST*}</textarea>

				{+START,IF_PASSED,COMCODE_PAGE_HINTS}{+START,IF_NON_EMPTY,{COMCODE_PAGE_HINTS}}
					<p class="vertical-alignment left">
						{+START,LOOP,COMCODE_PAGE_HINTS}
							<span {+START,IF,{$NEQ,{_loop_key},0}} class="mini-indent"{+END}>
								<label for="comcode_page_hint__{NAME*}__{HINT_CODENAME*}">{HINT_LABEL*}</label>
								<input title="{HINT_DESCRIPTION*}" type="checkbox" name="comcode_page_hint__{NAME*}__{HINT_CODENAME*}" id="comcode_page_hint__{NAME*}__{HINT_CODENAME*}" value="1"{+START,IF,{HINT_SELECTED}} checked="checked"{+END} />
							</span>
						{+END}
					</p>
				{+END}{+END}

				{+START,IF_PASSED,WORD_COUNTER}
					{$SET,word_count_id,{$RAND}}
					<div class="word-count" id="word-count-{$GET*,word_count_id}"></div>
				{+END}
			</div>
		</div>

		{+START,IF_NON_EMPTY,{$TRIM,{EMOTICON_CHOOSER}}}
			{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:cms_news}}}
				{+START,IF,{$DESKTOP}}{+START,IF,{$OR,{$CONFIG_OPTION,is_on_emoticon_choosers},{$CNS}}}
					<div {+START,IF,{$CONFIG_OPTION,is_on_emoticon_choosers}} class="emoticon-chooser box block-desktop"{+END}>
						{+START,IF,{$CNS}}
							<span class="right horiz-field-sep associated-link"><a rel="nofollow" target="_blank" class="js-link-click-open-site-emoticon-chooser-window" href="{$FIND_SCRIPT*,emoticons}?field_name={NAME*}{$KEEP*,0,1}" data-click-pd="1" title="{!EMOTICONS_POPUP} {!LINK_NEW_WINDOW}">{$?,{$CONFIG_OPTION,is_on_emoticon_choosers},{!VIEW_ARCHIVE},{!EMOTICONS_POPUP}}</a></span>
						{+END}

						{+START,IF,{$CONFIG_OPTION,is_on_emoticon_choosers}}
							{EMOTICON_CHOOSER}
						{+END}
					</div>
				{+END}{+END}
			{+END}
		{+END}

		{+START,IF,{$NOT,{$MATCH_KEY_MATCH,cms}}}
			{+START,IF_PASSED,POST_COMMENT}
				<p class="posting-rules">{!USE_WEBSITE_RULES,{$PAGE_LINK*,:rules},{$PAGE_LINK*,:privacy}}</p>
			{+END}
		{+END}

		{+START,IF,{$MATCH_KEY_MATCH,cms}}
			{+START,IF,{$VALUE_OPTION,download_associated_media}}
				<p class="vertical-alignment">
					<label for="{NAME*}_download_associated_media">{!comcode:DOWNLOAD_ASSOCIATED_MEDIA}</label>
					<input title="{!comcode:DESCRIPTION_DOWNLOAD_ASSOCIATED_MEDIA}" type="checkbox" name="{NAME*}_download_associated_media" id="{NAME*}_download_associated_media" value="1" checked="checked" />
				</p>
			{+END}
		{+END}

		{+START,IF,{$AND,{$BROWSER_MATCHES,simplified_attachments_ui},{$IS_NON_EMPTY,{ATTACHMENTS}}}}
			{$SET,init_drag_drop,1}
			{ATTACHMENTS}
			<input type="hidden" name="posting_ref_id" value="{$RAND%}" />
		{+END}

		<div class="tpl-placeholder" hidden="hidden" data-tpl="postingField" data-tpl-params="{+START,PARAMS_JSON,id,NAME,CLASS,WORD_COUNTER,word_count_id,init_drag_drop}{_*}{+END}"></div>
	</td>
</tr>

{+START,IF,{$AND,{$NOT,{$BROWSER_MATCHES,simplified_attachments_ui}},{$IS_NON_EMPTY,{ATTACHMENTS}}}}
	{$SET,init_drag_drop,1}
	<tr class="form-table-field-spacer" id="field-{$GET*,id}-attachments-ui">
		<th colspan="2" class="table-heading-cell">
			<a class="toggleable-tray-button js-click-pf-toggle-subord-fields" id="fes-attachments--{NAME*}" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>

			<span class="h2 toggleable-tray-button js-click-pf-toggle-subord-fields">
				{!ATTACHMENTS}

				{+START,IF,{$DESKTOP}}
					<a class="help-icon inline-desktop" data-cms-rich-tooltip="{}" title="{$STRIP_TAGS,{!ATTACHMENT_HELP}}" href="#!">{+START,INCLUDE,ICON}
						NAME=help
						ICON_SIZE=24
					{+END}</a>
				{+END}
			</span>

			{+START,IF_PASSED,HELP}
				<p style="display: none" id="fes-attachments-help--{NAME*}">
					{HELP*}
				</p>
			{+END}
		</th>
	</tr>
	<tr style="display: none" class="field-input" id="field-{$GET*,id}-attachments-ui-input">
		<td class="form-table-huge-field" colspan="2">
			{ATTACHMENTS}
			<input type="hidden" name="posting_ref_id" value="{$RAND%}" />
		</td>
	</tr>
{+END}
