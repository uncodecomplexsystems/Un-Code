{include file="head.internal.tpl" title="Edit Case / {$thisproject.project_name}"}

<script type="text/javascript">
var fitness,dead,num_con,max_con,timeslide_name,timeslide_information,start_date,end_date,actor_name,actor_information,cscore_start;
var actual_con_slider_clicked, fitness_slider_clicked = false;


    $(document).ready(function() {
	
    var last_valid_selectionT, last_valid_selectionA, last_valid_selectionC = null;
	  
	// Bind Hotkeys
	$(document).keydown(function(e) {
		switch(e.which) {
			case 113: // F2 = save
			saveData();
			break;

			default: return;
			}
		e.preventDefault();
	});
	
	// "Dead"-checkbox clicked
	$('#dead').click(function() {
		if ($('#dead').prop('checked')) $('#edit_xyz_hideable').fadeOut('slow');
		else { $('#edit_xyz_hideable').css('visibility', 'visible'); $('#edit_xyz_hideable').fadeIn('slow'); }
	});
	  
	// PDef/SDef - check that only one list is selected on each side (for edit button to work)
	$('#pdef_all').change(function(event) {
		$("#pdef_actor").val([]);
		$("#sdef_actor").val([]);
		$("#sdef_all").val([]);
		$('#toggle_add_element').css('visibility', 'hidden');
		$('#showscreen').css('visibility', 'visible');
		$('#showscreen_box').val( $('#'+($('#pdef_all').val())).val() );
	});
	$('#pdef_actor').change(function(event) {
		$("#pdef_all").val([]);
		$("#sdef_actor").val([]);
		$("#sdef_all").val([]);
		$('#toggle_add_element').css('visibility', 'hidden');
		$('#showscreen').css('visibility', 'visible');
		$('#showscreen_box').val( $('#'+($('#pdef_actor').val())).val() );
	});
	$('#sdef_all').change(function(event) {
		$("#sdef_actor").val([]);
		$("#pdef_actor").val([]);
		$("#pdef_all").val([]);
		$('#toggle_add_element').css('visibility', 'hidden');
		$('#showscreen').css('visibility', 'visible');
		$('#showscreen_box').val( $('#'+($('#sdef_all').val())).val() );
	});
	$('#sdef_actor').change(function(event) {
		$("#sdef_all").val([]);
		$("#pdef_actor").val([]);
		$("#pdef_all").val([]);
		$('#toggle_add_element').css('visibility', 'hidden');
		$('#showscreen').css('visibility', 'visible');
		$('#showscreen_box').val( $('#'+($('#sdef_actor').val())).val() );
	});
	  
	// Save-Check Time Slide
	$(function(ready){
      $('#timeslide_id').change(function() {
        if (fitness != null) { if ( (document.getElementById('fitness').value != fitness) || (document.getElementById('timeslide_name').value != timeslide_name) || (document.getElementById('timeslide_information').value != timeslide_information) || (document.getElementById('start_date').value != start_date) || (document.getElementById('end_date').value != end_date) || (document.getElementById('actor_name').value != actor_name) || (document.getElementById('actor_information').value != actor_information) || (document.getElementById("dead").checked != dead))
		{
			if (confirm('Do you want to save changes before moving on?'))
			{
				$(this).val(last_valid_selectionT);
				saveData();
			} else {
			last_valid_selectionT = $(this).val();
			getData();
			}
        } else {
          last_valid_selectionT = $(this).val();
		  getData();
        }
	} else {
    last_valid_selectionT = $(this).val();
	getData();
    }
    });
	});
	  
	  // Save-Check Actor
      $('#actor_id').change(function(event) {
        if (fitness != null) { if ( (document.getElementById('fitness').value != fitness) || (document.getElementById('timeslide_name').value != timeslide_name) || (document.getElementById('timeslide_information').value != timeslide_information) || (document.getElementById('start_date').value != start_date) || (document.getElementById('end_date').value != end_date) || (document.getElementById('actor_name').value != actor_name) || (document.getElementById('actor_information').value != actor_information) || (document.getElementById("dead").checked != dead))
		{
			if (confirm('Do you want to save changes before moving on?'))
			{
				$(this).val(last_valid_selectionA);
				saveData();
			} else {
			last_valid_selectionA = $(this).val();
			getData();
			}
        } else {
          last_valid_selectionA = $(this).val();
		  getData();
        }
	} else {
    last_valid_selectionA = $(this).val();
	getData();
    }
	});
	
	
	// Save-Checks Add/Del
	function checkChange()
	{
       if (fitness != null) { if ( (document.getElementById('fitness').value != fitness) || (document.getElementById('timeslide_name').value != timeslide_name) || (document.getElementById('timeslide_information').value != timeslide_information) || (document.getElementById('start_date').value != start_date) || (document.getElementById('end_date').value != end_date) || (document.getElementById('actor_name').value != actor_name) || (document.getElementById('actor_information').value != actor_information) || (document.getElementById("dead").checked != dead))
		   {
				if (confirm('Do you want to save changes before moving on?'))
				{
					saveData();
					return false;
				}
		   }
	   }
	   return true;
	}
		
	$('#addTimeslide').click(function(event) {
		if (checkChange()) addEntry('timeslide');
	});
	$('#copyTimeslide').click(function(event) {
		if (checkChange()) copyEntry('timeslide');
	});
	$('#deleteTimeslide').click(function(event) {
		if (checkChange()) deleteEntry('timeslide_id');
	});
	$('#addActor').click(function(event) {
		if (checkChange()) addEntry('actor');
	});
	$('#deleteActor').click(function(event) {
		if (checkChange()) deleteEntry('actor_id');
	});		
	$('#add_pdef_actor').click(function(event) {
		if (checkChange()) addEntry('pdef');
	});
	$('#del_pdef_all').click(function(event) {
		if (checkChange()) deleteEntry('pdef_all');
	});
	$('#del_pdef_actor').click(function(event) {
		if (checkChange()) deleteEntry('pdef_actor');
	});
	$('#add_sdef_actor').click(function(event) {
		if (checkChange()) addEntry('sdef');
	});
	$('#del_sdef_all').click(function(event) {
		if (checkChange()) deleteEntry('sdef_all');
	});
	$('#del_sdef_actor').click(function(event) {
		if (checkChange()) deleteEntry('sdef_actor');
	});
	
	
	// Fitness Slider
	$('#fitness_slider').mousedown(function(event) {
		fitness_slider_clicked = true;
		document.getElementById('fitness').value = document.getElementById('fitness_slider').value;
	});
	$('#fitness_slider').mouseup(function(event) {
		fitness_slider_clicked = false;
		document.getElementById('fitness').value = document.getElementById('fitness_slider').value;
	});
	$('#fitness_slider').mousemove(function(event) {
		if (fitness_slider_clicked) document.getElementById('fitness').value = document.getElementById('fitness_slider').value;
	});


// Source upload functionality
function upSource()
{
var input = document.getElementById("source");
var fd = new FormData();    
fd.append( 'source', input.files[0] );
$('#sourceForm').css('visibility', 'hidden');
$('#loading').css('visibility', 'visible');
$('#source_wait').css('visibility', 'visible');

	$.ajax({
	  url: '?p=addsource&project_id={$thisproject.project_id}&timeslide_id='+$('#timeslide_id').val(),
	  data: fd,
	  processData: false,
	  contentType: false,
	  type: 'POST',
	  success: function(text) {
		if (text == 'ok')
		{
			$('#loading').css('visibility', 'hidden');
			$('#source_wait').css('visibility', 'hidden');
			$('#togglesource').css('visibility', 'visible');
			getData();
			alert('Source successfully uploaded to database.');
		}
		else
		{
			$('#loading').css('visibility', 'hidden');
			$('#source_wait').css('visibility', 'hidden');
			$('#togglesource').css('visibility', 'visible');
			alert('An error occured while uploading the source: '+text);
		}
	  },
		
	  error: function (text) {
		$('#loading').css('visibility', 'hidden');
		$('#source_wait').css('visibility', 'hidden');
		$('#togglesource').css('visibility', 'visible');
		alert ('An error occured while uploading the source: '+text);	
	  }
	});
}
 
var form = document.getElementById("sourceForm");
form.addEventListener('submit', function (event) {
    event.preventDefault();
    upSource();
});

{if isset($timeslide_id)}
$("#timeslide_id").val({$timeslide_id});
{else}
$("#timeslide_id")[0].selectedIndex = 0;
{/if}
getData();

});  



function shorten(text, max)
{
	if (text.length > max) var shortText = jQuery.trim(text).substring(0, max).trim(this) + "..."; else var shortText = text;
	return (shortText);
}

function getData()
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
			var response = xmlhttp.responseText;
			var responseArray = response.split('^');
			
			// Easy data
			document.getElementById("timeslide_name").value=responseArray[0];
			timeslide_name = responseArray[0];
			document.getElementById("timeslide_information").value=responseArray[1];
			timeslide_information=responseArray[1];
            document.getElementById("start_date").value=responseArray[2];
			start_date=responseArray[2];
			document.getElementById("end_date").value=responseArray[3];
			end_date=responseArray[3];
            document.getElementById("actor_name").value=responseArray[4];
			actor_name=responseArray[4];
			document.getElementById("actor_information").value=responseArray[5];
			actor_information=responseArray[5];
			//document.getElementById("psd").value=responseArray[6];
			document.getElementById("cscore").value=responseArray[7];
			document.getElementById("fitness").value=responseArray[8];
			fitness=responseArray[8];
			document.getElementById("fitness_slider").value=responseArray[8];
			document.getElementById("num_con").value=responseArray[9];
			num_con = responseArray[9];
			
			if (responseArray[10] == 1) 
			{
				document.getElementById("dead").checked = true;
				dead = true;
			}
			else
			{
				document.getElementById("dead").checked = false;
				dead = false;
			}
			
			document.getElementById("act_num_sim_ele").value=responseArray[11];
			document.getElementById("max_num_sim_ele").value=responseArray[12];
			//document.getElementById("weight").value=shorten(responseArray[13],8);
			$('#max_con').val(responseArray[14]);
			cscore_start = responseArray[9] / responseArray[14];
			document.getElementById("cscore_start").value = shorten(cscore_start.toString(),8);
			
			var q = 15;
			
			// Actors PDefs and SDefs
			$('#memory').empty();
			$("#pdef_actor").empty();
			if (responseArray[q] == '0')
			{
				var g = q+1;
				$("<option/>").val(0).text("(No Problem Definitions)").appendTo("#pdef_actor");
			}
			else
			{
				for (g = q+1; g < (((responseArray[q])*2)+q+1); g += 2)
				{
					$("<option/>").val(responseArray[g]).text(shorten(responseArray[g+1], 30)).appendTo("#pdef_actor");
					var $mem = $('<input/>').attr({ type: 'hidden', id: responseArray[g], value: responseArray[g+1] });
					$("#memory").append($mem);
				}
			}
			$("#sdef_actor").empty();
			if (responseArray[g] == '0')
			{
				var h = g+1;
				$("<option/>").val(0).text("(No Solution Definitions)").appendTo("#sdef_actor");
			}
			else
			{
				for (h = g+1; h < (((responseArray[g])*2)+g+1); h += 2)
				{
					$("<option/>").val(responseArray[h]).text(shorten(responseArray[h+1], 30)).appendTo("#sdef_actor");
					var $mem = $('<input/>').attr({ type: 'hidden', id: responseArray[h], value: responseArray[h+1] });
					$("#memory").append($mem);
				}
			}
			
			document.getElementById('pdef_num_actor').value = responseArray[h];
			document.getElementById('sdef_num_actor').value = responseArray[h+1];
			h = h + 2;
			
			// All PDefs and SDefs
			$("#pdef_all").empty();
			if (responseArray[h] == '0')
			{
				var e = h+1;
				$("<option/>").val(0).text("(All definitions in use)").appendTo("#pdef_all");
			}
			else
			{
				for (e = h+1; e < (((responseArray[h])*2)+h+1); e += 2)
				{
					$("<option/>").val(responseArray[e]).text(shorten(responseArray[e+1], 30)).appendTo("#pdef_all");
					var $mem = $('<input/>').attr({ type: 'hidden', id: responseArray[e], value: responseArray[e+1] });
					$("#memory").append($mem);
				}
			}
			
			$("#sdef_all").empty();
			if (responseArray[e] == '0')
			{
				var f = e+1;
				$("<option/>").val(0).text("(All definitions in use)").appendTo("#sdef_all");
			}
			else
			{
				for (f = e+1; f < (((responseArray[e])*2)+e+1); f += 2)
				{
					$("<option/>").val(responseArray[f]).text(shorten(responseArray[f+1], 30)).appendTo("#sdef_all");
					var $mem = $('<input/>').attr({ type: 'hidden', id: responseArray[f], value: responseArray[f+1] });
					$("#memory").append($mem);
				}
			}
			
			// Sources List
			$("#source_id").empty();
			if (responseArray[f] == '0')
			{
				var a = f+1;
				$("<option/>").val(0).text("(No sources uploaded yet)").appendTo("#source_id");
				document.getElementById("source_id").disabled = true;
				document.getElementById("downloadSourceButton").disabled = true;
				document.getElementById("deleteSourceButton").disabled = true;
				$('#sources-select').css('background', 'url("img/droparrowselectgrey.png") no-repeat right #ECECEB');
			}
			else
			{
				for (a = f+1; a < ((responseArray[f]*2)+f+1); a += 2)
				{
					$("<option/>").val(responseArray[a]).text(responseArray[a+1]).appendTo("#source_id");
				}
				
				document.getElementById("source_id").disabled = false;
				document.getElementById("downloadSourceButton").disabled = false;
				document.getElementById("deleteSourceButton").disabled = false;
				$('#sources-select').css('background', 'url("img/droparrowselectgreen.png") no-repeat right #E3FAE8');
			}
			
			// Time Slide List
			var oldselected = $("#timeslide_id option:selected").val()
			$("#timeslide_id").empty();
			for (b = a+1; b < (((responseArray[a])*2)+a+1); b += 2)
				{
					$("<option/>").val(responseArray[b]).text('('+($('#timeslide_id option').length+1)+') '+shorten(responseArray[b+1],28)).appendTo("#timeslide_id");
				}
			
			$("#timeslide_id option").filter(function() {
			return $(this).val() == oldselected; 
			}).prop('selected', true);
				
			// Actors List
			var oldselected = $("#actor_id option:selected").val()
			$("#actor_id").empty();
			for (c = b+1; c < (((responseArray[b])*3)+b+1); c += 3)
			{
					if (responseArray[c+2] == 'd') $("<option/>").val(responseArray[c]).text(shorten(responseArray[c+1], 20)).addClass('dead').appendTo("#actor_id");
					else $("<option/>").val(responseArray[c]).text(shorten(responseArray[c+1], 28)).addClass('alive').appendTo("#actor_id");
			}
			$("#actor_id option").filter(function() {
			return $(this).val() == oldselected; 
			}).prop('selected', true);
			
			// Element Toggle Reset
			document.getElementById("element_name").value="";
			document.getElementById("element_type").value="";
			document.getElementById("element_button").value="";
			document.getElementById("element_description").value="";
			
			// Adjust visibility of elements
			if (!dead)
			{
				$('#edit_xyz_hideable').css('visibility', 'visible');
				$('#edit_xyz_hideable').fadeIn(0);
			}
			else
			{
				$('#edit_xyz_hideable').css('visibility', 'hidden');
				$('#edit_xyz_hideable').fadeOut(0);
			}
			$('#toggle_add_element').css('visibility', 'hidden');
			$('#showscreen').css('visibility', 'visible');
			$('#showscreen_box').val('');
			$('#togglesource').css('visibility', 'visible');
			$('#sourceForm').css('visibility', 'hidden');
			$('#loading').css('visibility', 'hidden');
			}
    }
	
	// If Time Slide and Actor are selected, these values are used. If only Time Slide is selected, the top Actor will be used.
	if (document.getElementById('timeslide_id').selectedIndex != -1)
	{
		var timeslide_id = document.getElementById("timeslide_id").value;
		if (document.getElementById('actor_id').selectedIndex == -1)
		{
			// Select first actor that is not dead
			$('#actor_id').val($("#actor_id .alive").val());			
		}
		var actor_id = document.getElementById("actor_id").value;
		$('#loading').css('visibility', 'visible');
		xmlhttp.open("GET","?p=data&project_id={$thisproject.project_id}&timeslide_id="+timeslide_id+"&actor_id="+actor_id,true);
		xmlhttp.send();
	}
}

function deleteSource()
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
				if (xmlhttp.responseText == "ok") getData();
				else
				{
					$('#loading').css('visibility', 'hidden');
					alert('Error: '+xmlhttp.responseText);
				}
			}
	}
		
	if (confirm('Do you really want to delete "'+document.getElementById('source_id').options[document.getElementById('source_id').selectedIndex].text+'"?'))
	{
		$('#loading').css('visibility', 'visible');
		var delete_id = document.getElementById('source_id').options[document.getElementById('source_id').selectedIndex].value;
		xmlhttp.open("GET","?p=deletesource&project_id={$thisproject.project_id}&source_id="+delete_id,true);
		xmlhttp.send();
	}
}

function reallySaveData()
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
					getData();
				}
				else
				{
					$('#loading').css('visibility', 'hidden');
					alert(xmlhttp.responseText);
				}
			}
		}

		$('#loading').css('visibility', 'visible');
		var sendData = serialize(document.forms[0]);
		xmlhttp.open("POST","?p=save&project_id={$thisproject.project_id}",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(sendData);
}

function saveData()
{
	// First check if x,y,z are within range
	if ((document.forms[0].elements["fitness"].value >= 0) && (document.forms[0].elements["fitness"].value <= 1) && (document.forms[0].elements["num_con"].value >= 0)) reallySaveData();
	else alert ("Entered values are out of range.");
}

function copyEntry(type)
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
				$('#loading').css('visibility', 'hidden');
				if (xmlhttp.responseText == 'ok') getData();
				else alert(xmlhttp.responseText);
			}
			
		}
		
		if (type == 'timeslide')
		{
			$('#loading').css('visibility', 'visible');
			xmlhttp.open("GET","?p=copy_element&project_id={$thisproject.project_id}&timeslide_id="+$('#timeslide_id option:selected').val(),true);
			xmlhttp.send();
		}
}

function addEntry(type)
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
				if (isNumber(xmlhttp.responseText))
				{
					getData();
				}
				else
				{
					$('#loading').css('visibility', 'hidden');
					alert(xmlhttp.responseText);
				}
			}
		}
		if ((type == 'sdef') ||(type == 'pdef'))
		{
			var add_id = document.forms[0].elements[type+"_all"].options[document.forms[0].elements[type+"_all"].selectedIndex].value;
			if ((add_id == null) || (add_id == 0)) return;
			var timeslide_id = document.forms[0].elements['timeslide_id'].options[document.forms[0].elements['timeslide_id'].selectedIndex].value;
			var actor_id = document.forms[0].elements['actor_id'].options[document.forms[0].elements['actor_id'].selectedIndex].value;
			xmlhttp.open("GET","?p=add&project_id={$thisproject.project_id}&type="+type+"&id="+add_id+"&timeslide_id="+timeslide_id+"&actor_id="+actor_id,true);
		}
		else if ((type == 'pdef_all') || (type == 'sdef_all'))
		{
			var add_name = document.getElementById('element_name').value;
			if ((add_name == '') || (add_id == 0)) return;
			xmlhttp.open('GET', '?p=add&project_id={$thisproject.project_id}&type='+type+'&add_name='+add_name, true);
			
		}
		else
		{
			xmlhttp.open("GET","?p=add&project_id={$thisproject.project_id}&type="+type,true);
		}
		$('#loading').css('visibility', 'visible');
		xmlhttp.send();
}

function deleteEntry(type)
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
					if ((type == 'timeslide_id') || (type == 'actor_id'))
					{
						if (document.getElementById(type).selectedIndex != 0) document.getElementById(type).selectedIndex = document.getElementById(type).selectedIndex - 1;
						else document.getElementById(type).selectedIndex = 1;
					}
					getData();
				}
				else
				{
					$('#loading').css('visibility', 'hidden');
					alert(xmlhttp.responseText);
				}
			}
		}
		
		if ((type == "timeslide") && ($("#source_id option:selected").text() != "(No sources uploaded yet)")) if (!confirm('The current Field has sources associated with it. If you delete this Field, its sources will also be deleted. Continue?')) return;
		
		var delete_id = $('#'+type).val();
		if ((delete_id == null) && ((type == 'pdef_all') || (type == 'sdef_all')))
		{
				if (type == 'pdef_all') delete_id = $('#pdef_actor').val(); 
				if (type == 'sdef_all') delete_id = $('#sdef_actor').val();				
		}
		
		if (delete_id == null) return;
		
		

		if ((type == 'pdef_all') || (type == 'sdef_all')) if (!confirm('Do you really want to globally delete this definition? It will be deleted from all actors sharing it as well.')) return;
		$('#loading').css('visibility', 'visible');
		if ((type == 'pdef_actor') || (type == 'sdef_actor'))
		{
			var timeslide_id = document.forms[0].elements['timeslide_id'].options[document.forms[0].elements['timeslide_id'].selectedIndex].value;
			var actor_id = document.forms[0].elements['actor_id'].options[document.forms[0].elements['actor_id'].selectedIndex].value;
			xmlhttp.open("GET","?p=delete&project_id={$thisproject.project_id}&type="+type+"&id="+delete_id+"&timeslide_id="+timeslide_id+"&actor_id="+actor_id,true);
			xmlhttp.send();
		}
		else
		{
			xmlhttp.open("GET","?p=delete&project_id={$thisproject.project_id}&type="+type+"&id="+delete_id,true);
			xmlhttp.send();
		}
}

function exportCSVthis()
{
	var actor_id = document.getElementById('actor_id').value;
	var timeslide_id = document.getElementById('timeslide_id').value;
	location.href = "?p=exportcsv&project_id={$thisproject.project_id}&timeslide_id="+timeslide_id+"&actor_id="+actor_id;
}

function exportCSVall()
{
	var actor_id = document.getElementById('actor_id').value;
	location.href = "?p=exportcsv&project_id={$thisproject.project_id}&actor_id="+actor_id;
}

function exportDef()
{
	location.href = "?p=exportdef&project_id={$thisproject.project_id}";
}

function persistence()
{
	location.href = "?p=persistence&project_id={$thisproject.project_id}";
}

function visualize()
{
	var timeslide_id = document.getElementById('timeslide_id').value;
	location.href = "?p=visualize&project_id={$thisproject.project_id}&timeslide_id="+timeslide_id;
}

function goToConnections()
{
	var timeslide_id = document.getElementById('timeslide_id').value;
	location.href = "?p=connections&project_id={$thisproject.project_id}&timeslide_id="+timeslide_id;
}

function downloadSource()
{
	location.href = '?p=downloadsource&project_id={$thisproject.project_id}&source_id='+document.getElementById("source_id").value;
}

function toggleAddElement(type)
{
	var description;
	if (type == 'pdef_all') description = 'New Problem-Definition';
	else if (type == 'sdef_all') description = 'New Solution-Definition';
	document.getElementById('element_description').innerHTML = description;
	document.getElementById('element_type').value = type;
	document.getElementById('element_name').value = '';
	document.getElementById('element_button').value="Add";
	document.getElementById('element_button').onclick=function() { addEntry(document.getElementById('element_type').value); };
	$('#showscreen').css('visibility', 'hidden');
	$('#toggle_add_element').css('visibility', 'visible');
	$("#element_name").focus();
}

function toggleEditElement(type)
{
	var edit_name, edit_id;
	if (document.getElementById(type+"_all").selectedIndex == -1)
	{
		if (document.getElementById(type+"_actor").selectedIndex == -1) return;
		else
		{
			edit_id = document.getElementById(type+"_actor").options[document.getElementById(type+"_actor").selectedIndex].value;
			edit_name = document.getElementById(type+"_actor").options[document.getElementById(type+"_actor").selectedIndex].text;
		}
	}
	else
	{
		edit_id = document.getElementById(type+"_all").options[document.getElementById(type+"_all").selectedIndex].value;
		edit_name = document.getElementById(type+"_all").options[document.getElementById(type+"_all").selectedIndex].text;
	}
	if ((edit_id == 0) || (edit_name == '') || (edit_name == null)) return;
	document.getElementById('element_description').innerHTML = 'Rephrase "'+edit_name+'"';
	document.getElementById('element_type').value = edit_id;
	document.getElementById('element_name').value = document.getElementById(edit_id).value;
	document.getElementById('element_button').value="Save Rephrase";
	document.getElementById('element_button').onclick=function() { editElement(); };
	$('#showscreen').css('visibility', 'hidden');
	$('#toggle_add_element').css('visibility', 'visible');
	$("#element_name").focus();
}

function editElement()
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
					getData();
				}
				else alert(xmlhttp.responseText);
				$('#loading').css('visibility', 'hidden');
			}
		}
		var element_id = document.getElementById('element_type').value;
		if ((element_id == null) || (element_id == 0)) return;
		var element_name = document.getElementById('element_name').value;
		if (element_id == null) return;
		$('#loading').css('visibility', 'visible');
		xmlhttp.open("GET","?p=edit_element&project_id={$thisproject.project_id}&element_id="+element_id+"&element_name="+element_name,true);
		xmlhttp.send();
}
</script>

<form>
<h3>&nbsp; {$thisproject.project_name}</h3>

<table id="timeslide"><tr><td>
<h4 style="text-align: center;">Fields in this case</h4>
<select name="timeslide_id" id="timeslide_id" class="biglist" size="8">
{foreach $timeslides as $row}
<option value="{$row.timeslide_id}">{$row.timeslide_name}</option>
{/foreach}
</select>
<span class="middle"><span id="smaller">Select a Field.</span>
<br><input type="button" value="Add" id="addTimeslide"> &nbsp; <input type="button" id="copyTimeslide" value="Copy"> &nbsp; <input type="button" id="deleteTimeslide" value="Delete">
</td></tr></table>
<table id="edit_timeslide"><tr><td>
<h4 style="text-align: center;">Edit Field</h4>
Name: <input type="text" id="timeslide_name" name="timeslide_name" value="" size="30" maxlength="40">
<br>Start Date: <input type="text" id="start_date" name="start_date" value="" size="10" maxlength="10"> <span id="smaller">(DD.MM.YYYY)</span>
<br>End Date: &nbsp; <input type="text" id="end_date" name="end_date" value="" size="10" maxlength="10"> <span id="smaller">(DD.MM.YYYY)</span>
<h4 style="text-align: center;">System Perturbation</h4>
<textarea style="width: 250px; height: 80px; resize: none;" id="timeslide_information" name="timeslide_information"></textarea>
</td></tr></table>

<table id="actor"><tr><td>
<h4 style="text-align: center;">Actors in this case</h4>
<select name="actor_id" id="actor_id" class="biglist" size="8">
{foreach $actors as $row}
{if $row.dead == 0}<option class="alive" value="{$row.actor_id}">{$row.actor_name}</option>
{else}<option class="dead" value="{$row.actor_id}">{$row.actor_name}</option>
{/if}
{/foreach}
</select>
<span class="middle"><span id="smaller">Select an Actor.</span>
<br><input type="button" value="Add" id="addActor"> &nbsp; &nbsp; <input type="button" id="deleteActor" value="Delete"></span>
</td></tr></table>

<table id="edit_actor"><tr><td>
<h4 style="text-align: center;">Edit Actor</h4>
Name: <input type="text" id="actor_name" value="" name="actor_name" size="30" maxlength="40"><br><br>
Additional Actor Information:<br>
<textarea style="width: 240px; height: 150px; resize: none;" id="actor_information" name="actor_information"></textarea>
<br><br><input type="checkbox" id="dead" name="dead"> Actor is not present in this Field
</td></tr></table>

<div id="edit_xyz_hideable">
<table id="edit_xyz" rules="rows" class="xyz-table">
<tr><td colspan="3"><h4 style="text-align: center;">Set Problem-Solution-Definitions</h4></td></tr>


<tr><td style="border-bottom:none;">Pool of Problem-Definitions: <select id="pdef_all" class="biglist" size="12"></select></td><td style="border-bottom:none;"><img class="cursor" src="img/arrow1.png" id="add_pdef_actor"><br><br><img class="cursor" src="img/arrow2.png" id="del_pdef_actor"></td><td style="border-bottom:none;">This actor's Problem-Definitions: <input id="pdef_num_actor" value="0" size="2" readonly><br><select id="pdef_actor" class="biglist" size="12"></select></td></tr>
<tr><td colspan="3"><span class="middle"><input type="button" value="Add" onclick="toggleAddElement('pdef_all');"> &nbsp; <input id="del_pdef_all" type="button" value="Delete"> &nbsp; <input type="button" value="Rephrase" onclick="toggleEditElement('pdef');"></span>
</td></tr>

<tr><td style="border-bottom:none;">Pool of Solution-Definitions: <select id="sdef_all" class="biglist" size="12"></select></td><td style="border-bottom:none;"><img class="cursor" src="img/arrow1.png" id="add_sdef_actor"><br><br><img class="cursor" src="img/arrow2.png" id="del_sdef_actor"></td><td style="border-bottom:none;">This actor's Solution-Definitions: <input id="sdef_num_actor" value="0" size="2" readonly><br><select id="sdef_actor" class="biglist" size="12"></select>
<tr><td colspan="3"><span class="middle"><input type="button" value="Add" onclick="toggleAddElement('sdef_all');"><input id="del_sdef_all" type="button" value="Delete"><input type="button" value="Rephrase" onclick="toggleEditElement('sdef');"></span>
</td></tr>

<tr style="border-bottom:none;"><td colspan="3" height="90px;">
<span class="middle" id="toggle_add_element"><span id="element_description" style="font-size:90%"></span>:<br><textarea id="element_name" onfocus="this.value = this.value;" style="width: 500px; height: 40px; resize: none;" maxlength="250" id="element_name" onfocus="this.value = this.value;"></textarea><br><input type="button" id="element_button"><input type="hidden" id="element_type"></span>

<span class="middle" id="showscreen">Selected Definition:<br><textarea id="showscreen_box" maxlength="250" disabled></textarea></span>
</td></tr>

</table>

<table id="edit_cscore"><tr><td colspan="2"><h4 style="text-align: center;">C-Score calculations</h4></td></tr>

<tr><td>Actual # of Connections:<br><input id="num_con" value="" size="6" class="middle" readonly><br>Max # of Connections:<br><input class="middle" id="max_con" value="" size="6" readonly></td>
<td>Actual # of sim elements:<br><input class="middle" id="act_num_sim_ele" value="" size="6" readonly><br>Max. # of sim elements:<br><input class="middle" id="max_num_sim_ele" value="" size="6" readonly></td></tr>

<tr><td>Starting C-Score:<br><input class="middle" id="cscore_start" value="" size="8" readonly><input class="middle" type="button" value="Set starting connections" onclick="goToConnections()"></td>
<td><span style="position:relative; top:-6px;">Final C-Score:</span><textarea class="middle" id="cscore" value="" style="resize:none; width: 120px; height: 30px; border: 1px dashed #736F6F;" readonly></textarea></td></tr>
</table>

<table id="edit_fitness"><tr><td colspan="2"><h4 style="text-align: center;">Edit Fitness</h4></td></tr>
<tr><td>Fitness (F): &nbsp; <input id="fitness" name="fitness" value="" size="8" readonly></td><td colspan="2"><span style="font-size:90%;">low<input id="fitness_slider" style="width: 100px;" type="range" min ="0" max="1" step="0.05">high</span></td></tr>
</td></tr></table>

</div>

</form>

<table id="sources"><tr><td>
<div id="sources-div">
<h4 style="text-align: center;">Sources for this Field</h4>
<div id="sources-select" class="sources-select">
<select id="source_id" name="source_id" disabled="true">
<option value="0">(loading sources...)</option>
</select>
</div>
<input type='button' id='downloadSourceButton' onclick='downloadSource()' value='Open'><input style='float:right;' type='button' id='deleteSourceButton' onclick='deleteSource()' value='Delete'>

<br><button id="togglesource" onclick="$('#sourceForm').css('visibility', 'visible'); $('#togglesource').css('visibility', 'hidden');">Add new source</button>
<span id="source_wait" id="smaller" style="visibility: hidden;">Please wait...</span>
<form id="sourceForm" method="post" enctype="multipart/form-data" style="visibility: hidden;"><label for="source">Choose file (max. 32 MB):</label>
<input type="file" name="source" id="source">
<input type="submit" value="Upload File" name="submit">
</form>
</div>
</tr></td></table>

<div id="border1"></div><div id="border2"></div>
<img id="loading" src='img/loading.gif'>


<div id="save" class="middle">
<input type="button" id="saveChanges" value="Save Changes (F2)" onclick="saveData()">
<br><input type="button" value="Lineage Definitions Overview" onclick="exportDef()">
<br><input type="button" value="Show PSD Persistence" onclick="persistence()">
<br>
<br><input type="button" value="3D-Visualize" onclick="visualize()">
<br><br>
<input type="button" value="Export this Field" onclick="exportCSVthis()">
<input type="button" value="Export all Fields" onclick="exportCSVall()">
</div>

<span id="memory"></span>

{include file="foot.tpl"}