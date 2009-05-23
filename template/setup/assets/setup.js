
var error = new Array();
error['no_website_title'] = 'You need to enter a title for your website';
error['no_template_dir'] = 'The files of Template blocks need to be self contained in their own folder';
error['no_database_server'] = 'Enter the server where your database is located';
error['no_database_name'] = 'Enter the name of the database you will use for this script';
error['no_database_user'] = 'Enter the username to log in to the database';
error['no_database_password'] = 'Enter the password to log in to the database';
error['no_admin_user'] = 'You need to enter an admin user to use Template Blocks';
error['no_admin_password'] = 'You need to enter an admin password to use Template Blocks';
error['data_not_saved'] = 'Could not save your data. Please make sure all files were uploaded properly and try again.';

var tabs = new Array('welcome','step1','step2','step3');


function gotoPage(page) {

  if( !document.getElementById('loading') ){ showLoading(); }

  var url = 'index.php?page='+page;
  new Ajax.Updater('content', url, {onComplete:function(){ hideLoading(); },asynchronous:true, evalScripts:true});
  // update the tabs
  for(i=0;i<4;i++){
    if(tabs[i] != page) {
      document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-default';
	} else {
      document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-selected';
	}
  }

}

function validateForm(form)
{
  if( form.name == 'setup-step1' ) {
    if( form.website.value == "" ) {
      showFeedback(error['no_website_title'], 'yellow');
	  form.website.focus();
    } else if( form.template_dir.value == "" ) {
      showFeedback(error['no_template_dir'], 'yellow');
	  form.template_dir.focus();
    } else {
      submitForm(form); 
	}
  }
  if(form.name == 'setup-step2') {
    if( form.database_server.value == "" ) {
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
    } else {
      submitForm(form); 
	}
  }
  if(form.name == 'setup-step3') {
    if( form.admin_user.value == "" ) {
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

  var next_step = Number(form.step.value)+1;

  showLoading();

  new Ajax.Request('index.php', {
    parameters: Form.serialize(form),
    onFailure: function(t) {
        showFeedback(error['data_not_saved'], 'red');
		hideLoading();
    },
    onSuccess: function(t){ 
      if ( t.responseText ){
        showFeedback(t.responseText, 'red');
		hideLoading();
      } else if( next_step < 4 ){
	    gotoPage('step'+next_step);
	  } else {
	    new Ajax.Request('index.php?mode=run_sql');
	    gotoPage('finish');
	  }
	}
  });
}

function showFeedback(text, color) {
  var feedback = document.getElementById('feedback');
  if( feedback.style.visibility == 'hidden' ){
    feedback.style.background = '#ffffff';
	feedback.style.visibility = 'visible';
  }
  switch(color) {
    case 'red':
	  var hex_color = '#ffdddd';
	  break;
    case 'green':
	  var hex_color = '#ddffdd';
	  break;
    case 'yellow':
	  var hex_color = '#ffffdd';
	  break;
	default:
      var hex_color = '#ffffff';
	  break;
  }
  feedback.innerHTML = text;
  new Effect.Highlight('feedback', {startcolor:'#ffffff', endcolor:hex_color, restorecolor:hex_color, afterFinish:function(){ if( color == 'green' ){ new Effect.Opacity('feedback', { from: 1, to: 0, afterFinish:function(){ feedback.style.visibility = 'hidden'; }}); } }});
}

function showLoading() {
	// get basic browser properties 
	var pageBody = document.getElementsByTagName("body").item(0);
	// start creating the div structure 
	var loadScreen = document.createElement("div");
	loadScreen.setAttribute('id','loading');
	loadScreen.className = 'loading';
	pageBody.appendChild(loadScreen);
	new Effect.Appear('loading', { from:0.0, to: 0.8, duration:0.3 });
}

function hideLoading() {
	new Effect.Fade('loading', { duration:0.5, afterFinish: function(){
	    // hide the loadind screen
	    var pageBody = document.getElementsByTagName("body").item(0);
	    var loadScreen =document.getElementById('loading');
	    loadScreen.style.display = 'none';
	    pageBody.removeChild(loadScreen);
      }
	});
}
