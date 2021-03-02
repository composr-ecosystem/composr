{$REQUIRE_JAVASCRIPT,cns_forum}

<td id="cell-mark-{ID*}" class="cns-topic-marker-cell cell-desktop" data-tpl="cnsTopicMarker">
	<form class="webstandards-checker-off inline" title="{!MARKER} #{ID*}" method="post" action="index.php" id="form-mark-{ID*}">
		{$INSERT_FORM_POST_SECURITY}

		<div class="inline">
			{+START,IF,{$NOT,{$IS_GUEST}}}<div class="accessibility-hidden"><label for="mark_{ID*}">{!MARKER} #{ID*}</label></div>{+END}{$,Guests don't see this so search engines don't; hopefully people with screen-readers are logged in}
			<input value="1" type="checkbox" id="mark_{ID*}" name="mark_{ID*}" class="js-click-checkbox-set-row-mark-class"{+START,IF,{$NOT,{$IS_GUEST}}} title="{!MARKER} #{ID*}"{+END} />
		</div>
	</form>
</td>
