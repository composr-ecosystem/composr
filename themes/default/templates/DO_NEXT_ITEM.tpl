{$SET,rand_do_next_item,{$RAND}}

<div id="do-next-item-{$GET,rand_do_next_item}" class="do-next-item" data-tpl="doNextItem" data-tpl-params="{+START,PARAMS_JSON,URL,TARGET,WARNING,AUTO_ADD,rand_do_next_item}{_*}{+END}">
	{+START,IF_NON_EMPTY,{DOC}}
		<div id="doc-{$GET,rand_do_next_item}" style="display: none">
			<div class="box box---global-helper-panel--tutorial"><div class="box-inner">
				<div class="global-helper-panel-text">{DOC}</div>
			</div></div>
		</div>
	{+END}

	<div class="do-next-item-icon">
		<a href="{URL*}" class="js-click-confirm-warning"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}>{+START,INCLUDE,ICON}
			NAME={PICTURE}
			ICON_DESCRIPTION={$STRIP_TAGS,{DESCRIPTION}}
			ICON_SIZE=48
		{+END}</a>
	</div>

	<div class="do-next-item-label">
		<a href="{URL*}"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}>{DESCRIPTION*}</a>
	</div>
</div>
