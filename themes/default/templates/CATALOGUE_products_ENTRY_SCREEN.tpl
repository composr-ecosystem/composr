<div itemscope="itemscope" itemtype="http://schema.org/Offer" class="product-view">
	<div class="fn product-name" itemprop="itemOffered">{TITLE}</div>

	{$REQUIRE_CSS,shopping}

	{WARNINGS}

	<div class="cart-info">
		{$SET-,rating,{$RATING,catalogue_entry__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_DYNAMIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			<div class="rating-part">
				<span class="field-name">{!RATING}:</span> {$GET,rating}
			</div>
		{+END}

		{$CART_LINK}
	</div>

	<div class="box box---catalogue-products-entry-screen"><div class="box-inner">
		<div class="hproduct"{$?,{$MATCH_KEY_MATCH,_WILD:_WILD:browse}, itemscope="itemscope" itemtype="http://schema.org/Offer"}>
			<div class="clearfix">
				{+START,IF_NON_EMPTY,{FIELD_7}}
					<p class="catalogue-entry-box-thumbnail">
						{$REPLACE, rel="lightbox", rel="lightbox" itemprop="image",{FIELD_7}}
					</p>
				{+END}

				{+START,IF_NON_EMPTY,{FIELD_9}}
					<div class="description" itemprop="description">
						{FIELD_9}{$,Product description}
					</div>
				{+END}

				{+START,IF_NON_EMPTY,{FIELD_2}}
					<div class="price-box">
						<span class="price">{!PRICE}: <span itemprop="price">{$CURRENCY,{FIELD_2_PLAIN},,{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}</span>{$,Product price}</span>
					</div>
				{+END}
			</div>

			{+START,IF_NON_EMPTY,{$TRIM,{FIELDS}}}
				<table id="product-attribute-specs-table" class="map-table catalogue-fields-table wide-table results-table">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="catalogue-fieldmap-field-name-column" />
							<col class="catalogue-fieldmap-field-value-column" />
						</colgroup>
					{+END}

					<tbody>
						{FIELDS}
					</tbody>
				</table>
			{+END}

			{+START,IF_NON_EMPTY,{FIELD_1}}
				<p class="product-ids sku">{!ECOM_CAT_sku}: <kbd>{FIELD_1}</kbd>{$,Product code}</p>
			{+END}
			{+START,IF_NON_EMPTY,{FIELD_3}}
				<p class="stock-level">{!STOCK}: <kbd>{$INTEGER_FORMAT*,{$STOCK_CHECK,{ID}},0}</kbd>{$,Stock level}</p>
			{+END}

			{CART_BUTTONS}
		</div>
	</div></div>

	<div itemscope="itemscope" itemtype="http://schema.org/WebPage">
		{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

		{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

		{$REVIEW_STATUS,catalogue_entry,{ID}}

		{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
		{+START,INCLUDE,STAFF_ACTIONS}
			1_URL={EDIT_URL*}
			1_TITLE={!EDIT_LINK}
			1_ACCESSKEY=q
			1_REL=edit
			1_ICON=admin/edit_this
			{+START,IF,{$ADDON_INSTALLED,tickets}}
				2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=catalogue_entry:content_id={ID}:redirect={$SELF_URL&}}
				2_TITLE={!report_content:REPORT_THIS}
				2_ICON=buttons/report
				2_REL=report
			{+END}
		{+END}

		<div class="content-screen-comments">
			{COMMENT_DETAILS}
		</div>
	</div>
</div>
