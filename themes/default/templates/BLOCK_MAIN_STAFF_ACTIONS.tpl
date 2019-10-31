{$REQUIRE_JAVASCRIPT,checking}

{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
<section id="tray-actionlog" data-toggleable-tray="{ save: true }" data-tpl="blockMainStaffActions" class="box box---block-main-staff-actions">
	<div class="box-inner">
		<h3 class="toggleable-tray-title js-tray-header">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
				NAME=trays/contract
				ICON_SIZE=24
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!MODULE_TRANS_NAME_admin_actionlog}</a> <span class="associated-details">(<a title="{!MODULE_TRANS_NAME_admin_actionlog}" href="{$PAGE_LINK*,adminzone:admin_actionlog}">{$LCASE,{!MORE}}</a>)</span>
		</h3>

		<div class="toggleable-tray js-tray-content">
{+END}
			<div data-ajaxify="{ callUrl: '{$FACILITATE_AJAX_BLOCK_CALL;*,{BLOCK_PARAMS},raw=.*\,cache=.*}', callParams: { raw: 1, cache: 0 }, callParamsFromTarget: ['.*'], targetsSelector: 'form.action-log-filters, .results-table-under a, .results-table-under form' }">
				<form title="{!FILTER}" class="action-log-filters" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}#tray-actionlog" method="get" data-ajaxify-target="1">
					{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,0,0,filter_by_member=<null>,include_duplicates=<null>,include_user_activities=<null>}}

					<div class="action-log-filter-part">
						<label for="filter_by_member">{!SHOW_ACTIONS_FOR}</label>:
						<select name="filter_by_member" id="filter_by_member" class="form-control form-control-sm js-onchange-submit-form">
							<option value="0"{+START,IF,{$NOT,{FILTER_BY_MEMBER}}} selected="selected"{+END}>{!ALL_USERS}</option>
							<option value="1"{+START,IF,{FILTER_BY_MEMBER}} selected="selected"{+END}>{!ME_ONLY}</option>
						</select>
					</div>

					<div class="action-log-filter-part">
						<label for="include_duplicates">{!INCLUDE_DUPLICATES}</label>:
						<input type="checkbox" name="include_duplicates" id="include_duplicates" class="js-onchange-submit-form" value="1"{+START,IF,{INCLUDE_DUPLICATES}} checked="checked"{+END} />
					</div>

					<div class="action-log-filter-part">
						<label for="include_user_activities">{!INCLUDE_USER_ACTIVITIES}</label>:
						<input type="checkbox" name="include_user_activities" id="include_user_activities" class="js-onchange-submit-form" value="1"{+START,IF,{INCLUDE_USER_ACTIVITIES}} checked="checked"{+END} />
					</div>

					<button data-disable-on-click="1" accesskey="u" class="accessibility-hidden button-micro buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!FILTER}</button>
				</form>

				{CONTENT}
			</div>

{+START,IF,{$NEQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}
		</div>
	</div>
</section>
{+END}
