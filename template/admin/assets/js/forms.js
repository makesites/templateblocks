
var error = new Array();
error['no_block_title'] = 'You need to put a Title for your block';
error['no_block_content'] = 'You need to put some content in your block';
error['no_template_title'] = 'Your template needs a title';
error['no_section_title'] = 'What is the title of this section?';
error['no_section_slug'] = 'You need to enter a unique name for the section that will be part of it\'s URL. No spaces please...';
error['no_section_content'] = 'Doesn\'t this section have any content?';
error['reserved_word'] = 'You have used a reserved word on the selected field - please try another one.';

error['no_website_title'] = 'You need to enter a title for your website';
error['no_template_dir'] = 'The files of Template blocks need to be self contained in their own folder';
error['no_database_server'] = 'Enter the server where your database is located';
error['no_database_name'] = 'Enter the name of the database you will use for this script';
error['no_database_user'] = 'Enter the username to log in to the database';
error['no_database_password'] = 'Enter the password to log in to the database';

error['no_admin_user'] = 'You need to enter the admin user';
error['no_admin_password'] = 'You need to enter the admin password';

var info = new Array();
info['external_script'] = '<p>Please enter the path of the PHP script you want to use - that is the full URL without the domain in front but with the complete filename of the script in the end. <br />Example: /path/to/script.php</p>';

function validateForm(form) {

  if( form.name == 'login' ) {
    if(form.username.value == "" ) {
      showFeedback(error['no_admin_user'], 'yellow');
	  form.username.focus();
	} else if (form.password.value == "") {
      showFeedback(error['no_admin_password'], 'yellow');
	  form.password.focus();
    } else {
      submitForm(form);
	}
  }

  if (form.name == 'blocks-edit') {
    if(form.block_title.value == "" ) {
      showFeedback(error['no_block_title'], 'yellow');
	  form.block_title.focus();
	} else if (form.block_title.value == "template") {
      showFeedback(error['reserved_word'], 'yellow');
	  form.block_title.focus();
	} else if (form.block_content.value == "") {
      showFeedback(error['no_block_content'], 'yellow');
	  form.block_content.focus();
    } else {
      submitForm(form); 
	}
  }
  
  if (form.name == 'templates-edit') {
    if(form.template_title.value == "" ) {
      showFeedback(error['no_template_title'], 'yellow');
	  form.template_title.focus();
    } else {
      submitForm(form); 
	}
  }
  
  if (form.name == 'sections-edit') {
    form.section_slug.value = fixSlug(form.section_slug.value);
    if( form.section_title.value == "" ) {
      showFeedback(error['no_section_title'], 'yellow');
	  form.section_title.focus();
	} else if( form.section_slug.value == "" || form.section_slug.value.indexOf(" ") != -1 ) {
      showFeedback(error['no_section_slug'], 'yellow');
	  form.section_slug.focus();
	} else if( form.section_content.value == "" ) {
      showFeedback(error['no_section_content'], 'yellow');
	  form.section_content.focus();
    } else {
      submitForm(form); 
	}
  }

  if( form.name == 'settings' ) {
    if( form.website.value == "" ) {
      showFeedback(error['no_website_title'], 'yellow');
	  form.website.focus();
    } else if( form.template_dir.value == "" ) {
      showFeedback(error['no_template_dir'], 'yellow');
	  form.template_dir.focus();
    } else if( form.database_server.value == "" ) {
      showFeedback(error['no_database_server'], 'yellow');
	  form.database_server.focus();
    } else if( form.database_name.value == "" ) {
      showFeedback(error['no_database_name'], 'yellow');
	  form.database_name.focus();
    } else if( form.database_user.value == "" ) {
      showFeedback(error['no_database_user'], 'yellow');
	  form.database_user.focus();
    } else if( form.database_password.value == "" ) {
      showFeedback(error['no_database_password'], 'yellow');
	  form.database_password.focus();
    } else if( form.admin_user.value == "" ) {
      showFeedback(error['no_admin_user'], 'yellow');
	  form.admin_user.focus();
    } else if( form.admin_password.value == "" ) {
      showFeedback(error['no_admin_password'], 'yellow');
	  form.admin_password.focus();
    } else {
      submitForm(form); 
	}
  }

}

function submitForm(form) {

  showLoading();
	
  var formdata = form;
  $(formdata).request({
    onSuccess: function(t) {
    },
    // Handle 404
    on404: function(t) {
      showFeedback('Error 404: location "' + t.statusText + '" was not found.', 'red');
    },
    // Handle other errors
    onFailure: function(t) {
      showFeedback('Error: ' + t.status + ' -- ' + t.statusText, 'red');
    },
    onComplete: showResponse
  });
  
}

function showResponse(t) {

  hideLoading();

  if(t.responseText && t.responseText !='1'){
    showFeedback(t.responseText, 'red');
  } else if ( document.getElementById('action').value == 'delete' || document.getElementById('action').value == 'add' ) {
    gotoPage( document.getElementById('page').value );
  } else if ( document.getElementById('action').value == 'login' ) {
    // login successful - redirect to the admin page
	window.location="admin.php";
  } else {
    showFeedback('Form data saved!', 'green');
  }

}


function showFeedback(text, color) {
  var feedback = document.getElementById('feedback');
  if( feedback.style.display == 'none' ){
    feedback.style.background = '#ffffff';
    feedback.style.display = 'block';
  }
  switch(color) {
    case 'red':
	  var hex_color = '#ffdddd';
	  break;
    case 'green':
	  var hex_color = '#eeffdd';
	  break;
    case 'yellow':
	  var hex_color = '#ffffdd';
	  break;
	default:
      var hex_color = '#ffffff';
	  break;
  }
  feedback.innerHTML = text;
  new Effect.Highlight('feedback', {startcolor:'#ffffff', endcolor:hex_color, restorecolor:hex_color, afterFinish:function(){ new Effect.Fade('feedback'); }});
}

function deleteEntry(form) {
  var validate = confirm('Are you sure you want to delete this item?');
  if( validate ){
    document.getElementById('action').value='delete'; 
    submitForm(form);
  }
}

function fixSlug(string) {
  string = string.toLowerCase();
  // remove special characters like "$" and "," etc...
  re = /\$|,|@|#|~|`|\%|\*|\^|\&|\(|\)|\+|\=|\[|\-|\]|\[|\}|\{|\;|\:|\'|\"|\<|\>|\?|\||\\|\!|\$|\./g;
  string = string.replace(re, "");
  return string;
}

function changeContent(type){
  if( type == 'external' ){ 
    // this is a section that has an external script providing the content
	var external = document.getElementById('is_external');
	var content = document.getElementById('section-content');
	if(external.checked) {
	  var validate = confirm('Are you sure you want to use a script? The content already entered will be erased.');
	  if( validate ){
        content.innerHTML = '<p>Path: <input type="text" name="section_content" id="section_content" style="width: 460px" /></p>'+info['external_script'];
	  } else {
	    external.checked = false;
	  }
	} else {
	  content.innerHTML = '<textarea name="section_content" id="section_content" class="content-wide"></textarea>';
	}
  }  
}
