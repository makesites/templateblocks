
function gotoPage(page) {

  var tabs = new Array('home','sections','templates','blocks');
  var url = 'admin.php?page='+page;
  
  new Ajax.Updater('content', url, {onComplete:function(){ },asynchronous:true, evalScripts:true});
  // update the tabs
  var page_section=page.split("&");
  for(i=0;i<4;i++){
    if(tabs[i] != page_section[0]) {
     document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-default';
      //document.getElementById('tab-'+tabs[i]).firstChild.style.background = '#aaaaaa';
	} else {
      document.getElementById('tab-'+tabs[i]).firstChild.className = 'tab-selected';
      //document.getElementById('tab-'+tabs[i]).firstChild.style.background = '#ffffff';
	}
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
  old.removeChild(element);
  if( old.id == 'blocks-head' || old.id == 'blocks-body' ){
    updateBlocksField();
  }
  addBlock(id,type,content,destination);
}

function addBlock(id,type,content,container) {
  var parent = document.getElementById(container);
  var newelement = document.createElement('li');
  newelement.setAttribute('id',id);
  newelement.innerHTML = content;
  newelement.setAttribute('class',type);
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

function checkSection() {
  // this function checks if the content of the section is an external script and changes the content field accordingly
  var content = document.getElementById('section_content').value;
  if( content.substring(0,8) == 'external' ){
    content_path = content.split('|');
	document.getElementById('is_external').checked = true;
    document.getElementById('section-content').innerHTML = '<p>Path: <input type="text" name="section_content" id="section_content" style="width: 460px" /></p>'+info['external_script'];
	document.getElementById('section_content').value = content_path[1];
  }
}

