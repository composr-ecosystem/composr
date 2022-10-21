{+START,IF_PASSED,FILTERS_ROW_A}{+START,IF_PASSED,URL}
	<div class="box box-filter-inputs"><div class="box-inner">
		<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{URL}}" target="_self" method="get">
			{$HIDDENS_FOR_GET_FORM,{URL}}

			{+START,IF_PASSED,FILTERS_HIDDEN}{+START,IF_NON_EMPTY,{FILTERS_HIDDEN}}
				{FILTERS_HIDDEN}
			{+END}{+END}

			{+START,IF_NON_EMPTY,{FILTERS_ROW_A}}
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
			{+END}

			{+START,IF_PASSED,FILTERS_ROW_B}{+START,IF_NON_EMPTY,{FILTERS_ROW_B}}
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
			{+END}{+END}

			<div class="filter-inputs">
				<div class="search-button">
					<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scri buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
				</div>
				<div class="search-button">
					<button data-cms-href="{URL*}" class="btn btn-primary btn-scri buttons--clear" type="button">{+START,INCLUDE,ICON}NAME=buttons/clear{+END} {!RESET_FILTER}</button>
				</div>
			</div>
		</form>
	</div></div>
{+END}{+END}
