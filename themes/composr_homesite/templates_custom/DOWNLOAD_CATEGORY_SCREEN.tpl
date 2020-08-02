{$REQUIRE_JAVASCRIPT,jquery.stepcarousel}

{TITLE}

{+START,IF_NON_EMPTY,{DESCRIPTION}}
	<div itemprop="description">
		{DESCRIPTION}
	</div>
{+END}

{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,download_category,{ID}}}
{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

{+START,IF_NON_EMPTY,{SUBCATEGORIES}}
	<div class="float_surrounder">
		{SUBCATEGORIES}
	</div>
{+END}

{+START,IF_NON_EMPTY,{DOWNLOADS}}
	<div class="cntRow">
		{DOWNLOADS}
	</div>
{+END}

<div class="right">
	{+START,INCLUDE,NOTIFICATION_BUTTONS}
		NOTIFICATIONS_TYPE=download
		NOTIFICATIONS_ID={ID}
	{+END}
</div>

<div class="box category_sorter inline_block"><div class="box_inner">
	{$SET,show_sort_button,1}
	{SORTING}
</div></div>

{+START,IF,{$CONFIG_OPTION,show_content_tagging}}{TAGS}{+END}

{$REVIEW_STATUS,download_category,{ID}}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={SUBMIT_URL*}
	1_TITLE={!ADD_DOWNLOAD}
	1_REL=add
	1_ICON=menu/_generic_admin/add_one
	2_URL={ADD_CAT_URL*}
	2_TITLE={!ADD_DOWNLOAD_CATEGORY}
	2_REL=add
	2_ICON=menu/_generic_admin/add_one_category
	3_ACCESSKEY=q
	3_URL={EDIT_CAT_URL*}
	3_TITLE={!EDIT_DOWNLOAD_CATEGORY}
	3_REL=edit
	3_ICON=menu/_generic_admin/edit_this_category
{+END}

{+START,IF,{$CONFIG_OPTION,show_screen_actions}}{+START,IF_PASSED,_TITLE}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}{+END}

{+START,IF,{$NOT,{$MOBILE}}}
	{+START,IF,{$AND,{$PREG_MATCH,^Version [\d\.]+,{$METADATA,title}},{$IN_STR,{$BREADCRUMBS},Addons}}}
		<div class="cntRow">
			<div class="ftHolder headFT">
				Top Rated Addons:
			</div>

			{$BLOCK,block=main_multi_content,param=download,select={ID}*,efficient=0,zone=_SELF,sort=average_rating DESC,max=10,no_links=1,guid=top_downloads,quick_cache=1}

			<script>// <![CDATA[
				$(document).ready(function() {
					stepcarousel.setup({
						galleryid: 'downloads_gallery', //id of carousel DIV
						beltclass: 'belt', //class of inner "belt" DIV containing all the panel DIVs
						panelclass: 'panel', //class of panel DIVs each holding content
						autostep: {enable:true, moveby:1, pause:3000},
						panelbehavior: {speed:500, wraparound:false, wrapbehavior:'slide', persist:true},
						defaultbuttons: {enable: true, moveby: 1, leftnav: ['{$IMG;,composr_homesite/downloads/left-arrow}', -40, 70], rightnav: ['{$IMG;,composr_homesite/downloads/right-arrow}', 10, 70]},
						statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
						contenttype: ['inline'] //content setting ['inline'] or ['ajax', 'path_to_external_file']
					})
				});
			//]]></script>
		</div>
	{+END}
{+END}

{+START,INCLUDE,_COMMUNITY_BANNERS}{+END}
