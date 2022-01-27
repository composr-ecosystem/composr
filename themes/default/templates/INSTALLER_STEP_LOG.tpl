<p>
	{!INSTALL_LOG_BELOW,{CURRENT_STEP*}}:
</p>

<div class="actions_list installer_main_min">
	<div class="install_log_table">
		<p class="lonely_label">{!INSTALL_LOG}:</p>
		<ul>
			{LOG}
		</ul>
	</div>
</div>

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" autocomplete="off">
	<div>
		{HIDDEN}

		<p class="proceed_button">
			<input id="proceed_button" class="button_screen buttons__proceed" type="submit" value="{!PROCEED}" />
		</p>
	</div>
</form>

<script>// <![CDATA[
	/* Code to auto-submit the form after 5 seconds, but only if there were no errors */
	var ps=document.getElementsByTagName('p');
	var doh={$?,{$_GET,keep_show_timings},true,false};
	for (var i=0;i<ps.length;i++)
	{
		if (ps[i].className=='installer_warning') doh=true;
	}
	if (!doh)
	{
		var button=document.getElementById('proceed_button');
		button.countdown=6;
		var timer;
		var continue_func=function()
			{
				button.value="{!PROCEED} ({!AUTO_IN} "+button.countdown+")";
				if (button.countdown==0)
				{
					if (timer) window.clearInterval(timer); timer=null;
					button.form.submit();
				} else
				{
					button.countdown--;
				}
			};
		continue_func();
		timer=window.setInterval(continue_func, 1000);
		button.onmouseover=function() { if (timer) window.clearInterval(timer); timer=null; };
		window.onunload=function() { if (timer) window.clearInterval(timer); timer=null; };
		button.onmouseout=function() { timer=window.setInterval(continue_func, 1000); };
	}
//]]></script>
