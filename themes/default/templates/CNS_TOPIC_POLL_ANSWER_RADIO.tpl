<tr>
	<td class="cns-topic-poll-radio-2"><input{+START,IF,{DISABLE_ANSWERS}} disabled="disabled"{+END} type="radio" id="vote-{I*}" name="vote" value="{I*}" /></td>
	<th class="de-th cns-topic-poll-radio"><label for="vote-{I*}">{+START,FRACTIONAL_EDITABLE,{ANSWER},answer_{I},_SEARCH:cms_polls:_edit_poll:{ID}}{ANSWER*}{+END}</label></th>
</tr>
