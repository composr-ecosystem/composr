{$REQUIRE_JAVASCRIPT,cns_forum}

<td id="cell-mark-{ID*}" class="cns-topic-marker-cell cell-desktop" data-tpl="cnsTopicMarker">
	<form class="inline" title="{!MARKER} #{ID*}" method="post" action="#" id="form-mark-{ID*}">
		{$INSERT_FORM_POST_SECURITY}

		<div class="inline">
			<label for="mark_{ID*}" class="accessibility-hidden">{!MARKER} #{ID*}</label>
			<input value="1" type="checkbox" id="mark_{ID*}" name="mark_{ID*}" class="js-click-checkbox-set-row-mark-class"{+START,IF,{$NOT,{$IS_GUEST}}} title="{!MARKER} #{ID*}"{+END} />
		</div>
	</form>
</td>
