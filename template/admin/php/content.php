<?php

global $html;

$html['Indent'] = '  ';

$html['Sections']['Templates_Select']['Item_Open'] = '<option value="{{VALUE}}" {{SELECTED}}>';
$html['Sections']['Templates_Select']['Item_Close'] = '</option>' . "\n";

$html['No_Listings_Found'] = '<option>No Listings Found</option>' ."\n";

$html['Sections']['List_Item_Open'] = '<li id="item_{{ID}}" title="Click to edit this section"><a href="javascript:void(0)" onClick="gotoPage(\'' . $_REQUEST['page'] . '&action=edit&id={{ID}}\')">';
$html['Sections']['List_Item_Close'] = '</a><ul style="padding:8px;">{{CHILDREN}}</ul></li>' . "\n";
$html['Sections']['No_Listings_Found'] = 'No Listings Found';

$html['Sections']['Custom_Class'] = 'You can load your own custom functions to connect with the external script and customize elements of the template, for example the page title.';

$html['Templates']['Doctype_Select']['Item_Open'] = '<option value="{{VALUE}}" {{SELECTED}}>';
$html['Templates']['Doctype_Select']['Item_Close'] = '</option>' . "\n";

$html['Templates']['List_Item_Open'] = '<li id="item_{{ID}}" title="Click to edit this template"><a href="javascript:void(0)" onClick="gotoPage(\'' . $_REQUEST['page'] . '&action=edit&id={{ID}}\')">';
$html['Templates']['List_Item_Close'] = '</a></li>' . "\n";
$html['Templates']['No_Listings_Found'] = 'No Listings Found';

$html['Templates']['Blocks_Used']['Item_Open'] = '<li id="item_{{ID}}" class="item_{{TYPE}}"><span>';
$html['Templates']['Blocks_Used']['Item_Close'] = '</span> <a href="javascript:void(0)" onClick="removeBlock(\'item_{{ID}}\', \'item_{{TYPE}}\', \'blocks-other\')" id="delete_item_{{ID}}" class="delete-icon" title="click here to delete this block">x</a></li>' . "\n";

$html['Templates']['Blocks_Other']['Item_Open'] = '<li id="item_{{ID}}" class="item_{{TYPE}}"><span>';
$html['Templates']['Blocks_Other']['Item_Close'] = '</span> <a href="javascript:void(0)" onClick="removeBlock(\'item_{{ID}}\', \'item_{{TYPE}}\', \'blocks-other\')" id="delete_item_{{ID}}" style="visibility: hidden;" class="delete-icon" title="click here to delete this block">x</a></li>' . "\n";

$html['Templates']['Content']['Item'] = '<li id="item_X">CONTENT</li>' . "\n";

$html['JavaScript']['Blocks_Draggable'] = '  new Draggable("item_{{ID}}",{revert:true});' . "\n";

$html['Blocks']['List_Item_Open'] = '<li id="item_{{ID}}" class="item_{{TYPE}}" title="Click to edit this block"><a href="javascript:void(0)" onClick="gotoPage(\'' . $_REQUEST['page'] . '&action=edit&id={{ID}}\')">';
$html['Blocks']['List_Item_Close'] = '</a></li>' . "\n";
$html['Blocks']['No_Listings_Found'] = 'No Listings Found';

$html['Blocks']['Block_type_Select']['Item_Open'] = '    <option value="{{VALUE}}" {{SELECTED}}>';
$html['Blocks']['Block_type_Select']['Item_Close'] = '</option>' . "\n";

$html['View']['List_Item_Open'] = '<li id="item_{{ID}}">';
$html['View']['List_Item_Close'] = '</li>' . "\n";

$html['Doctype'] = array( 'XHTML 1.1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . "\n" . ' "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
						  'XHTML 1.0 Strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n" . ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
						  'XHTML 1.0 Transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . "\n" . ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
						  'XHTML 1.0 Frameset' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' . "\n" . ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
						  'HTML 4.01' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"' . "\n" . ' "http://www.w3.org/TR/html4/strict.dtd">',
						  'HTML 4.01 Transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n" . ' "http://www.w3.org/TR/html4/loose.dtd">',
						  'HTML 4.01 Frameset' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"' . "\n" . ' "http://www.w3.org/TR/html4/frameset.dtd">',
						  'HTML 3.2' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">',
						);
$html['Block_type'] = array( 'HTML' => 'html', 
							 'CSS' => 'css', 
							 'JavaScript' => 'js', 
							 'PHP' => 'php', 
						);

$html['Error']['Delete_Index_Page'] = 'You cannot delete the index page. What are your viewers going to see??';
$html['Error']['Rename_Index_Page'] = 'You need an index page for your site so you can\'t rename it.';
$html['Error']['Delete_Index_Template'] = 'You cannot delete this template as it is currently connected with your index page. Change the template of the index page and you will be able to delete this template, no worries.';
$html['Error']['No_Duplicate_Page'] = 'You can not have two pages with the same slug name.';
$html['Error']['No_Duplicate_Block'] = 'You can not have two blocks with the same name.';
$html['Error']['Index_Template'] = 'You cannot delete the template of the index page.';


$html['Info']['Sections']['Index'] = '<p>These are the main parts of your website. Each section can hold one or more webpages (if using an external script). Some might say it\'s the equivalent of the site\'s main menu - although that is not binding in any way.</p>';
$html['Info']['Sections']['Edit'] = '<p>Each section must have a template assigned to it. The title you enter is presented in the web pages of the section while the slug is used for the URL, when not called from an external script.</p>';
$html['Info']['Templates']['Index'] = '<p>Templates are the generic interfaces used in more than one web pages and are compiled from blocks.</p>';
$html['Info']['Templates']['Edit'] = '<p>Drag blocks from the bin below to use on your template. Place them eiher on the &lt;HEAD&gt; or the &lt;BODY&gt; section and switch their order until you are satisfied by their position. The layout created here has a direct impact in the source code of the template.</p>';
$html['Info']['Blocks']['Index'] = '<p>Here is a list of all the blocks you have created. You will find the latest blocks last.</p>';
$html['Info']['Blocks']['Edit'] = '<p>Blocks are the main contruction parts. They are self-contained sections of your markup. <br />There are four types of blocks: HTML, CSS, JavaScript and PHP.</p><p>Each type strictly represents the content you are entering. So, if for example you want to enter some &lt;script&gt; tags of external Javascript code (for example a Goggle Analytics code) you need to create an HTML block for that markup (and not a JavaScript block) so it is rendered as HTML.</p>';


?>