<section class="box box---catalogue-products-grid-entry-wrap"><div class="box-inner">
	<h3><span class="name">{FIELD_0}</span></h3>

	{+START,IF_NON_EMPTY,{FIELD_7}}
		<div class="catalogue-entry-box-thumbnail">
			<a href="{VIEW_URL*}"><img class="img-thumb" alt="{$STRIP_TAGS,{FIELD_0}}" src="{$THUMBNAIL*,{FIELD_7_PLAIN}}" /></a>
		</div>
	{+END}

	{+START,IF,{ALLOW_RATING}}
		{$SET-,rating,{$RATING,catalogue_entry__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_DYNAMIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			<div class="ratings">{$GET,rating}</div>
		{+END}
	{+END}

	<div class="price-box">
		<span class="price">{$CURRENCY,{FIELD_2_PLAIN},,{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}</span>
	</div>

	<div class="buttons-group">
		<div class="buttons-group-inner">
			{+START,IF_PASSED,ADD_TO_CART}
				<a class="btn btn-primary btn-scri buttons--cart-add" href="{ADD_TO_CART*}" title="{!ADD_TO_CART}"><span>{+START,INCLUDE,ICON}NAME=buttons/cart_add{+END} {!BUY}</span></a>
			{+END}
			<a class="btn btn-primary btn-scri buttons--more" href="{VIEW_URL*}" title="{!VIEW_PRODUCT}"><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
		</div>
	</div>
</div></section>
