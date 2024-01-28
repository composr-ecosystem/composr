/*

This file does a lot of stuff relating to overlays...

It provides callback-based *overlay*-driven substitutions for the standard browser windowing API...
 - alert
 - prompt
 - confirm
 - open (known as popups)
 - showModalDialog
A term we are using for these kinds of 'overlay' is '(faux) modal window'.

It provides a generic function to open a link as an overlay.

It provides a function to open an image link as a 'lightbox' (we use the term lightbox exclusively to refer to images in an overlay).

*/

if (typeof window.overlay_zIndex=='undefined')
{
	window.overlay_zIndex=999999; // Has to be higher than plupload, which is 99999
}

function open_link_as_overlay(ob,width,height,target)
{
	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		if ((typeof width=='undefined') || (!width)) var width='800';
		if ((typeof height=='undefined') || (!height)) var height='auto';
		var url=(typeof ob.href=='undefined')?ob.action:ob.href;
		if (/:\/\/(.[^:\/]+)/.exec(url)[1]!=window.location.hostname) return true; // Cannot overlay, different domain
		if ((typeof target=='undefined') || (!target)) var target='_top';
		var url_stripped=url.replace(/#.*/,'');
		var new_url=url_stripped+((url_stripped.indexOf('?')==-1)?'?':'&')+'wide_high=1'+url.replace(/^[^\#]+/,'');
		faux_open(new_url,null,'width='+width+';height='+height,target);
		return false;
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		return true;
	/*{+END}*/
}

/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
	function open_images_into_lightbox(imgs,start)
	{
		if (typeof start=='undefined') start=0;

		var modal=_open_image_into_lightbox(imgs[start][0],imgs[start][1],start+1,imgs.length,true,imgs[start][2]);
		modal.positionInSet=start;

		var previous_button=document.createElement('img');
		previous_button.className='previous_button';
		previous_button.src='{$IMG;,mediaset_previous}'.replace(/^https?:/,window.location.protocol);
		var previous=function(e) {
			if (typeof e=='undefined') e=window.event;
			cancel_bubbling(e);

			var new_position=modal.positionInSet-1;
			if (new_position<0) new_position=imgs.length-1;
			modal.positionInSet=new_position;
			_open_different_image_into_lightbox(modal,new_position,imgs);
			return false;
		};
		previous_button.onclick=previous;
		modal.left=previous;
		modal.box_wrapper.childNodes[0].appendChild(previous_button);

		var next_button=document.createElement('img');
		next_button.className='next_button';
		next_button.src='{$IMG;,mediaset_next}'.replace(/^https?:/,window.location.protocol);
		var next=function(e) {
			if (typeof e=='undefined') e=window.event;
			cancel_bubbling(e);

			var new_position=modal.positionInSet+1;
			if (new_position>=imgs.length) new_position=0;
			modal.positionInSet=new_position;
			_open_different_image_into_lightbox(modal,new_position,imgs);
			return false;
		};
		next_button.onclick=next;
		modal.right=next;
		modal.box_wrapper.childNodes[0].appendChild(next_button);
	}

	function open_image_into_lightbox(a,is_video)
	{
		if (typeof is_video=='undefined') is_video=false;
		var has_full_button=(typeof a.childNodes[0]=='undefined') || (a.href!==a.childNodes[0].src);
		_open_image_into_lightbox(a.href,(typeof a.cms_tooltip_title!='undefined')?a.cms_tooltip_title:a.title,null,null,has_full_button,is_video);
	}

	function _open_image_into_lightbox(initial_img_url,description,x,n,has_full_button,is_video)
	{
		if (typeof has_full_button=='undefined') has_full_button=false;

		// Set up overlay for Lightbox
		var lightbox_code=' \
			<div style="text-align: center"> \
				<p class="ajax_loading" id="lightbox_image"><img src="'+'{$IMG*;,loading}'.replace(/^https?:/,window.location.protocol)+'" /></p> \
				<p id="lightbox_meta" style="display: none" class="associated_link associated_links_block_group"> \
					<span id="lightbox_description">'+description+'</span> \
					'+((n===null)?'':('<span id="lightbox_position_in_set"><span id="lightbox_position_in_set_x">'+x+'</span> / <span id="lightbox_position_in_set_n">'+n+'</span></span>'))+' \
					'+(is_video?'':('<span id="lightbox_full_link"><a href="'+escape_html(initial_img_url)+'" target="_blank" title="{$STRIP_TAGS;,{!SEE_FULL_IMAGE}} {!LINK_NEW_WINDOW;^}">{!SEE_FULL_IMAGE;^}</a></span>'))+' \
				</p> \
			</div> \
		';

		// Show overlay
		var my_lightbox={
			type: 'lightbox',
			text: lightbox_code,
			cancel_button: '{!INPUTSYSTEM_CLOSE;^}',
			width: '450', // This will be updated with the real image width, when it has loaded
			height: '300' // "
		};
		var modal=new ModalWindow();
		modal.open(my_lightbox);

		// Load proper image
		window.setTimeout(function() { // Defer execution until the HTML was parsed
			if (is_video)
			{
				var video=document.createElement('video');
				video.controls='controls';
				video.autoplay='autoplay';
				set_inner_html(video,initial_img_url);
				video.className='lightbox_image';
				video.id='lightbox_image';
				add_event_listener_abstract(video,'loadedmetadata',function() { _resize_lightbox_dimensions_img(modal,video,has_full_button,true); });
			} else
			{
				var img=modal.top_window.document.createElement('img');
				img.className='lightbox_image';
				img.id='lightbox_image';
				img.onload=function() { _resize_lightbox_dimensions_img(modal,img,has_full_button,false); };
				img.src=initial_img_url;
			}
		},0);

		return modal;
	}

	function _open_different_image_into_lightbox(modal,position,imgs)
	{
		var is_video=imgs[position][2];

		// Load proper image
		window.setTimeout(function() { // Defer execution until the HTML was parsed
			if (is_video)
			{
				var video=document.createElement('video');
				video.controls='controls';
				video.autoplay='autoplay';
				set_inner_html(video,imgs[position][0]);
				video.className='lightbox_image';
				video.id='lightbox_image';
				add_event_listener_abstract(video,'loadedmetadata',function() { _resize_lightbox_dimensions_img(modal,video,true,true); });
			} else
			{
				var img=modal.top_window.document.createElement('img');
				img.className='lightbox_image';
				img.id='lightbox_image';
				img.src='{$IMG_INLINE;,loading}';
				window.setTimeout(function() { // Defer execution until after loading is set
					img.onload=function() { _resize_lightbox_dimensions_img(modal,img,true,is_video); };
					img.src=imgs[position][0];
				},0);
			}

			var lightbox_description=modal.top_window.document.getElementById('lightbox_description');
			var lightbox_position_in_set_x=modal.top_window.document.getElementById('lightbox_position_in_set_x');
			if (lightbox_description) set_inner_html(lightbox_description,imgs[position][1]);
			if (lightbox_position_in_set_x) set_inner_html(lightbox_position_in_set_x,position+1);
		},0);
	}

	function _resize_lightbox_dimensions_img(modal,img,has_full_button,is_video)
	{
		if (!modal.box_wrapper) return; /* Overlay closed already */

		var real_width=is_video?img.videoWidth:img.naturalWidth;
		var width=real_width;
		var real_height=is_video?img.videoHeight:img.naturalHeight;
		var height=real_height;

		var max_dims=_get_max_lightbox_img_dims(modal,has_full_button);
		var max_width=max_dims[0];
		var max_height=max_dims[1];

		var lightbox_image=modal.top_window.document.getElementById('lightbox_image');

		var lightbox_meta=modal.top_window.document.getElementById('lightbox_meta');
		var lightbox_description=modal.top_window.document.getElementById('lightbox_description');
		var lightbox_position_in_set=modal.top_window.document.getElementById('lightbox_position_in_set');
		var lightbox_full_link=modal.top_window.document.getElementById('lightbox_full_link');

		var dims_func=function()
		{
			lightbox_description.style.display=(lightbox_description.childNodes.length>0)?'inline':'none';
			if (lightbox_full_link) lightbox_full_link.style.display=(!is_video && has_full_button && (real_width>max_width || real_height>max_height))?'inline':'none';
			lightbox_meta.style.display=(lightbox_description.style.display=='inline' || lightbox_position_in_set!==null || lightbox_full_link && lightbox_full_link.style.display=='inline')?'block':'none';

			// Might need to rescale using some maths, if natural size is too big
			var max_dims=_get_max_lightbox_img_dims(modal,has_full_button);
			var max_width=max_dims[0];
			var max_height=max_dims[1];
			if (width>max_width)
			{
				width=max_width;
				height=window.parseInt(max_width*real_height/real_width-1);
			}
			if (height>max_height)
			{
				width=window.parseInt(max_height*real_width/real_height-1);
				height=max_height;
			}

			img.width=width;
			img.height=height;
			modal.reset_dimensions(''+width,''+height,false,true); // Temporarily forced, until real height is known (includes extra text space etc)

			window.setTimeout(function() {
				modal.reset_dimensions(''+width,''+height,false);
			},0);

			if (img.parentNode)
			{
				img.parentNode.parentNode.parentNode.style.width='auto';
				img.parentNode.parentNode.parentNode.style.height='auto';
			}
		};

		var sup=lightbox_image.parentNode;
		sup.removeChild(lightbox_image);
		if (sup.childNodes.length!=0)
		{
			sup.insertBefore(img,sup.childNodes[0]);
		} else
		{
			sup.appendChild(img);
		}
		sup.className='';
		sup.style.textAlign='center';
		sup.style.overflow='hidden';

		dims_func();
		modal.add_event( window, 'resize', function() { dims_func(); });
	}

	function _get_max_lightbox_img_dims(modal,has_full_button)
	{
		var max_width=modal.top_window.get_window_width()-modal.WINDOW_SIDE_GAP*2-modal.BOX_EAST_PERIPHERARY-modal.BOX_WEST_PERIPHERARY-modal.BOX_PADDING*2;
		var max_height=modal.top_window.get_window_height()-modal.WINDOW_TOP_GAP-modal.BOX_NORTH_PERIPHERARY-modal.BOX_SOUTH_PERIPHERARY-modal.BOX_PADDING*2;
		if (has_full_button) max_height-=120;
		return [max_width,max_height];
	}
/*{+END}*/

function fauxmodal_confirm(question,callback,title)
{
	if (typeof title=='undefined') title='{!Q_SURE;^}';

	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		var my_confirm={
			type: 'confirm',
			text: escape_html(question).replace(/\n/g,'<br />'),
			yes_button: '{!YES;^}',
			no_button: '{!NO;^}',
			cancel_button: null,
			title: title,
			yes: function() {
				callback(true);
			},
			no: function() {
				callback(false);
			},
			width: '450'
		};
		new ModalWindow().open(my_confirm);
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		callback(window.confirm(question));
	/*{+END}*/
}

function fauxmodal_alert(notice,callback,title,unescaped)
{
	if ((typeof callback=='undefined') || (!callback)) var callback=function() {};

	if (typeof title=='undefined' || title===null) var title='{!MESSAGE;^}';
	if (typeof unescaped=='undefined') unescaped=false;

	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		var my_alert={
			type: 'alert',
			text: unescaped?notice:escape_html(notice).replace(/\n/g,'<br />'),
			yes_button: '{!INPUTSYSTEM_OK;^}',
			width: '600',
			yes: callback,
			title: title,
			cancel_button: null
		};
		new ModalWindow().open(my_alert);
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		if ((typeof window.alert!='undefined') && (window.alert!=null))
		{
			window.alert(notice);
		} else
		{
			console.log(notice);
		}
		callback();
	/*{+END}*/
}

function fauxmodal_prompt(question,defaultValue,callback,title,input_type)
{
	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		var my_prompt={
			type: 'prompt',
			text: escape_html(question).replace(/\n/g,'<br />'),
			yes_button: '{!INPUTSYSTEM_OK;^}',
			cancel_button: '{!INPUTSYSTEM_CANCEL;^}',
			defaultValue: (defaultValue===null)?'':defaultValue,
			title: title,
			yes: function(value) {
				callback(value);
			},
			cancel: function() {
				callback(null);
			},
			width: '450'
		};
		if (input_type) my_prompt.input_type=input_type;
		new ModalWindow().open(my_prompt);
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		callback(window.prompt(question,defaultValue));
	/*{+END}*/
}

function faux_showModalDialog(url,name,options,callback,target,cancel_text)
{
	if ((typeof callback=='undefined') || (!callback)) var callback=function() {};

	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		var width=null,height=null,scrollbars=null,unadorned=null;

		if (typeof cancel_text=='undefined') cancel_text='{!INPUTSYSTEM_CANCEL;^}';

		if (options)
		{
			var parts=options.split(/[;,]/g);
			for (var i=0;i<parts.length;i++)
			{
				var bits=parts[i].split('=');
				if (typeof bits[1]!='undefined')
				{
					if ((bits[0]=='dialogWidth') || (bits[0]=='width'))
						width=bits[1].replace(/px$/,'');
					if ((bits[0]=='dialogHeight') || (bits[0]=='height'))
					{
						if (bits[1]=='100%')
						{
							height=''+(get_window_height()-100);
						} else
						{
							height=bits[1].replace(/px$/,'');
						}
					}
					if (((bits[0]=='resizable') || (bits[0]=='scrollbars')) && scrollbars!==true)
						scrollbars=((bits[1]=='yes') || (bits[1]=='1'))/*if either resizable or scrollbars set we go for scrollbars*/;
					if (bits[0]=='unadorned') unadorned=((bits[1]=='yes') || (bits[1]=='1'));
				}
			}
		}

		if (url.indexOf(window.location.host)!=-1)
		{
			url+=((url.indexOf('?')==-1)?'?':'&')+'overlay=1';
		}

		var my_frame={
			type: 'iframe',
			finished: function(value) {
				callback(value);
			},
			name: name,
			width: width,
			height: height,
			scrollbars: scrollbars,
			href: url.replace(/^https?:/,window.location.protocol)
		};
		my_frame.cancel_button=(unadorned!==true)?cancel_text:null;
		if (target) my_frame.target=target;
		new ModalWindow().open(my_frame);
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		options=options.replace('height=auto','height=520');

		var timer=new Date().getTime();
		try
		{
			var result=window.showModalDialog(url,name,options);
		}
		catch (e) {} // IE gives "Access is denied" if popup was blocked, due to var result assignment to non-real window
		var timer_now=new Date().getTime();
		if (timer_now-100>timer) // Not popup blocked
		{
			if ((typeof result=='undefined') || (result===null))
			{
				callback(null);
			} else
			{
				callback(result);
			}
		}
	/*{+END}*/
}

function faux_open(url,name,options,target,cancel_text)
{
	if (typeof cancel_text=='undefined') cancel_text='{!INPUTSYSTEM_CANCEL;^}';

	/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
		faux_showModalDialog(url,name,options,null,target,cancel_text);
	/*{+END}*/

	/*{+START,IF,{$NOT,{$CONFIG_OPTION,js_overlays}}}*/
		options=options.replace('height=auto','height=520');
		window.open(url,name,options);
	/*{+END}*/
}

/*{+START,IF,{$CONFIG_OPTION,js_overlays}}*/
/*
Originally...

Script: modalwindow.js
	ModalWindow - Simple javascript popup overlay to replace builtin alert, prompt and confirm, and more.

License:
	PHP-style license.

Copyright:
	Copyright (c) 2009 [Kieron Wilson](http://kd3sign.co.uk/).

Code & Documentation:
	http://kd3sign.co.uk/examples/modalwindow/

HEAVILY Modified by ocProducts for Composr.

*/

function ModalWindow()
{
	return {
		// Constants
		WINDOW_SIDE_GAP: {$?,{$MOBILE},5,25},
		WINDOW_TOP_GAP: 25, // Will also be used for bottom gap for percentage heights
		BOX_EAST_PERIPHERARY: 4,
		BOX_WEST_PERIPHERARY: 4,
		BOX_NORTH_PERIPHERARY: 4,
		BOX_SOUTH_PERIPHERARY: 4,
		BOX_PADDING: 20,
		VCENTRE_FRACTION_SHIFT: 0.5, // Fraction of remaining top gap also removed (as overlays look better slightly higher than vertical centre)
		LOADING_SCREEN_HEIGHT: 100,

		// Properties
		box_wrapper: null,
		button_container: null,
		returnValue: null,
		top_window: null,
		iframe_restyle_timer: null,

		// Methods...

		open: function() {
			var options=arguments[0] || {};
			var defaults={
				'type': 'alert',
				'opacity': '0.5',
				'width': 'auto',
				'height': 'auto',
				'title': '',
				'text': '',
				'yes_button': '{!YES;^}',
				'no_button': '{!NO;^}',
				'cancel_button': '{!INPUTSYSTEM_CANCEL;^}',
				'yes': null,
				'no': null,
				'finished': null,
				'cancel': null,
				'href': null,
				'scrollbars': null,
				'defaultValue': null,
				'target': '_self',
				'input_type': 'text'
			};

			this.top_window=window.top;
			this.top_window=this.top_window.top;

			for (var key in defaults) {
				this[key]=(typeof options[key]!='undefined')?options[key]:defaults[key];
			}

			this.close(this.top_window);
			this.init_box();
		},

		close: function(win) {
			if (this.box_wrapper) {
				this.top_window.document.body.style.overflow='';

				this.remove(this.box_wrapper,win);
				this.box_wrapper=null;

				this.remove_event(document,'keyup',this.keyup);
				this.remove_event(document,'mousemove',this.keyup);
			}
			this.opened=false;
		},

		option: function(method) {
			var win=this.top_window; // The below call may end up killing our window reference (for nested alerts), so we need to grab it first
			if (this[ method ]) {
				if (this.type=='prompt') {
					this[ method ](this.input.value);
				}
				else if (this.type=='iframe') {
					this[ method ](this.returnValue);
				}
				else this[ method ]();
			}
			if (method!='left' && method!='right')
				this.close(win);
		},

		reset_dimensions: function(width,height,init,force_height) {
			if (typeof force_height=='undefined') force_height=false;

			if (window_scrolling_blocked()) force_height=true;

			if (!this.box_wrapper) return;

			var dim=this.get_page_size();

			var bottom_gap=this.WINDOW_TOP_GAP;
			if (this.button_container.childNodes.length>0) bottom_gap+=find_height(this.button_container);

			if (!force_height)
				height='auto'; // Actually we always want auto heights, no reason to not for overlays

			// Store for later (when browser resizes for example)
			this.width=width;
			this.height=height;

			// Normalise parameters (we don't have px on the end of pixel units, and these units refer to internal space size [% ones are relative to window though])
			width=width.replace(/px$/,'');
			height=height.replace(/px$/,'');

			// Constrain to window width
			if (width.match(/^\d+$/)!==null)
			{
				if ((window.parseInt(width)>dim.window_width-this.WINDOW_SIDE_GAP*2-this.BOX_EAST_PERIPHERARY-this.BOX_WEST_PERIPHERARY) || (width=='auto'))
					width=''+(dim.window_width-this.WINDOW_SIDE_GAP*2-this.BOX_EAST_PERIPHERARY-this.BOX_WEST_PERIPHERARY);
			}

			// Auto width means full width
			if (width=='auto')
			{
				width=''+(dim.window_width-this.WINDOW_SIDE_GAP*2-this.BOX_EAST_PERIPHERARY-this.BOX_WEST_PERIPHERARY);
			}
			// NB: auto height feeds through without a constraint (due to infinite growth space), with dynamic adjustment for iframes

			// Calculate percentage sizes
			var match;
			match=width.match(/^([\d\.]+)%$/);
			if (match!==null)
			{
				width=''+(window.parseFloat(match[1])*(dim.window_width-this.WINDOW_SIDE_GAP*2-this.BOX_EAST_PERIPHERARY-this.BOX_WEST_PERIPHERARY));
			}
			match=height.match(/^([\d\.]+)%$/);
			if (match!==null)
			{
				height=''+(window.parseFloat(match[1])*(dim.page_height-this.WINDOW_TOP_GAP-bottom_gap-this.BOX_NORTH_PERIPHERARY-this.BOX_SOUTH_PERIPHERARY));
			}

			// Work out box dimensions
			var box_width,box_height;
			if (width.match(/^\d+$/)!==null)
			{
				box_width=width+'px';
			} else
			{
				box_width=width;
			}
			if (height.match(/^\d+$/)!==null)
			{
				box_height=height+'px';
			} else
			{
				box_height=height;
			}

			// Save into HTML
			var detected_box_height;
			this.box_wrapper.childNodes[0].style.width=box_width;
			this.box_wrapper.childNodes[0].style.height=box_height;
			var iframe=this.box_wrapper.getElementsByTagName('iframe');
			if ((has_iframe_ownership(iframe[0])) && (iframe[0].contentWindow.document.body)) // Balance iframe height
			{
				iframe[0].style.width='100%';
				if (height=='auto')
				{
					if (!init)
					{
						detected_box_height=get_window_scroll_height(iframe[0].contentWindow);
						iframe[0].style.height=detected_box_height+'px';
					}
				} else
				{
					iframe[0].style.height='100%';
				}
			}

			// Work out box position
			if (!detected_box_height) detected_box_height=find_height(this.box_wrapper.childNodes[0]);
			var _box_pos_top,_box_pos_left,box_pos_top,box_pos_left;
			if (box_height=='auto')
			{
				if (init)
				{
					_box_pos_top=(dim.window_height/(2+(this.VCENTRE_FRACTION_SHIFT*2)))-(this.LOADING_SCREEN_HEIGHT/2)+this.WINDOW_TOP_GAP; // This is just temporary
				} else
				{
					_box_pos_top=(dim.window_height/(2+(this.VCENTRE_FRACTION_SHIFT*2)))-(detected_box_height/2)+this.WINDOW_TOP_GAP;
				}

				if (typeof iframe[0]!='undefined') _box_pos_top=this.WINDOW_TOP_GAP; // Actually, for frames we'll put at top so things don't bounce about during loading and if the frame size changes
			} else
			{
				_box_pos_top=(dim.window_height/(2+(this.VCENTRE_FRACTION_SHIFT*2)))-(parseInt(box_height)/2)+this.WINDOW_TOP_GAP;
			}
			if (_box_pos_top<this.WINDOW_TOP_GAP) _box_pos_top=this.WINDOW_TOP_GAP;
			_box_pos_left=((dim.window_width/2)-((parseInt(box_width)+this.WINDOW_SIDE_GAP+this.BOX_EAST_PERIPHERARY+this.BOX_WEST_PERIPHERARY)/2));
			box_pos_top=_box_pos_top+'px';
			box_pos_left=_box_pos_left+'px';

			// Save into HTML
			this.box_wrapper.childNodes[0].style.top=box_pos_top;
			this.box_wrapper.childNodes[0].style.left=box_pos_left;
			if (this.title=='') {
				this.box_wrapper.childNodes[0].style.padding=this.BOX_PADDING+'px';
			}

			var do_scroll=false;

			// Absolute positioning instead of fixed positioning
			if (('{$MOBILE}'==1) || (detected_box_height>dim.window_height) || (this.box_wrapper.style.position=='absolute'/*don't switch back to fixed*/))
			{
				var was_fixed=(this.box_wrapper.style.position=='fixed');

				this.box_wrapper.style.position='absolute';
				this.box_wrapper.style.height=((dim.page_height>(detected_box_height+bottom_gap+_box_pos_left))?dim.page_height:(detected_box_height+bottom_gap+_box_pos_left))+'px';
				this.top_window.document.body.style.overflow='';
				{+START,IF,{$NOT,{$MOBILE}}}
					this.box_wrapper.childNodes[0].style.position='absolute';
					box_pos_top={$?,{$MOBILE},0,this.WINDOW_TOP_GAP}+'px';
					this.box_wrapper.childNodes[0].style.top=box_pos_top;
				{+END}
				{+START,IF,{$MOBILE}}
					{$,iOS/Android uses static anyway}
				{+END}

				if ((init) || (was_fixed)) do_scroll=true;
				if (/*maybe a navigation has happened and we need to scroll back up*/(typeof iframe[0]!='undefined') && (has_iframe_ownership(iframe[0])) && (typeof iframe[0].contentWindow.scrolled_up_for=='undefined'))
				{
					do_scroll=true;
				}
			} else // Fixed positioning, with scrolling turned off until the overlay is closed
			{
				this.box_wrapper.style.position='fixed';
				this.box_wrapper.childNodes[0].style.position='fixed';
				this.top_window.document.body.style.overflow='hidden';
			}

			if (do_scroll)
			{
				window.setTimeout(function() {
					try // Scroll to top to see
					{
						this.top_window.scrollTo(0,0);
						if ((typeof iframe[0]!='undefined') && (has_iframe_ownership(iframe[0])))
							iframe[0].contentWindow.scrolled_up_for=true;
					}
					catch (e) {}
				}, 25);
			}
		},

		init_box: function() {
			var dim=this.get_page_size();

			this.box_wrapper=this.element('div',{ // Black out the background
				'styles': {
					'background': 'rgba(0,0,0,0.7)',
					'zIndex': this.top_window.overlay_zIndex++,
					'overflow': 'hidden',
					'position': '{$?,{$MOBILE},absolute,fixed}',
					'left': '0',
					'top': '0',
					'width': '100%',
					'height': '100%'
				}
			});

			this.box_wrapper.appendChild(this.element('div',{ // The main overlay
				'class': 'box overlay '+this.type,
				'role': 'dialog',
				'styles': {
					// This will be updated immediately in reset_dimensions
					'position': '{$?,{$MOBILE},static,fixed}',
					'margin': '0 auto' // Centering for iOS/Android which is statically positioned (so the container height as auto can work)
				}
			}));

			var _this=this;
			var width=this.width;
			var height=this.height;
			this.inject(this.box_wrapper);

			var container=this.element('div',{
				'class': 'box_inner',
				'styles': {
					'width': 'auto',
					'height': 'auto'
				}
			});

			var overlay_header=null;
			if (this.title!='' || this.type=='iframe') {
				overlay_header=this.element('h3',{
					'html': this.title,
					'styles': {
						'display': (this.title=='')?'none':'block'
					}
				});
				container.appendChild(overlay_header);
			}

			if (this.text!='') {
				if (this.type=='prompt')
				{
					var div=this.element('p');
					div.appendChild(this.element('label',{
						'for': 'overlay_prompt',
						'html': this.text
					}));
					container.appendChild(div);
				} else
				{
					container.appendChild(this.element('div',{
						'html': this.text
					}));
				}
			}

			this.button_container=this.element('p',{
				'class': 'proceed_button'
			});

			var _this=this;

			this.clickout_cancel=function() {
				_this.option('cancel');
			};

			this.clickout_finished=function() {
				_this.option('finished');
			};

			this.clickout_yes=function() {
				_this.option('yes');
			};

			this.keyup=function(e) {
				if (!e) e=window.event;
				var key_code=(e)?(e.which || e.keyCode):null;

				if (key_code==37) // Left arrow
				{
					_this.option('left');
				} else
				if (key_code==39) // Right arrow
				{
					_this.option('right');
				} else
				if ((key_code==13/*enter*/) && (_this.yes))
				{
					_this.option('yes');
				}
				if ((key_code==13/*enter*/) && (_this.finished))
				{
					_this.option('finished');
				} else if ((key_code==27/*esc*/) && (_this.cancel_button) && ((_this.type=='prompt') || (_this.type=='confirm') || (_this.type=='lightbox') || (_this.type=='alert')))
				{
					_this.option('cancel');
				}
			};

			this.mousemove=function(e) {
				if (!e) e=window.event;
				if (_this.box_wrapper && _this.box_wrapper.childNodes[0].className.indexOf(' mousemove')==-1)
				{
					_this.box_wrapper.childNodes[0].className+=' mousemove';
					window.setTimeout(function() {
						if (_this.box_wrapper)
						{
							_this.box_wrapper.childNodes[0].className=_this.box_wrapper.childNodes[0].className.replace(/ mousemove/g,'');
						}
					},2000);
				}
			};

			this.add_event(this.box_wrapper.childNodes[0],'click',function(e) {
				try { _this.top_window.cancel_bubbling(e); } catch (e) {}
				/*{+START,IF,{$MOBILE}}*/
					if (_this.type=='lightbox') // IDEA: Swipe detect would be better, but JS does not have this natively yet
					{
						_this.option('right');
					}
				/*{+END}*/
			});

			switch (this.type)
			{
				case 'iframe':
					var iframe_width=(this.width.match(/^[\d\.]+$/)!==null)?((this.width-14)+'px'):this.width;
					var iframe_height=(this.height.match(/^[\d\.]+$/)!==null)?((this.height-14	)+'px'):((this.height=='auto')?(this.LOADING_SCREEN_HEIGHT+'px'):this.height);

					var iframe=this.element('iframe',{
						'frameBorder': '0',
						'scrolling': window_scrolling_blocked()?'yes':'no',
						'title': '',
						'name': 'overlay_iframe',
						'id': 'overlay_iframe',
						'allowTransparency': 'true',
						//'seamless': 'seamless',	Not supported, and therefore testable yet. Would be great for mobile browsing.
						'styles': {
							'width': iframe_width,
							'height': iframe_height,
							'background': 'transparent'
						}
					});

					container.appendChild(iframe);

					animate_frame_load(iframe,'overlay_iframe',50,true);

					window.setTimeout(function() { _this.add_event(_this.box_wrapper,'click',_this.clickout_finished); },1000);

					this.add_event(iframe,'load',function() {
						if ((has_iframe_ownership(iframe)) && (typeof iframe.contentWindow.document.getElementsByTagName('h1')[0]=='undefined') && (typeof iframe.contentWindow.document.getElementsByTagName('h2')[0]=='undefined'))
						{
							if (iframe.contentWindow.document.title!='')
							{
								set_inner_html(overlay_header,escape_html(iframe.contentWindow.document.title));
								overlay_header.style.display='block';
							}
						}
					});

					// Fiddle it, to behave like a popup would
					var name=this.name;
					var make_frame_like_popup=function() {
						if (iframe.parentNode.parentNode.parentNode.parentNode==null && _this.iframe_restyle_timer!=null)
						{
							clearInterval(_this.iframe_restyle_timer);
							_this.iframe_restyle_timer=null;
							return;
						}

						if ((has_iframe_ownership(iframe)) && (iframe.contentWindow.document.body) && (typeof iframe.contentWindow.document.body.done_popup_trans=='undefined'))
						{
							iframe.contentWindow.document.body.style.background='transparent';

							if (iframe.contentWindow.document.body.className.indexOf('overlay')==-1)
								iframe.contentWindow.document.body.className+=' overlay lightbox';

							// Allow scrolling, if we want it
							//iframe.scrolling=(_this.scrollbars===false)?'no':'auto';	Actually, not wanting this now

							// Remove fixed width
							var main_website_inner=iframe.contentWindow.document.getElementById('main_website_inner');
							if (main_website_inner) main_website_inner.id='';

							// Remove main_website marker
							var main_website=iframe.contentWindow.document.getElementById('main_website');
							if (main_website) main_website.id='';

							// Remove popup spacing
							var popup_spacer=iframe.contentWindow.document.getElementById('popup_spacer');
							if (popup_spacer) popup_spacer.id='';

							// Set linking scheme
							var bases=iframe.contentWindow.document.getElementsByTagName('base');
							var base_element;
							if (!bases[0])
							{
								base_element=iframe.contentWindow.document.createElement('base');
								if (iframe.contentWindow.document)
								{
									var heads=iframe.contentWindow.document.getElementsByTagName('head');
									if (heads[0])
									{
										heads[0].appendChild(base_element);
									}
								}
							} else
							{
								base_element=bases[0];
							}
							base_element.target=_this.target;
							// Firefox 3.6 does not respect <base> element put in via DOM manipulation :(
							var forms=iframe.contentWindow.document.getElementsByTagName('form');
							for (var i=0;i<forms.length;i++)
							{
								if (!forms[i].target) forms[i].target=_this.target;
							}
							var as=iframe.contentWindow.document.getElementsByTagName('a');
							for (var i=0;i<as.length;i++)
							{
								if (!as[i].target) as[i].target=_this.target;
							}

							// Set frame name
							if (name && iframe.contentWindow.name!=name) iframe.contentWindow.name=name;

							// Create close function
							if (typeof iframe.contentWindow.faux_close=='undefined')
							{
								iframe.contentWindow.faux_close=function() {
									if (iframe && iframe.contentWindow && typeof iframe.contentWindow.returnValue!='undefined')
										_this.returnValue=iframe.contentWindow.returnValue;
									_this.option('finished');
								};
							}

							if (get_inner_html(iframe.contentWindow.document.body).length>300) // Loaded now
							{
								iframe.contentWindow.document.body.done_popup_trans=true;
							}
						} else
						{
							if (has_iframe_loaded(iframe) && !has_iframe_ownership(iframe)) {
								iframe.scrolling='yes';
								iframe.style.height='500px';
							}
						}

						// Handle iframe sizing
						if (_this.height=='auto')
						{
							_this.reset_dimensions(_this.width,_this.height,false);
						}
					};
					window.setTimeout(function() {
						illustrate_frame_load(iframe,'overlay_iframe');
						iframe.src=_this.href;
						make_frame_like_popup();

						if (_this.iframe_restyle_timer==null)
							_this.iframe_restyle_timer=window.setInterval(make_frame_like_popup,300); // In case internal nav changes
					},0);
					break;

				case 'lightbox':
				case 'alert':
					if (this.yes)
					{
						var button=this.element('button',{
							'html': this.yes_button,
							'class': 'buttons__proceed button_screen_item'
						});
						this.add_event(button,'click',function() { _this.option('yes'); });
						window.setTimeout(function() { _this.add_event(_this.box_wrapper,'click',_this.clickout_yes); },1000);
						this.button_container.appendChild(button);
					} else
					{
						window.setTimeout(function() { _this.add_event(_this.box_wrapper,'click',_this.clickout_cancel); },1000);
					}
					break;

				case 'confirm':
					var button=this.element('button',{
						'html': this.yes_button,
						'class': 'buttons__yes button_screen_item',
						'style': 'font-weight: bold;'
					});
					this.add_event(button,'click',function() { _this.option('yes'); });
					this.button_container.appendChild(button);
					var button=this.element('button',{
						'html': this.no_button,
						'class': 'buttons__no button_screen_item'
					});
					this.add_event(button,'click',function() { _this.option('no'); });
					this.button_container.appendChild(button);
					break;

				case 'prompt':
					this.input=this.element('input',{
						'name': 'prompt',
						'id': 'overlay_prompt',
						'type': this.input_type,
						'size': '40',
						'class': 'wide_field',
						'value': (this.defaultValue===null)?'':this.defaultValue
					});
					var input_wrap=this.element('div',{
						'class': 'constrain_field'
					});
					input_wrap.appendChild(this.input);
					container.appendChild(input_wrap);

					if (this.yes)
					{
						var button=this.element('button',{
							'html': this.yes_button,
							'class': 'buttons__yes button_screen_item',
							'style': 'font-weight: bold;'
						});
						this.add_event(button,'click',function() { _this.option('yes'); });
						this.button_container.appendChild(button);
					}
					window.setTimeout(function() { _this.add_event(_this.box_wrapper,'click',_this.clickout_cancel); },1000);
					break;
			}

			// Cancel button handled either via button in corner (if there's no other buttons) or another button in the panel (if there's other buttons)
			if (this.cancel_button)
			{
				var button;
				if (this.button_container.childNodes.length>0)
				{
					button=this.element('button',{
						'html': this.cancel_button,
						'class': 'button_screen_item buttons__cancel'
					});
					this.button_container.appendChild(button);
				} else
				{
					button=this.element('img',{
						'src': '{$IMG;,button_lightbox_close}'.replace(/^https?:/,window.location.protocol),
						'alt': this.cancel_button,
						'class': 'overlay_close_button'
					});
					container.appendChild(button);
				}
				this.add_event(button,'click',function() { _this.option(this.cancel?'cancel':'finished'); });
			}

			// Put together
			if (this.button_container.childNodes.length>0)
			{
				if (this.type=='iframe')
					container.appendChild(this.element('hr',{ 'class': 'spaced_rule' }));
				container.appendChild(this.button_container);
			}
			this.box_wrapper.childNodes[0].appendChild(container);

			// Handle dimensions
			this.reset_dimensions(this.width,this.height,true);
			this.add_event(window,'resize',function() { _this.reset_dimensions(width,height,false); });

			// Focus first button by default
			if (this.input)
			{
				window.setTimeout(function() { _this.input.focus(); },0);
			}
			else if (typeof this.box_wrapper.getElementsByTagName('button')[0]!='undefined')
			{
				this.box_wrapper.getElementsByTagName('button')[0].focus();
			}

			window.setTimeout(function() { // Timeout needed else keyboard activation of overlay opener may cause instant shutdown also
				_this.add_event(document,'keyup',_this.keyup);
				_this.add_event(document,'mousemove',_this.mousemove);
			},100);
		},

		inject: function(el) {
			this.top_window.document.body.appendChild(el);
		},

		remove: function(el,win) {
			if (!win) win=this.top_window;
			win.document.body.removeChild(el);
		},

		element: function() {
			var tag=arguments[0],options=arguments[1];
			var el=this.top_window.document.createElement(tag);
			var attributes={
				'html': 'innerHTML',
				'class': 'className',
				'for': 'htmlFor',
				'text': 'innerText'
			};
			if (options)
			{
				if (typeof options=='object')
				{
					for (var name in options)
					{
						var value=options[name];
						if (name=='styles')
						{
							this.set_styles(el,value);
						} else if (name=='html')
						{
							set_inner_html(el,value);
						} else if (attributes[name])
						{
							el[attributes[name]]=value;
						} else
						{
							el.setAttribute(name,value);
						}
					}
				}
			}
			return el;
		},

		add_event: function(o,e,f) {
			if (o)
			{
				if (o.addEventListener) o.addEventListener(e,f,false);
				else if (o.attachEvent) o.attachEvent('on'+e,f);
			}
		},

		remove_event: function(o,e,f) {
			if (o)
			{
				if (o.removeEventListener) o.removeEventListener(e,f,false);
				else if (o.detachEvent) o.detachEvent('on'+e,f);
			}
		},

		set_styles: function(e,o) {
			for (var k in o)
			{
				this.set_style(e,k,o[k]);
			}
		},

		set_style: function(e,p,v) {
			if (p=='opacity')
			{
				this.top_window.set_opacity(e,v);
			} else
			{
				try
				{
					e.style[p]=v;
				}
				catch (e){}
			}
		},

		get_page_size: function() {
			return {
				'page_width': this.top_window.get_window_scroll_width(this.top_window),
				'page_height': this.top_window.get_window_scroll_height(this.top_window),
				'window_width': this.top_window.get_window_width(this.top_window),
				'window_height': this.top_window.get_window_height()
			};
		}
	};
}
/*{+END}*/

function window_scrolling_blocked()
{
	return (document.documentElement.style.overflowY=='hidden') || (document.documentElement.style.position=='fixed');
}
