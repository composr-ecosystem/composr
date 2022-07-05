{TITLE}

<div>
	<div class="float_surrounder"><div class="tabs" role="tablist">
		<a aria-controls="g_thumbnails" role="tab" href="#" id="t_thumbnails" class="tab tab_active tab_first" onclick="event.returnValue=false; select_tab('g','thumbnails'); return false;"><span>{!VIEW_THUMBNAILS}</span></a>

		<a aria-controls="g_listing" role="tab" href="#" id="t_listing" class="tab{+START,IF_EMPTY,{CREATE_FOLDER_FORM}{UPLOAD_FORM}} tab_last{+END}" onclick="event.returnValue=false; select_tab('g','listing'); return false;"><span>{!VIEW_LISTING}</span></a>

		{+START,IF_NON_EMPTY,{CREATE_FOLDER_FORM}}
			<a aria-controls="g_create_folder" role="tab" href="#" id="t_create_folder" class="tab{+START,IF_EMPTY,{UPLOAD_FORM}} tab_last{+END}" onclick="event.returnValue=false; select_tab('g','create_folder'); return false;"><span>{!FILEDUMP_CREATE_FOLDER}</span></a>
		{+END}

		{+START,IF_NON_EMPTY,{UPLOAD_FORM}}
			<a aria-controls="g_upload" role="tab" href="#" id="t_upload" class="tab tab_last" onclick="event.returnValue=false; select_tab('g','upload'); return false;"><span>{!UPLOAD}</span></a>
		{+END}
	</div></div>
	<div class="tab_surround">
		<div aria-labeledby="t_thumbnails" role="tabpanel" id="g_thumbnails" style="display: block">
			<a id="tab__thumbnails"></a>

			{+START,INCLUDE,FILEDUMP_SEARCH}
				I=1
				TAB=thumbnails
			{+END}

			{+START,IF_NON_EMPTY,{$_GET,filename}}
				<script>// <![CDATA[
					add_event_listener_abstract(window,'load',function() {
						faux_open('{$PAGE_LINK;,_SELF:_SELF:embed:place={$_GET&,place}:file={$_GET&,filename}:wide_high=1}',null,'width=950;height=700','_top');
					});
				//]]></script>
			{+END}

			<form title="{!ACTION}" action="{POST_URL*}#tab__thumbnails" method="post" onsubmit="return check_filedump_selections(this);" autocomplete="off">
				{$INSERT_SPAMMER_BLACKHOLE}

				{+START,IF_NON_EMPTY,{THUMBNAILS}}
					<div class="float_surrounder filedump_thumbnails">
						{+START,LOOP,THUMBNAILS}
							<div class="box"><div class="box_inner">
								{+START,IF,{CHOOSABLE}}
									<span class="filedump_select">{ACTIONS}</span>
								{+END}

								{+START,IF_PASSED,EMBED_URL}
									<p class="filedump_embed"><a id="embed_link_{FILENAME|*}" href="{EMBED_URL*}" onclick="return open_link_as_overlay(this,950,680);" class="link_exempt">{!_FILEDUMP_EMBED}</a></p>
								{+END}

								<p><a{+START,IF,{IS_IMAGE}} rel="lightbox"{+END} href="{URL*}">{THUMBNAIL}</a></p>

								<p class="meta associated_details">
									<strong>{FILENAME*}</strong><br />
									<span class="associated_details">({+START,IF_NON_EMPTY,{_SIZE}}{SIZE*}{+END}{+START,IF_NON_EMPTY,{TIME}}{+START,IF_NON_EMPTY,{_SIZE}}, {+END}{TIME*}{+END}{+START,IF_NON_EMPTY,{WIDTH}}, {WIDTH*}&times;{HEIGHT*}{+END})</span>
								</p>
							</div></div>
						{+END}
					</div>
				{+END}
				{+START,IF_EMPTY,{THUMBNAILS}}
					<p class="nothing_here">{!NO_ENTRIES}</p>
				{+END}

				{+START,IF,{SOMETHING_EDITABLE}}
					{+START,INCLUDE,FILEDUMP_FOOTER}I=1{+END}
				{+END}
			</form>

			{+START,INCLUDE,NOTIFICATION_BUTTONS}
				NOTIFICATIONS_TYPE=filedump
				NOTIFICATIONS_ID={PLACE}
				RIGHT=1
			{+END}

			{+START,IF_NON_EMPTY,{PAGINATION_THUMBNAILS}}
				<div class="float_surrounder force_margin">
					{PAGINATION_THUMBNAILS}
				</div>
			{+END}
		</div>

		<div aria-labeledby="t_listing" role="tabpanel" id="g_listing" style="display: {$?,{$JS_ON},none,block}">
			<a id="tab__listing"></a>

			{+START,INCLUDE,FILEDUMP_SEARCH}
				I=2
				TAB=listing
			{+END}

			<form title="{!ACTION}" action="{POST_URL*}#tab__listing" method="post" onsubmit="return check_filedump_selections(this);" autocomplete="off">
				{$INSERT_SPAMMER_BLACKHOLE}

				{+START,IF_NON_EMPTY,{LISTING}}
					{LISTING}
				{+END}
				{+START,IF_EMPTY,{LISTING}}
					<p class="nothing_here">{!NO_ENTRIES}</p>
				{+END}

				{+START,IF,{SOMETHING_EDITABLE}}
					{+START,INCLUDE,FILEDUMP_FOOTER}I=2{+END}
				{+END}
			</form>

			{+START,INCLUDE,NOTIFICATION_BUTTONS}
				NOTIFICATIONS_TYPE=filedump
				NOTIFICATIONS_ID={PLACE}
				RIGHT=1
			{+END}

			{+START,IF_NON_EMPTY,{PAGINATION_LISTING}}
				<div class="float_surrounder force_margin">
					{PAGINATION_LISTING}
				</div>
			{+END}
		</div>

		{+START,IF_NON_EMPTY,{CREATE_FOLDER_FORM}}
			<div aria-labeledby="t_create_folder" role="tabpanel" id="g_create_folder" style="display: {$?,{$JS_ON},none,block}">
				<a id="tab__create_folder"></a>

				{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

				{CREATE_FOLDER_FORM}
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{UPLOAD_FORM}}
			<div aria-labeledby="t_upload" role="tabpanel" id="g_upload" style="display: {$?,{$JS_ON},none,block}">
				<a id="tab__upload"></a>

				{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

				{UPLOAD_FORM}
			</div>
		{+END}
	</div>
</div>

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		find_url_tab();
	});

	function check_filedump_selections(form)
	{
		var action=form.elements['action'].options[form.elements['action'].selectedIndex].value;

		if (action=='')
		{
			fauxmodal_alert('{!SELECT_AN_ACTION;}');
			return false;
		}

		if (action=='edit') return true;

		for (var i=0;i<form.elements.length;i++)
		{
			if ((form.elements[i].name.match(/^select_\d+$/)) && (form.elements[i].checked))
			{
				return true;
			}
		}

		fauxmodal_alert('{!NOTHING_SELECTED_YET;}');
		return false;
	}
//]]></script>

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,IF,{$AND,{$SHOW_DOCS},{$HAS_PRIVILEGE,see_software_docs}}}
	{+START,INCLUDE,STAFF_ACTIONS}
		STAFF_ACTIONS_TITLE={!STAFF_ACTIONS}

		1_URL={$TUTORIAL_URL*,tut_collaboration}
		1_TITLE={!HELP}
		1_REL=help
		1_ICON=menu/pages/help

		{+START,IF,{$IS_ADMIN}}
			2_URL={$PAGE_LINK*,_SELF:_SELF:broken}
			2_TITLE={!FIND_BROKEN_FILEDUMP_LINKS}
			2_ICON=menu/adminzone/tools/cleanup
		{+END}
	{+END}
{+END}
