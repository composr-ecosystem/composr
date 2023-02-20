{TITLE}

<p>
	Support and development services are no longer available from ocProducts Ltd as we are winding down operations and the core developers are transitioning Composr's management and development to a new model.
	For support options please engage with the community on the <a href="{$PAGE_LINK*,forumview:}">forum</a> and in the <a href="{$PAGE_LINK*,:partners}">partner directory</a>.
</p>

<!--
{+START,IF_NON_EMPTY,{MESSAGE}}
	<p>{MESSAGE}</p>
{+END}

{+START,IF,{$NOT,{$IS_GUEST}}}
	<div class="box box___support_tickets_screen"><div class="box_inner vertical_alignment">
		<form title="{!FILTER}" class="float_surrounder" id="ticket_type_form" action="{$URL_FOR_GET_FORM*,{$SELF_URL,0,1}}" method="get" onsubmit="try { window.scrollTo(0,0); } catch(e) {}" autocomplete="off">
			{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,1},ticket_type_id,open}

			<div class="float_surrounder">
				<div class="inline">
					<label class="field_name" for="ticket_type_id">{!TICKET_TYPE}:</label>
					<select id="ticket_type_id" name="ticket_type_id" class="input_list_required">
						<option value="">&mdash;</option>
						{+START,LOOP,TYPES}
							<option value="{TICKET_TYPE_ID*}"{+START,IF,{SELECTED}} selected="selected"{+END}>{NAME*}</option>{$,You can also use {LEAD_TIME} to get the ticket type's lead time}
						{+END}
					</select>
				</div>

				<div class="inline spaced">
					<label class="field_name" for="open">{!OPEN_TICKETS_ONLY}:</label>
					<input type="checkbox" id="open" name="open" value="1"{+START,IF,{$_GET,open}} checked="checked"{+END} />
				</div>

				<div class="inline spaced">
					<input onclick="disable_button_just_clicked(this);" class="button_screen_item buttons__filter" type="submit" value="{!FILTER}" />
				</div>
			</div>
		</form>
	</div></div>

	{+START,IF_EMPTY,{LINKS}}
		{$?,{$HAS_PRIVILEGE,support_operator},<p class="nothing_here">{!NO_ENTRIES}</p>,{$PARAGRAPH,{!SUPPORT_NO_TICKETS}}<p>Looking for a tracker issue? Search the tracker using the <a href="{$BASE_URL*}/tracker/view_filters_page.php?for_screen=1&amp;view_type=advanced&amp;target_field=reporter_id[]">Reporter</a> field.</p>}
	{+END}
	{+START,IF_NON_EMPTY,{LINKS}}
		<div class="wide_table_wrap"><table class="columned_table results_table wide_table support_tickets autosized_table">
			<thead>
				<tr>
					<th>
						{!SUPPORT_TICKET}
					</th>
					<th>
						{!TICKET_TYPE}
					</th>
					{+START,IF,{$NOT,{$MOBILE}}}
						<th>
							{!COUNT_POSTS}
						</th>
					{+END}
					<th>
						{!BY}
					</th>
					<th>
						{!LAST_POST}
					</th>
					<th>
						{!ASSIGNED_TO}
					</th>
				</tr>
			</thead>
			<tbody>
				{LINKS}
			</tbody>
		</table></div>
	{+END}
{+END}

<p class="buttons_group">
	<a class="button_screen buttons__add_ticket" rel="add" href="{$PAGE_LINK*,:contact}"><span>{!ADD_TICKET}</span></a>
</p>
-->
