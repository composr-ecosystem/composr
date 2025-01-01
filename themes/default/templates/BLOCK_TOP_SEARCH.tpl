{$REQUIRE_JAVASCRIPT,search}
{$REQUIRE_JAVASCRIPT,checking}

<form class="top-button-wrapper" role="search" title="{TITLE*}" data-tpl="blockTopSearch" data-tpl-params="{+START,PARAMS_JSON,SEARCH_TYPE}{_*}{+END}" action="{$URL_FOR_GET_FORM*,{URL}}" method="get">
	{$HIDDENS_FOR_GET_FORM,{URL}}

	{+START,LOOP,LIMIT_TO}
		<input type="hidden" name="{_loop_var*}" value="1" />
	{+END}
	{+START,LOOP,EXTRA}
		<input type="hidden" name="{_loop_key*}" value="{_loop_var*}" />
	{+END}
	{+START,IF_NON_EMPTY,{AUTHOR}}<input type="hidden" name="author" value="{AUTHOR*}" />{+END}
	{+START,IF,{$NEQ,{DAYS},-1}}<input type="hidden" name="days" value="{DAYS*}" />{+END}
	{+START,IF,{$NEQ,{SORT},relevance}}<input type="hidden" name="sort" value="{SORT*}" />{+END}
	{+START,IF,{$NEQ,{DIRECTION},DESC}}<input type="hidden" name="direction" value="{DIRECTION*}" />{+END}
	{+START,IF,{$NEQ,{ONLY_TITLES},0}}<input type="hidden" name="only_titles" value="{ONLY_TITLES*}" />{+END}
	{+START,IF,{$NEQ,{ONLY_SEARCH_META},0}}<input type="hidden" name="only_search_meta" value="{ONLY_SEARCH_META*}" />{+END}

	<a accesskey="=" id="top-search-button" class="top-button top-button-search js-click-toggle-button-popup" href="#!">{+START,INCLUDE,ICON}
		NAME=buttons/search
		ICON_SIZE=24
	{+END}</a>

	<div class="top-button-popup" id="top-search-rel" style="display: none">
		<div class="box box-arrow box--block-top-personal-stats">
			<div class="box-inner">
				<label class="accessibility-hidden" for="top-search-content-{BLOCK_ID%}">{!SEARCH}</label>
				<div class="input-group">
					<input {+START,IF,{$MOBILE}} autocorrect="off"{+END} size="{$?,{!takes_lots_of_space},10,20}" maxlength="255" class="form-control form-control-sm js-input-keyup-update-ajax-search-list" type="search" id="top-search-content-{BLOCK_ID%}" name="content" />
					<div class="input-group-append">
						<button class="btn btn-primary btn-sm buttons--search" type="submit">{+START,INCLUDE,ICON}NAME=buttons/search{+END} <span>{!SEARCH}</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
