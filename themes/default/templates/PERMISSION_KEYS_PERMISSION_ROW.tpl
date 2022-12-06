{$REQUIRE_JAVASCRIPT,core_permission_management}

<tr class="{$CYCLE,zebra,zebra-0,zebra-1}" data-tpl="permissionRow" data-tpl-params="{+START,PARAMS_JSON,ROW_MODERATOR_GROUP_CELL_IDS,ROW_GROUP_CELL_IDS}{_*}{+END}">
	<td>
		<label class="accessibility-hidden" for="key_{UID*}">{!MATCH_KEY}</label>
		<div>
			<input class="form-control form-control-wide" maxlength="80" type="text" id="key_{UID*}" name="key_{UID*}" value="{KEY*}" />
		</div>
	</td>
	{CELLS}
	<td>
		<button class="btn btn-primary btn-sm js-click-btn-toggle-value" type="button">{+START,IF,{ALL_OFF}}+{+END}{+START,IF,{$NOT,{ALL_OFF}}}-{+END}</button>
	</td>
</tr>
