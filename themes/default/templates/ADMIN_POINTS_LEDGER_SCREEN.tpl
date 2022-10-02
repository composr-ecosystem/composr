{TITLE}

{+START,IF_PASSED,TEXT}
	{$PARAGRAPH,{TEXT}}
{+END}

{+START,IF_NON_EMPTY,{FILTERS_ROW_A}{FILTERS_ROW_B}}
	<div class="box advanced-ledger-search"><div class="box-inner">
		<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{URL}}" target="_self" method="get">

			<div class="search-fields clearfix">
				<div class="filter-inputs">
					{+START,LOOP,FILTERS_ROW_A}
						<div class="filter-input">
							<label for="{PARAM*}">{LABEL*}</label>
							{FIELD}
						</div>
					{+END}
				</div>
			</div>

			{+START,IF_NON_EMPTY,{FILTERS_ROW_B}}
				<div class="search-fields clearfix">
					<div class="filter-inputs">
						{+START,LOOP,FILTERS_ROW_B}
							<div class="filter-input">
								<label for="{PARAM*}">{LABEL*}</label>
								{FIELD}
							</div>
						{+END}
					</div>
				</div>
			{+END}

			<div class="filter-inputs">
				<div class="search-button filter-input">
					<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scri buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
				</div>
				<div class="search-button">
						<button data-cms-href="{$PAGE_LINK*,_SELF:_SELF}" class="btn btn-primary btn-scri buttons--clear" type="button">{+START,INCLUDE,ICON}NAME=buttons/clear{+END} {!RESET_FILTER}</button>
				</div>
			</div>
		</form>
	</div></div>
{+END}

{RESULTS_TABLE}

{+START,IF_PASSED,FORM}
	{FORM}
{+END}
