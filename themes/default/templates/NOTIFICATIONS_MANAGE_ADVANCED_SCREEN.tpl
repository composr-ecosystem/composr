{TITLE}

<p>
	{!DECIDE_PER_CATEGORY_NOTIFICATIONS,{_TITLE*}}
</p>

<form title="{!NOTIFICATIONS}" method="post" action="{ACTION_URL*}">
	{$INSERT_FORM_POST_SECURITY}

	<div>
		{+START,IF_NON_EMPTY,{$TRIM,{TREE}}}
			<table class="columned-table wide-table results-table notifications-form">
				<colgroup>
					<col class="notifications-field-name-column" />
					{+START,LOOP,NOTIFICATION_TYPES_TITLES}
						<col class="notifications-tick-column" />
					{+END}
				</colgroup>

				<thead>
					<tr>
						<th></th>
						{+START,LOOP,NOTIFICATION_TYPES_TITLES}
							<th>
								<img class="gd-text" data-gd-text="1" src="{$FIND_SCRIPT_NOHTTP*,gd_text}?trans_color={COLOR*}&amp;text={$ESCAPE,{LABEL},UL_ESCAPED}{$KEEP*}" title="" alt="{LABEL*}" />
							</th>
						{+END}
					</tr>
				</thead>

				<tbody>
					{TREE}
				</tbody>
			</table>

			<p class="proceed-button">
				<button type="submit" class="btn btn-primary btn-scr buttons--save">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
			</p>
		{+END}

		{+START,IF_EMPTY,{$TRIM,{TREE}}}
			<p class="nothing-here">
				{!NO_CATEGORIES}
			</p>
		{+END}
	</div>
</form>
