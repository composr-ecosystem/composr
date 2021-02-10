"use strict";

// Constants
window.MESSAGE_CHECK_INTERVAL={$ROUND%,{$MAX,3000,{$CONFIG_OPTION,chat_message_check_interval}}};
window.TRANSITORY_ALERT_TIME={$ROUND%,{$CONFIG_OPTION,chat_transitory_alert_time}};
window.LOGS_DOWNLOAD_INTERVAL=3000;

// Tracking variables
var last_message_id=-1;
var last_timestamp=0;
var last_event_id=-1;
var message_checking=false;
var no_im_html='';
var text_colour;
var opened_popups={};
var load_from_room_id=null;
var already_received_room_invites={};
var already_received_contact_alert={};
var instant_go=false;
var is_shutdown=false;
var all_conversations={};

// Code...

window.has_focus=true;
add_event_listener_abstract(window,'blur',function() { window.has_focus=false; });
add_event_listener_abstract(window,'focus',function() { window.has_focus=true; });

function play_sound_url(url) // Used for testing different sounds
{
	if (typeof window.soundManager=='undefined') return;

	var base_url=((url.indexOf('data_custom')==-1)&&(url.indexOf('uploads/')==-1))?'{$BASE_URL_NOHTTP;}':'{$CUSTOM_BASE_URL_NOHTTP;}';
	var sound_object=window.soundManager.createSound({url: base_url+'/'+url});
	if (sound_object) sound_object.play();
}

function play_chat_sound(s_id,for_member)
{
	if (typeof window.soundManager=='undefined') return;

	var play_sound=window.document.getElementById('play_sound');

	if ((play_sound) && (!play_sound.checked)) return;

	if (for_member)
	{
		var override=window.top_window.soundManager.getSoundById(s_id+'_'+for_member,true);
		if (override)
		{
			s_id=s_id+'_'+for_member;
		}
	}

	if (typeof window.top_window.console!='undefined')
	{
		window.top_window.console.log('Playing '+s_id+' sound'); // Useful when debugging sounds when testing using SU, otherwise you don't know which window they came from
	}

	window.top_window.soundManager.play(s_id);
}

function chat_load(room_id)
{
	window.top_window=window;

	try
	{
		document.getElementById('post').focus();
	}
	catch (e) {}

	if (window.location.href.indexOf('keep_chattest')==-1) begin_chatting(room_id);

	window.text_colour=document.getElementById('text_colour');
	if (window.text_colour) window.text_colour.style.color=text_colour.value;

	manage_scroll_height(document.getElementById('post'));
}

function begin_chatting(room_id)
{
	window.load_from_room_id=room_id;

	if (typeof window.do_ajax_request!='undefined')
	{
		chat_check(true,0);
		play_chat_sound('you_connect');
	} else
	{
		window.setTimeout(begin_chatting,10);
	}
}

function check_chat_options(ob)
{
	if (!ob.elements['text_colour'].value.match(/^#[0-9A-F][0-9A-F][0-9A-F]([0-9A-F][0-9A-F][0-9A-F])?$/))
	{
		window.fauxmodal_alert('{!chat:BAD_HTML_COLOUR;^}');
		return false;
	}

	return check_form(ob);
}

function on_font_change(o)
{
	var value=o.options[o.selectedIndex].value;
	document.getElementById('font').value=value;
	document.getElementById('post').style.fontFamily=value;
	manage_scroll_height(document.getElementById('post'));
}

function get_ticked_people(form)
{
	var people='';

	for (var i=0;i<form.elements.length;i++)
	{
		if ((form.elements[i].type=='checkbox') && (form.elements[i].checked))
			people+=((people!='')?',':'')+form.elements[i].name.substr(7);
	}

	if (people=='')
	{
		window.fauxmodal_alert('{!chat:NOONE_SELECTED_YET;^}');
		return '';
	}

	return people;
}

function do_input_private_message(field_name)
{
	if (typeof window.insert_textbox=='undefined') return;
	window.fauxmodal_prompt(
		'{!chat:ENTER_RECIPIENT;^}',
		'',
		function(va)
		{
			if (va!=null)
			{
				var vb=window.fauxmodal_prompt(
					'{!MESSAGE;^}',
					'',
					function(vb)
					{
						if (vb!=null) insert_textbox(document.getElementById(field_name),'[private="'+va+'"]'+vb+'[/private]');
					},
					'{!chat:INPUT_CHATCODE_private_message;^}'
				);
			}
		},
		'{!chat:INPUT_CHATCODE_private_message;^}'
	);
}

function do_input_invite(field_name)
{
	if (typeof window.insert_textbox=='undefined') return;
	window.fauxmodal_prompt(
		'{!chat:ENTER_RECIPIENT;^}',
		'',
		function(va)
		{
			if (va!=null)
			{
				var vb=window.fauxmodal_prompt(
					'{!chat:ENTER_CHATROOM;^}',
					'',
					function(vb)
					{
						if (vb!=null) insert_textbox(document.getElementById(field_name),'[invite="'+va+'"]'+vb+'[/invite]');
					},
					'{!chat:INPUT_CHATCODE_invite;^}'
				);
			}
		},
		'{!chat:INPUT_CHATCODE_invite;^}'
	);
}

function do_input_new_room(field_name)
{
	if (typeof window.insert_textbox=='undefined') return;
	window.fauxmodal_prompt(
		'{!chat:ENTER_CHATROOM;^}',
		'',
		function(va)
		{
			if (va!=null)
			{
				var vb=window.prompt(
					'{!chat:ENTER_ALLOW;^}',
					'',
					function(vb)
					{
						if (vb!=null) insert_textbox(document.getElementById(field_name),'[newroom="'+va+'"]'+vb+'[/newroom]');
					},
					'{!chat:INPUT_CHATCODE_new_room;^}'
				);
			}
		},
		'{!chat:INPUT_CHATCODE_new_room;^}'
	);
}

// Post a chat message
function chat_post(event,current_room_id,field_name,font_name,font_colour)
{
	// Catch the data being submitted by the form, and send it through XMLHttpRequest if possible. Stop the form submission if this is achieved.
	if ((window.do_ajax_request) && (typeof window.do_ajax_request!='undefined'))
	{
		var element=document.getElementById(field_name);
		cancel_bubbling(event,element);
		var message_text=element.value;

		if (message_text!='')
		{
			if (typeof window.top_window.console!='undefined')
				window.top_window.console.log('Posting chat message (' + new Date().getTime() + ')');

			if (window.top_window.cc_timer) {
				if (typeof window.top_window.console!='undefined')
					window.top_window.console.log('Clearing existing chat timer as this posting will take control until finished (' + new Date().getTime() + ')');

				window.top_window.clearTimeout(window.top_window.cc_timer);
				window.top_window.cc_timer=null;
			}

			// Reinvite last left member if necessary
			if ((typeof element.force_invite!='undefined') && (element.force_invite!==null))
			{
				invite_im(element.force_invite);
				element.force_invite=null;
			}

			// Send it through XMLHttpRequest, and append the results.
			var url='{$FIND_SCRIPT;,messages}?action=post';
			element.disabled=true;
			window.top_window.currently_sending_message=true;
			var func=function(result) {
				if (typeof window.top_window.console!='undefined')
					window.top_window.console.log('Successfully posted chat message (' + new Date().getTime() + ')');

				window.top_window.currently_sending_message=false;
				element.disabled=false;
				var responses=result.getElementsByTagName('result');
				if (responses[0])
				{
					process_chat_xml_messages(responses[0],true);

					window.setTimeout(function() { element.value=''; },20);
					element.style.height='auto';

					play_chat_sound('message_sent');
				} else
				{
					window.fauxmodal_alert('{!MESSAGE_POSTING_ERROR;^}');
				}

				// Reschedule the next check (cc_timer was reset already higher up in function)
				if (typeof window.top_window.console!='undefined')
					window.top_window.console.log('Setting new chat timer (' + new Date().getTime() + ')');
				window.top_window.cc_timer=window.top_window.setTimeout(function() {window.top_window.chat_check(false,window.top_window.last_message_id,window.top_window.last_event_id); },window.MESSAGE_CHECK_INTERVAL);

				try
				{
					element.focus();
				}
				catch (e) {}
			}
			var error_func=function() {
				if (typeof window.top_window.console!='undefined')
					window.top_window.console.log('Failed to post chat message (' + new Date().getTime() + ')');

				window.top_window.currently_sending_message=false;
				element.disabled=false;

				// Reschedule the next check (cc_timer was reset already higher up in function)
				if (typeof window.top_window.console!='undefined')
					window.top_window.console.log('Setting new chat timer (' + new Date().getTime() + ')');
				window.top_window.cc_timer=window.top_window.setTimeout(function() { window.top_window.chat_check(false,window.top_window.last_message_id,window.top_window.last_event_id); },window.MESSAGE_CHECK_INTERVAL);
			}
			var full_url=maintain_theme_in_link(url+window.top_window.keep_stub(false));
			var post_data='room_id='+window.encodeURIComponent(current_room_id)+'&message='+window.encodeURIComponent(message_text)+'&font='+window.encodeURIComponent(font_name)+'&colour='+window.encodeURIComponent(font_colour)+'&message_id='+window.encodeURIComponent((window.top_window.last_message_id===null)?-1:window.top_window.last_message_id)+'&event_id='+window.encodeURIComponent(window.top_window.last_event_id);
			do_ajax_request(full_url,[func,error_func],post_data);
		}

		return false;
	}
	else
	{
		// Let the form be submitted the old-fashioned way.
		return true;
	}
}

// Check for new messages
function chat_check(backlog,message_id,event_id)
{
	if (window.currently_sending_message) { // We'll reschedule once our currently-in-progress message is sent
		if (typeof window.top_window.console!='undefined')
			window.top_window.console.log('Skip checking for chat messages (chat timer), as a message posting is pending completion (' + new Date().getTime() + ')');

		return null;
	}

	if (typeof window.top_window.console!='undefined')
		window.top_window.console.log('Checking for chat messages (chat timer) (' + new Date().getTime() + ')');

	if ((typeof event_id=='undefined') || (!event_id)) var event_id=-1; // Means, we don't want to look at events, but the server will give us a null event

	// Check for new messages on the server the new or old way
	if (typeof window.do_ajax_request!='undefined')
	{
		// AJAX!
		window.setTimeout(function() { chat_check_timeout(backlog,message_id,event_id); },window.MESSAGE_CHECK_INTERVAL*1.2);
		var the_date=new Date();
		if ((!window.message_checking) || (window.message_checking+window.MESSAGE_CHECK_INTERVAL<=the_date.getTime())) // If not currently in process, or process stalled
		{
			window.message_checking=the_date.getTime();
			var url;
			var _room_id=(window.load_from_room_id == null) ? -1 : window.load_from_room_id;
			if (backlog)
			{
				url='{$FIND_SCRIPT;,messages}?action=all&room_id='+window.encodeURIComponent(_room_id);
			} else
			{
				url='{$FIND_SCRIPT;,messages}?action=new&room_id='+window.encodeURIComponent(_room_id)+'&message_id='+window.encodeURIComponent(message_id?message_id:-1)+'&event_id='+window.encodeURIComponent(event_id);
			}
			if (window.location.href.indexOf('no_reenter_message=1')!=-1) url=url+'&no_reenter_message=1';
			var func=function(ajax_result_frame,ajax_result) {
			chat_check_response(ajax_result_frame,ajax_result,backlog/*backlog = skip_incoming_sound*/);
			};
			var error_func=function() {
				chat_check_response(null,null);
			};
			var full_url=maintain_theme_in_link(url+keep_stub(false));
			do_ajax_request(full_url,[func,error_func]);
			return false;
		} else {
			if (typeof window.top_window.console!='undefined')
				window.top_window.console.log('Skip checking for chat messages (chat timer), as a previous check is pending completion and not yet timed out (' + new Date().getTime() + ')');
		}
		return null;
	} else
	{
		// Resort to reloading the page
		window.location.reload(true);
		return true;
	}
}

// Check to see if there's been a packet loss
function chat_check_timeout(backlog,message_id,event_id)
{
	var the_date=new Date();
	if ((window.message_checking) && (window.message_checking<=the_date.getTime()-window.MESSAGE_CHECK_INTERVAL*1.2) && (!window.currently_sending_message)) // If we are awaiting a response (message_checking is not false, and that response was made more than 12 seconds ago
	{
		if (typeof window.top_window.console!='undefined')
			window.top_window.console.log('(Guard) Making sure our last actioned chat check completed and was on time - and it did not! (' + new Date().getTime() + ')');

		// Our response is tardy - presume we've lost our scheduler / AJAX request, so fire off a new AJAX request and reset the chat_check_timeout timer
		chat_check(backlog,message_id,event_id);
	} else {
		if (typeof window.top_window.console!='undefined')
			window.top_window.console.log('(Guard) Making sure our last actioned chat check completed and was on time - and it did (' + new Date().getTime() + ')');
	}
}

// Deal with the new messages response. Wraps around process_chat_xml_messages as it also adds timers to ensure the message check continues to function even if background errors might have happened.
function chat_check_response(ajax_result_frame,ajax_result,skip_incoming_sound)
{
	if (ajax_result!=null)
	{
		if (typeof window.top_window.console!='undefined')
			window.top_window.console.log('Received chat check response (' + new Date().getTime() + ')');

		if (typeof skip_incoming_sound=='undefined') skip_incoming_sound=false;

		var temp=process_chat_xml_messages(ajax_result,skip_incoming_sound);
		if (temp==-2) return false;
	} else {
		if (typeof window.top_window.console!='undefined')
			window.top_window.console.log('Chat check failed (' + new Date().getTime() + ')');
	}

	// Schedule the next check
	if (typeof window.top_window.console!='undefined')
		window.top_window.console.log('Schedule next chat message check (chat timer) (' + new Date().getTime() + ')');
	if (window.cc_timer) { window.clearTimeout(window.cc_timer); window.cc_timer=null; }
	window.cc_timer=window.setTimeout(function() { chat_check(false,window.last_message_id,window.last_event_id); },window.MESSAGE_CHECK_INTERVAL);

	window.message_checking=false; // All must be ok so say we are happy we got a response and scheduled the next check

	return true;
}

function process_chat_xml_messages(ajax_result,skip_incoming_sound)
{
	if (!ajax_result) return; // Some kind of error happened

	if (typeof skip_incoming_sound=='undefined') skip_incoming_sound=false;

	var messages=ajax_result.childNodes;
	var message_container=document.getElementById('messages_window');
	var message_container_global=(message_container!=null);
	var _cloned_message,cloned_message;
	var current_room_id=window.load_from_room_id;
	var tab_element;
	var flashable_alert=false;
	var username,room_name,room_id,event_type,member_id,tmp_element,rooms,avatar_url,participants;
	var id,timestamp;
	var first_set=false;
	var newest_id_here=null,newest_timestamp_here=null;
	var cannot_process_all=false;

	// Look through our messages
	for (var i=0;i<messages.length;i++)
	{
		if (messages[i].nodeName=='div') // MESSAGE
		{
			// Find out about our message
			id=messages[i].getAttribute('id');
			timestamp=messages[i].getAttribute('timestamp');
			if (!id) id=messages[i].id; // Weird fix for Opera
			if (((window.top_window.last_message_id) && (parseInt(id)<=window.top_window.last_message_id)) && ((window.top_window.last_timestamp) && (parseInt(timestamp)<=window.top_window.last_timestamp))) continue;

			// Find container to place message
			if (!message_container_global)
			{
				room_id=messages[i].getAttribute('room_id');
				current_room_id=room_id;
				message_container=null;
			} else
			{
				current_room_id=messages[i].getAttribute('room_id');
			}
			if (document.getElementById('messages_window_'+current_room_id))
			{
				message_container=document.getElementById('messages_window_'+current_room_id);
				tab_element=document.getElementById('tab_'+current_room_id);
				if ((tab_element) && (tab_element.className.indexOf('chat_lobby_convos_current_tab')==-1))
				{
					tab_element.className=((tab_element.className.indexOf('chat_lobby_convos_tab_first')!=-1)?'chat_lobby_convos_tab_first ':'')+'chat_lobby_convos_tab_new_messages';
				}
			} else if ((typeof opened_popups['room_'+current_room_id]!='undefined') && (!opened_popups['room_'+current_room_id].is_shutdown) && (opened_popups['room_'+current_room_id].document)) // Popup
			{
				message_container=opened_popups['room_'+current_room_id].document.getElementById('messages_window_'+current_room_id);
			}
			if (!message_container)
			{
				cannot_process_all=true;
				continue; // Still no luck
			}

			// If we got this far, recognise the message as received
			newest_id_here=parseInt(id);
			if ((newest_timestamp_here=null) || (newest_timestamp_here<parseInt(timestamp))) newest_timestamp_here=parseInt(timestamp);

			var doc=document;
			if (typeof opened_popups['room_'+current_room_id]!='undefined')
			{
				var popup_win=opened_popups['room_'+current_room_id];
				if (!popup_win.document) // We have nowhere to put the message
				{
					cannot_process_all=true;
					continue;
				}
				doc=popup_win.document;

				// Feed in details, so if it becomes autonomous, it knows what to run with
				popup_win.last_timestamp=window.last_timestamp;
				popup_win.last_event_id=window.last_event_id;
				if ((newest_id_here) && ((popup_win.last_message_id==null) || (popup_win.last_message_id<newest_id_here)))
				{
					popup_win.last_message_id=newest_id_here;
				}
				if (popup_win.last_timestamp<newest_timestamp_here)
					popup_win.last_timestamp=newest_timestamp_here;
			}

			if (doc.getElementById('chat_message__'+id)) continue; // Already there

			// Clone the node so that we may insert it
			cloned_message=doc.createElement('div');
			set_inner_html(cloned_message,(typeof messages[i].xml!='undefined')?messages[i].xml/*IE-only optimisation*/:get_outer_html(messages[i].childNodes[0]));
			cloned_message=cloned_message.childNodes[0];
			cloned_message.id='chat_message__'+id;

			// Non-first message
			if (message_container.childNodes.length>0)
			{
				message_container.insertBefore(cloned_message,message_container.childNodes[0]);

				if (!first_set) // Only if no other message sound already for this event update
				{
					if (!skip_incoming_sound)
					{
						if (typeof window.play_chat_sound!='undefined') play_chat_sound(window.has_focus?'message_received':'message_background',messages[i].getAttribute('sender_id'));
					}
					flashable_alert=true;
				}
			} else // First message
			{
				set_inner_html(message_container,'');
				message_container.appendChild(cloned_message);
				first_set=true; // Let the code know the first set of messages has started, squashing any extra sounds for this event update
				if (!skip_incoming_sound)
				{
					play_chat_sound('message_initial');
				}
			}

			if (!message_container_global)
			{
				current_room_id=-1; // We'll be gathering for all rooms we're in now, because this messaging is coming through the master control window
			}
		}

		else if (messages[i].nodeName=='chat_members_update') // UPDATE MEMBERS LIST IN ROOM
		{
			var members_element=document.getElementById('chat_members_update');
			if (members_element) set_inner_html(members_element,merge_text_nodes(messages[i].childNodes));
		}

		else if ((messages[i].nodeName=='chat_event') && (typeof window.im_participant_template!='undefined')) // Some kind of transitory event
		{
			event_type=messages[i].getAttribute('event_type');
			room_id=messages[i].getAttribute('room_id');
			member_id=messages[i].getAttribute('member_id');
			username=messages[i].getAttribute('username');
			avatar_url=messages[i].getAttribute('avatar_url');

			id=merge_text_nodes(messages[i].childNodes);

			switch (event_type)
			{
				case 'BECOME_ACTIVE':
					if (window.TRANSITORY_ALERT_TIME!=0)
					{
						flashable_alert=true;
						tmp_element=document.getElementById('online_'+member_id);
						if (tmp_element)
						{
							if (get_inner_html(tmp_element).toLowerCase()=='{!ACTIVE;^}'.toLowerCase()) break;
							set_inner_html(tmp_element,'{!ACTIVE;^}');
							var friend_img=document.getElementById('friend_img_'+member_id);
							if (friend_img) friend_img.className='friend_active';
							var alert_box_wrap=document.getElementById('alert_box_wrap');
							if (alert_box_wrap) alert_box_wrap.style.display='block';
							var alert_box=document.getElementById('alert_box');
							if (alert_box) set_inner_html(alert_box,'{!NOW_ONLINE;^}'.replace('{'+'1}',username));
							window.setTimeout(function() {
								if (document.getElementById('alert_box')) // If the alert box is still there, remove it
									alert_box_wrap.style.display='none';
							} , window.TRANSITORY_ALERT_TIME);

							if (!skip_incoming_sound)
							{
								play_chat_sound('contact_on',member_id);
							}
						} else if (!document.getElementById('chat_lobby_convos_tabs'))
						{
							create_overlay_event(/*skip_incoming_sound*/true,member_id,'{!NOW_ONLINE;^}'.replace('{'+'1}',username),function() { start_im(member_id,true); return false; } ,avatar_url);
						}
					}

					rooms=find_im_convo_room_ids();
					for (var r in rooms)
					{
						room_id=rooms[r];
						var doc=document;
						if ((typeof opened_popups['room_'+room_id]!='undefined') && (!opened_popups['room_'+room_id].is_shutdown))
						{
							if (!opened_popups['room_'+room_id].document) continue;
							doc=opened_popups['room_'+room_id].document;
						}
						tmp_element=doc.getElementById('participant_online__'+room_id+'__'+member_id);
						if (tmp_element)
						{
							set_inner_html(tmp_element,'{!ACTIVE;^}');
						}
					}
					break;

				case 'BECOME_INACTIVE':
					var friend_being_tracked=false;
					tmp_element=document.getElementById('online_'+member_id);
					if (tmp_element)
					{
						if (get_inner_html(tmp_element).toLowerCase()=='{!INACTIVE;^}'.toLowerCase()) break;
						set_inner_html(tmp_element,'{!INACTIVE;^}');
						document.getElementById('friend_img_'+member_id).className='friend_inactive';
						friend_being_tracked=true;
					}

					rooms=find_im_convo_room_ids();
					for (var r in rooms)
					{
						room_id=rooms[r];
						var doc=document;
						if (typeof opened_popups['room_'+room_id]!='undefined')
						{
							if (!opened_popups['room_'+room_id].document) continue;
							doc=opened_popups['room_'+room_id].document;
						}
						tmp_element=doc.getElementById('participant_online__'+room_id+'__'+member_id);
						if (tmp_element) set_inner_html(tmp_element,'{!INACTIVE;^}');
						friend_being_tracked=true;
					}

					if (!skip_incoming_sound)
					{
						if (friend_being_tracked)
							play_chat_sound('contact_off',member_id);
					}
					break;

				case 'JOIN_IM':
					add_im_member(room_id,member_id,username,messages[i].getAttribute('away')=='1',avatar_url);

					var doc=document;
					if ((typeof opened_popups['room_'+room_id]!='undefined') && (!opened_popups['room_'+room_id].is_shutdown))
					{
						if (!opened_popups['room_'+room_id].document) break;
						doc=opened_popups['room_'+room_id].document;
					}
					tmp_element=doc.getElementById('participant_online__'+room_id+'__'+member_id);
					if (tmp_element)
					{
						if (get_inner_html(tmp_element).toLowerCase()=='{!ACTIVE;^}'.toLowerCase()) break;
						set_inner_html(tmp_element,'{!ACTIVE;^}');
						document.getElementById('friend_img_'+member_id).className='friend_active';
					}

					if (!skip_incoming_sound)
					{
						play_chat_sound('contact_on',member_id);
					}
					break;

				case 'PREINVITED_TO_IM':
					add_im_member(room_id,member_id,username,messages[i].getAttribute('away')=='1',avatar_url);
					break;

				case 'DEINVOLVE_IM':
					var doc=document;
					if (typeof opened_popups['room_'+room_id]!='undefined')
					{
						if (!opened_popups['room_'+room_id].document) break;
						doc=opened_popups['room_'+room_id].document;
					}

					tmp_element=doc.getElementById('participant__'+room_id+'__'+member_id);
					if ((tmp_element) && (tmp_element.parentNode))
					{
						var parent=tmp_element.parentNode;
						/*Actually prefer to let them go away it's cleaner if (parent.childNodes.length==1) // Don't really let them go, flag them merely as away - we'll reinvite them upon next post
						{
							tmp_element=doc.getElementById('post_'+room_id);
							if (tmp_element) tmp_element.force_invite=member_id;

							tmp_element=doc.getElementById('participant_online__'+room_id+'__'+member_id);
							if (tmp_element)
							{
								if (get_inner_html(tmp_element).toLowerCase()=='{!INACTIVE;^}'.toLowerCase()) break;
								set_inner_html(tmp_element,'{!INACTIVE;^}');
							}
						} else*/
						{
							parent.removeChild(tmp_element);
						}
						/*if (parent.childNodes.length==0)		Don't set to none, as we want to allow the 'force_invite' IM re-activation feature, to draw the other guy back -- above we pretended they're merely 'away', not just left
						{
							set_inner_html(parent,'<em class="none">{!NONE;^}</em>');
						}*/

						if (!skip_incoming_sound)
						{
							play_chat_sound('contact_off',member_id);
						}
					}
					break;
			}
		} else // INVITES

		if ((messages[i].nodeName=='chat_invite') && (typeof window.im_participant_template!='undefined'))
		{
			room_id=merge_text_nodes(messages[i].childNodes);

			if ((!document.getElementById('room_'+room_id)) && ((typeof opened_popups['room_'+room_id]=='undefined') || (opened_popups['room_'+room_id].is_shutdown)))
			{
				room_name=messages[i].getAttribute('room_name');
				avatar_url=messages[i].getAttribute('avatar_url');
				participants=messages[i].getAttribute('participants');
				var is_new=(messages[i].getAttribute('num_posts')=='0');
				var by_you=(messages[i].getAttribute('inviter')==messages[i].getAttribute('you'));

				if ((!by_you) && (!window.instant_go) && (!document.getElementById('chat_lobby_convos_tabs')))
				{
					create_overlay_event(skip_incoming_sound,messages[i].getAttribute('inviter'),'{!IM_INFO_CHAT_WITH;^}'.replace('{'+'1}',room_name),function() { window.last_message_id=-1 /*Ensure messages re-processed*/; detected_conversation(room_id,room_name,participants); return false; } ,avatar_url,room_id);
				} else
				{
					detected_conversation(room_id,room_name,participants);
				}
				flashable_alert=true;
			}
		} else

		if (messages[i].nodeName=='chat_tracking') // TRACKING
		{
			window.top_window.last_message_id=messages[i].getAttribute('last_msg');
			window.top_window.last_event_id=messages[i].getAttribute('last_event');
		}
	}

	// Get attention, to indicate something has happened
	if (flashable_alert)
	{
		if ((room_id) && (typeof opened_popups['room_'+room_id]!='undefined') && (!opened_popups['room_'+room_id].is_shutdown))
		{
			if (typeof opened_popups['room_'+room_id].getAttention!='undefined') opened_popups['room_'+room_id].getAttention();
			if (typeof opened_popups['room_'+room_id].focus!='undefined')
			{
				try
				{
					opened_popups['room_'+room_id].focus();
				}
				catch (e) {}
			}
			if (opened_popups['room_'+room_id].document)
			{
				var post=opened_popups['room_'+room_id].document.getElementById('post');
				if (post)
				{
					try
					{
						post.focus();
					}
					catch (e) {}
				}
			}
		} else
		{
			if (typeof window.getAttention!='undefined') window.getAttention();
			if (typeof window.focus!='undefined')
			{
				try
				{
					window.focus();
				}
				catch (e) {}
			}
			var post=document.getElementById('post');
			if (post && post.name=='message'/*The chat posting field is named message and IDd post*/)
			{
				try
				{
					post.focus();
				}
				catch (e) {}
			}
		}
	}

	if (window.top_window.last_timestamp<newest_timestamp_here)
		window.top_window.last_timestamp=newest_timestamp_here;

	return current_room_id;
}

function create_overlay_event(skip_incoming_sound,member_id,message,click_event,avatar_url,room_id)
{
	if (typeof room_id=='undefined') room_id=null;

	if (window!=window.top_window) return; // Can't display in an autonomous popup

	// Make sure to not show multiple equiv ones, which could happen in various situations
	if (room_id!==null)
	{
		if ((typeof window.already_received_room_invites[room_id]!='undefined') && (window.already_received_room_invites[room_id]))
			return;
		window.already_received_room_invites[room_id]=true;
	} else
	{
		if ((typeof window.already_received_contact_alert[member_id]!='undefined') && (window.already_received_contact_alert[member_id]))
			return;
		window.already_received_contact_alert[member_id]=true;
	}

	// Ping!
	if (!skip_incoming_sound)
	{
		play_chat_sound('invited',member_id);
	}

	// Start DOM stuff
	var div=document.createElement('div');
	div.className='im_event';
	//div.style.left=(get_window_width()/2-140)+'px';
	div.style.right='1em';
	div.style.bottom=((get_elements_by_class_name(document.body,'im_event').length)*185+20)+'px';
	var links=document.createElement('ul');
	links.className='actions_list';

	// Close link
	var close_popup=function() {
		if (div)
		{
			if (room_id)
			{
				generate_question_ui(
					'{!HOW_REMOVE_CHAT_NOTIFICATION;^}',
					{/*buttons__cancel: '{!INPUTSYSTEM_CANCEL;^}',*/buttons__proceed: '{!CLOSE;^}',buttons__ignore: '{!HIDE;^}'},
					'{!REMOVE_CHAT_NOTIFICATION;^}',
					null,
					function(answer)
					{
						/*if (answer.toLowerCase()=='{!INPUTSYSTEM_CANCEL;^}'.toLowerCase()) return;*/
						if (answer.toLowerCase()=='{!CLOSE;^}'.toLowerCase())
						{
							deinvolve_im(room_id,false,false);
						}
						document.body.removeChild(div);
						div=null;
					}
				);
			} else
			{
				document.body.removeChild(div);
				div=null;
			}
		}
	};
	var imgclose=document.createElement('img');
	imgclose.setAttribute('src','{$IMG;,icons/14x14/delete}'.replace(/^https?:/,window.location.protocol));
	imgclose.className='im_popup_close_button blend';
	imgclose.onclick=close_popup;
	div.appendChild(imgclose);

	// Avatar
	if (avatar_url)
	{
		var img1=document.createElement('img');
		img1.setAttribute('src',avatar_url);
		img1.className='im_popup_avatar';
		div.appendChild(img1);
	}

	// Message
	var p_message=document.createElement('p');
	set_inner_html(p_message,message);
	div.appendChild(p_message);

	// Open link
	if (!browser_matches('non_concurrent')) // Can't do on iOS due to not being able to run windows/tabs concurrently - so for iOS we only show a lobby link
	{
		var a_popup_open=document.createElement('a');
		a_popup_open.onclick=function() {
			click_event();
			document.body.removeChild(div);
			div=null;
			return false;
		};
		a_popup_open.href='#';
		set_inner_html(a_popup_open,'{!OPEN_IM_POPUP;^}');
		var li_popup_open=document.createElement('li');
		li_popup_open.appendChild(a_popup_open);
		links.appendChild(li_popup_open);
	}

	// Lobby link
	var a_goto_lobby=document.createElement('a');
	a_goto_lobby.href=window.lobby_link.replace('%21%21',member_id);
	a_goto_lobby.onclick=close_popup;
	a_goto_lobby.target='_blank';
	set_inner_html(a_goto_lobby,'{!GOTO_CHAT_LOBBY;^}');
	var li_goto_lobby=document.createElement('li');
	li_goto_lobby.appendChild(a_goto_lobby);
	links.appendChild(li_goto_lobby);

	// Add it all in
	div.appendChild(links);
	document.body.appendChild(div);

	// Contact ones disappear after a time
	if (room_id===null)
	{
		window.setTimeout(function() {
			close_popup();
		} , window.TRANSITORY_ALERT_TIME);
	}
}

function start_im(people,just_refocus)
{
	if ((browser_matches('non_concurrent')) && (!document.getElementById('chat_lobby_convos_tabs'))) return true; // Let it navigate to chat lobby

	var message=(people.indexOf(',')==-1)?'{!ALREADY_HAVE_THIS_SINGLE;^}':'{!ALREADY_HAVE_THIS;^}';
	if ((typeof window.top_window.all_conversations[people]!='undefined') && (window.top_window.all_conversations[people]!==null))
	{
		if (just_refocus)
		{
			try
			{
				var room_id=window.top_window.all_conversations[people];
				if (document.getElementById('tab_'+room_id))
				{
					chat_select_tab(document.getElementById('tab_'+room_id));
				} else
				{
					window.top_window.opened_popups['room_'+room_id].focus();
				}
				return false;
			}
			catch (e) {}
		}

		window.fauxmodal_confirm(
			message,
			function(answer) {
				if (answer) _start_im(people,false); // false, because can't recycle if its already open
			}
		);
	} else
	{
		_start_im(people,true); // true, because an IM may exist we don't have open, so let that be recycled
	}
	return false;
}

function _start_im(people,may_recycle)
{
	var div=document.createElement('div');
	div.className='loading_overlay';
	set_inner_html(div,'{!LOADING;^}');
	document.body.appendChild(div);
	do_ajax_request(maintain_theme_in_link('{$FIND_SCRIPT;,messages}?action=start_im&message_id='+window.encodeURIComponent((window.top_window.last_message_id===null)?-1:window.top_window.last_message_id)+'&may_recycle='+(may_recycle?'1':'0')+'&event_id='+window.encodeURIComponent(window.top_window.last_event_id)+keep_stub(false)),function(result) {
		var responses=result.getElementsByTagName('result');
		if (responses[0])
		{
			window.instant_go=true;
			process_chat_xml_messages(responses[0],true);
			window.instant_go=false;
		}
		document.body.removeChild(div);
	},'people='+people);
}

function invite_im(people)
{
	var room_id=find_current_im_room();
	if (!room_id)
	{
		window.fauxmodal_alert('{!NO_IM_ACTIVE;^}');
	} else
	{
		do_ajax_request('{$FIND_SCRIPT;,messages}?action=invite_im'+keep_stub(false),function() {},'room_id='+window.encodeURIComponent(room_id)+'&people='+people);
	}
}

function count_im_convos()
{
	var chat_lobby_convos_tabs=document.getElementById('chat_lobby_convos_tabs');
	var count=0,i;
	for (i=0;i<chat_lobby_convos_tabs.childNodes.length;i++)
	{
		if (chat_lobby_convos_tabs.childNodes[i].nodeName=='#text') continue;
		if (chat_lobby_convos_tabs.childNodes[i].id.substr(0,4)=='tab_') count++;
	}
	return count;
}

function find_im_convo_room_ids()
{
	var chat_lobby_convos_tabs=document.getElementById('chat_lobby_convos_tabs');
	var rooms=[],i;
	if (!chat_lobby_convos_tabs)
	{
		for (i in opened_popups)
		{
			if (i.substr(0,5)=='room_')
			{
				rooms.push(window.parseInt(i.substr(5)));
			}
		}
		return rooms;
	}
	for (i=0;i<chat_lobby_convos_tabs.childNodes.length;i++)
	{
		if (chat_lobby_convos_tabs.childNodes[i].nodeName=='#text') continue;
		if (chat_lobby_convos_tabs.childNodes[i].id.substr(0,4)=='tab_') rooms.push(window.parseInt(chat_lobby_convos_tabs.childNodes[i].id.substr(4)));
	}
	return rooms;
}

function close_chat_conversation(room_id)
{
	var is_popup=(document.body.className.indexOf('sitewide_im_popup_body')!=-1);
	/*{+START,IF,{$OR,{$NOT,{$ADDON_INSTALLED,cns_forum}},{$NOT,{$CNS}}}}*/
	generate_question_ui(
		'{!WANT_TO_DOWNLOAD_LOGS*;^}',
		{buttons__cancel: '{!INPUTSYSTEM_CANCEL*;^}',buttons__yes: '{!YES*;^}',buttons__no: '{!NO*;^}'},
		'{!CHAT_DOWNLOAD_LOGS*;^}',
		null,
		function(logs)
		{
			if (logs.toLowerCase()!='{!INPUTSYSTEM_CANCEL*;^}'.toLowerCase())
			{
				if (logs.toLowerCase()=='{!YES*;^}'.toLowerCase())
				{
					window.open('{$FIND_SCRIPT*;,download_chat_logs}?room='+room_id+'{$KEEP*;^}');
					deinvolve_im(room_id,true,is_popup);
					return;
				}
	/*{+END}*/
				deinvolve_im(room_id,false,is_popup);
	/*{+START,IF,{$OR,{$NOT,{$ADDON_INSTALLED,cns_forum}},{$NOT,{$CNS}}}}*/
			}
		}
	);
	/*{+END}*/
}

function deinvolve_im(room_id,logs,is_popup) // is_popup means that we show a progress indicator over it, then kill the window after deinvolvement
{
	if (is_popup)
	{
		var body=document.getElementsByTagName('body');
		if (typeof body[0]!='undefined')
		{
			body[0].className+=' site_unloading';
			set_inner_html(body[0],'<div class="spaced"><div aria-busy="true" class="ajax_loading vertical_alignment"><img src="'+'{$IMG*;,loading}'.replace(/^https?:/,window.location.protocol)+'" alt="{!LOADING;^}" /> <span>{!LOADING;^}<\/span><\/div><\/div>');
		}
	}

	var element,participants=null;
	var tabs=document.getElementById('chat_lobby_convos_tabs');
	if (tabs)
	{
		element=document.getElementById('room_'+room_id);
		if (!element) return; // Probably already been clicked once, lag

		var tab_element=document.getElementById('tab_'+room_id);
		element.style.display='none';
		tab_element.style.display='none';

		participants=tab_element.participants;
	} else
	{
		if (is_popup)
			participants=((typeof window.already_autonomous!='undefined') && (window.already_autonomous))?window.participants:window.top_window.opened_popups['room_'+room_id].participants;
	}

	window.top_window.already_received_room_invites[room_id]=false;
	if (is_popup) window.is_shutdown=true;

	window.setTimeout(function() // Give time for any logs to download (download does not need to have finished - but must have loaded into a request response on the server side)
	{
		window.top_window.do_ajax_request('{$FIND_SCRIPT;,messages}?action=deinvolve_im'+window.top_window.keep_stub(false),function() {},'room_id='+window.encodeURIComponent(room_id)); // Has to be on top_window or it will be lost if the window was explicitly closed (it is unloading mode and doesn't want to make a new request)

		if (participants)
			window.top_window.all_conversations[participants]=null;

		if (tabs)
		{
			if ((element) && (element.parentNode)) element.parentNode.removeChild(element);
			if (!tab_element.parentNode) return;

			tab_element.parentNode.removeChild(tab_element);

			// All gone?
			var count=count_im_convos();
			if (count==0)
			{
				set_inner_html(tabs,'&nbsp;');
				document.getElementById('chat_lobby_convos_tabs').style.display='none';
				set_inner_html(document.getElementById('chat_lobby_convos_areas'),no_im_html);
				if (document.getElementById('invite_ongoing_im_button')) document.getElementById('invite_ongoing_im_button').disabled=true;
			} else
			{
				chat_select_tab(document.getElementById('tab_'+find_im_convo_room_ids().pop()));
			}
		} else if (is_popup)
		{
			window.onbeforeunload=null;
			window.close();
		}
	}, logs?window.LOGS_DOWNLOAD_INTERVAL:10);
}

function detected_conversation(room_id,room_name,participants) // Assumes conversation is new: something must check that before calling here
{
	window.top_window.last_event_id=-1; // So that invite events re-run

	var areas=document.getElementById('chat_lobby_convos_areas');
	var tabs=document.getElementById('chat_lobby_convos_tabs');
	var lobby;
	if (tabs) // Chat lobby
	{
		tabs.style.display='block';
		if (document.getElementById('invite_ongoing_im_button')) document.getElementById('invite_ongoing_im_button').disabled=false;
		var count=count_im_convos();
		// First one?
		if (count==0)
		{
			window.no_im_html=get_inner_html(areas);
			set_inner_html(areas,'');
			set_inner_html(tabs,'');
		}

		lobby=true;
	} else // Not chat lobby (sitewide IM)
	{
		lobby=false;
	}

	window.top_window.all_conversations[participants]=room_id;

	var url='{$FIND_SCRIPT_NOHTTP;,messages}?action=join_im&event_id='+window.top_window.last_event_id+window.top_window.keep_stub(false);
	var post='room_id='+window.encodeURIComponent(room_id);

	// Add in
	var new_one=window.im_area_template.replace(/\_\_room_id\_\_/g,room_id).replace(/\_\_room\_name\_\_/g,room_name);
	if (lobby)
	{
		var new_div;
		new_div=document.createElement('div');
		set_inner_html(new_div,new_one);
		areas.appendChild(new_div);

		// Add tab
		new_div=document.createElement('div');
		new_div.className='chat_lobby_convos_tab_uptodate'+((count==0)?' chat_lobby_convos_tab_first':'');
		set_inner_html(new_div,escape_html(room_name));
		new_div.setAttribute('id','tab_'+room_id);
		new_div.participants=participants;
		new_div.onclick=function() { chat_select_tab(new_div); } ;
		tabs.appendChild(new_div);
		chat_select_tab(new_div);

		// Tell server we've joined
		do_ajax_request(url,function(ajax_result_frame,ajax_result) { process_chat_xml_messages(ajax_result,true); },post);
	} else
	{
		// Open popup
		var im_popup_window_options='width=370,height=460,menubar=no,toolbar=no,location=no,resizable=no,scrollbars=yes,top='+((screen.height-520)/2)+',left='+((screen.width-440)/2);
		var new_window=window.open('{$BASE_URL;,0}'.replace(/^https?:/,window.location.protocol)+'/data/empty.html?instant_messaging','room_'+room_id,im_popup_window_options); // The "?instant_messaging" is just to make the location bar less surprising to the user ;-) [modern browsers always show the location bar for security, even if we try and disable it]
		if ((!new_window) || (typeof new_window.window=='undefined' /*BetterPopupBlocker for Chrome returns a fake new window but won't have this defined in it*/))
		{
			fauxmodal_alert('{!chat:_FAILED_TO_OPEN_POPUP;,{$PAGE_LINK*,_SEARCH:popup_blockers:failure=1,0,1}}',null,'{!chat:FAILED_TO_OPEN_POPUP;^}',true);
		}
		window.setTimeout(function() // Needed for Safari to set the right domain, and also to give window an opportunity to attach itself on its own accord
		{
			if ((typeof opened_popups['room_'+room_id]!='undefined') && (opened_popups['room_'+room_id]!=null) && (!opened_popups['room_'+room_id].is_shutdown)) // It's been reattached already
			{
				return;
			}

			opened_popups['room_'+room_id]=new_window;

			if ((new_window) && (typeof new_window.document!='undefined'))
			{
				new_window.document.open();
				new_window.document.write(new_one); // This causes a blocking on Firefox while files download/parse. It's annoying, you'll see the popup freezes. But it works after a few seconds.
				new_window.document.close();
				new_window.top_window=window;
				new_window.room_id=room_id;
				new_window.load_from_room_id=-1;

				window.setTimeout(function() // Allow XHTML to render; needed for .document to be available, which is needed to write in seeded chat messages
				{
					if (!new_window.document) return;

					new_window.participants=participants;

					new_window.onbeforeunload=function() {
						return '{!CLOSE_VIA_END_CHAT_BUTTON;^}';
						//new_window.close_chat_conversation(room_id);
					};

					try
					{
						new_window.focus();
					}
					catch (e) {}

					// Tell server we have joined
					do_ajax_request(url,function(ajax_result_frame,ajax_result) { process_chat_xml_messages(ajax_result,true); },post);

					// Set title
					var dom_title=new_window.document.getElementsByTagName('title')[0];
					if (dom_title!=null)
						new_window.document.title=get_inner_html(dom_title).replace(/<.*?>/g,''); // For Safari

				},500); /* Could be 60 except for Firefox which is slow */
			}
		},60);
	}
}

function add_im_member(room_id,member_id,username,away,avatar_url)
{
	window.setTimeout(function() {
		var doc=document;
		if (typeof opened_popups['room_'+room_id]!='undefined')
		{
			if (opened_popups['room_'+room_id].is_shutdown) return;
			if (!opened_popups['room_'+room_id].document) return;
			doc=opened_popups['room_'+room_id].document;
		}
		if (away)
		{
			var tmp_element=doc.getElementById('online_'+member_id);
			if ((tmp_element) && (get_inner_html(tmp_element).toLowerCase()=='{!ACTIVE;^}'.toLowerCase())) away=false;
		}
		if (doc.getElementById('participant__'+room_id+'__'+member_id)) return; // They're already put in it
		var new_participant=doc.createElement('div');
		var new_participant_inner=window.im_participant_template.replace(/\_\_username\_\_/g,username);
		new_participant_inner=new_participant_inner.replace(/\_\_id\_\_/g,member_id);
		new_participant_inner=new_participant_inner.replace(/\_\_room\_id\_\_/g,room_id);
		new_participant_inner=new_participant_inner.replace(/\_\_avatar\_url\_\_/g,avatar_url);
		if (avatar_url=='') new_participant_inner=new_participant_inner.replace('style="display: block" id="avatar__','style="display: none" id="avatar__');
		new_participant_inner=new_participant_inner.replace(/\_\_online\_\_/g,away?'{!INACTIVE;^}':'{!ACTIVE;^}');
		set_inner_html(new_participant,new_participant_inner);
		new_participant.setAttribute('id','participant__'+room_id+'__'+member_id);
		var element=doc.getElementById('participants__'+room_id);
		if (element) // If we've actually got the HTML for the room setup
		{
			var p_list=get_inner_html(element).toLowerCase();

			if ((p_list.indexOf('<em class="none">')!=-1) || (p_list.indexOf('<em class="loading">')!=-1))
				set_inner_html(element,'');
			element.appendChild(new_participant);
			if (doc.getElementById('friend_img_'+member_id)) doc.getElementById('friend__'+member_id).style.display='none';
		}
	}, 0);
}

function find_current_im_room()
{
	var chat_lobby_convos_tabs=document.getElementById('chat_lobby_convos_tabs');
	if (!chat_lobby_convos_tabs) return window.room_id;
	for (var i=0;i<chat_lobby_convos_tabs.childNodes.length;i++)
	{
		if ((chat_lobby_convos_tabs.childNodes[i].nodeName.toLowerCase()=='div') && (chat_lobby_convos_tabs.childNodes[i].className.indexOf('chat_lobby_convos_current_tab')!=-1))
		{
			return window.parseInt(chat_lobby_convos_tabs.childNodes[i].id.substr(4));
		}
	}
	return null;
}

function chat_select_tab(element)
{
	var i;
	var chat_lobby_convos_tabs=document.getElementById('chat_lobby_convos_tabs');
	for (i=0;i<chat_lobby_convos_tabs.childNodes.length;i++)
	{
		if (chat_lobby_convos_tabs.childNodes[i].className.indexOf('chat_lobby_convos_current_tab')!=-1)
		{
			chat_lobby_convos_tabs.childNodes[i].className=((chat_lobby_convos_tabs.childNodes[i].className.indexOf('chat_lobby_convos_tab_first')!=-1)?'chat_lobby_convos_tab_first ':'')+'chat_lobby_convos_tab_uptodate';
			document.getElementById('room_'+chat_lobby_convos_tabs.childNodes[i].id.substr(4)).style.display='none';
			break;
		}
	}

	document.getElementById('room_'+element.id.substr(4)).style.display='block';
	try
	{
		document.getElementById('post_'+element.id.substr(4)).focus();
	}
	catch (e) {}
	element.className=((element.className.indexOf('chat_lobby_convos_tab_first')!=-1)?'chat_lobby_convos_tab_first ':'')+'chat_lobby_convos_tab_uptodate chat_lobby_convos_current_tab';
}

function detect_if_chat_window_closed(die_on_lost,become_autonomous_on_lost)
{
	var lost_connection=false;
	try
	{
		/*if (browser_matches('non_concurrent'))	Pointless as document.write doesn't work on iOS without tabbing back and forth, so initial load is horribly slow in first place
		{
			throw 'No multi-process on iOS';
		}*/

		if ((!window.opener) || (!window.opener.document))
		{
			lost_connection=true;
		} else
		{
			var room_id=find_current_im_room();
			if (typeof window.opener.opened_popups['room_'+room_id]=='undefined')
			{
				var chat_lobby_convos_tabs=window.opener.document.getElementById('chat_lobby_convos_tabs');
				if (chat_lobby_convos_tabs) // Now in the chat lobby, consider this a confirmed loss, because we don't want duplicate IM spaces
				{
					die_on_lost=true;
					become_autonomous_on_lost=false;
					lost_connection=true;
				} else
				{
					window.opener.opened_popups['room_'+room_id]=window; // Reattach, presumably a navigation has happened

					if ((typeof window.already_autonomous!='undefined') && (window.already_autonomous)) // Losing autonomity again?
					{
						window.top_window=window.opener;
						chat_check(false,window.last_message_id,window.last_event_id);
						window.already_autonomous=false;
					}

					if (typeof window.opener.console.log!='undefined') window.opener.console.log('Reattaching chat window to re-navigated master window.');
				}
			}
		}
	}
	catch(err)
	{
		lost_connection=true;
	}

	if (lost_connection)
	{
		if (typeof die_on_lost=='undefined') die_on_lost=false;
		if (typeof become_autonomous_on_lost=='undefined') become_autonomous_on_lost=false;

		if (become_autonomous_on_lost) // Becoming autonomous means allowing to work with a master window
		{
			chat_window_become_autonomous();
		}
		else if (die_on_lost)
		{
			window.is_shutdown=true;
			window.onbeforeunload=null;
			window.close();
		} else
		{
			if ((typeof window.already_autonomous=='undefined') || (!window.already_autonomous))
			{
				window.setTimeout(function() { detect_if_chat_window_closed(false,true); }, 3000); // If connection still lost after this time then kill the window
			}
		}
	}
}

function chat_window_become_autonomous()
{
	if ((typeof window.already_autonomous=='undefined') || (!window.already_autonomous))
	{
		window.top_window=window;
		chat_check(false,window.last_message_id,window.last_event_id);
		window.already_autonomous=true;
	}
}
