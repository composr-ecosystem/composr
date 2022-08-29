{TITLE}

{+START,IF_PASSED,PRICE}
	<p>
		{!COST}: <strong>{PRICE}</strong>
	</p>
{+END}
{+START,IF_PASSED,TEXT}
	{$PARAGRAPH,{TEXT}}
{+END}

<form method="post" enctype="multipart/form-data" action="{$PAGE_LINK*,_SELF:_SELF}">
	{$INSERT_FORM_POST_SECURITY}

	<input type="hidden" name="type" value="{PAGE_TYPE*}" />
	{+START,IF_PASSED,PARAM}
		<input type="hidden" name="param" value="{PARAM*}" />
	{+END}

	<div class="wide-table-wrap"><table class="map-table autosized-table results-table wide-table responsive-blocked-table"><tbody>
		<tr>
			<th>{!NAME}</th>
			<td><label class="accessibility-hidden" for="portal-name">{!NAME}</label><input type="text" size="20" name="portal_name" id="portal-name" class="form-control" value="{NAME*}" /></td>
			<td>{!W_EG_PORTAL_NAME}</td>
		</tr>
		<tr>
			<th>{!W_PORTAL_TEXT}</th>
			<td><label class="accessibility-hidden" for="text">{!W_PORTAL_TEXT}</label><input type="text" size="20" name="text" id="text" class="form-control" value="{PORTAL_TEXT*}" /></td>
			<td>{!W_EG_PORTAL_TEXT}</td>
		</tr>
		<tr>
			<th>{!W_DESTINATION_REALM}</th>
			<td><label class="accessibility-hidden" for="end_location_realm">{!W_DESTINATION_REALM}</label><input type="text" size="20" name="end_location_realm" id="end_location_realm" class="form-control" value="{END_LOCATION_REALM*}" /></td>
			<td>{!W_EG_DESTINATION_REALM}</td>
		</tr>
		<tr>
			<th>{!W_DESTINATION_X}</th>
			<td><label class="accessibility-hidden" for="end_location_x">{!W_DESTINATION_X}</label><input type="text" size="20" name="end_location_x" id="end_location_x" class="form-control" value="{END_LOCATION_X*}" /></td>
			<td>{!W_EG_DESTINATION_X}</td>
		</tr>
		<tr>
			<th>{!W_DESTINATION_Y}</th>
			<td><label class="accessibility-hidden" for="end_location_y">{!W_DESTINATION_Y}</label><input type="text" size="20" name="end_location_y" id="end_location_y" class="form-control" value="{END_LOCATION_Y*}" /></td>
			<td>{!W_EG_AND_Y}</td>
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
				<td>{!W_EG_MOVE_X}</td>
			</tr>
			<tr>
				<th>Y</th>
				<td><label class="accessibility-hidden" for="new_y">Y</label><input type="text" size="20" name="new_y" id="new_y" class="form-control" value="{Y*}" /></td>
				<td>{!W_EG_MOVE_X}</td>
			</tr>
			<tr>
				<th>{!W_REALM}</th>
				<td><label class="accessibility-hidden" for="new_realm">{!W_REALM}</label><input type="text" size="20" name="new_realm" id="new_realm" class="form-control" value="{REALM*}" /></td>
				<td>{!W_EG_MOVE_X}</td>
			</tr>
		{+END}{+END}{+END}
	</tbody></table></div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
