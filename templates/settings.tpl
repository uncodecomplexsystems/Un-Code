{include file="head.internal.tpl" title="User Settings"}

<script type="text/javascript">
function saveSettings()
{
	// AJAX
	if (window.XMLHttpRequest)
    {
        // IE7+, Chrome, Firefox, Safari, Opera
        xmlhttp=new XMLHttpRequest();
    }
    else
    {
        // IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
	
	xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				if (xmlhttp.responseText == 'ok') alert('Settings successfully saved.');
				else alert('Error: '+xmlhttp.responseText);
			}
		}
		
		var sendData = serialize(document.forms[0]);
		xmlhttp.open("POST","?p=savesettings",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(sendData);
}
</script>

<h3> &nbsp; Personal User Settings</h3>
<br>
<div class="overview_study">
<table>
<form>
<tr><td>
<br>
<b>Change password</b>
<br>Old password: &nbsp; <input class="input" type="password" name="oldpw" id="oldpw" value="" maxlength="40">
<br>New password: <input class="input" type="password" name="newpw" id="newpw" value="" maxlength="40">
<br><br>
</tr></td><tr><td>
<div>
<br>
<b>Special options</b>
<br>Delimiter for csv files: <input name="delimiter" id="delimiter" type="text" size="1" maxlength="1" value="{$delimiter}">
<br><span id="smaller">Changing this option only matters when exporting CSV files and viewing the export with Microsoft Excel.
<br>Please set the delimiter to "," (standard) for international language Office version or to ";" for German language Office version.</span>
<br><br>
</div>
</tr></td></table>
</table>
<br>
<div>
<input type="button" value="Save Settings" onclick="saveSettings()">
</div>
</form>
</div>

{include file="foot.tpl"}