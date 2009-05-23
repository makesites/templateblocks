<?php

global $html;

$html['Sections']['Templates_Select']['Item_Open'] = '<option value="{{VALUE}}" {{SELECTED}}>';
$html['Sections']['Templates_Select']['Item_Close'] = '</option>' . "\n";

$html['Error']['Delete_Index_Page'] = 'You cannot delete the index page. What are your viewers going to see??';
$html['Error']['Rename_Index_Page'] = 'You need an index page for your site so you can\'t rename it.';
$html['Error']['Delete_Index_Template'] = 'You cannot delete this template as it is currently connected with your index page. Change the template of the index page and you will be able to delete this template, no worries.';
$html['Error']['No_Duplicate_Page'] = 'You can not have two pages with the same slug name.';


?>