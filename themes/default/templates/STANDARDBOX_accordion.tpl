{$REQUIRE_JAVASCRIPT,core_rich_media}

<div class="box box---standardbox-accordion accordion-trayitem js-tray-accordion-item{+START,IF_PASSED,CLASS} {CLASS*}{+END}"{+START,IF_NON_EMPTY,{WIDTH}} style="width: {WIDTH*}"{+END}>
	<div class="box-inner">
		{+START,IF_NON_EMPTY,{TITLE}}
			<h3 class="toggleable-tray-title js-tray-onclick-toggle-accordion">
				{+START,IF_NOT_IN_ARRAY,OPTIONS,tray_open}
					<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
						NAME=trays/expand
						ICON_SIZE=24
					{+END}</a>
				{+END}
				{+START,IF_IN_ARRAY,OPTIONS,tray_open}
					<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
						NAME=trays/contract
						ICON_SIZE=24
					{+END}</a>
				{+END}

				{+START,IF_NON_EMPTY,{TOP_LINKS}}
					{TOP_LINKS}
				{+END}

				<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">{TITLE}</a>
			</h3>
		{+END}

		<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body"{+START,IF_NOT_IN_ARRAY,OPTIONS,tray_open} style="display: none"{+END}>
			{+START,IF_NON_EMPTY,{META}}
				<div class="meta-details" role="note">
					<dl class="meta-details-list">
						{+START,LOOP,META}
							<dt class="field-name">{KEY}:</dt> <dd>{VALUE}</dd>
						{+END}
					</dl>
				</div>
			{+END}

			{$PARAGRAPH,{CONTENT}}

			{+START,IF_NON_EMPTY,{LINKS}}
				<ul class="horizontal-links associated-links-block-group">
					{+START,LOOP,LINKS}
						<li>{_loop_var}</li>
					{+END}
				</ul>
			{+END}
		</div>
	</div>
</div>
