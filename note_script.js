// Initialisiert jQuery UI Dialog
jQuery(function() {
 		jQuery(".sd_admin_note").dialog({
 		autoOpen: false,
 		show: 'clip',
 		hide: 'clip',
 		dialogClass: 'admin-ajax-note'
 		});
 		
 		jQuery("#new_admin_note").dialog({
 		autoOpen: false,
 		show: 'clip',
 		hide: 'clip',
 		dialogClass: 'admin-ajax-note'
 		});

 		jQuery("#edit_admin_note").dialog({
 		autoOpen: false,
 		show: 'clip',
 		hide: 'clip',
 		dialogClass: 'admin-ajax-note'
 		});
 		
 		jQuery("#delete_admin_note_sure").dialog({
 		autoOpen: false,
  		modal: true,
 		resizable: false,
 		draggable: false,
 		dialogClass: 'admin-ajax-note'
 		});
		
 	});


// √ñffnet Mini Men√º
function show_note_mini_menu()
{
	if(jQuery(".note_mini_menu").is(":hidden")) {
		jQuery(".note_mini_menu").show('slide');
	}
	else
	{
		jQuery(".note_mini_menu").hide('slide');
	}

}

// √ñffnet Note
function sd_show_notes(id)
{
var note_id = "#sd_note_" + id + "";
	
		jQuery(note_id).dialog("open");
	
jQuery(".note_mini_menu").slideUp("slow");
	
}

 
// √ñffnet New Admin Node Dialog, Reset Background Color
function new_admin_note()
{

// RESET Background Color
jQuery("#new_admin_note_head").css({
	'background-color': '#FFF'
	});
	
jQuery("#new_admin_textarea").css({
	'background-color': '#FFF'
	});

jQuery("#new_admin_note").dialog("open");
jQuery(".note_mini_menu").slideUp("slow");
document.getElementById("new_admin_note_head").value = "";
document.getElementById("new_admin_textarea").value = "";
document.getElementById("new_admin_note_share").value = "0";

}

// Reset Background Color, Pr√ºfung ob Feld leer, sendet Ajax
function add_new_admin_note() {
 jQuery(".admin_note_ajax_loader").css({
  'display' : 'inline-block'
  });

// RESET Background Color
jQuery("#new_admin_note_head").css({
	'background-color': '#FFF'
	});
	
jQuery("#new_admin_textarea").css({
	'background-color': '#FFF'
	});

var head = document.getElementById("new_admin_note_head").value;
var content = document.getElementById("new_admin_textarea").value;
var share = document.getElementById("new_admin_note_share").value;

if(head == "")
{
	// √ÑNDERN
	jQuery("#new_admin_note_head").css({
	'background-color': '#FF0000'
	});
	 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });

	
}
else
{
	if(content == "")
	{
		// √ÑNDERN
		jQuery("#new_admin_textarea").css({
		'background-color': '#FF0000'
		});
		 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });

	}
	else
	{

		var data = {
		action: 'admin_notes',
		script: 'new_note',
		head: head,
		content: content,
		share: share
		};

		var response = "";		
		jQuery.post(ajaxurl, data, function(response){
			create_new_note(response, head, content, share);
		});
	}
}

}

// Bekommt aus Ajax Schleife neue ID, Erstellt neuen Node Dialog und neuen Button Mini Men√º
function create_new_note(id, head, content, share)
{
var new_note_html = "<div id='sd_note_" + id + "' class='sd_admin_note' title='" + head + "'>";

new_note_html += content;
new_note_html += "<div class='admin_note_edit_button' onclick='load_edit_admin_note(\"" + id + "\", \"" + head + "\", \"" + content + "\", \"" + share + "\")'></div>";
    if(share == '0')
    {
        new_note_html += "<span class='admin_note_info_button'>Note ist private.</span>";
    }
    else if(share == '-1')
    {
        new_note_html += "<span class='admin_note_info_button'>Note ist visible for all.</span>";
    }
    else
    {
        new_note_html += "<span class='admin_note_info_button'>Note ist visible for User with ID: " + share + "</span>";
    }
new_note_html += "</div>";
jQuery("#new_node_dummy").append(new_note_html);
jQuery("#new_admin_note").dialog("close");

jQuery(function() {
 		jQuery("#sd_note_" + id).dialog({
 		autoOpen: true,
 		show: 'blind',
 		hide: 'slide',
 		dialogClass: 'admin-ajax-note'
 		});
});

var new_note_menu = "<li><a id='note_mini_menu_" + id + "' class='note_mini_menu_note' href='javascript:sd_show_notes(\"" + id + "\");'>" + head + "</a></li>";
jQuery("#new_note_menu_dummy").append(new_note_menu);
 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });

}

// Reset Background f√ºr Edit Node, √ñffnet Edit Note, √ºbertr√§gt Daten (Daten werden √ºberschrieben)
function load_edit_admin_note(id, head, content, share) {
// RESET Background Color
document.getElementById("edit_admin_note_head").value = "";
document.getElementById("edit_admin_note_textarea").value = "";
document.getElementById("edit_admin_note_share").value = "";
jQuery("#edit_admin_note_head").css({
	'background-color': '#FFF'
	});
	
jQuery("#edit_admin_note_textarea").css({
	'background-color': '#FFF'
	});
	
jQuery("#edit_admin_note").dialog("open");
jQuery("#sd_note_" + id).dialog("close");
document.getElementById("edit_admin_note_id").value = id;

document.getElementById("edit_admin_note_head").className = "aan_wait";
document.getElementById("edit_admin_note_textarea").className = "aan_wait";
document.getElementById("edit_admin_note_share").className = "aan_wait";

    var data = {
	    action: 'admin_notes',
	    script: 'get_note',
	    id: id,
        row: 'head'
	};
    var response = "";
	jQuery.post(ajaxurl, data, function(response) {
        document.getElementById("edit_admin_note_head").value = response;
        document.getElementById("edit_admin_note_head").className = "";
    });

        var data = {
	    action: 'admin_notes',
	    script: 'get_note',
	    id: id,
        row: 'content'
	};
    var response = "";
	jQuery.post(ajaxurl, data, function(response) {
        document.getElementById("edit_admin_note_textarea").value = response;
        document.getElementById("edit_admin_note_textarea").className = "";
    });

        var data = {
	    action: 'admin_notes',
	    script: 'get_note',
	    id: id,
        row: 'share_id'
	};
    var response = "";
	jQuery.post(ajaxurl, data, function(response) {
        document.getElementById("edit_admin_note_share").value = response;
        document.getElementById("edit_admin_note_share").className = "";
    });

}

// Zeigt Ajax Loader, L√§dt Variablen, Pr√ºft ob Felder leer, Sendet Ajax
function edit_admin_note() {
 jQuery(".admin_note_ajax_loader").css({
  'display' : 'inline-block'
  });
var edit_id = document.getElementById("edit_admin_note_id").value;
var edit_head = document.getElementById("edit_admin_note_head").value;
var edit_content = document.getElementById("edit_admin_note_textarea").value;
var edit_share = document.getElementById("edit_admin_note_share").value;

// RESET Background Color
jQuery("#edit_admin_note_head").css({
	'background-color': '#FFF'
	});
	
jQuery("#edit_admin_note_textarea").css({
	'background-color': '#FFF'
	});
	
if(edit_head == "")
{
	jQuery("#edit_admin_note_head").css({
	'background-color': '#FF0000'
	});
	 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });


}
else
{
	if(edit_content == "")
	{
		// √ÑNDERN
		jQuery("#edit_admin_note_textarea").css({
		'background-color': '#FF0000'
		});
		 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });

	}
	else
	{

		var data = {
		action: 'admin_notes',
		script: 'edit_note',
		id: edit_id,
		head: edit_head,
		content: edit_content,
        share: edit_share
		};

		var response = "";		
		jQuery.post(ajaxurl, data, function(response){
			create_edit_note(response, edit_id, edit_head, edit_content, edit_share); 
		});
	}
}

}

// Bearbeitet vorhandenen Dialog, √úbergabe Response == 0
function create_edit_note(response, id, head, content, share) {
	if(response = "0")
	{
		jQuery("#edit_admin_note").dialog("close");
		 jQuery(".admin_note_ajax_loader").css({
  'display' : 'none'
  });

		document.getElementById("note_mini_menu_" + id).innerHTML = head;
		document.getElementById("ui-dialog-title-sd_note_" + id).innerHTML = head;
 
		var new_content = content + "<div class='admin_note_edit_button' onclick='load_edit_admin_note(\"" + id + "\")'></div>";
            if(share == '0')
            {
                new_content += "<span class='admin_note_info_button'>Note ist private.</span>";
            }
            else if(share == '-1')
            {
                new_content += "<span class='admin_note_info_button'>Note ist visible for all.</span>";
            }
               else
            {
                new_content += "<span class='admin_note_info_button'>Note ist visible for User with ID: " + share + "</span>";
            }
		document.getElementById("sd_note_" + id).innerHTML = new_content;
		        jQuery("#sd_note_" + id).dialog("open");
	}

}

// √ñffnet Dialog f√ºr L√∂schbest√§tigung
function delete_admin_note() {
	jQuery("#delete_admin_note_sure").dialog("open");
}

// L√∂schbest√§tigung erfolgreich, L√§dt Variablen aus Feld, Sendet Ajax
function delete_admin_note_yes() {

var delete_id = document.getElementById("edit_admin_note_id").value;

	var data = {
	action: 'admin_notes',
	script: 'delete_note',
	id: delete_id
	};
	
	jQuery.post(ajaxurl, data);
	
	jQuery("#delete_admin_note_sure").dialog("close");
	jQuery("#sd_note_" + delete_id).dialog("destroy");
	jQuery("#edit_admin_note").dialog("close");
	document.getElementById("note_mini_menu_" + delete_id).style.visibility = "hidden"; 
	document.getElementById("note_mini_menu_" + delete_id).innerHTML = ""; 

}


// L√∂schbest√§tigung negativ, Dialog wird beendet
function delete_admin_note_no () {
jQuery("#delete_admin_note_sure").dialog("close");
}




// Vielen Dank f√ºr die Nutzung meines Plugins
// Mehr von mir auf katzenhirn.com
// oder auf twitter.com/D4N13L
// Freue mich auf eure Bugs, Verbesserungsvorschl√§ge und Feedback