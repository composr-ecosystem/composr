{$SET,name_set_elsewhere,1}

<div class="vcard member_profile_screen" itemscope="itemscope" itemtype="http://schema.org/ProfilePage">
	{TITLE}

	<!-- Member: #{MEMBER_ID%} -->

	{+START,IF,{$GT,{TABS},1}}
		<div class="float_surrounder"><div class="tabs" role="tablist">
			{+START,LOOP,TABS}
				<a aria-controls="g_{TAB_CODE*}" role="tab" href="#" id="t_{TAB_CODE*}" class="tab{+START,IF,{TAB_FIRST}} tab_active tab_first{+END}{+START,IF,{TAB_LAST}} tab_last{+END}" onclick="event.returnValue=false; select_tab('g','{TAB_CODE;*}'); return false;">{+START,IF_NON_EMPTY,{TAB_ICON}}<img alt="" src="{$IMG*,icons/24x24/{TAB_ICON}}" srcset="{$IMG*,icons/48x48/{TAB_ICON}} 2x" /> {+END}<span>{TAB_TITLE*}</span></a>
			{+END}
		</div></div>
		<div class="tab_surround">
			{+START,LOOP,TABS}
				<div aria-labeledby="t_{TAB_CODE*}" role="tabpanel" id="g_{TAB_CODE*}" style="display: {$?,{$OR,{TAB_FIRST},{$NOT,{$JS_ON}}},block,none}">
					<a id="tab__{TAB_CODE*}"></a>

					{+START,IF_PASSED,TAB_CONTENT}
						{TAB_CONTENT}
					{+END}

					{+START,IF_NON_PASSED,TAB_CONTENT}
						<p class="ajax_loading"><img class="vertical_alignment" src="{$IMG*,loading}" /></p>

						<script>// <![CDATA[
							function load_tab__{TAB_CODE%}(automated)
							{
								if (typeof window['load_tab__{TAB_CODE%}'].done!='undefined' && window['load_tab__{TAB_CODE%}'].done) return;

								if (automated)
								{
									window.setTimeout(function() {
										try { window.scrollTo(0,0); } catch (e) {};
									}, 25);
								}

								// Self destruct loader after this first run
								window['load_tab__{TAB_CODE%}'].done=true;

								load_snippet('profile_tab&tab={TAB_CODE%}&member_id={MEMBER_ID%}'+window.location.search.replace('?','&'),null,function(result) {
									// Give DOM some time to load, and protect against errors
									window.setTimeout(function() {
										find_url_tab();
									},0);

									set_inner_html(document.getElementById('g_{TAB_CODE%}'),result.responseText);
								});
							}
						//]]></script>
					{+END}
				</div>
			{+END}
		</div>
	{+END}

	{+START,IF,{$EQ,{TABS},1}}
		{+START,LOOP,TABS}
			{+START,IF_PASSED,TAB_CONTENT}
				{TAB_CONTENT}
			{+END}
		{+END}
	{+END}
</div>

{+START,IF,{$GT,{TABS},1}}
	<script>// <![CDATA[
		// we do not want it to scroll down
		var old_hash=window.location.hash;
		window.location.hash='#';

		add_event_listener_abstract(window,'load',function() {
			window.location.hash='#';

			find_url_tab(old_hash);
		});
	//]]></script>
{+END}
