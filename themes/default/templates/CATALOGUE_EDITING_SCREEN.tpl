{TITLE}

{$PARAGRAPH,{TEXT}}

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" target="_top" id="catalogue_form" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		{HIDDEN}

		<div class="wide_table_wrap"><table class="map_table form_table wide_table scrollable_inside">
			{+START,IF,{$NOT,{$MOBILE}}}
				<colgroup>
					<col class="field_name_column" />
					<col class="field_input_column" />
				</colgroup>
			{+END}

			<tbody>
				{FIELDS}
			</tbody>
		</table></div>

		<h2>{!FIELDS_EXISTING}</h2>
		{FIELDS_EXISTING}

		<h2>{!FIELDS_NEW}</h2>
		<p>{!FIELDS_NEW_HELP}</p>
		{FIELDS_NEW}

		<script>// <![CDATA[
			add_event_listener_abstract(window,'load',function() {
				catalogue_field_change_watching();
			});
		//]]></script>

		{+START,INCLUDE,FORM_STANDARD_END}
			FORM_NAME=catalogue_form
			SUPPORT_AUTOSAVE=1
		{+END}
	</div>
</form>
