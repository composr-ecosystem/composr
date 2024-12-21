<section class="box box---trackback-wrapper"><div class="box-inner">
	<h3>{!TRACKBACKS}</h3>

	<!--
	<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
		<rdf:Description rdf:about="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" dc:identifier="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" trackback:ping="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}" />
	</rdf:RDF>
	-->
	<!--dc:title="{TRACKBACK_TITLE*}" -->

	{+START,IF_NON_EMPTY,{TRACKBACKS}}
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_trackbacks}}
		<form title="{!TRACKBACKS}" action="{$PAGE_LINK*,admin_trackbacks:delete:redirect={$SELF_URL&}}" method="post">
			{$INSERT_FORM_POST_SECURITY}
		{+END}
		{TRACKBACKS}
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_trackbacks}}
			<p class="proceed-button">
				<button data-disable-on-click="1" class="btn btn-danger btn-scr" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} {!MANAGE_TRACKBACKS}</button>
			</p>
		</form>
		{+END}
	{+END}
	{+START,IF_EMPTY,{TRACKBACKS}}
		<p class="nothing-here">{!NO_TRACKBACKS}</p>
	{+END}

	<ul class="associated-links-block-group horizontal-links">
		<li><a rel="nofollow" data-click-pd="1" data-click-alert="{!DONT_CLICK_TRACKBACK=}" href="{$FIND_SCRIPT*,trackback}?page={TRACKBACK_FEEDBACK_TYPE*}&amp;id={TRACKBACK_ID*}&amp;time={$FROM_TIMESTAMP}">{!TRACKBACK_LINK}</a></li>
	</ul>
</div></section>
