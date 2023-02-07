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

	<table class="map-table autosized-table results-table wide-table responsive-blocked-table"><tbody>
		{+START,IF_PASSED,ITEMS}
			<tr>
				<th>{!NAME}</th>
				<td>
					<label class="accessibility-hidden" for="item-name">{!NAME}</label>
					<select name="item_name" id="item-name" class="form-control">
						{ITEMS}
					</select>
				</td>
				<td>Select a pre-existing item to replicate</td>
			</tr>
		{+END}
		<tr>
			<th>{!W_NOT_INFINITE}</th>
			<td>
				<label class="accessibility-hidden" for="not_infinite">{!W_NOT_INFINITE}</label>
				{+START,IF,{NOT_INFINITE}}
					<input type="checkbox" checked="checked" name="not_infinite" id="not_infinite" value="1" />
				{+END}
				{+START,IF,{$NOT,{NOT_INFINITE}}}
					<input type="checkbox" name="not_infinite" id="not_infinite" value="1" />
				{+END}
			</td>
			<td>{!W_EG_NOT_INFINITE}</td>
		</tr>
		<tr>
			<th>{!COST}</th>
			<td><label class="accessibility-hidden" for="price">{!COST}</label><input type="text" name="price" id="price" class="form-control" value="{PRICE*}" /></td>
			<td>{!W_EG_ITEM_COPY_PRICE}</td>
		</tr>
		{+START,IF_PASSED,X}{+START,IF_PASSED,Y}{+START,IF_PASSED,REALM}
			<tr>
				<th>X</th>
				<td><label class="accessibility-hidden" for="new_x">X</label><input type="text" size="20" id="new_x" class="form-control" name="new_x" value="{X*}" /></td>
				<td>{!W_EG_MOVE_X}</td>
			</tr>
			<tr>
				<th>Y</th>
				<td><label class="accessibility-hidden" for="new_y">Y</label><input type="text" size="20" id="new_y" class="form-control" name="new_y" value="{Y*}" /></td>
				<td>{!W_EG_MOVE_X}</td>
			</tr>
			<tr>
				<th>{!W_REALM}</th>
				<td><label class="accessibility-hidden" for="new_realm">{!W_REALM}</label><input type="text" size="20" id="new_realm" class="form-control" name="new_realm" value="{REALM*}" /></td>
				<td>{!W_EG_MOVE_X}</td>
			</tr>
		{+END}{+END}{+END}
		{+START,IF_PASSED,OWNER}
			<tr>
				<th>{!OWNER}</th>
				<td><label class="accessibility-hidden" for="new_owner">{!OWNER}</label><input type="text" size="20" id="new_owner" class="form-control" name="new_owner" value="{OWNER*}" /></td>
				<td>{!W_EG_OWNER}</td>
			</tr>
		{+END}
	</tbody></table>

	<input type="hidden" name="type" value="{PAGE_TYPE*}" />
	{+START,IF_PASSED,ITEM}
		<input type="hidden" name="item" value="{ITEM*}" />
	{+END}
	{+START,IF_PASSED,MEMBER}
		<input type="hidden" name="member" value="{MEMBER*}" />
	{+END}

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
