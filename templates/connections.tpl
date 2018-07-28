{include file="head.internal.tpl" title="Connections Table / {$thisproject.project_name}"}

<script type="text/javascript">

function saveChanges()
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
				if (xmlhttp.responseText == "ok")
				{
					alert('Changes saved! Going back to the case...');
					location.href = "?p=project&project_id={$thisproject.project_id}&timeslide_id={$timeslide_id}";
				}
				else
				{
					alert('Error while saving: '+xmlhttp.responseText);
				}
			}
		}

		var sendData = serialize(document.forms[0]);
		xmlhttp.open("POST","?p=connections_save&project_id={$thisproject.project_id}&timeslide_id={$timeslide_id}",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(sendData);
}
	
function syncBox(box)
{
	var arr = box.value.split('^');
	var old_num = arr[0]+'\\^'+arr[1];
	var new_num = arr[1]+'\\^'+arr[0];
	if ($('#'+old_num).prop('checked')) $('#'+new_num).prop('checked', true);
	else $('#'+new_num).prop('checked', false);
}
	
</script>

<h3>Starting connections: {$timeslide_name}</h3>
<br><div class="middle">These connections reflect each actor's situation at the <b>beginning</b> of this Field, taking into consideration its history. It determins the actual connections, which is part of the C-Score calculation.</div>
<br><br><h4 class="middle">Field: {$timeslide_name}</h4><br>
<form>
<table style="margin:0 auto;">
<tbody>
<tr>
<td>Starting<br>Connections</td>
{for $h=0 to {$actors|@count-1}}
{if $actors[$h].dead == 0}
<td><b>{$actors[$h].actor_name}</b></td>
{else}
<td><b><span class="connections_dead">{$actors[$h].actor_name}</span></b></td>
{/if}
{/for}
</tr>

{for $a=0 to {$actors|@count-1}} 
{if $actors[$a].dead == 0}
<tr><td><b>{$actors[$a].actor_name}</b></td>
{else}
<td><b><span class="connections_dead">{$actors[$a].actor_name}</span></b></td>
{/if}
{for $b=0 to {$actors|@count-1}}
{assign var=f value="`$actors[$a].actor_id`^"}
{assign var=g value=$actors[$b].actor_id}
<td><input class="middle" type="checkbox" name="box[]" onclick="syncBox(this);" id="{$actors[$a].actor_id}^{$actors[$b].actor_id}" value="{$actors[$a].actor_id}^{$actors[$b].actor_id}"{if (isset ( $con["`$f``$g`"]))} checked{/if}{if ($actors[$a].actor_id == $actors[$b].actor_id)} disabled{/if}></td>
{/for}
</tr>
{/for}
</tbody>
</table>
</form>
<br><br>
<input class="middle" type="button" id="saveButton" value="Save and continue to case" onclick="saveChanges();">
<br>

{include file="foot.tpl"}