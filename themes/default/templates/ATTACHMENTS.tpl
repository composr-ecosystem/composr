{$SET,IMAGE_TYPES,{IMAGE_TYPES}}

{+START,IF,{$BROWSER_MATCHES,simplified_attachments_ui}}
	<div id="attachment_store__{POSTING_FIELD_NAME%}" class="accessibility_hidden">
		{$,plupload will attach upload code to here}
	</div>

	<div id="attachment_progress_bars">
		<div id="fsUploadProgress" class="progressBars">
		</div>
	</div>

	<script>// <![CDATA[
		var attachment_template='{ATTACHMENT_TEMPLATE;^/}';
		var max_attachments={MAX_ATTACHMENTS%};
		var num_attachments=1;

		function rebuild_attachment_button_for_next__{POSTING_FIELD_NAME%}(attachment_upload_button)
		{
			if (typeof attachment_upload_button=='undefined') {
				if (document.getElementById('cke_{POSTING_FIELD_NAME%}')) {
					attachment_upload_button=document.getElementById('cke_{POSTING_FIELD_NAME%}').getElementsByClassName('cke_button__composr_image')[0].id;
				} else {
					attachment_upload_button='attachment_upload_button__{POSTING_FIELD_NAME%}';
				}
			}

			prepare_simplified_file_input('attachment_multi','file'+window.num_attachments,null,'{POSTING_FIELD_NAME;/}',{+START,IF_PASSED,FILTER}'{FILTER;/}'{+END}{+START,IF_NON_PASSED,FILTER}null{+END},attachment_upload_button);
		}

		add_event_listener_abstract(window,'real_load',function() {
			aub=document.getElementById('attachment_upload_button__{POSTING_FIELD_NAME%}');
			if ((aub) && (aub.className.indexOf('for_field_{POSTING_FIELD_NAME%} ')!=-1))
			{
				add_event_listener_abstract(window,'load',function () {
					window.rebuild_attachment_button_for_next__{POSTING_FIELD_NAME%}('attachment_upload_button__{POSTING_FIELD_NAME%}');
				} );
			}
		});
	//]]></script>
{+END}

{+START,IF,{$NOT,{$BROWSER_MATCHES,simplified_attachments_ui}}}
	{+START,IF,{$ADDON_INSTALLED,filedump}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,filedump}}{+START,IF,{$EQ,{$ZONE},cms}}
		<p>
			{!ADD_ATTACHMENTS_MEDIA_LIBRARY,{POSTING_FIELD_NAME;*}}
		</p>
	{+END}{+END}{+END}

	<script>// <![CDATA[
		var attachment_template='{ATTACHMENT_TEMPLATE;^/}';
	//]]></script>

	<div id="attachment_store__{POSTING_FIELD_NAME%}">
		{ATTACHMENTS}
	</div>

	{+START,IF,{$JS_ON}}
		<script>// <![CDATA[
			var max_attachments={MAX_ATTACHMENTS%};
			var num_attachments={NUM_ATTACHMENTS%};
		//]]></script>
	{+END}

	{+START,IF_NON_EMPTY,{$_GET,id}}
		<p>
			{!comcode:DELETE_ATTACHMENTS,<a rel="nofollow" title="{!comcode:ATTACHMENT_POPUP} {!LINK_NEW_WINDOW}" target="_blank" href="{$FIND_SCRIPT*,attachment_popup}?field_name={POSTING_FIELD_NAME*}{$KEEP*,0,1}" onclick="window.faux_open(maintain_theme_in_link(this.href)\,'site_attachment_chooser'\,'width=550\,height=600\,status=no\,resizable=yes\,scrollbars=yes'); return false;">{!comcode:ATTACHMENT_POPUP}</a>}
		</p>
	{+END}
{+END}
