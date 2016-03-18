<section id="tray_{!STAFF_ACTIONS|}" class="box box___block_main_staff_actions">
	<h3 class="toggleable_tray_title">
		<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode,false,'{!STAFF_ACTIONS|}');"><img alt="{!CONTRACT}: {$STRIP_TAGS,{!STAFF_ACTIONS}}" title="{!CONTRACT}" src="{$IMG*,1x/trays/contract2}" srcset="{$IMG*,2x/trays/contract2} 2x" /></a>

		<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode,false,'{!STAFF_ACTIONS|}');">{!STAFF_ACTIONS}</a>
	</h3>

	<div class="toggleable_tray">
		{$SET,wrapper_id,ajax_block_wrapper_{$RAND%}}
		<div id="{$GET*,wrapper_id}">
			{CONTENT}

			{$REQUIRE_JAVASCRIPT,ajax}
			{$REQUIRE_JAVASCRIPT,checking}

			<script>// <![CDATA[
				add_event_listener_abstract(window,'load',function() {
					internalise_ajax_block_wrapper_links('{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS}}',document.getElementById('{$GET;,wrapper_id}'),['.*'],{ },false,true);
				});
			//]]></script>
		</div>
	</div>
</section>
