"use strict";

// Handle the realtime_rain button on the bottom bar
function realtime_rain_button_load_handler()
{
	var img=document.getElementById('realtime_rain_img');

	var e=document.getElementById('real_time_surround');
	if (e) // Clicked twice - so now we close it
	{
		bubbles_tidy_up();
		if (window.bubble_timer_1)
		{
			window.clearInterval(window.bubble_timer_1);
			window.bubble_timer_1=null;
		}
		if (window.bubble_timer_2)
		{
			window.clearInterval(window.bubble_timer_2);
			window.bubble_timer_2=null;
		}
		if (e.parentNode) e.parentNode.parentNode.removeChild(e.parentNode);
		img.src='{$IMG;,icons/24x24/tool_buttons/realtime_rain_on}'.replace(/^https?:/,window.location.protocol);
		if (typeof img.srcset!='undefined')
			img.srcset='{$IMG;,icons/48x48/tool_buttons/realtime_rain_on} 2x'.replace(/^https?:/,window.location.protocol);
		return false;
	}

	img.src='{$IMG;,icons/24x24/tool_buttons/realtime_rain_off}'.replace(/^https?:/,window.location.protocol);
	if (typeof img.srcset!='undefined')
		img.srcset='{$IMG;,icons/48x48/tool_buttons/realtime_rain_off} 2x'.replace(/^https?:/,window.location.protocol);
	var tmp_element=document.getElementById('realtime_rain_img_loader');
	if (tmp_element) tmp_element.parentNode.removeChild(tmp_element);
	img.className='';

	var x=document.createElement('div');
	document.body.appendChild(x);
	set_inner_html(x,load_snippet('realtime_rain_load'));
	e=document.getElementById('real_time_surround');
	e.style.position='absolute';
	e.style.zIndex=100;
	e.style.left=0;
	e.style.top=0;
	e.style.width='100%';
	e.style.height=(get_window_scroll_height()-40)+'px';
	smooth_scroll(0);

	start_realtime_rain();

	return false; // No need to load link now, because we've done an overlay
}

// Called to start the animation
function start_realtime_rain()
{
	register_mouse_listener();

	var news_ticker=document.getElementById('news_ticker');
	news_ticker.style.top='20px';
	news_ticker.style.left=(find_width(document.getElementById('main_website_inner'))/2-find_width(news_ticker)/2)+'px';

	document.getElementById('loading_icon').style.display='block';

	window.current_time=time_now()-10;
	window.time_window=10;
	get_more_events(window.current_time+1,window.current_time+window.time_window); // note the +1 is because the time window is inclusive
	window.bubble_timer_1=window.setInterval(function() { if (window.paused) return; get_more_events(window.current_time+1,window.current_time+window.time_window); } , 10000);
	window.bubble_timer_2=window.setInterval(function() { if (window.paused) return; if (window.time_window+window.current_time>time_now()) { window.time_window=10; window.current_time=time_now()-10; } if ((typeof window.disable_real_time_indicator=='undefined') || (!window.disable_real_time_indicator)) set_time_line_position(window.current_time); window.current_time+=window.time_window/10.0; } , 1000);
}

function get_more_events(from,to)
{
	if (document.hidden) {
		return; /*{$,Don't hurt server performance needlessly when running in a background tab - let an e-mail notification alert them instead}*/
	}

	from=Math.round(from);
	to=Math.round(to);

	var url='{$BASE_URL_NOHTTP;}/data/realtime_rain.php?from='+window.encodeURIComponent(from)+'&to='+window.encodeURIComponent(to)+keep_stub();
	do_ajax_request(url,received_events);
}

function received_events(ajax_result_frame,ajax_result)
{
	document.getElementById('loading_icon').style.display='none';

	var bubbles=document.getElementById('bubbles_go_here');

	var max_height=find_height(bubbles.parentNode);
	var total_vertical_slots=max_height/183;
	var height_per_second=max_height/10;
	var frame_delay=(1000/height_per_second)/1.1; // 1.1 is a fudge factor to reduce chance of overlap (creates slight inaccuracy in spacing though)

	var window_width=get_window_width();

	var elements=ajax_result.childNodes;
	var left_pos=25,func,height,_cloned_message,cloned_message,vertical_slot;
	for (var i=0;i<elements.length;i++)
	{
		if (elements[i].nodeName.toLowerCase()=='div')
		{
			// Set up HTML (difficult, as we are copying from XML)
			_cloned_message=careful_import_node(elements[i]);
			cloned_message=document.createElement('div');
			cloned_message.id=_cloned_message.getAttribute('id');
			cloned_message.className=_cloned_message.getAttribute('class');
			set_inner_html(cloned_message,get_inner_html(_cloned_message));
			left_pos+=200;
			if (left_pos>=window_width) break; // Too much!
			window.setTimeout(function() {
				window.pending_eval_function(cloned_message);

				// Set positioning (or break-out if we have too many bubbles to show)
				cloned_message.style.position='absolute';
				cloned_message.style.zIndex=50;
				cloned_message.style.left=left_pos+'px';
				bubbles.appendChild(cloned_message);
				vertical_slot=Math.round(total_vertical_slots*cloned_message.time_offset/window.time_window);
				cloned_message.style.top=(-(vertical_slot+1)*find_height(cloned_message))+'px';

				// JS events, for pausing and changing z-index
				cloned_message.onmouseover=function() { this.style.zIndex=160; if (!window.paused) { this.pausing=true; window.paused=true; } };
				cloned_message.onmouseout=function() { this.style.zIndex=50; if (this.pausing) { this.pausing=false; window.paused=false; } };

				// Draw lines and emails animation (after delay, so that we know it's rendered by then and hence knows full coordinates)
				window.setTimeout( function(cloned_message) { return function() {
					if (typeof cloned_message.lines_for!='undefined')
					{
						if (!browser_matches('ie')) /* Too slow / inaccurate */
						{
							//for (var j=0;j<cloned_message.lines_for.length;j++)		Too slow even on Chrome
							//	draw_line(cloned_message.lines_for[j],cloned_message.id);
						}

						if ((typeof cloned_message.icon_multiplicity!='undefined') && (!browser_matches('ie')/*Too slow on IE*/))
						{
							var num=cloned_message.icon_multiplicity;
							var main_icon=get_elements_by_class_name(cloned_message,'email-icon')[0];
							var next_icon;
							var icon_spot=document.getElementById('real_time_surround');
							if (find_pos_y(icon_spot,true)>0) icon_spot=icon_spot.parentNode;
							for (var x=0;x<num;x++)
							{
								window.setTimeout( function(main_icon,next_icon) { return function() {
									next_icon=document.createElement('div');
									next_icon.className=main_icon.className;
									set_inner_html(next_icon,get_inner_html(main_icon));
									next_icon.style.position='absolute';
									next_icon.style.left=find_pos_x(main_icon,true)+'px';
									next_icon.style.top=find_pos_y(main_icon,true)+'px';
									next_icon.style.zIndex=80;
									next_icon.x_vector=5-Math.random()*10;
									next_icon.y_vector=-Math.random()*6;
									next_icon.opacity=1.0;
									icon_spot.appendChild(next_icon);
									next_icon.animation_timer=window.setInterval(function(next_icon) { return function() {
										if (window.paused) return;

										var left=(sts(next_icon.style.left)+next_icon.x_vector);
										next_icon.style.left=left+'px';
										var top=(sts(next_icon.style.top)+next_icon.y_vector);
										next_icon.style.top=top+'px';
										set_opacity(next_icon,next_icon.opacity);
										next_icon.opacity*=0.98;
										next_icon.y_vector+=0.2;
										if ((top>max_height) || (next_icon.opacity<0.05) || (left+50>window_width) || (left<0))
										{
											window.clearInterval(next_icon.animation_timer);
											next_icon.animation_timer=null;
											next_icon.parentNode.removeChild(next_icon);
										}
									} }(next_icon) , 50 );
								} }(main_icon,next_icon), 7000+500*x);
							}
						}
					}
				} } (cloned_message) , 100);

				// Set up animation timer
				func=function(e) { return function() { animate_down(e); } }(cloned_message);
				cloned_message.timer=window.setInterval(func , frame_delay );
			} , 0);
		}
	}
}

function animate_down(e,avoid_remove)
{
	if (window.paused) return;

	var bubbles=document.getElementById('bubbles_go_here');
	var max_height=find_height(bubbles.parentNode);
	var jump_speed=1;
	var new_pos=sts(e.style.top)+jump_speed;
	e.style.top=new_pos+'px';

	if ((new_pos>max_height) || (!e.parentNode))
	{
		if (!avoid_remove)
		{
			if (e.parentNode)
			{
				var lines=get_elements_by_class_name(e,'line');
				window.total_lines-=lines.length;
				e.parentNode.removeChild(e);
			}
			window.clearInterval(e.timer);
			e.timer=null;
		}
	}

	// Also animate any lines too
	/*if (e.className!='line')		No need, they are defined relative to bubbles
	{
		var lines=get_elements_by_class_name(e,'line');
		for (var i=0;i<lines.length;i++)
			animate_down(lines[i],true);
	}*/
}

function time_now()
{
	return Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
}

function timeline_click(timeline,prospective)
{
	var pos=window.mouse_x-find_pos_x(document.getElementById('time_line_image'),true);
	var timeline_length=808;
	var min_time=window.min_time;
	var max_time=time_now();
	var time=min_time+pos*(max_time-min_time)/timeline_length;
	if (!prospective)
	{
		window.current_time=time;
		bubbles_tidy_up();
		set_inner_html(document.getElementById('real_time_date'),'{!SET}');
		set_inner_html(document.getElementById('real_time_time'),'');
		document.getElementById('loading_icon').style.display='block';
	} else
	{
		set_time_line_position(time);
	}
}

function bubbles_tidy_up()
{
	var bubbles_go_here=document.getElementById('bubbles_go_here');
	if (!bubbles_go_here) return;
	var bubbles=get_elements_by_class_name(document.getElementById('real_time_surround').parentNode,'bubble_wrap');
	for (var i=0;i<bubbles.length;i++)
	{
		if (bubbles[i].timer)
		{
			window.clearInterval(bubbles[i].timer);
			bubbles[i].timer=null;
		}
	}
	set_inner_html(bubbles_go_here,'');
	window.bubble_groups=[];
	window.total_lines=0;
	var icons=get_elements_by_class_name(document.getElementById('real_time_surround').parentNode,'email_icon');
	for (var i=0;i<icons.length;i++)
	{
		if (icons[i].animation_timer)
		{
			window.clearInterval(icons[i].animation_timer);
			icons[i].animation_timer=null;
		}
		icons[i].parentNode.removeChild(icons[i]);
	}
}

function set_time_line_position(time)
{
	time=Math.round(time);

	var marker=document.getElementById('real_time_indicator');
	var timeline_length=808;
	var min_time=window.min_time;
	var max_time=time_now();
	var timeline_range=max_time-min_time;
	var timeline_offset_time=time-min_time;
	var timeline_offset_position=timeline_offset_time*timeline_length/timeline_range;
	marker.style.marginLeft=(50+timeline_offset_position)+'px';

	var date_object=new Date();
	date_object.setTime(time*1000+Date.UTC(1970,0,1));
	var realtimedate=document.getElementById('real_time_date');
	var realtimetime=document.getElementById('real_time_time');
	if (!realtimedate) return;
	set_inner_html(realtimedate,date_object.getFullYear()+'/'+(''+date_object.getMonth())+'/'+(''+date_object.getDate()));
	set_inner_html(realtimetime,(''+date_object.getHours())+':'+(''+date_object.getMinutes())+':'+(''+date_object.getSeconds()));
}

function toggle_window_pausing(button)
{
	if (window.paused)
	{
		window.paused=false;
		button.className='';
	} else
	{
		window.paused=true;
		button.className='paused';
	}
}

function draw_line(group_id,bubble_id)
{
	if (typeof window.bubble_groups[group_id]=='undefined')
	{
		window.bubble_groups[group_id]=[];
	} else
	{
		if (window.bubble_groups[group_id].indexOf(bubble_id)!=-1) return;
		if (window.total_lines>20) return; // Performance

		var others=window.bubble_groups[group_id];
		var ob=document.getElementById(bubble_id+'_main'),ob2;
		if (!ob) return;
		if (!ob.parentNode) return;
		var width=find_width(ob);
		var height=find_height(ob);
		var line;
		var x=find_pos_x(ob,true);
		var y=find_pos_y(ob,true);
		for (var i=0;i<others.length;i++)
		{
			ob2=document.getElementById(others[i]+'_main');
			if ((ob2) && (ob2.parentNode))
			{
				line=create_line(width/2,height/2,find_pos_x(ob2,true)+width/2-x,find_pos_y(ob2,true)+height/2-y,88);
				ob.appendChild(line);
				window.total_lines++;
			}
		}
	}

	window.bubble_groups[group_id].push(bubble_id);
}

// Based on http://www.gapjumper.com/research/lines.html
function create_line(x1, y1, x2, y2, margin)
{
	if (x2 < x1)
	{
		var temp=x1;
		x1=x2;
		x2=temp;
		temp=y1;
		y1=y2;
		y2=temp;
	}
	var length=Math.sqrt((x1-x2)*(x1-x2) + (y1-y2)*(y1-y2));
	if (length-margin*2<0) return;
	var line=document.createElement('div');
	line.className='line';
	line.style.position='absolute';
	line.style.zIndex=20;
	line.style.width=(length-margin*2) + 'px';
	line.style.marginLeft=margin + 'px';
	line.style.marginRight=margin + 'px';
	line.style.height='1px';

	if (browser_matches('ie'))
	{
		line.style.top=(y2 > y1) ? y1 + 'px' : y2 + 'px';
		line.style.left=x1 + 'px';
		var nCos=(x2-x1)/length;
		var nSin=(y2-y1)/length;
		line.style.filter='progid:DXImageTransform.Microsoft.Matrix(sizingMethod=\'auto expand\', M11=' + nCos + ', M12=' + -1*nSin + ', M21=' + nSin + ', M22=' + nCos + ')';
	}
	else
	{
		var angle=((x2-x1)==0)?1.57:Math.atan((y2-y1)/(x2-x1));
		line.style.top=Math.round(y1 + 0.5*length*Math.sin(angle)) + 'px';
		line.style.left=Math.round(x1 - 0.5*length*(1 - Math.cos(angle))) + 'px';
		if (!line.style.left) line.style.left=0;
		line.style.MozTransform=line.style.WebkitTransform=line.style.OTransform=line.style.transform='rotate(' + angle + 'rad)';
	}
	return line;
}
