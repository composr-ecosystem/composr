<div itemscope="itemscope" itemtype="http://schema.org/Offer" class="product_view">
	<div class="fn product-name" itemprop="itemOffered">{TITLE}</div>

	{$REQUIRE_CSS,shopping}

	{WARNINGS}

	<div class="cart_info">
		{$SET-,rating,{$RATING,catalogues__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_DYNAMIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			<div class="rating_part">
				<span class="field_name">{!RATING}:</span> {$GET,rating}
			</div>
		{+END}

		{$CART_LINK}
	</div>

	<div class="box box___catalogue_products_entry_screen"><div class="box_inner">
		<div class="hproduct"{$?,{$MATCH_KEY_MATCH,_WILD:_WILD:browse}, itemscope="itemscope" itemtype="http://schema.org/Offer"}>
			<div class="float_surrounder">
				{+START,IF_NON_EMPTY,{FIELD_7_THUMB}}
					<p class="catalogue_entry_box_thumbnail">
						{$REPLACE, rel="lightbox", rel="lightbox" itemprop="image",{FIELD_7}}
					</p>
				{+END}

				{+START,IF_NON_EMPTY,{FIELD_9}}
					<div class="description" itemprop="description">
						{FIELD_9}{$,Product description}
					</div>
				{+END}

				{+START,IF_NON_EMPTY,{FIELD_2}}
					<div class="price_box">
						<span class="price">{!PRICE}: <span itemprop="priceCurrency">{$CURRENCY_SYMBOL}</span><span itemprop="price">{$FLOAT_FORMAT*,{FIELD_2_PLAIN}}</span>{$,Product price}</span>
					</div>
				{+END}
			</div>

			{+START,IF_NON_EMPTY,{$TRIM,{FIELDS}}}
				<div class="wide_table_wrap">
					<table id="product-attribute-specs-table" class="map_table catalogue_fields_table wide_table results_table">
						{+START,IF,{$NOT,{$MOBILE}}}
							<colgroup>
								<col class="catalogue_fieldmap_field_name_column" />
								<col class="catalogue_fieldmap_field_value_column" />
							</colgroup>
						{+END}

						<tbody>
							{FIELDS}
						</tbody>
					</table>
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{FIELD_1}}
				<p class="product-ids sku">{!ECOM_CAT_sku}: <kbd>{FIELD_1}</kbd>{$,Product code}</p>
			{+END}
			{+START,IF_NON_EMPTY,{FIELD_3}}
				<p class="stock_level">{!STOCK}: <kbd>{$NUMBER_FORMAT*,{$STOCK_CHECK,{ID}}}</kbd>{$,Stock level}</p>
			{+END}

			{CART_BUTTONS}
		</div>
	</div></div>

	<div itemscope="itemscope" itemtype="http://schema.org/WebPage">
		{$REVIEW_STATUS,catalogue_entry,{ID}}

		{+START,IF,{$CONFIG_OPTION,show_content_tagging}}{TAGS}{+END}

		{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
		{+START,INCLUDE,STAFF_ACTIONS}
			1_URL={EDIT_URL*}
			1_TITLE={!EDIT_LINK}
			1_ACCESSKEY=q
			1_REL=edit
			1_ICON=menu/_generic_admin/edit_this
		{+END}

		<div class="content_screen_comments">
			{COMMENT_DETAILS}
		</div>
	</div>

	{+START,IF,{$CONFIG_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}
</div>
