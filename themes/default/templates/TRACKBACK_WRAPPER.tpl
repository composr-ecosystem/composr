<section class="box box___trackback_wrapper"><div class="box_inner">
	<h3>{!TRACKBACKS}</h3>

	<!--
	<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
		<rdf:Description rdf:about="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" dc:identifier="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" trackback:ping="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" />
	</rdf:RDF>
	-->
	<!--dc:title="{TRACKBACK_TITLE*}" -->

	{+START,IF_NON_EMPTY,{TRACKBACKS}}
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,_SEARCH:admin_trackbacks}}
		<form title="{!TRACKBACKS}" action="{$PAGE_LINK*,_SEARCH:admin_trackbacks:delete:redirect={$SELF_URL}}" method="post" autocomplete="off">
			{$INSERT_SPAMMER_BLACKHOLE}
		{+END}
		{TRACKBACKS}
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,_SEARCH:admin_trackbacks}}
			<p class="proceed_button">
				<input onclick="disable_button_just_clicked(this);" class="button_screen menu___generic_admin__delete" type="submit" value="{!MANAGE_TRACKBACKS}" />
			</p>
		</form>
		{+END}
	{+END}
	{+START,IF_EMPTY,{TRACKBACKS}}
		<p class="nothing_here">{!NO_TRACKBACKS}</p>
	{+END}

	{+START,IF,{$JS_ON}}
		<ul class="associated_links_block_group horizontal_links">
			<li><a rel="nofollow" onclick="window.fauxmodal_alert('{!DONT_CLICK_TRACKBACK=;}'); return false;" href="{$FIND_SCRIPT*,trackback}?feedback_type={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}&amp;time={$FROM_TIMESTAMP}">{!TRACKBACK_LINK}</a></li>
		</ul>
	{+END}
</div></section>
