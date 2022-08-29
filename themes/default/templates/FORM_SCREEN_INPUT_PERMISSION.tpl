{$REQUIRE_JAVASCRIPT,core_form_interfaces}

<tr id="access-{GROUP_ID*}-privilege-container" class="{$CYCLE,permissions_zebra,zebra-0,zebra-1}" data-view="FormScreenInputPermission" data-view-params="{+START,PARAMS_JSON,ALL_GLOBAL,GROUP_ID}{_*}{+END}">
	<th class="form-table-field-name">
		<p class="form-field-name field-name">{GROUP_NAME*}</p>
		{+START,IF,{$OR,{FORCE_PRESETS},{$IS_NON_EMPTY,{OVERRIDES}}}}
			<label for="access_{GROUP_ID*}_presets">
				<span class="accessibility-hidden">{!PINTERFACE_PRESETS} ({GROUP_NAME*})</span>

				<select tabindex="{TABINDEX*}" id="access_{GROUP_ID*}_presets" name="access_{GROUP_ID*}_presets" class="form-control js-click-copy-perm-presets js-change-copy-perm-presets">
					{+START,IF,{ALL_GLOBAL}}
						<option selected="selected" value="-1">{!PINTERFACE_LEVEL_GLOBAL}</option>
					{+END}
					{+START,IF,{$NOT,{ALL_GLOBAL}}}
						<option id="access-{GROUP_ID*}-custom-option" selected="selected" value="">{!PINTERFACE_LEVEL_CUSTOM}</option>
						<option value="-1">{!PINTERFACE_LEVEL_GLOBAL}</option>
					{+END}
					<optgroup label="{!PINTERFACE_PRESETS}">
						<option value="0">{!PINTERFACE_LEVEL_0}</option>
						<option value="1">{!PINTERFACE_LEVEL_1}</option>
						<option value="2">{!PINTERFACE_LEVEL_2}</option>
						<option value="3">{!PINTERFACE_LEVEL_3}</option>
					</optgroup>
				</select>
			</label>
		{+END}
	</th>

	<td class="form-table-field-input">
		<div class="accessibility-hidden"><label for="access_{GROUP_ID*}">{PINTERFACE_VIEW*} ({GROUP_NAME*})</label></div>

		<input tabindex="{TABINDEX*}" class="input-tick" type="checkbox" id="access_{GROUP_ID*}" name="access_{GROUP_ID*}" title="{PINTERFACE_VIEW*}" value="1"{+START,IF,{VIEW_ACCESS}} checked="checked"{+END} />
	</td>

	{OVERRIDES}

	{+START,IF,{$OR,{FORCE_PRESETS},{$IS_NON_EMPTY,{OVERRIDES}}}}
		<td class="form-table-field-input">
			<button class="btn btn-primary btn-scri buttons--copy js-click-perm-repeating" type="button" id="copy-button-access-{GROUP_ID*}">{+START,INCLUDE,ICON}NAME=buttons/copy{+END} <span>{!REPEAT_PERMISSION}</span></button>
		</td>
	{+END}
</tr>
