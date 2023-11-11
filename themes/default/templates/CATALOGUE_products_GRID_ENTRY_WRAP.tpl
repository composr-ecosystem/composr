<section class="box box___catalogue_products_grid_entry_wrap"><div class="box_inner">
	<h3><span class="name">{FIELD_0}</span></h3>

	{+START,SET,TOOLTIP}
		{+START,IF_NON_EMPTY,{$TRIM,{FIELDS_GRID}}}
			<table class="map_table results_table">
				<tbody>
					{FIELDS_GRID}
				</tbody>
			</table>
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{FIELD_7_THUMB}}
		<div class="catalogue_entry_box_thumbnail">
			<a onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{$TRIM*;^,{$GET,TOOLTIP}}','500px');" href="{VIEW_URL*}">{FIELD_7_THUMB}</a>
		</div>
	{+END}

	{+START,IF,{ALLOW_RATING}}
		{$SET-,rating,{$RATING,catalogues__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_STATIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			<div class="ratings">{$GET,rating}</div>
		{+END}
	{+END}

	<div class="price_box">
		<span class="price">{$CURRENCY_SYMBOL}{$FLOAT_FORMAT*,{FIELD_2_PLAIN}}</span>
	</div>

	<div class="buttons_group">
		{+START,IF_PASSED,ADD_TO_CART}
			<a class="button_screen_item buttons__cart_add" href="{ADD_TO_CART*}" title="{!ADD_TO_CART}"><span>{!BUY}</span></a>
		{+END}
		<a class="button_screen_item buttons__more" href="{VIEW_URL*}" title="{!VIEW_PRODUCT}"><span>{!VIEW}</span></a>
	</div>
</div></section>
