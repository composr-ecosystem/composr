{$SET,RAND_ID_OVERLAY,overlay_{$RAND}}

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		{+START,IF_NON_EMPTY,{ID}}
		if (read_cookie('og_{ID;/}')!='1')
		{
		{+END}
			window.setTimeout(function() {
				var bi=document.getElementById('main_website_inner');
				smooth_scroll(0);
				var element=document.getElementById('{$GET;/,RAND_ID_OVERLAY}');
				element.style.display='block';
				if (bi) element.style.left=({X%}+find_pos_x(bi,true)) + 'px';
				element.parentNode.removeChild(element);
				document.body.appendChild(element);
				if (bi) set_opacity(bi,0.4);
				if (typeof window.fade_transition!='undefined') {
					set_opacity(element,0.0);
					fade_transition(element,100,30,3);
				}
				if ({TIMEOUT%}!=-1) window.setTimeout(function() {
					if (bi) set_opacity(bi,1.0);
					if (element) element.style.display='none';
				}, {TIMEOUT%});
			} , {TIMEIN%}+100);
		{+START,IF_NON_EMPTY,{ID}}
		}
		{+END}
	});
//]]></script>

<div role="dialog" class="comcode_overlay box" id="{$GET,RAND_ID_OVERLAY}" style="display: none; position: absolute; left: {X*}px; top: {Y*}px; width: {WIDTH*}px; height: {HEIGHT*}px"><div class="comcode_overlay_inner box_inner">
	<div class="comcode_overlay_main">
		{EMBED}
	</div>

	<div class="comcode_overlay_dismiss">
		<p class="associated_link suggested_link"><a href="#" onclick="var bi=document.getElementById('main_website_inner'); if (bi) set_opacity(bi,1.0); document.getElementById('{$GET,RAND_ID_OVERLAY}').style.display='none'; if ('{ID;*}'!='') set_cookie('og_{ID;*}','1',365); return false;">{!DISMISS}</a></p>
	</div>
</div></div>
