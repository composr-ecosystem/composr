{$REQUIRE_JAVASCRIPT,checking}
<div class="box-wrapper" data-ajaxify="{ callUrl: '{$FACILITATE_AJAX_BLOCK_CALL;*,{BLOCK_PARAMS}}', callParamsFromTarget: ['^staff_tips_dismiss$', '^rand$'] }">
	<section id="tray-{!TIPS|}" class="box box---block-main-staff-tips" data-toggleable-tray="{ save: true }">
		<div class="box-inner">
			<h3 class="toggleable-tray-title js-tray-header">
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">
					{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
					{+END}
				</a>
				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!TIPS}</a>
			</h3>

			<div class="toggleable-tray js-tray-content">
				<p>
					{TIP}
				</p>

				<div class="tips-trail">
					{+START,IF_NON_EMPTY,{TIP_CODE}}
						<ul class="horizontal-links associated-links-block-group">
							<li><a data-ajaxify-target="1" href="{$PAGE_LINK*,adminzone:staff_tips_dismiss={TIP_CODE}}">{!DISMISS_TIP}</a></li>
							{+START,IF,{$NEQ,{TIP_CODE},0a}}
								<li><a data-ajaxify-target="1" accesskey="k" href="{$PAGE_LINK*,adminzone:rand={$RAND}}">{!ANOTHER_TIP}</a></li>
							{+END}
						</ul>
					{+END}
				</div>
			</div>
		</div>
	</section>
</div>
