{TITLE}

<div class="clearfix">
	{+START,IF_NON_EMPTY,{AVATAR}}
		<div class="buildr-avatar">
			<img alt="{!AVATAR}" src="{AVATAR*}" />
			{+START,IF_NON_EMPTY,{PHOTO}}
				[<a title="{!W_PHOTO} {!LINK_NEW_WINDOW}" target="_blank" href="{$THUMBNAIL*,{PHOTO}}">{!W_PHOTO}</a>]
			{+END}
		</div>
	{+END}

	<p>
		{!W_HAS_HEALTH,{USERNAME*},{HEALTH*}}
	</p>
</div>

{+START,IF_NON_EMPTY,{INVENTORY}}
	<table class="columned-table wide-table buildr-inventory results-table buildr-centered-contents autosized-table responsive-table">
		<thead>
			<tr>
				<th>{!W_PICTURE}</th>
				<th>{!NAME}/{!DESCRIPTION}</th>
				<th>{!COUNT_TOTAL}</th>
				<th>{!W_PROPERTIES}</th>
			</tr>
		</thead>

		<tbody>
			{INVENTORY}
		</tbody>
	</table>
{+END}

{+START,IF_EMPTY,{INVENTORY}}
	<p class="nothing-here">{!W_EMPTY_INVENTORY}</p>
{+END}
