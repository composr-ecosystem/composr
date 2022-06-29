<script>// <![CDATA[
	function check_passwords(form,ignore_blank_master_password)
	{
		if ((typeof form.confirm!='undefined') && (form.confirm)) return true;

		if (typeof form.elements['cns_admin_password_confirm']!='undefined')
		{
			if (!_check_password(form,'cns_admin_password','{!ADMIN_USERS_PASSWORD;^/}',false)) return false;
		}

		if (typeof form.elements['master_password_confirm']!='undefined')
		{
			if (!_check_password(form,'master_password','{!MASTER_PASSWORD;^/}',ignore_blank_master_password)) return false;
		}

		if ('{PASSWORD_PROMPT;/}'!='')
		{
			window.alert('{PASSWORD_PROMPT;/}');
		}

		return true;
	}

	function _check_password(form,field_name,field_label,ignore_blank_password)
	{
		if (ignore_blank_password && form.elements[field_name].value=='') return true;

		// Check matches with confirm field
		if (form.elements[field_name+'_confirm'].value!=form.elements[field_name].value)
		{
			window.alert('{!PASSWORDS_DO_NOT_MATCH;^/}'.replace('\{1\}',field_label));
			return false;
		}

		// Check does not match database password
		if (typeof form.elements['db_site_password']!='undefined')
		{
			if (form.elements[field_name].value!='' && form.elements[field_name].value==form.elements['db_site_password'].value)
			{
				window.alert('{!PASSWORDS_DO_NOT_REUSE;^/}'.replace('\{1\}',field_label));
				return false;
			}
		}

		// Check password is secure
		var is_secure_password=true;
		if (form.elements[field_name].value.length<8) is_secure_password=false;
		if (!form.elements[field_name].value.match(/[a-z]/)) is_secure_password=false;
		if (!form.elements[field_name].value.match(/[A-Z]/)) is_secure_password=false;
		if (!form.elements[field_name].value.match(/\d/)) is_secure_password=false;
		if (!form.elements[field_name].value.match(/[^a-zA-Z\d]/)) is_secure_password=false;
		if (!is_secure_password)
		{
			return window.confirm('{!PASSWORD_INSECURE;^/}'.replace('\{1\}',field_label)) && window.confirm('{!CONFIRM_REALLY;^/} {!PASSWORD_INSECURE;^/}'.replace('\{1\}',field_label));
		}

		return true;
	}
//]]></script>
