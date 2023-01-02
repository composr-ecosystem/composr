{$REQUIRE_JAVASCRIPT,core_permission_management}

<tr class="{$CYCLE,zebra,zebra-0,zebra-1}" data-tpl="permissionRow" data-tpl-params="{+START,PARAMS_JSON,ROW_MODERATOR_GROUP_CELL_IDS,ROW_GROUP_CELL_IDS}{_*}{+END}">
	<th class="responsive-table-no-prefix-no-indent">
		{PERMISSION*}
		{+START,IF_PASSED,DESCRIPTION}
			{+START,IF,{$DESKTOP}}
				<span class="inline-desktop">
					<a class="button-icon" data-cms-tooltip="{DESCRIPTION=}">{+START,INCLUDE,ICON}
						NAME=help
						ICON_SIZE=24
					{+END}</a>
				</span>
			{+END}

			<span class="block-mobile">
				{DESCRIPTION}
			</span>
		{+END}
	</th>
	{CELLS}
	<td>
		<button title="{!SET_ALL_PERMISSIONS_ON_ROW}" class="btn btn-primary btn-sm js-click-btn-toggle-value permission-toggle-button" type="button">{$?,{HAS},-,+}</button>
	</td>
</tr>
