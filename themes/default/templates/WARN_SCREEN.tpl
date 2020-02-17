{TITLE}

{$REQUIRE_CSS,messages}

{$SET,show_sitemap,{$IN_STR,{TEXT},{!MISSING_RESOURCE_SUBSTRING}}}

{+START,IF,{$NOT,{$GET,show_sitemap}}}
	{+START,IF_PASSED,WEBSERVICE_RESULT}
		<div class="box box___warn_screen"><div class="box_inner">
			{TEXT*}
		</div></div>

		<div class="ssm_warn expanded_advice">
			<h2>Expanded advice</h2>

			{WEBSERVICE_RESULT}
		</div>
	{+END}
	{+START,IF_NON_PASSED,WEBSERVICE_RESULT}
		<div class="site_special_message ssm_warn" role="alert"{+START,IF_PASSED,IMAGE_URL} style="background-image: url('{IMAGE_URL;*}');"{+END}>
			<div class="site_special_message_inner">
				<div class="box box___warn_screen"><div class="box_inner">
					{TEXT*}
				</div></div>
			</div>
		</div>
	{+END}
{+END}
{+START,IF,{$GET,show_sitemap}}
	<p class="red_alert" role="error">{TEXT*}</p>

	<h2>{!SITEMAP}</h2>

	{$REQUIRE_CSS,menu__sitemap}
	{$REQUIRE_JAVASCRIPT,menu_sitemap}
	{$BLOCK-,block=menu,param=\,use_page_groupings=1,type=sitemap,quick_cache=1}

	{+START,IF,{$ADDON_INSTALLED,search}}
		<h2>{!SEARCH}</h2>

		{$BLOCK,block=main_search,failsafe=1}
	{+END}
{+END}

{+START,IF,{PROVIDE_BACK}}{+START,IF,{$JS_ON}}{+START,IF,{$NOT,{$RUNNING_SCRIPT,preview}}}
	<p class="back_button">
		<a href="#" onclick="history.back(); return false;"><img title="{!NEXT_ITEM_BACK}" alt="{!NEXT_ITEM_BACK}" src="{$IMG*,icons/48x48/menu/_generic_admin/back}" /></a>
	</p>
{+END}{+END}{+END}

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		if ((typeof window.trigger_resize!='undefined') && (window.top!=window)) trigger_resize();
	});
//]]></script>
