
function gotoPage(page) {

  showLoading();

  var url = 'admin.php?page='+page;
  
  new Ajax.Updater('content', url, {
  onComplete: function(t) { 
    if( t.responseText =='timeout' ){
      // session timed out - redirect to the login page
	  window.location="admin.php";
    } else {
	  // what to do after the page is loaded
	  if(page == "home"){
	    updateHomepage();
	  } else if(page == "settings"){
	    fixExceptions();
	  }
      updateTabs(page);
    }
  },asynchronous:true, evalScripts:true});
  // update the tabs

}

function updateTabs(page) {

  hideLoading();

  var tabs = new Array('home','sections','templates','blocks');
  var page_section=page.split("&");

  for(i=0;i<4;i++){
    if(tabs[i] != page_section[0]) {
      document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-default';
	} else {
      document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-selected';
	}
  }

}

function updateHomepage(){
  var script = 'admin/php/homepage.php?task=';

  new Ajax.Updater('latest-version', script+'version', { onComplete: function(t) { checkVersion(t.responseText); },asynchronous:true, evalScripts:true});
  new Ajax.Updater('repository', script+'repository', {asynchronous:true, evalScripts:true});
  new Ajax.Updater('comments', script+'comments', {asynchronous:true, evalScripts:true});

}

function checkVersion(new_version){
  old_version = parseFloat(document.getElementById('current-version').innerHTML);
  new_version = parseFloat(new_version);
  if( new_version > old_version ){
    document.getElementById('latest-version').innerHTML += ' - <a href="http://www.templateblocks.com/download.html">Click here to update</a>';
  }
}

function saveTree(page) {

  var url = 'admin.php?page='+page+'&action=save&tree=1';
  
  var xtraParameters = Sortable.serialize(page);
  //var xtraParameters = '';
  new Ajax.Request(url, {asynchronous:true, parameters:xtraParameters, 
    onSuccess: function() {
    },
	onFailure: function() {
      showFeedback('There was an error in the saving process', 'red');
    },
    onComplete: function(){ 
      showFeedback('Section tree has been saved!', 'green');
	}
  });
}

function updateBlocksField() {
  var values = '';
  var headvalues = Sortable.serialize('blocks-head');
  headvalues = headvalues.replace(/blocks-head\[\]=/gi,'');
  headvalues = headvalues.replace(/&/g,'|');
  values += headvalues+'#';
  var bodyvalues = Sortable.serialize('blocks-body');
  bodyvalues = bodyvalues.replace(/blocks-body\[\]=/gi,'');
  bodyvalues = bodyvalues.replace(/&/g,'|');
  values += bodyvalues;
  document.getElementById('template_blocks').value = values;
}

function removeBlock(id,type,destination) {
  var element = document.getElementById(id);
  var old = element.parentNode;
  var content = element.innerHTML;
  if( old.id != destination){
    Droppables.remove(element); 
    old.removeChild(element);
    addBlock(id,type,content,destination);
  }
  if( old.id == 'blocks-head' || old.id == 'blocks-body' ){
    updateBlocksField();
  }
}

function addBlock(id,type,content,container) {
  var parent = document.getElementById(container);
  var newelement = document.createElement('li');
  newelement.setAttribute('id',id);
  newelement.innerHTML = content;
  newelement.setAttribute('class',type);
  newelement.setAttribute("className",type);
  parent.appendChild(newelement);
  var delicon = document.getElementById('delete_'+id);
  if(container == 'blocks-other'){
	$(delicon).style.visibility = 'hidden';
    new Draggable(id,{revert:true});
  }
  if( container == 'blocks-head' || container == 'blocks-body' ){
	$(delicon).style.visibility = 'visible';
	Sortable.create(container, { onChange: function(){ updateBlocksField()} });
	updateBlocksField();
  }
  
}

function showLoading() {
	// get basic browser properties 
	var pageBody = document.getElementsByTagName("body").item(0);
	// start creating the div structure 
	var loadScreen = document.createElement("div");
	loadScreen.setAttribute('id','loading');
	loadScreen.className = 'loading';
	pageBody.appendChild(loadScreen);
	// find the browser's window dimensions
	pageScrolls = pageScroll();
    document.getElementById('loading').style.top = String( pageScrolls[1] )+'px';
	new Effect.Appear('loading', { from:0.0, to: 0.8, duration:0.3 });
	window.onscroll = function() { 
 	                        pageScrolls = pageScroll();
                            document.getElementById('loading').style.top = String( pageScrolls[1] )+'px';
					}
}

function hideLoading() {
	window.onscroll = null; 
	new Effect.Fade('loading', { duration:0.5, afterFinish: function(){
	    // hide the loadind screen
	    var pageBody = document.getElementsByTagName("body").item(0);
	    var loadScreen =document.getElementById('loading');
	    loadScreen.style.display = 'none';
	    pageBody.removeChild(loadScreen);
      }
	});
}

function checkSection() {
  // this function checks if the content of the section is an external script and changes the content field accordingly
  var content = document.getElementById('section_content').value;
  if( content.substring(0,8) == 'external' ){
    content_path = content.split('|');
	document.getElementById('is_external').checked = true;
    document.getElementById('section-content').innerHTML = '<p>Path: <input type="text" name="section_content" id="section_content" style="width: 460px" value="'+content_path[1];+'" /></p>'+info['external_script'];
	if( content_path[2] ){ 
	  var custom_class = content_path[2];
	} else {
	  custom_class = '0';
	}
    getClasses(custom_class);
  }
}

function getClasses( custom_class ) {
  var url = 'admin/php/sections.php?task=get-classes';
  // notice the use of a proxy to circumvent the Same Origin Policy.

  new Ajax.Request(url, {
    method: 'get',
    onSuccess: function(t) {
	  var external_content = document.getElementById('section-content').innerHTML;
      document.getElementById('section-content').innerHTML = external_content + t.responseText;
	  var radioClass = document.getElementById('sections-edit').custom_class;
	  for(var i = 0; i < radioClass.length; i++) {
		radioClass[i].checked = false;
		if(radioClass[i].value == custom_class) {
			radioClass[i].checked = true;
		}
	  }
    }
  });

}

function pageScroll(){
	var xScroll, yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
		xScroll = self.pageXOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
		xScroll = document.documentElement.scrollLeft;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
		xScroll = document.body.scrollLeft;	
	}
	return [xScroll,yScroll];
}
