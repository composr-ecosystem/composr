{+START,IF,{$OR,{$AND,{UNDERNEATH},{$IS_NON_EMPTY,{TREE}}},{$IS_NON_EMPTY,{OPTIONS}}}}
	<tr class="search-form-screen-advanced">
		<td colspan="3">
			<h3 class="search-advanced-title">{!ADDITIONAL_FILTERS}&hellip;</h3>

			{OPTIONS}

			{+START,IF,{UNDERNEATH}}
				<div class="clearfix">
					<p>
						<label for="search_under">{!SEARCH_UNDERNEATH}:</label>
					</p>
					<div>
						{+START,IF,{AJAX}}
							{TREE}
						{+END}
						{+START,IF,{$NOT,{AJAX}}}
							<select id="search_under" name="search_under" class="form-control">
								{TREE}
							</select>
						{+END}
					</div>
				</div>
			{+END}
		</td>
	</tr>
{+END}
