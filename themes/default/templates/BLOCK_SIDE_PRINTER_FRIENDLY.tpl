<div class="box box---block-side-printer-friendly"><div class="box-inner">
	{+START,IF,{$_GET,wide_print}}
		<span class="print-icon">
			{+START,INCLUDE,ICON}
				NAME=links/print
				ICON_SIZE=24
			{+END}
			{!IN_PRINT_MODE}
		</span>
	{+END}
	{+START,IF,{$NOT,{$_GET,wide_print}}}
		<a class="print-icon" rel="print" target="_blank" title="{!PRINTER_FRIENDLY} {!LINK_NEW_WINDOW}" href="{URL*}">{+START,INCLUDE,ICON}NAME=links/print{+END} {!PRINTER_FRIENDLY}</a>
	{+END}
</div></div>
