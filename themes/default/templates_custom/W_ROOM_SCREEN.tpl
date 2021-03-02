{TITLE}

{+START,IF_PASSED,PRICE}
	<p>
		{!COST}: <strong>{PRICE}</strong>
	</p>
{+END}
{+START,IF_PASSED,TEXT}
	{$PARAGRAPH,{TEXT}}
{+END}

<form method="post" enctype="multipart/form-data" action="{$PAGE_LINK*,_SELF:_SELF:uploading=1}">
	{$INSERT_FORM_POST_SECURITY}

	<div class="wide-table-wrap"><table class="map-table autosized-table results-table wide-table responsive-blocked-table"><tbody>
		<tr>
			<th>{!NAME}</th>
			<td><label class="accessibility-hidden" for="room-name">{!NAME}</label><input type="text" size="20" name="room_name" id="room-name" class="form-control" value="{NAME*}" /></td>
			<td>{!W_EG_ROOM_NAME}</td>
		</tr>
		<tr>
			<th>{!W_ROOM_TEXT}</th>
			<td><label class="accessibility-hidden" for="text">{!W_ROOM_TEXT}</label><input type="text" size="20" name="text" id="text" class="form-control" value="{ROOM_TEXT*}" /></td>
			<td>{!W_EG_ROOM_TEXT}</td>
		</tr>
		<tr>
			<th>{!W_ENTRANCE_QUESTION}</th>
			<td><label class="accessibility-hidden" for="password_question">{!W_ENTRANCE_QUESTION}</label><input type="text" size="20" name="password_question" id="password_question" class="form-control" value="{PASSWORD_QUESTION*}" /></td>
			<td>{!W_EG_ENTRANCE_QUESTION}</td>
		</tr>
		<tr>
			<th>{!W_ENTRANCE_ANSWER}</th>
			<td><label class="accessibility-hidden" for="password_answer">{!W_ENTRANCE_ANSWER}</label><input type="text" size="20" name="password_answer" id="password_answer" class="form-control" value="{PASSWORD_ANSWER*}" /></td>
			<td>{!W_EG_ENTRANCE_ANSWER}</td>
		</tr>
		<tr>
			<th>{!W_ENTRANCE_FAIL_MESSAGE}</th>
			<td><label class="accessibility-hidden" for="password_fail_message">{!W_ENTRANCE_FAIL_MESSAGE}</label><input type="text" size="20" name="password_fail_message" id="password_fail_message" class="form-control" value="{PASSWORD_FAIL_MESSAGE*}" /></td>
			<td>{!W_EG_ENTRANCE_FAIL_MESSAGE}</td>
		</tr>
		<tr>
			<th>{!W_REQUIRED_ITEM}</th>
			<td><label class="accessibility-hidden" for="required_item">{!W_REQUIRED_ITEM}</label><input type="text" size="20" name="required_item" id="required_item" class="form-control" value="{REQUIRED_ITEM*}" /></td>
			<td>{!W_EG_REQUIRED_ITEM}</td>
		</tr>
		<tr>
			<th>{!W_UP_DIRECTION_LOCKED}</th>
			<td>
				<label class="accessibility-hidden" for="locked_up">{!W_UP_DIRECTION_LOCKED}</label>
			{+START,IF,{LOCKED_UP}}
				<input type="checkbox" value="1" name="locked_up" id="locked_up" checked="checked" />
			{+END}
			{+START,IF,{$NOT,{LOCKED_UP}}}
				<input type="checkbox" value="1" name="locked_up" id="locked_up" />
			{+END}
			</td>
			<td>{!W_DIR_NOTE}</td>
		</tr>
		<tr>
			<th>{!W_DOWN_DIRECTION_LOCKED}</th>
			<td>
				<label class="accessibility-hidden" for="locked_down">{!W_DOWN_DIRECTION_LOCKED}</label>
				{+START,IF,{LOCKED_DOWN}}
					<input type="checkbox" value="1" name="locked_down" id="locked_down" checked="checked" />
				{+END}
				{+START,IF,{$NOT,{LOCKED_DOWN}}}
					<input type="checkbox" value="1" name="locked_down" id="locked_down" />
				{+END}
			</td>
			<td>{!W_DIR_NOTE}</td>
		</tr>
		<tr>
			<th>{!W_RIGHT_DIRECTION_LOCKED}</th>
			<td>
				<label class="accessibility-hidden" for="locked_right">{!W_RIGHT_DIRECTION_LOCKED}</label>
				{+START,IF,{LOCKED_RIGHT}}
					<input type="checkbox" value="1" name="locked_right" id="locked_right" checked="checked" />
				{+END}
				{+START,IF,{$NOT,{LOCKED_RIGHT}}}
					<input type="checkbox" value="1" name="locked_right" id="locked_right" />
				{+END}
			</td>
			<td>{!W_DIR_NOTE}</td>
		</tr>
		<tr>
			<th>{!W_LEFT_DIRECTION_LOCKED}</th>
			<td>
				<label class="accessibility-hidden" for="locked_left">{!W_LEFT_DIRECTION_LOCKED}</label>
				{+START,IF,{LOCKED_LEFT}}
					<input type="checkbox" value="1" name="locked_left" id="locked_left" checked="checked" />
				{+END}
				{+START,IF,{$NOT,{LOCKED_LEFT}}}
					<input type="checkbox" value="1" name="locked_left" id="locked_left" />
				{+END}
			</td>
			<td>{!W_DIR_NOTE}</td>
		</tr>
		<tr>
			<th>{!W_ALLOW_PORTAL}</th>
			<td><label class="accessibility-hidden" for="allow_portal">{!W_ALLOW_PORTAL}:</label> <input type="checkbox" value="1" id="allow_portal" name="allow_portal"{+START,IF,{ALLOW_PORTAL}} checked="checked"{+END} /></td>
			<td>{!W_EG_ALLOW_PORTAL}</td>
		</tr>
		<tr>
			<th>{!W_PICTURE}</th>
			<td><label class="accessibility-hidden" for="pic">{!W_PICTURE}</label><input type="file" size="20" name="pic" id="pic" /></td>
			<td>{!W_EG_ROOM_PICTURE}</td>
		</tr>
		<tr>
			<th>{!ALT_FIELD,{!URL}}</th>
			<td><label class="accessibility-hidden" for="url">{!URL}</label><input type="text" size="20" name="url" id="url" class="form-control" value="{PICTURE_URL*}" /></td>
			<td>{!DESCRIPTION_ALTERNATE_URL}</td>
		</tr>
		{+START,IF_PASSED,OWNER}
			<tr>
				<th>{!OWNER}</th>
				<td><label class="accessibility-hidden" for="new_owner">{!OWNER}</label><input type="text" size="20" name="new_owner" id="new_owner" class="form-control" value="{OWNER*}" /></td>
				<td>{!W_EG_OWNER}</td>
			</tr>
		{+END}
		{+START,IF_PASSED,X}{+START,IF_PASSED,Y}{+START,IF_PASSED,REALM}
			<tr>
					<th>X</th>
					<td><label class="accessibility-hidden" for="new_x">X</label><input type="text" size="20" name="new_x" id="new_x" class="form-control" value="{X*}" /></td>
					<td>{!W_EG_MOVE_ROOM}</td>
			</tr>
			<tr>
					<th>Y</th>
					<td><label class="accessibility-hidden" for="new_y">Y</label><input type="text" size="20" name="new_y" id="new_y" class="form-control" value="{Y*}" /></td>
					<td>{!W_EG_MOVE_ROOM}</td>
			</tr>
			<tr>
					<th>{!W_REALM}</th>
					<td><label class="accessibility-hidden" for="new_realm">{!W_REALM}</label><input type="text" size="20" name="new_realm" id="new_realm" class="form-control" value="{REALM*}" /></td>
					<td>{!W_EG_MOVE_ROOM}</td>
			</tr>
		{+END}{+END}{+END}
		{+START,IF_NON_PASSED,X}
			<tr>
				<th>{!W_POSITION}</th>
				<td>
					<label for="position_0">{!W_UP} <input type="radio" checked="checked" id="position_0" name="position" value="0" /></label>
					<label for="position_1" class="horiz-field-sep">{!W_DOWN}<input type="radio" id="position_1" name="position" value="1" /></label>
					<label for="position_2" class="horiz-field-sep">{!W_RIGHT} <input type="radio" id="position_2" name="position" value="2" /></label>
					<label for="position_3" class="horiz-field-sep">{!W_LEFT} <input type="radio" id="position_3" name="position" value="3" /></label>
				</td>
				<td>{!W_ROOM_POS}</td>
			</tr>
		{+END}
	</tbody></table></div>

	<input type="hidden" name="type" value="{PAGE_TYPE*}" />

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</p>
</form>
