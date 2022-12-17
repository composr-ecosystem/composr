{+START,LOOP,NOTIFICATION_TYPES}
	<td data-tpl="notificationTypes_item">
		<div>
			<label for="notification_{SCOPE*}_{NTYPE*}">{LABEL*}</label>

			{+START,IF,{AVAILABLE}}
				<input class="js-click-handle-ntype-tick" data-tp-raw="{RAW%}" title="{LABEL*}"{+START,IF,{CHECKED}} checked="checked"{+END} id="notification_{SCOPE*}_{NTYPE*}" name="notification_{SCOPE*}_{NTYPE*}" type="checkbox" value="1"{+START,IF_PASSED_AND_TRUE,TYPE_HAS_CHILDREN_SET} data-cms-unchecked-is-indeterminate="1"{+END} />
			{+END}

			{+START,IF,{$NOT,{AVAILABLE}}}
				<input title="{LABEL*}" disabled="disabled" id="notification_{SCOPE*}_{NTYPE*}"{+START,IF,{CHECKED}} checked="checked"{+END} name="notification_{SCOPE*}_{NTYPE*}" type="checkbox" value="1" />
			{+END}
		</div>
	</td>
{+END}
