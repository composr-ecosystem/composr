{$REQUIRE_CSS,news}
{$REQUIRE_JAVASCRIPT,core_rich_media}

{$,Try and set to year of currently viewed item or otherwise the current year}
{$SET,news_archive_year,{$?,{$IS_EMPTY,{$_GET,year}},{$?,{$IS_EMPTY,{$METADATA,created}},{$FROM_TIMESTAMP,Y},{$PREG_REPLACE,-.*$,,{$METADATA,created}}},{$_GET,year}}}

<section class="box box---block-side-news-archive" data-toggleable-tray="{ accordion: true }">
	<div class="box-inner">
		<h3>{TITLE*}</h3>

		<ul class="compact-list">
			{+START,LOOP,YEARS}
				{$SET,is_current_year,{$EQ,{YEAR},{$GET,news_archive_year}}}

				{+START,IF_NON_EMPTY,{TIMES}}
					<li class="accordion-trayitem js-tray-accordion-item">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{$?,{$GET,is_current_year},{!CONTRACT},{!EXPAND}}">{+START,INCLUDE,ICON}
							NAME=trays/{$?,{$GET,is_current_year},contract,expand}
							ICON_SIZE=20
						{+END}</a>

						<span class="js-tray-onclick-toggle-accordion"><strong>{YEAR*}</strong></span>:

						<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body"{+START,IF,{$NOT,{$GET,is_current_year}}} style="display: none"{+END}>
							<ul class="compact-list associated-details">
								{+START,LOOP,TIMES}
									<li>
										<a href="{URL*}">{MONTH_STRING*}</a>
									</li>
								{+END}
							</ul>
						</div>
					</li>
				{+END}
			{+END}
		</ul>
	</div>
</section>
