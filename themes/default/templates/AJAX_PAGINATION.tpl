{$REQUIRE_JAVASCRIPT,ajax}

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		internalise_ajax_block_wrapper_links('{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS;/}{+END}&page={$PAGE&}&zone={$ZONE&}',document.getElementById('{WRAPPER_ID%}'),['[^_]*_start','[^_]*_max'], { });

		{$,Infinite scrolling hides the pagination when it comes into view, and auto-loads the next link, appending below the current results}
		{+START,IF,{$AND,{ALLOW_INFINITE_SCROLL},{$NEQ,{$_GET,keep_infinite_scroll},0}}}
			{+START,IF,{$CONFIG_OPTION,infinite_scrolling}}
				var infinite_scrolling_{WRAPPER_ID%}=function (event) {
					var url_stem='{$FACILITATE_AJAX_BLOCK_CALL;,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS;/}{+END}';
					var wrapper=document.getElementById('{WRAPPER_ID%}');
					internalise_infinite_scrolling(url_stem,wrapper);
				};
				add_event_listener_abstract(window,'scroll',infinite_scrolling_{WRAPPER_ID%});
				add_event_listener_abstract(window,'touchmove',infinite_scrolling_{WRAPPER_ID%});
				add_event_listener_abstract(window,'keydown',infinite_scrolling_block);
				add_event_listener_abstract(window,'mousedown',infinite_scrolling_block_hold);
				add_event_listener_abstract(window,'mousemove',function() { infinite_scrolling_block_unhold(infinite_scrolling_{WRAPPER_ID%}); }); // mouseup/mousemove does not work on scrollbar, so best is to notice when mouse moves again (we know we're off-scrollbar then)
				infinite_scrolling_{WRAPPER_ID%}();
			{+END}
		{+END}
	});
//]]></script>
