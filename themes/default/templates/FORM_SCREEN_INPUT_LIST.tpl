{+START,IF,{INLINE_LIST}}
<select size="{SIZE*}" tabindex="{TABINDEX*}" class="input_list{REQUIRED*} wide_field" id="{NAME*}" name="{NAME*}">
{+END}
{+START,IF,{$NOT,{INLINE_LIST}}}
<select tabindex="{TABINDEX*}" class="input_list{REQUIRED*}" id="{NAME*}" name="{NAME*}">
{+END}
	{CONTENT}
</select>

{+START,IF,{$NOT,{INLINE_LIST}}}
	<script>// <![CDATA[
		{+START,IF_PASSED,IMAGES}
			function format_select_image(o)
			{
				if (!o.id)
					return o.text; // optgroup
				{+START,LOOP,IMAGES}
				if (o.id=='{_loop_var;/}')
					return '<span class="vertical_alignment inline_lined_up"><img style="width: 24px;" src="{$IMG*;/,{_loop_var}}" \/> '+escape_html(o.text)+'</span>';
				{+END}
				return escape_html(o.text);
			}
		{+END}

		add_event_listener_abstract(window,'load',function() {
			var element=document.getElementById("{NAME#/}");
			if ((element.options.length>20)/*only for long lists*/ && (!get_inner_html(element.options[1]).match(/^\d+$/)/*not for lists of numbers*/))
			{
				if (typeof $(element).select2!='undefined')
				{
					$(element).select2({
						{+START,IF_PASSED,IMAGES}
							formatResult: format_select_image,
						{+END}
						dropdownAutoWidth: window.parent==window, /*Otherwise can overflow*/
						containerCssClass: 'wide_field'
					});
				}
			}
		});
	//]]></script>
{+END}
