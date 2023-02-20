<div aria-busy="true" class="spaced" id="loading_space">
	<div class="ajax_loading vertical_alignment">
		<img src="{$IMG*,loading}" title="{!LOADING}" alt="{!LOADING}" />
		<span>{!LOADING}</span>
	</div>
</div>

<script>// <![CDATA[
	var element;
	var target_window=window.opener?window.opener:window.parent;
	element=target_window.document.getElementById('{FIELD_NAME;/}');
	if (!element)
	{
		var overlay=target_window.document.getElementById('overlay_iframe');
		if (overlay)
		{
			target_window=overlay.contentWindow;
			element=target_window.document.getElementById('{FIELD_NAME;/}');
		}
	}
	if (!element)
	{
		target_window=target_window.frames['iframe_page'];
		element=target_window.document.getElementById('{FIELD_NAME;/}');
	}
	element=ensure_true_id(element,'{FIELD_NAME;/}');
	var is_wysiwyg=target_window.is_wysiwyg_field(element);

	var comcode,comcode_semihtml;
	comcode='{COMCODE;^/}';
	window.returnValue=comcode;
	comcode_semihtml='{COMCODE_SEMIHTML;^/}';

	var loading_space=document.getElementById('loading_space');

	function shutdown_overlay()
	{
		var win=window;

		window.setTimeout(function() { // Close master window in timeout, so that this will close first (issue on Firefox) / give chance for messages
			if (typeof win.faux_close!='undefined')
				win.faux_close();
			else
				win.close();
		}, 200);
	}

	function dispatch_block_helper()
	{
		var win=window;
		if ('{SAVE_TO_ID;/}'!='')
		{
			var ob=target_window.wysiwyg_editors[element.id].document.$.getElementById('{$_GET%,save_to_id}');

			{+START,IF,{DELETE}}
				ob.parentNode.removeChild(ob);
			{+END}
			{+START,IF,{$NOT,{DELETE}}}
				var input_container=document.createElement('div');
				set_inner_html(input_container,comcode_semihtml.replace(/^\s*/,''));
				ob.parentNode.replaceChild(input_container.childNodes[0],ob);
			{+END}

			target_window.wysiwyg_editors[element.id].updateElement();

			shutdown_overlay();
		} else
		{
			var message='';
			if (comcode.indexOf('[attachment')!=-1)
			{
				if (comcode.indexOf('[attachment_safe')!=-1)
				{
					if (is_wysiwyg)
					{
						message='';//'{!ADDED_COMCODE_ONLY_SAFE_ATTACHMENT_INSTANT;}'; Not really needed
					} else
					{
						message='{!ADDED_COMCODE_ONLY_SAFE_ATTACHMENT;}';
					}
				} else
				{
					//message='{!ADDED_COMCODE_ONLY_ATTACHMENT;}';	Kind of states the obvious
				}
			} else
			{
				//message='{!ADDED_COMCODE_ONLY;}';	Kind of states the obvious
			}

			target_window.insert_comcode_tag=function(rep_from,rep_to,ret) { // We define as a temporary global method so we can clone out the tag if needed (e.g. for multiple attachment selections)
				var _comcode_semihtml=comcode_semihtml;
				var _comcode=comcode;
				if (typeof rep_from!='undefined')
				{
					for (var i=0;i<rep_from.length;i++)
					{
						_comcode_semihtml=_comcode_semihtml.replace(rep_from[i],rep_to[i]);
						_comcode=_comcode.replace(rep_from[i],rep_to[i]);
					}
				}

				if (typeof ret!='undefined' && ret)
				{
					return [_comcode_semihtml,_comcode];
				}

				if ((element.value.indexOf(comcode_semihtml)==-1) || (comcode.indexOf('[attachment')==-1)) // Don't allow attachments to add twice
				{
					target_window.insert_textbox(element,_comcode,target_window.document.selection?target_window.document.selection:null,true,_comcode_semihtml);
				}
			};
			{+START,IF_PASSED,PREFIX}
				target_window.insert_textbox(element,'{PREFIX;/}',target_window.document.selection?target_window.document.selection:null,true);
			{+END}
			target_window.insert_comcode_tag();

			if (target_window.is_wysiwyg_field(element))
			{
				target_window.generate_background_preview(element);
			}

			if (message!='')
			{
				window.fauxmodal_alert(
					message,
					function() {
						shutdown_overlay();
					}
				);
			} else
			{
				shutdown_overlay();
			}
		}
	}

	var attached_event_action=false;

	{$,WYSIWYG-editable attachments must be synched}
	{+START,IF,{$EQ,{BLOCK},attachment_safe}}
		{+START,IF,{$PREG_MATCH,^new_\d+$,{TAG_CONTENTS}}}
			var field='file{$SUBSTR;/,{TAG_CONTENTS},4}';
			var upload_element=target_window.document.getElementById(field);
			if (!upload_element) upload_element=target_window.document.getElementById('hidFileID_'+field);
			if ((typeof upload_element.plupload_object!='undefined') && (is_wysiwyg))
			{
				var ob=upload_element.plupload_object;
				if (ob.state==target_window.plupload.STARTED)
				{
					ob.bind('UploadComplete',function() { window.setTimeout(dispatch_block_helper,100);/*Give enough time for everything else to update*/ });
					ob.bind('Error',shutdown_overlay);

					// Keep copying the upload indicator
					var image_progress_element=target_window.document.getElementById('fsUploadProgress_'+field);
					if (image_progress_element)
					{
						var progress=get_inner_html(image_progress_element);
						window.setInterval(function() {
							if (progress!='')
							{
								set_inner_html(loading_space,progress);
								loading_space.className='spaced flash';
							}
						},100);
					}

					attached_event_action=true;
				}
			}
		{+END}
	{+END}

	if (!attached_event_action)
	{
		window.setTimeout(dispatch_block_helper,1000); // Delay it, so if we have in a faux popup it can set up faux_close
	}
//]]></script>

