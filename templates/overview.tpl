{include file="head.internal.tpl" title="Studies Overview"}

<script type="text/javascript">
$(document).ready(function() {

	// Study
    var dialog_study, form_study,
	  study_name = $( "#study_name" ),
      study_description = $( "#study_description" ),
      allFieldsStudy = $( [] ).add( study_name ).add( study_description ),
      tipsStudy = $( ".validateTipsStudy" );
 
    function checkLengthStudy( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTipsStudy( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

     function updateTipsStudy( t ) {
      tipsStudy
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tipsStudy.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
  
     function createStudy() {
      var valid = true;
      allFieldsStudy.removeClass( "ui-state-error" );
 
      valid = valid && checkLengthStudy( study_name, "Study Title", 3, 50 );
      valid = valid && checkLengthStudy( study_description, "Study Description", 10, 1000 );
 
      if ( valid ) {
        dialog_study.dialog( "close" );
		
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok') location.href = '?';
					else alert(xmlhttp.responseText);
				}
			}
			
			var sendData = serialize(document.forms[0]);
			xmlhttp.open("POST","?p=createstudy",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);		
		
      }
      return valid;
    }
 
    dialog_study = $( "#create_study" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Create Study": createStudy,
        Cancel: function() {
          dialog_study.dialog( "close" );
        }
      },
      close: function() {
        form_study[ 0 ].reset();
        allFieldsStudy.removeClass( "ui-state-error" );
      }
    });
 
    form_study = dialog_study.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      createStudy;
    });
 
    $( "#toggle_create_study" ).button().on( "click", function() {
      dialog_study.dialog( "open" );
    });
	
	
	// Lineage (= Project)
	 var dialog_lineage, form_lineage,
 
	  lineage_name = $( "#lineage_name" ),
      lineage_description = $( "#lineage_description" ),
      allFieldsLineage = $( [] ).add( lineage_name ).add( lineage_description ),
      tipsLineage = $( ".validateTipsLineage" );
 
    function updateTipsLineage( t ) {
      tipsLineage
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tipsLineage.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLengthLineage( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTipsLineage( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
     function createLineage() {
      var valid = true;
      allFieldsLineage.removeClass( "ui-state-error" );
 
      valid = valid && checkLengthLineage( lineage_name, "Lineage Title", 3, 50 );
      valid = valid && checkLengthLineage( lineage_description, "Lineage Description", 10, 1000 );
 
      if ( valid ) {
        dialog_lineage.dialog( "close" );
		
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok') location.href = '?';
					else alert(xmlhttp.responseText);
				}
			}
			
			var sendData = serialize(document.forms[1]);
			xmlhttp.open("POST","?p=createproject",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);		
      }
      return valid;
    }
 
    dialog_lineage = $( "#create_lineage" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Create Lineage": createLineage,
        Cancel: function() {
          dialog_lineage.dialog( "close" );
        }
      },
      close: function() {
        form_lineage[ 0 ].reset();
        allFieldsLineage.removeClass( "ui-state-error" );
      }
    });
 
    form_lineage = dialog_lineage.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      createLineage;
    });
	
	$( "#toggle_create_lineage" ).button().on( "click", function() {
      dialog_lineage.dialog( "open" );
    });
	

	
	// Edit Lineage
	var dialog_elineage, eform_lineage,
	  elineage_name = $( "#elineage_name" ),
      elineage_description = $( "#elineage_description" ),
      eallFieldsLineage = $( [] ).add( elineage_name ).add( elineage_description ),
      etipsLineage = $( ".evalidateTipsLineage" );
 
    function eupdateTipsLineage( t ) {
      etipsLineage
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        etipsLineage.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function echeckLengthLineage( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        eupdateTipsLineage( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
	
	function toggleEditLineage(lineage_id)
	{
		$('#elineage_id').val(lineage_id);
		$('#elineage_name').val($('#memory_lineage_name option[value="'+lineage_id+'"]').text());
		$('#elineage_description').val($('#memory_lineage_description option[value="'+lineage_id+'"]').text());
		$('#elineage_study_id').val($('#memory_lineage_study_id option[value="'+lineage_id+'"]').text());
		dialog_elineage.dialog( "open" );
	}
	
	function editLineage()
	{
	  var valid = true;
      eallFieldsLineage.removeClass( "ui-state-error" );
 
      valid = valid && echeckLengthLineage( elineage_name, "Lineage Title", 3, 50 );
      valid = valid && echeckLengthLineage( elineage_description, "Lineage Description", 10, 1000 );
 
      if ( valid ) {
        dialog_elineage.dialog( "close" );
		
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok') location.href = '?';
					else alert(xmlhttp.responseText);
				}
			}
			
			var sendData = serialize(document.forms[2]);
			xmlhttp.open("POST","?p=editproject",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);		
      }
      return valid;
	}
	
	var dialog_elineage;
	
	  dialog_elineage = $( "#edit_lineage" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Edit Lineage": editLineage,
        Cancel: function() {
          dialog_elineage.dialog( "close" );
        }
      },
      close: function() {
        eform_lineage[ 0 ].reset();
        eallFieldsLineage.removeClass( "ui-state-error" );
      }
    });
 
    eform_lineage = dialog_elineage.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      editLineage;
    });
	
	{if (isset($projects))}
	{foreach $projects as $rowp}
	$( "#toggle_edit_lineage{$rowp.project_id}" ).button().on( "click", function() {
      toggleEditLineage({$rowp.project_id});
    });
	$('#toggle_edit_lineage{$rowp.project_id}').removeClass( "ui-button ui-widget ui-state-default ui-corner-all" );
	{/foreach}
	{/if}
	
	// Edit Study
	var dialog_estudy, eform_study,
	  estudy_name = $( "#estudy_name" ),
      estudy_description = $( "#estudy_description" ),
      eallFieldsStudy = $( [] ).add( estudy_name ).add( estudy_description ),
      etipsStudy = $( ".evalidateTipsStudy" );
 
    function eupdateTipsStudy( t ) {
      etipsStudy
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        etipsStudy.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function echeckLengthStudy( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        eupdateTipsStudy( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
	
	function toggleEditStudy(study_id)
	{
		$('#estudy_name').val($('#memory_study_name option[value="'+study_id+'"]').text());
		$('#estudy_description').val($('#memory_study_description option[value="'+study_id+'"]').text());
		$('#estudy_id').val(study_id);
		dialog_estudy.dialog( "open" );
	}
	
	function editStudy()
	{
	  var valid = true;
      eallFieldsStudy.removeClass( "ui-state-error" );
 
      valid = valid && echeckLengthStudy( estudy_name, "Study Title", 3, 50 );
      valid = valid && echeckLengthStudy( estudy_description, "Study Description", 10, 1000 );
 
      if ( valid ) {
        dialog_estudy.dialog( "close" );
		
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok') location.href = '?';
					else alert(xmlhttp.responseText);
				}
			}
			
			var sendData = serialize(document.forms[3]);
			xmlhttp.open("POST","?p=editstudy",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);		
      }
      return valid;
	}
	
	var dialog_estudy;
	
	  dialog_estudy = $( "#edit_study" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Edit Study": editStudy,
        Cancel: function() {
          dialog_estudy.dialog( "close" );
        }
      },
      close: function() {
        eform_study[ 0 ].reset();
        eallFieldsStudy.removeClass( "ui-state-error" );
      }
    });
 
    eform_study = dialog_estudy.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      editStudy;
    });
	
	{if (isset($studies))}
	{foreach $studies as $rows}
	$( "#toggle_edit_study{$rows.study_id}" ).button().on( "click", function() {
      toggleEditStudy({$rows.study_id});
    });
	$('#toggle_edit_study{$rows.study_id}').removeClass( "ui-button ui-widget ui-state-default ui-corner-all" );
	{/foreach}
	{/if}
	
	
});

function copyproject(project_id)
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
				if (xmlhttp.responseText == 'ok') location.href = '?';
				else alert(xmlhttp.responseText);
			}
		}
		
		if (confirm('Do you really want to copy this Lineage?'))
		{
			xmlhttp.open("GET","?p=copyproject&project_id="+project_id,true);
			xmlhttp.send();
		}
}

function deleteproject(project_id)
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
				if (xmlhttp.responseText == 'ok') location.href = '?';
				else alert(xmlhttp.responseText);
			}
		}
		
		if (confirm('Do you really want to delete this lineage, including all its data, fields, actors and sources?'))
		{
			if (confirm('Really delete this lineage? Last warning!'))
			{
				xmlhttp.open("GET","?p=deleteproject&project_id="+project_id,true);
				xmlhttp.send();
			}
		}
}

function deletestudy(study_id)
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
				if (xmlhttp.responseText == 'ok') location.href = '?';
				else alert(xmlhttp.responseText);
			}
		}
		
		if (confirm('Do you really want to delete this study?'))
		{
			xmlhttp.open("GET","?p=deletestudy&study_id="+study_id,true);
			xmlhttp.send();
		}
}
</script>

<h3> &nbsp; Studies Overview</h3>

<br><div class="middle">
<button id="toggle_create_study">Create new Study</button>{if (isset($studies))} &nbsp; &nbsp; <button id="toggle_create_lineage">Create new Lineage</button>{/if}
</div>
<br>
<div class="overview">

{if (!isset($studies))}
<br>No studies in database yet. Start by creating one.
{else}
{$study_counter = 0}
{foreach $studies as $row1}
{$study_counter = $study_counter + 1}
<div class="overview_study">
<p><h2>{$study_counter}. {$row1.study_name}</h2></p>
<span class="overview_study_buttons">{if (isset($projects))}<input type="button" value="Study Definitions Overview" onclick="location.href='?p=exportdef&study_id={$row1.study_id}'"> &nbsp; {/if}<input type="button" value="Edit study details" id="toggle_edit_study{$row1.study_id}"></span>
<p class="description">{$row1.study_description}</p>
{$projects_exist = 0}
{if (isset($projects))}
<p>This study includes the following lineages:
{foreach $projects as $row2}
{if $row1.study_id == $row2.study_id}
{$projects_exist = 1}
<table><tr><td>
<p><h4>{$row2.project_name}</h4></p>
<p class="overview_project_description">{$row2.project_description}</p>
<ul>
<li>{$row2.timeslides} Fields</li>
<li>{$row2.actors} Actors</li>
<li>{$row2.elements} Definitions</li>
</ul>
</td>
<td>
<div class="overview_project_buttons">
<input type="button" value="Open" onclick="location.href='?p=project&project_id={$row2.project_id}'">
<br><input type="button" value="3D-Visualize" onclick="location.href='?p=visualize&project_id={$row2.project_id}'">
<br><input type="button" value="PSD Persistence" onclick="location.href='?p=persistence&project_id={$row2.project_id}'">
<br><input type="button" value="Edit" id="toggle_edit_lineage{$row2.project_id}">
<br><input type="button" value="Copy" onclick="copyproject({$row2.project_id})">
<br><input type="button" value="Delete" onclick="deleteproject({$row2.project_id})">
</div>
</td></tr></table>
{/if}
{/foreach}
{/if}
{if $projects_exist == 0}
<br><br>This study does not contain any lineages, yet.
<br>Add lineages to this project by creating a new lineage or by editing existing ones to belong to this study.
<br><br>
<input type="button" value="Delete the whole study" onclick="deletestudy({$row1.study_id})">
{/if}
</p>
</div>
<br>
{/foreach}
{/if}
</div>


<div id="create_study" style="font-size: 70%" title="Create Study">
  <p class="validateTipsStudy">All form fields are required.</p>
  <form>
    <fieldset>
      <label for="study_name">Study Title</label>
      <br><input type="text" style="width: 400px;" name="study_name" id="study_name" class="text ui-widget-content ui-corner-all" maxlength="50">
	  <br><label for="study_description">Study Description</label>
      <br><textarea style="width: 400px; height: 100px; resize: none;" id="study_description" name="study_description"></textarea>
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<div id="create_lineage" style="font-size: 70%" title="Create Lineage">
  <p class="validateTipsLineage">All form fields are required.</p>
  <form>
    <fieldset>
      <label for="lineage_name">Lineage Title</label>
      <br><input type="text" style="width: 400px;" name="lineage_name" id="lineage_name" class="text ui-widget-content ui-corner-all" maxlength="50">
	  <br><label for="lineage_description">Lineage Description</label>
      <br><textarea style="width: 400px; height: 100px; resize: none;" id="lineage_description" name="lineage_description"></textarea>
	  <br><label for="lineage_study">This lineage belongs to study:</label>
	  <br><select id="study_id" name="study_id">
	  {foreach $studies as $row}
	  <option value="{$row.study_id}">{$row.study_name}</option>
	  {/foreach}
	  </select>
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<div id="edit_lineage" style="font-size: 70%" title="Edit Lineage">
  <p class="evalidateTipsLineage">All form fields are required.</p>
  <form>
    <fieldset>
	 <input type="hidden" name="elineage_id" id="elineage_id" value="">
      <label for="elineage_name">Lineage Title</label>
      <br><input type="text" style="width: 400px;" name="elineage_name" id="elineage_name" class="text ui-widget-content ui-corner-all" maxlength="50">
	  <br><label for="elineage_description">Lineage Description</label>
      <br><textarea style="width: 400px; height: 100px; resize: none;" id="elineage_description" name="elineage_description"></textarea>
	  <br><label for="elineage_study_id">This lineage belongs to study:</label>
	  <br><select id="elineage_study_id" name="elineage_study_id">
	  {foreach $studies as $row}
	  <option value="{$row.study_id}">{$row.study_name}</option>
	  {/foreach}
	  </select>
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<div id="edit_study" style="font-size: 70%" title="Edit Study">
  <p class="evalidateTipsStudy">All form fields are required.</p>
  <form>
    <fieldset>
	  <input type="hidden" name="estudy_id" id="estudy_id" value="">
      <label for="estudy_name">Study Title</label>
      <br><input type="text" style="width: 400px;" name="estudy_name" id="estudy_name" class="text ui-widget-content ui-corner-all" maxlength="50">
	  <br><label for="estudy_description">Study Description</label>
      <br><textarea style="width: 400px; height: 100px; resize: none;" id="estudy_description" name="estudy_description"></textarea>

      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<span id="memory">
{if (isset($studies))}
<select id="memory_study_name">
{foreach $studies as $row}
<option value="{$row.study_id}">{$row.study_name}</option>
{/foreach}
</select>
<select id="memory_study_description">
{foreach $studies as $row}
<option value="{$row.study_id}">{$row.study_description}</option>
{/foreach}
</select>
{/if}
</select>
{if (isset($projects))}
<select id="memory_lineage_name">
{foreach $projects as $row}
<option value="{$row.project_id}">{$row.project_name}</option>
{/foreach}
</select>
<select id="memory_lineage_description">
{foreach $projects as $row}
<option value="{$row.project_id}">{$row.project_description}</option>
{/foreach}
</select>
<select id="memory_lineage_study_id">
{foreach $projects as $row}
<option value="{$row.project_id}">{$row.study_id}</option>
{/foreach}
</select>
{/if}
</select>
</span>


{include file="foot.tpl"}