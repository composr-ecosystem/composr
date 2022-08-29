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
			<td><label class="accessibility-hidden" for="realm-name">{!NAME}</label><input type="text" size="20" name="realm_name" id="realm-name" class="form-control" value="{NAME*}" /></td>
			<td>{!W_EG_REALM_NAME}</td>
		</tr>
		<tr>
			<th>{!W_REALM_TROLL_NAME}</th>
			<td><label class="accessibility-hidden" for="troll_name">{!W_REALM_TROLL_NAME}</label><input type="text" size="20" name="troll_name" id="troll_name" class="form-control" value="{TROLL_NAME*}" /></td>
			<td>{!W_EG_REALM_TROLL_NAME}</td>
		</tr>
		<tr>
			<th>{!W_PRIVATE_REALM}</th>
			<td>
				<label class="accessibility-hidden" for="private">{!W_PRIVATE_REALM}</label>
				{+START,IF,{PRIVATE}}
					<input type="checkbox" value="1" id="private" name="private" checked="checked" />
				{+END}
				{+START,IF,{$NOT,{PRIVATE}}}
					<input type="checkbox" value="1" id="private" name="private" />
				{+END}
			</td>
			<td>{!W_EG_PRIVATE_REALM}</td>
		</tr>
		{+START,IF_NON_PASSED,OWNER}
			<tr>
				<th>{!W_REALM_JAIL_NAME}</th>
				<td><label class="accessibility-hidden" for="jail_name">{!W_REALM_JAIL_NAME}</label><input type="text" size="20" name="jail_name" id="jail_name" class="form-control" /></td>
				<td>{!W_EG_REALM_JAIL_NAME}</td>
			</tr>
			<tr>
				<th>{!W_REALM_JAIL_TEXT}</th>
				<td><label class="accessibility-hidden" for="jail_text">{!W_REALM_JAIL_TEXT}</label><input type="text" size="20" name="jail_text" id="jail_text" class="form-control" /></td>
				<td>{!W_EG_REALM_JAIL_TEXT}</td>
			</tr>
			<tr>
				<th>{!W_REALM_JAIL_PIC}</th>
				<td><label class="accessibility-hidden" for="jail_pic">{!W_REALM_JAIL_PIC}</label><input type="file" size="20" name="jail_pic" id="jail_pic" class="form-control" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>{!ALT_FIELD,{!URL}}</th>
				<td><label class="accessibility-hidden" for="jail_pic_url">{!ALT_FIELD,{!URL}}</label><input type="text" size="20" name="jail_pic_url" id="jail_pic_url" class="form-control" /></td>
				<td>{!DESCRIPTION_ALTERNATE_URL}</td>
			</tr>
			<tr>
				<th>{!W_REALM_JAILHOUSE_NAME}</th>
				<td><label class="accessibility-hidden" for="jail_house_name">{!W_REALM_JAILHOUSE_NAME}</label><input type="text" size="20" name="jail_house_name" id="jail_house_name" class="form-control" /></td>
				<td>{!W_EG_REALM_JAILHOUSE_NAME}</td>
			</tr>
			<tr>
				<th>{!W_REALM_JAILHOUSE_TEXT}</th>
				<td><label class="accessibility-hidden" for="jail_house_text">{!W_REALM_JAILHOUSE_TEXT}</label><input type="text" size="20" name="jail_house_text" id="jail_house_text" class="form-control" /></td>
				<td>{!W_EG_REALM_JAILHOUSE_TEXT}</td>
			</tr>
			<tr>
				<th>{!W_REALM_JAILHOUSE_PIC}</th>
				<td><label class="accessibility-hidden" for="jail_house_pic">{!W_REALM_JAILHOUSE_PIC}</label><input type="file" size="20" name="jail_house_pic" id="jail_house_pic" class="form-control" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>{!ALT_FIELD,{!URL}}</th>
				<td><label class="accessibility-hidden" for="jail_house_pic_url">{!ALT_FIELD,{!URL}}</label><input type="text" size="20" name="jail_house_pic_url" id="jail_house_pic_url" class="form-control" /></td>
				<td>{!DESCRIPTION_ALTERNATE_URL}</td>
			</tr>
			<tr>
				<th>{!W_REALM_LOBBY_NAME}</th>
				<td><label class="accessibility-hidden" for="lobby_name">{!W_REALM_LOBBY_NAME}</label><input type="text" size="20" name="lobby_name" id="lobby_name" class="form-control" /></td>
				<td>{!W_EG_REALM_LOBBY_NAME}</td>
			</tr>
			<tr>
				<th>{!W_REALM_LOBBY_TEXT}</th>
				<td><label class="accessibility-hidden" for="lobby_text">{!W_REALM_LOBBY_TEXT}</label><input type="text" size="20" name="lobby_text" id="lobby_text" class="form-control" /></td>
				<td>{!W_EG_REALM_LOBBY_TEXT}</td>
			</tr>
			<tr>
				<th>{!W_REALM_LOBBY_PIC}</th>
				<td><label class="accessibility-hidden" for="lobby_pic">{!W_REALM_LOBBY_PIC}</label><input type="file" size="20" name="lobby_pic" id="lobby_pic" class="form-control" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>{!ALT_FIELD,{!URL}}</th>
				<td><label class="accessibility-hidden" for="lobby_pic_url">{!ALT_FIELD,{!URL}}</label><input type="text" size="20" name="lobby_pic_url" id="lobby_pic_url" class="form-control" /></td>
				<td>{!DESCRIPTION_ALTERNATE_URL}</td>
			</tr>
		{+END}
		{+START,IF_PASSED,OWNER}
			<tr>
				<th>{!OWNER}</th>
				<td><label class="accessibility-hidden" for="new_owner">{!OWNER}</label><input type="text" size="20" name="new_owner" id="new_owner" value="{OWNER*}" class="form-control" /></td>
				<td>{!W_EG_OWNER}</td>
			</tr>
		{+END}
	</tbody></table></div>

	<p>{!W_TROLL_QUESTIONS}</p>

	{QA}

	<input type="hidden" name="type" value="{PAGE_TYPE*}" />

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
