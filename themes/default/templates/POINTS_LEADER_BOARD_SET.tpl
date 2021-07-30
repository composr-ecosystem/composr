<section class="box{+START,IF,{$NOT,{IS_BLOCK}}} box---points-leader-board-page{+END}">
	<div class="box-inner">
		<h3>{!LEADER_BOARD_SET,{TITLE*}}</h3>

		<p>{ABOUT*}</p>

		<div class="wide-table-wrap"><table class="map-table autosized-table leader-board-table results-table wide-table">
			<tbody>
				{ROWS}
			</tbody>
		</table></div>

		{+START,IF,{IS_BLOCK}}
			<ul class="horizontal-links associated-links-block-group force-margin">
				<li><a rel="archives" href="{URL*}" title="{!MORE}: {!POINT_LEADER_BOARD}">{!ARCHIVES}</a></li>
			</ul>
		{+END}
	</div>
</section>
