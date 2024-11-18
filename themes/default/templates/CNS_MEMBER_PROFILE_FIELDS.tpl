{$SET,is_point_field,{$EQ,{NAME_FULL},{!SPECIAL_CPF__cms_points_rank},{!SPECIAL_CPF__cms_points_balance}}}

{+START,IF,{$NOT,{$GET,is_point_field}}}{+START,IF,{$EQ,{SECTION},}}{+START,IF_NON_EMPTY,{RAW}}
	<tr id="cpf-{NAME|*}" class="cpf-{$REPLACE,_,-,{FIELD_ID|*}}">
		<th class="de-th">
			{NAME*}:
		</th>

		<td>
			<span>
				{+START,INCLUDE,CNS_MEMBER_PROFILE_FIELD}{+END}
			</span>
		</td>
	</tr>
{+END}{+END}{+END}
