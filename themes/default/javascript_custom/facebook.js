"use strict";

function facebook_init(app_id,channel_url,just_logged_out,serverside_fbuid,home_page_url,logout_page_url)
{
	window.fbAsyncInit=function() {
		window.FB.init({
			appId: app_id,
			channelUrl: channel_url,
			status: true,
			cookie: true,
			xfbml: true,
			version: 'v12.0'
		});

		// Ignore floods of "Unsafe JavaScript attempt to access frame with URL" errors in Chrome they are benign

		{+START,IF,{$CONFIG_OPTION,facebook_allow_signups}}
			// Calling this effectively waits until the login is active on the client side, which we must do before we can do anything (including calling a log out)
			window.FB.getLoginStatus(function(response) {
				if ((response.status=='connected') && (response.authResponse))
				{
					// If Composr is currently logging out, tell FB connect to disentangle
					// Must have JS FB login before can instruct to logout. Will not re-auth -- we know we have auth'd due to FB_CONNECT_LOGGED_OUT being set
					if (just_logged_out)
					{
						window.FB.logout(function(response) {
							if (typeof window.console!='undefined' && window.console) console.log('Facebook: Logged out.');
						});
					}

					// Facebook has automatically rebuilt its expired fbsr cookie, auth.login not triggered as already technically logged in
					else
					{
						if (serverside_fbuid===null) // Definitive mismatch between server-side and client-side, so we must refresh
						{
							facebook_trigger_refresh(home_page_url);
						}
					}

					// Leech extra code to the Facebook logout action to logout links
					var forms=document.getElementsByTagName('form');
					for (var i=0;i<forms.length;i++)
					{
						if (forms[i].action.indexOf(logout_page_url)!=-1)
						{
							forms[i].onsubmit=function(logout_link) { return function() {
								window.FB.logout(function(response) {
									if (typeof window.console!='undefined' && window.console) console.log('Facebook: Logged out.');
									window.location=logout_link;
								});
								// We cancel the form submit, as we need to wait for the AJAX request to happen
								return false;
							} }(forms[i].action);
						}
					}
				}
			});

			if (serverside_fbuid===null) // If not already in a Composr Facebook login session we may need to listen for explicit new logins
			{
				window.FB.Event.subscribe('auth.login',function(response) { // New login status arrived - so a Composr Facebook login session should be established, or ignore as we are calling a logout within this request (above)
					if (!just_logged_out) // Check it is not that logout
					{
						// ... and therefore refresh to let Composr server-side re-sync, as this was a new login initiated just now on the client side
						if ((response.status=='connected') && (response.authResponse)) // Check we really are logged in, in case this event calls without us being
						{
							facebook_trigger_refresh(home_page_url);
						}
					}
				});
			}
		{+END}
	};
}

function facebook_trigger_refresh(home_page_url)
{
	window.setTimeout(function() { // Firefox needs us to wait a bit
		if (typeof document.getElementsByTagName('failover')[0]!='undefined') return;

		if ((window.location.href.indexOf('login')!=-1) && (window==window.top))
		{
			window.location=home_page_url; // If currently on login screen, should go to home page not refresh
		} else
		{
			var current_url=window.top.location.href;
			if (current_url.indexOf('refreshed_once=1')==-1)
			{
				current_url=current_url.replace(/#.*$/,'')+((current_url.indexOf('?')==-1)?'?':'&')+'refreshed_once=1';
				window.top.location=current_url;
			}
			else if (current_url.indexOf('keep_refreshed_once=1')==-1)
			{
				//window.alert('Could not login, probably due to restrictive cookie settings.');
				window.top.location=window.top.location.toString()+'&keep_refreshed_once=1';
			}
		}
	},500);
}
