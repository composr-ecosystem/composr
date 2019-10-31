{TITLE}

<p>{!SUPPORT_SEARCH_FAQ}</p>

{RESULTS}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{$INSERT_SPAMMER_BLACKHOLE}

	<input type="hidden" name="faq_searched" value="1" />

	{POST_FIELDS}

	<p class="proceed-button">
		<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--send" type="submit">{+START,INCLUDE,ICON}NAME=buttons/send{+END} {!MAKE_POST}</button>
	</p>
</form>
