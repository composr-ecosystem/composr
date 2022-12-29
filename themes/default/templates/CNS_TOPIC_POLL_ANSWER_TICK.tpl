<tr>
	<td class="cns-topic-poll-result-column2 cns-column6">
		<input {+START,IF,{DISABLE_ANSWERS}} disabled="disabled"{+END} value="1" type="checkbox" id="vote_{I*}" name="vote_{I*}" />
	</td>
	<th class="de-th cns-topic-poll-result"><label for="vote_{I*}">{+START,FRACTIONAL_EDITABLE,{ANSWER},answer_{I},_SEARCH:topics:_edit_poll:{ID}}{ANSWER*}{+END}</label></th>
</tr>
