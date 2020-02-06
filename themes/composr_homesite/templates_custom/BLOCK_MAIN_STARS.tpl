<div class="cntRow mainStar">
	{+START,IF_NON_EMPTY,{STARS}}
		<table>
			<thead>
				<tr class="row-top">
					<th class="blk1">
						Avatar
					</th>

					<th class="blk2 adj">
						Details
					</th>

					<th class="blk3 adj2 height-nil">
						Bio
					</th>
				</tr>
			</thead>

			{+START,LOOP,STARS}
				<tr>
					<td class="blk1">
						{+START,IF_NON_EMPTY,{AVATAR_URL}}
							<img alt="Avatar" width="100" height="100" src="{AVATAR_URL*}" />
						{+END}

						{+START,IF_EMPTY,{AVATAR_URL}}
							<img alt="" width="100" height="100" src="{$IMG*,cns_default_avatars/default,0,,1}" />
						{+END}
					</td>

					<td class="blk2">
						<strong>Username :</strong> <a href="{URL*}">{USERNAME*}</a><br />
						<strong>Role Points :</strong> {POINTS*}<br />
						<strong>Rank :</strong> {RANK*}<br />
					</td>

					<td class="blk3">
						{$CPF_VALUE,{!cns_special_cpf:DEFAULT_CPF_about_NAME},{MEMBER_ID},1}
					</td>
				</tr>
			{+END}
		</table>
	{+END}

	{+START,IF_EMPTY,{STARS}}
		<p style="font-weight: bold; padding: 10px">Nobody yet &ndash; could you be here?</p>
	{+END}
</div>
