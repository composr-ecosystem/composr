{+START,IF_NON_PASSED,CATEGORY}
	{+START,IF,{$AND,{$NOT,{MONEY_INVOLVED}},{POINTS_INVOLVED}}}
		{$PARAGRAPH,{!ECOM_PRODUCTS_INTRO_POINTS_ONLY,{$USERNAME*,{$MEMBER},1},{$AVAILABLE_POINTS*}}}
	{+END}
	{+START,IF,{$AND,{MONEY_INVOLVED},{POINTS_INVOLVED}}}
		{$PARAGRAPH,{!ECOM_PRODUCTS_INTRO_BOTH,{$USERNAME*,{$MEMBER},1},{$AVAILABLE_POINTS*}}}
	{+END}
	{+START,IF,{$AND,{MONEY_INVOLVED},{$NOT,{POINTS_INVOLVED}}}}
		{$PARAGRAPH,{!ECOM_PRODUCTS_INTRO_MONEY_ONLY,{$USERNAME*,{$MEMBER},1},{$AVAILABLE_POINTS*}}}
	{+END}
{+END}

{$SET,has_products,0}
{+START,IF_NON_EMPTY,{PRODUCTS}}
	<div itemprop="significantLinks">
		{+START,LOOP,PRODUCTS}
			{+START,IF,{$NEQ,{NUM_PRODUCTS_IN_CATEGORY},0}}{+START,IF,{CAN_PURCHASE}}
				<div class="ecom-product">
					<div class="box box---ecom-purchase-stage-choose"><div class="box-inner">
						<h2>{ITEM_NAME*}</h2>

						<div class="clearfix">
							{+START,IF_NON_EMPTY,{IMAGE_URL}}
								<img width="48" height="48" src="{$THUMBNAIL*,{IMAGE_URL},48x48}" alt="" class="right float-separation" />
							{+END}

							{+START,IF_NON_EMPTY,{DESCRIPTION}}
								<p>
									{DESCRIPTION}
								</p>
							{+END}

							{+START,IF_PASSED,WRITTEN_PRICE}
								<p>
									<span class="field-name">{!PRICE}</span>: {WRITTEN_PRICE*}
								</p>
							{+END}

							{+START,IF_PASSED,URL}
								<ul class="horizontal-links associated-links-block-group">
									<li><a title="{!CHOOSE}: {ITEM_NAME*}" href="{URL*}">{!CHOOSE}</a></li>
								</ul>
							{+END}
						</div>
					</div></div>
				</div>

				{$SET,has_products,1}
			{+END}{+END}
		{+END}
	</div>
{+END}
{+START,IF,{$NOT,{$GET,has_products}}}
	<p class="nothing-here">
		{!NO_CATEGORIES}
	</p>
{+END}
