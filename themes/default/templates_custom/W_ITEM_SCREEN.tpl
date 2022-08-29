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
			<td><label class="accessibility-hidden" for="item-name">{!NAME}</label><input type="text" size="20" name="item_name" id="item-name" class="form-control" value="{+START,IF_PASSED,ITEM}{ITEM*}{+END}" /></td>
			<td>{!W_EG_ITEM_NAME}</td>
		</tr>
		<tr>
			<th>{!DESCRIPTION}</th>
			<td><label class="accessibility-hidden" for="description">{!DESCRIPTION}</label><input type="text" size="20" name="description" id="description" class="form-control" value="{DESCRIPTION*}" /></td>
			<td>{!W_EG_ITEM_DESCRIPTION}</td>
		</tr>
		<tr>
			<th>{!W_BRIBABLE}</th>
			<td>
				<label class="accessibility-hidden" for="bribable">{!W_BRIBABLE}</label>
				<input type="checkbox" name="bribable" id="bribable" value="1" {+START,IF,{BRIBABLE}} checked="checked"{+END} />
			</td>
			<td>{!W_EG_BRIBABLE}</td>
		</tr>
		<tr>
			<th>{!W_HEALTHY}</th>
			<td>
				<label class="accessibility-hidden" for="healthy">{!W_HEALTHY}</label>
				<input type="checkbox" name="healthy" id="healthy" value="1" {+START,IF,{HEALTHY}} checked="checked"{+END} />
			</td>
			<td>{!W_EG_HEALTHY}</td>
		</tr>
		<tr>
			<th>{!W_PICTURE}</th>
			<td><label class="accessibility-hidden" for="pic">{!W_PICTURE}</label><input type="file" size="20" name="pic" id="pic" class="form-control" /></td>
			<td>{!W_EG_ROOM_PICTURE}</td>
		</tr>
		<tr>
			<th>{!URL}</th>
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
		<tr>
			<th>{!W_MAXIMUM_PER_PLAYER}</th>
			<td><label class="accessibility-hidden" for="max_per_player">{!W_MAXIMUM_PER_PLAYER}</label><input type="text" size="20" name="max_per_player" id="max_per_player" class="form-control" value="{MAX_PER_PLAYER*}" /></td>
			<td>{!W_EG_MAXIMUM_PER_PLAYER}</td>
		</tr>
		<tr>
			<th>{!W_REPLICATEABLE}</th>
			<td>
				<label class="accessibility-hidden" for="replicateable">{!W_REPLICATEABLE}</label>
				<input type="checkbox" name="replicateable" id="replicateable" value="1" {+START,IF,{REPLICATEABLE}} checked="checked"{+END} />
			</td>
			<td>{!W_EG_REPLICATEABLE}</td>
		</tr>
	</tbody></table></div>

	<input type="hidden" name="type" value="{PAGE_TYPE*}" />
	{+START,IF_PASSED,ITEM}
		<input type="hidden" name="item" value="{ITEM*}" />
	{+END}

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
