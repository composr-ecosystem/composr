{$REQUIRE_JAVASCRIPT,chat}

<div data-tpl="chatSetEffectsSettingBlock" data-tpl-params="{+START,PARAMS_JSON,EFFECTS}{_*}{+END}"{+START,IF_PASSED,MEMBER_ID} data-toggleable-tray="{}"{+END}>
	{+START,IF_PASSED,USERNAME}{+START,IF_PASSED,MEMBER_ID}
		<div class="toggleable-tray-title js-tray-header">
			{!OVERRIDES_FOR_FRIEND,{USERNAME*}}
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{$?,{HAS_SOME},{!CONTRACT},{!EXPAND}}">{+START,INCLUDE,ICON}
				NAME=trays/{$?,{HAS_SOME},contract,expand}
				ICON_SIZE=20
			{+END}</a>
		</div>
	{+END}{+END}

	<div class="toggleable-tray js-tray-content"{+START,IF_PASSED,MEMBER_ID} id="user-{MEMBER_ID*}"{+END}{+START,IF,{$NOT,{HAS_SOME}}} style="display: none"{+END} aria-expanded="false">
		<div class="wide-table-wrap"><table class="map-table form-table wide-table scrollable-inside">
			{+START,IF,{$DESKTOP}}
				<colgroup>
					<col class="field-name-column" />
					<col class="field-input-column" />
				</colgroup>
			{+END}

			<tbody>
				{+START,LOOP,EFFECTS}
					<tr class="form-table-field-spacer">
						<th colspan="2" class="table-heading-cell">
							<span class="h2">{EFFECT_TITLE*}</span>
						</th>
					</tr>

					<tr class="field-input">
						<th id="form-table-field-name--select-{KEY*}{+START,IF_PASSED,MEMBER_ID}-{MEMBER_ID*}{+END}" class="form-table-field-name">
							<label for="select_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}"><span class="form-field-name field-name">{!BROWSE}</span></label>
						</th>

						<td id="form-table-field-input--select-{KEY*}{+START,IF_PASSED,MEMBER_ID}-{MEMBER_ID*}{+END}" class="form-table-field-input">
							<select name="select_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" id="select_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" class="form-control">
								{+START,IF_PASSED,USERNAME}
									<option {+START,IF,{$EQ,-1,{VALUE}}} selected="selected"{+END} value="-1">{$STRIP_TAGS,{!_UNSET}}</option>
								{+END}
								<option {+START,IF,{$EQ,,{VALUE}}} selected="selected"{+END} value="">{!NONE_EM}</option>
								{+START,LOOP,LIBRARY}
									<option {+START,IF,{$EQ,{EFFECT},{VALUE}}} selected="selected"{+END} value="{EFFECT*}">{EFFECT_SHORT*}</option>
								{+END}
								{+START,IF,{$EQ,{$SUBSTR,{VALUE},0,8},uploads/}}
									<option selected="selected" value="{VALUE*}">{!CUSTOM_UPLOAD}</option>
								{+END}
							</select>

							<button class="btn btn-primary btn-scri menu--social--chat--sound js-click-require-sound-selection" data-tp-select-id="select_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" type="button" title="{EFFECT_TITLE*}">{+START,INCLUDE,ICON}NAME=menu/social/chat/sound{+END} <span>{!TEST_SOUND}</span></button>
						</td>
					</tr>

					<tr class="field-input">
						<th id="form-table-field-name--upload-{KEY*}{+START,IF_PASSED,MEMBER_ID}-{MEMBER_ID*}{+END}" class="form-table-field-name">
							<span class="form-field-name field-name">{!ALT_FIELD,{!UPLOAD}}</span>
						</th>

						<td id="form-table-field-input--upload-{KEY*}{+START,IF_PASSED,MEMBER_ID}-{MEMBER_ID*}{+END}" class="form-table-field-input">
							<div class="upload-field">
								<label class="accessibility-hidden" for="upload_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}">{!ALT_FIELD,{!UPLOAD}}</label>
								<input name="upload_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" id="upload_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" type="file" />

								<input type="hidden" name="clear-button-upload_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" id="clear-button-upload_{KEY*}{+START,IF_PASSED,MEMBER_ID}_{MEMBER_ID*}{+END}" />
							</div>
						</td>
					</tr>
				{+END}
			</tbody>
		</table></div>
	</div>
</div>
