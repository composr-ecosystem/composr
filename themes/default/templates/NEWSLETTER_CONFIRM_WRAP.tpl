{+START,IF,{$NOT,{$VALUE_OPTION,xhtml_strict}}}
	<div class="box box___newsletter_confirm_wrap"><div class="box_inner">
		<h2>{SUBJECT*} &ndash; {!HTML_VERSION}</h2>

		<iframe{$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} id="preview_frame" name="preview_frame" title="{!PREVIEW}" class="hidden_preview_frame" src="{$BASE_URL*}/uploads/index.html">{!PREVIEW}</iframe>

		<noscript>
			{PREVIEW*}
		</noscript>

		<script>// <![CDATA[
			document.getElementById('preview_frame').onload = function() {
				var adjusted_preview='{PREVIEW;^/}'.replace(/<!DOCTYPE[^>]*>/i,'').replace(/<html[^>]*>/i,'').replace(/<\/html>/i,'');
				var de=window.frames['preview_frame'].document.documentElement;
				var body=de.getElementsByTagName('body');
				if (body.length==0)
				{
					set_inner_html(de,adjusted_preview);
				} else
				{
					var head_element=de.getElementsByTagName('head')[0];
					if (!head_element)
					{
						head_element=document.createElement('head');
						de.appendChild(head_element);
					}
					if (de.getElementsByTagName('style').length==0 && adjusted_preview.indexOf('<head')!=-1) {$,The conditional is needed for Firefox - for some odd reason it is unable to parse any head tags twice}
						set_inner_html(head_element,adjusted_preview.replace(/^(.|\n)*<head[^>]*>((.|\n)*)<\/head>(.|\n)*$/i,'$2'));
					set_inner_html(body[0],adjusted_preview.replace(/^(.|\n)*<body[^>]*>((.|\n)*)<\/body>(.|\n)*$/i,'$2'));
				}

				resize_frame('preview_frame',300);
			};
		//]]></script>
	</div></div>

	{+START,IF_NON_EMPTY,{TEXT_PREVIEW}}
		<div class="box box___newsletter_confirm_wrap"><div class="box_inner">
			<h2>{SUBJECT*} &ndash; {!TEXT_VERSION}</h2>

			<div class="whitespace_visible">{TEXT_PREVIEW*}</div>
		</div></div>
	{+END}
{+END}

<p>
	{!NEWSLETTER_CONFIRM_MESSAGE}
</p>
