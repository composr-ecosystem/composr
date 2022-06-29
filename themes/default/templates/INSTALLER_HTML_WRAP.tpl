<!DOCTYPE html>

<html lang="{$LCASE*,{$LANG}}" dir="{!dir}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$CHARSET*}" />
		<meta name="GENERATOR" content="Composr" />
		<meta name="description" content="Composr installer" />
		<link rel="icon" href="{$BRAND_BASE_URL*}/favicon.ico" type="image/x-icon" />
		<link href="{CSS_URL*}" rel="stylesheet" />
		<link href="{CSS_URL_2*}" rel="stylesheet" />
		<style>
			{CSS_NOCACHE*}
		</style>
		<title>{!INSTALLER,Composr}</title>

		<meta name="robots" content="noindex, nofollow" />

		<script>// <![CDATA[
			function install_stage_load()
			{
				//set_cookie('js_on',1,120);

				var none=document.getElementById('{DEFAULT_FORUM;/}');
				if (none) none.checked=true;

				if (('{DEFAULT_FORUM;/}'!='none') && ('{DEFAULT_FORUM;/}'!='cns'))
				{
					var d=document.getElementById('forum_path');
					if (d) d.style.display='block';
				}

				var forms=document.getElementsByTagName('form');
				if (typeof forms[0]!='undefined') forms[0].title='';
			}

			function submit_settings(form)
			{
				if ((form.elements['board_prefix']) && (form.elements['board_prefix'].type!='hidden') && (form.elements['board_prefix'].value==form.elements['base_url'].value))
				{
					window.alert('{!FORUM_BASE_URL_INVALID;/}');
					return false;
				}
				if ((form.elements['board_prefix']) && (form.elements['board_prefix'].type!='hidden') && (form.elements['board_prefix'].value.substr(-7)=='/forums') && (!form.elements['board_prefix'].changed))
				{
					if (!window.confirm('{!FORUM_BASE_URL_UNCHANGED;/}')) return false;
				}

				var i;
				for (i=0;i<form.elements.length;i++)
				{
					if ((form.elements[i].className.indexOf('required1')!=-1) && (form.elements[i].value==''))
					{
						window.alert('{!IMPROPERLY_FILLED_IN;/}');
						return false;
					}
				}

				if (!check_passwords(form,false)) return false;

				if ((form.elements['db_site_password']) && (window.do_ajax_field_test))
				{
					var url='install.php?type=ajax_db_details';
					var post='db_type='+window.encodeURIComponent(form.elements['db_type'].value)+'&db_site_host='+window.encodeURIComponent(form.elements['db_site_host'].value)+'&db_site='+window.encodeURIComponent(form.elements['db_site'].value)+'&db_site_user='+window.encodeURIComponent(form.elements['db_site_user'].value)+'&db_site_password='+window.encodeURIComponent(form.elements['db_site_password'].value);
					if (!do_ajax_field_test(url,post)) return false;
				}

				if ((form.elements['db_forums_password']) && (window.do_ajax_field_test))
				{
					var url='install.php?type=ajax_db_details';
					var post='db_type='+window.encodeURIComponent(form.elements['db_type'].value)+'&db_forums_host='+window.encodeURIComponent(form.elements['db_forums_host'].value)+'&db_forums='+window.encodeURIComponent(form.elements['db_forums'].value)+'&db_forums_user='+window.encodeURIComponent(form.elements['db_forums_user'].value)+'&db_forums_password='+window.encodeURIComponent(form.elements['db_forums_password'].value);
					if (!do_ajax_field_test(url,post)) return false;
				}

				if ((form.elements['ftp_domain']) && (window.do_ajax_field_test))
				{
					var url='install.php?type=ajax_ftp_details';
					var post='ftp_domain='+window.encodeURIComponent(form.elements['ftp_domain'].value)+'&ftp_folder='+window.encodeURIComponent(form.elements['ftp_folder'].value)+'&ftp_username='+window.encodeURIComponent(form.elements['ftp_username'].value)+'&ftp_password='+window.encodeURIComponent(form.elements['ftp_password'].value);
					if (!do_ajax_field_test(url,post)) return false;
				}

				return true;
			}

			function set_cookie(cookieName,cookieValue,nDays)
			{
				var today=new Date();
				var expire=new Date();
				if (nDays==null || nDays==0) nDays=1;
				expire.setTime(today.getTime()+3600000*24*nDays);
				document.cookie=cookieName+"="+escape(cookieValue)+";expires="+expire.toUTCString();
			}

			function do_forum_choose(object,versions)
			{
				set_inner_html(document.getElementById('versions'),versions);

				var type='none';
				if ((object.id!='none') && (object.id!='cns'))
				{
					type='block';
					var label=document.getElementById('sep_forum');
					if (label) set_inner_html(label,object.nextSibling.nodeValue);
				}

				document.getElementById('forum_database_info').style.display=type;
				if (document.getElementById('forum_path'))
					document.getElementById('forum_path').style.display=type;
			}

			function toggle_section(id)
			{
				// Try and grab our item
				var itm=document.getElementById(id);
				var img=document.getElementById('img_'+id);

				if (itm.style.display=='none')
				{
					itm.style.display='block';
					if (img)
					{
						img.src='{$BASE_URL;/}/install.php?type=contract';
						img.alt=img.alt.replace('{!EXPAND;}','{!CONTRACT;}');
						img.title=img.title.replace('{!EXPAND;}','{!CONTRACT;}');
					}
				} else
				{
					itm.style.display='none';
					if (img)
					{
						img.src='{$BASE_URL;/}/install.php?type=expand';
						img.alt=img.alt.replace('{!CONTRACT;}','{!EXPAND;}');
						img.title=img.title.replace('{!CONTRACT;}','{!EXPAND;}');
					}
				}
			}
		//]]></script>

		{+START,INCLUDE,PASSWORD_CHECK_JS}INSTALLER=1{+END}
	</head>

	<body id="installer_body" class="website_body" onload="install_stage_load();">
		<div class="installer_main">
			<img alt="Composr" src="{LOGO_URL*}" width="550" height="115" />
		</div>

		<script>// <![CDATA[
			window.setTimeout(function() {
				if (window.alert===null)
				{
					/* Parser hint: .innerHTML okay */
					document.getElementById('extra_errors').innerHTML='<p class="installer_warning"><strong class="popup_blocker_warning">Your popup blocker is too aggressive<\/strong> (even error alerts cannot display). Please disable for the installer.<\/p>';
				}
			}, 0);
		//]]></script>

		<div class="installer_main_inner">
			<div class="box box___installer_html_wrap"><div class="box_inner">
				<h1>{!INSTALLER,Composr}: {!INSTALLER_STEP,{STEP},10}</h1>

				<div id="extra_errors"></div>

				<div>
					{CONTENT}
				</div>
			</div></div>
		</div>

		<div class="installer_version">
			<p>
				{!VERSION_NUM,{VERSION}}
				<br />
				Composr, {!CREATED_BY,ocProducts}
			</p>
			<p>
				<a target="_blank" title="compo.sr {!LINK_NEW_WINDOW}" href="{$BRAND_BASE_URL*}">{$BRAND_BASE_URL*}</a>
			</p>
		</div>
	</body>
</html>

