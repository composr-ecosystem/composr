{TITLE}

{+START,IF_NON_EMPTY,{POINTS}}
	{+START,IF,{$GT,{POINTS},0}}
		<div class="meta_details" role="note">
			<dl class="meta_details_list">
				<dt class="field_name">{!POINTS}:</dt> <dd>{POINTS*}</dd>
			</dl>
		</div>
	{+END}
{+END}

{WARNING_DETAILS}

{$REQUIRE_CSS,quizzes}

{+START,IF_NON_EMPTY,{START_TEXT}}
	<div class="box box___quiz_screen"><div class="box_inner">
		<div itemprop="description">
			{$PARAGRAPH,{START_TEXT}}
		</div>
	</div></div>
{+END}

{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,quiz,{ID}}}
{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

{+START,IF_NON_EMPTY,{TIMEOUT}}
	<p class="quiz_timer">
		{!TIME_REMAINING,<strong><span id="quiz_timer" style="display: none">{TIMEOUT*}</span><span id="quiz_timer_minutes_and_seconds"></span></strong>}
	</p>

	<script>// <![CDATA[
		setTimeout(function() {
			document.getElementById('quiz_form').submit();
		},{TIMEOUT%}*1000);

		setInterval(function() { iterate_countdown(-1); },1000);

		function iterate_countdown(dif)
		{
			var st=document.getElementById('quiz_timer');
			var new_value=window.parseInt(get_inner_html(st))+dif;
			if (new_value>=0)
			{
				set_inner_html(st,new_value);
			}

			var st2=document.getElementById('quiz_timer_minutes_and_seconds');
			if (st2)
			{
				if (new_value>=0)
				{
					var v='';
					v+=Math.floor(new_value/60);
					v+=':';
					if (new_value%60<10) v+='0';
					v+=new_value%60;
					set_inner_html(st2,v);
				}
			}
		}
		iterate_countdown(0); // Because quiz_timer_minutes_and_seconds needs setting correctly
	//]]></script>
{+END}

{$SET,no_required_stars,{ALL_REQUIRED}}
{+START,IF,{$NOT,{ALL_REQUIRED}}}
	{+START,IF,{$IN_STR,{FIELDS},required_star}}
		{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
	{+END}
{+END}

<form title="{!SAVE}" id="quiz_form" class="quiz_form" method="post" onsubmit="return check_form(this);" action="{URL*}" autocomplete="off">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		<div class="wide_table_wrap"><table class="map_table form_table wide_table">
			{+START,IF,{$NOT,{$MOBILE}}}
				<colgroup>
					<col class="quiz_field_name_column" />
					<col class="quiz_field_input_column" />
				</colgroup>
			{+END}

			<tbody>
				{FIELDS}
			</tbody>
		</table></div>

		<p class="proceed_button">
			<input accesskey="u" class="button_screen buttons__proceed" type="submit" value="{!SUBMIT}" />
		</p>
	</div>
</form>

<script>// <![CDATA[
	var e=get_elements_by_class_name(document.getElementById('quiz_form'),'field_input');
	for (var i=0;i<e.length;i++)
		set_up_change_monitor(e[i].childNodes[0]);
//]]></script>

{$REVIEW_STATUS,quiz,{ID}}

{+START,IF,{$CONFIG_OPTION,show_content_tagging}}{TAGS}{+END}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={EDIT_URL*}
	1_TITLE={!EDIT}
	1_ACCESSKEY=q
	1_REL=edit
	1_ICON=menu/_generic_admin/edit_this
{+END}

