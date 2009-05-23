<?php

require_once("config.php");

dbConnect();


#Login Routine
if( isset( $action ) )
{
	if( $action == "login" )
	{
		$LightCMSName = trim( $LightCMSName );
		$LightCMSPass = trim( $LightCMSPass );
		if( $LightCMSName == "" ) error( "Admin name required" );
		if( $LightCMSPass == "" ) error( "Admin password required" );
		if( $LightCMSName != $username ) error( "Invalid admin name" );
		if( $LightCMSPass != $password ) error( "Invalid password" );
		session_register( "LightCMSName" );
		session_register( "LightCMSPass" );
		cpMenu();
	}
	elseif( $action == "logout" )
	{
		session_start();
		session_unregister( "LightCMSName" );
		session_unregister( "LightCMSPass" );
		cpHeader( "Admin Logout" );
		echo "<p align=\"center\"><font size=\"4\">You have logout successfully!</font></p>\n";
		echo "<p align=\"center\"><a href=\"admin.php\"><b>Login</b></a> | <a href=\"index.php\"><b>Homepage</b></a></p>\n";
		cpFooter();
	}
}
else
{

	if( verifyAdmin() ) cpMenu();
	cpHeader( "Admin Login" );
	echo "<p align=\"center\"><font size=\"4\">Admin Login</font></p>\n";
	echo "<div align=\"center\">\n";
	echo "  <center>\n";
	echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "  <form method=\"post\" action=\"$PHP_SELF?action=login\">\n";
	echo "    <tr>\n";
	echo "      <td width=\"80\" height=\"25\">Name</td>\n";
	echo "      <td width=\"160\" height=\"25\"><input type=\"text\" name=\"LightCMSName\" size=\"20\" maxlength=\"50\"></td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "      <td width=\"80\" height=\"25\">Password</td>\n";
	echo "      <td width=\"160\" height=\"25\"><input type=\"password\" name=\"LightCMSPass\" size=\"20\" maxlength=\"12\"></td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "      <td width=\"8\" height=\"25\"></td>\n";
	echo "      <td width=\"160\" height=\"25\"><input type=\"submit\" value=\"  Login  \"></td>\n";
	echo "    </tr>\n";
	echo "  </form>\n";
	echo "  </table>\n";
	echo "  </center>\n";
	echo "</div>\n";
	echo "<p align=\"center\"><a href=\"index.php\"><b>Homepage</b></a></p>\n";
	cpFooter();
}


function cpMenu()
{
	global $job, $type;
	if( !verifyAdmin() ) header( "Location: ./index.php" );
	if( isset( $job ) )
	{
		if( $job == "create" ) {
			cpCreate();
		} elseif( $job == "view" ) {
			cpView($type);
		} elseif( $job == "edit" ) {
			cpEdit();
		} elseif( $job == "delete" ) {
			cpDelete();
		} elseif( $job == "createok" ) {
			actCreate();
		} elseif( $job == "editok" ) {
			actEdit();
		} elseif( $job == "deleteok" ) {
			actDelete();
		}
	}
	else
	{
		cpHeader( "Light CMS: Control Panel" );
		echo "<p align=\"center\"><font size=\"4\">Light CMS: Control Panel</font></p>\n";

			echo "
			<div align=\"center\">
			  <center>
			  <table border=\"0\" width=\"300\" cellspacing=\"1\" cellpadding=\"0\">
			    <tr>
			      <td><b>
				    <p align=\"center\"><a href=\"admin.php?job=create&type=page\">New Webpage</a></p>
					<hr width=\"200\" align=\"center\">
				    <p align=\"center\"><a href=\"admin.php?job=view&type=template\">Templates</a></p>
					<hr width=\"200\" align=\"center\">
				    <p align=\"center\"><a href=\"admin.php?job=view&type=module\">Modules</a></p>
					<hr width=\"200\" align=\"center\">
				    <p align=\"center\"><a href=\"admin.php?job=view&type=content\">Content</a></p>
				  </b></td>
			    </tr>
			  </table>
			  </center>
			<p>
			</div>
			";

		cpView('page');

	echo "<p align=\"center\"><a href=\"admin.php?action=logout\"><b>Logout</b></a></p>\n";
	cpFooter();
	}
	cpHeader( "Admin Logout" );
	echo "<p align=\"center\"><font size=\"4\">You have logout successfully!</font></p>\n";
	echo "<p align=\"center\"><a href=\"admin.php\"><b>Login</b></a> | <a href=\"index.php\"><b>Homepage</b></a></p>\n";
	cpFooter();

}

function cpCreate()
{
	global $job, $type, $webpages, $templates, $modules, $contents, $finish;
	if(!$type) error( "Don't know what to create!" );

	cpHeader( $ROOT_NAME." - Create New Record" );
	if( $finish == "yes" ) { echo "<font color=\"#FF0000\">&nbsp;Your record has been created</font>"; $finish = "no"; }
	if ($type == "page") {
		$pagetitle = "Webpage";
	} elseif ($type == "template") {
		$pagetitle = "Template";
	} elseif ($type == "module") {
		$pagetitle = "Module";
	} elseif ($type == "content") {
		$pagetitle = "Content";
	}
	echo "<p align=\"center\"><font size=\"4\">Create $pagetitle</font></p>\n";

	echo "<div align=\"center\">\n";
	echo "  <center>\n";
	echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n";
	echo "    <tr>\n";
	echo "      <td width=\"100%\">\n";
	echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"100%\">\n";
	echo "        <form enctype=\"multipart/form-data\" method=\"post\" action=\"admin.php\">\n";
	echo "          <input type=\"hidden\" name=\"job\" value=\"createok\">\n";
	echo "          <input type=\"hidden\" name=\"type\" value=\"$type\">\n";
	if ($type == "page") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Template</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; selTemplate(''); echo "</td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Content</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">Click to edit content</td>\n";
	echo "          </tr>\n";
	}
	if ($type == "template") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Modules</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; checkModules(); echo "</td>\n";
	echo "          </tr>\n";
	}
	if ($type == "module") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	}
	if ($type == "content") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Module</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; selModule(); echo "</td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Code</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><textarea name=\"Code\" cols=\"50\" rows=\"10\"></textarea></td>\n";
	echo "          </tr>\n";
	}
	echo "          <tr>\n";
	echo "            <td width=\"100%\" height=\"25\" colspan=\"3\"><input type=\"submit\" value=\"  Add New Record  \"><input type=\"reset\" value=\"  Reset  \"></td>\n";
	echo "          </tr>\n";
	echo "        </form>\n";
	echo "        </table>\n";
       	echo "      </td>\n";
	echo "    </tr>\n";
	echo "  </table>\n";
	echo "  </center>\n";
	echo "</div>\n";

	echo "<p align=\"center\"><a href=\"admin.php\"><b>Back to Menu</b></a> | <a href=\"admin.php?action=logout\"><b>Logout</b></a></p>\n";
	cpFooter();

}

function cpView($type)
{
	global $webpages, $templates, $modules, $contents, $page, $finish;
	if(!$type) error( "Don't know what to show!" );

	$maxRecord = 10;
	if ($type == "page") {
	$dbtable = $webpages;
	$pagetitle = "Webpage Management";
	} elseif ($type == "template") {
	$dbtable = $templates;
	$pagetitle = "Template Management";
	} elseif ($type == "module") {
	$dbtable = $modules;
	$pagetitle = "Modules Management";
	} elseif ($type == "content") {
	$dbtable = $contents;
	$pagetitle = "Content Management";
	}
	$result = mysql_query( "SELECT * FROM $dbtable ORDER BY ID DESC" ) or error( mysql_error() );
	$totalRecords = mysql_num_rows( $result );
	if( $totalRecords <= $maxRecord ) $totalPages = 1;
	elseif( $totalRecords % $maxRecord == 0 ) $totalPages = $totalRecords / $maxRecord;
	else $totalPages = ceil( $totalRecords / $maxRecord );
	if( !isset( $page ) ) $page = 1;
	elseif( $page > $totalPages ) $page = 1;
	if( $totalRecords == 0 ) $recStart = 0;
	else $recStart = $maxRecord * $page - $maxRecord + 1;
	if( $page == $totalPages ) $recEnd = $totalRecords;
	else $recEnd = $maxRecord * $page;
	$prePage = $page - 1;
	$nextPage = $page + 1;
	$initRecord = $maxRecord * $page - $maxRecord;

	$manage = mysql_query( "SELECT * FROM $dbtable ORDER BY ID ASC LIMIT $initRecord, $maxRecord" ) or error( mysql_error() );
	cpHeader( "Light CMS: " . $pagetitle );
	echo "<p align=\"center\"><font size=\"4\">$pagetitle</font></p>\n";
	echo "<p align=\"center\"><a href=\"admin.php?job=create&type=$type\"><font size=\"2\">(create new)</font></p>\n";

	if( $finish == "yes" ) { echo "<font color=\"#FF0000\">&nbsp;Your record has been updated</font>"; $finish = "no"; }
	echo "<div align=\"center\">\n";
	echo "  <center>\n";
	echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n";
	echo "  <form method=\"post\" action=\"admin.php?job=delete&type=$type\">\n";
	if ($type == "page") {
	echo "    <tr bgcolor=\"#6090D0\">\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>#</b></font></td>\n";
	echo "      <td width=\"200\" height=\"25\" align=\"left\"><font color=\"#FFFFFF\"><b>Name</b></font></td>\n";
	echo "      <td width=\"150\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Template</b></font></td>\n";
	echo "      <td width=\"50\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Content</b></font></td>\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Delete</b></font></td>\n";
	echo "    </tr>\n";
	} elseif ($type == "template") {
	echo "    <tr bgcolor=\"#6090D0\">\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>#</b></font></td>\n";
	echo "      <td width=\"200\" height=\"25\" align=\"left\"><font color=\"#FFFFFF\"><b>Name</b></font></td>\n";
	echo "      <td width=\"150\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Modules</b></font></td>\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Delete</b></font></td>\n";
	echo "    </tr>\n";
	} elseif ($type == "module") {
	echo "    <tr bgcolor=\"#6090D0\">\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>#</b></font></td>\n";
	echo "      <td width=\"200\" height=\"25\" align=\"left\"><font color=\"#FFFFFF\"><b>Name</b></font></td>\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Delete</b></font></td>\n";
	echo "    </tr>\n";
	} elseif ($type == "content") {
	echo "    <tr bgcolor=\"#6090D0\">\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>#</b></font></td>\n";
	echo "      <td width=\"200\" height=\"25\" align=\"left\"><font color=\"#FFFFFF\"><b>Module</b></font></td>\n";
	echo "      <td width=\"150\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Code</b></font></td>\n";
	echo "      <td width=\"150\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>ID</b></font></td>\n";
	echo "      <td width=\"25\" height=\"25\" align=\"center\"><font color=\"#FFFFFF\"><b>Delete</b></font></td>\n";
	echo "    </tr>\n";
	}
	echo "    <tr>\n";
	echo "      <td width=\"100%\" height=\"1\" colspan=\"5\" bgcolor=\"#FFFFFF\"></td>\n";
	echo "    </tr>\n";
	if( $totalRecords == 0 )
	{
	echo "    <tr>\n";
	echo "      <td width=\"100%\" height=\"25\" colspan=\"5\" align=\"center\"><font size=\"4\">No Records Created</font>\n";
	echo "    </tr>\n";
	}
	$i = $recStart;
	while( $row = mysql_fetch_array( $manage ) )
	{
		if ($type == "page") {
		echo "    <tr>\n";
		echo "      <td height=\"25\" align=\"center\">{$i}.</td>\n";
		echo "      <td height=\"25\" align=\"left\"><a href=\"admin.php?job=edit&type=$type&num={$row['ID']}\" title=\"Click to Edit\">{$row['Name']}</a></td>\n";
		echo "      <td height=\"25\" align=\"center\">{$row['Template']}</td>\n";
		echo "      <td height=\"25\" align=\"center\">{$row['Content']}</td>\n";
		echo "      <td height=\"25\" align=\"center\"><input type=\"checkbox\" name=\"c[]\" value=\"{$row['ID']}\"></td>\n";
		echo "    </tr>\n";
		} elseif ($type == "template") {
		echo "    <tr>\n";
		echo "      <td height=\"25\" align=\"center\">{$i}.</td>\n";
		echo "      <td height=\"25\" align=\"left\"><a href=\"admin.php?job=edit&type=$type&num={$row['ID']}\" title=\"Click to Edit\">{$row['Name']}</a></td>\n";
		echo "      <td height=\"25\" align=\"center\">{$row['Modules']}</td>\n";
		echo "      <td height=\"25\" align=\"center\"><input type=\"checkbox\" name=\"c[]\" value=\"{$row['ID']}\"></td>\n";
		echo "    </tr>\n";
		} elseif ($type == "module") {
		echo "    <tr>\n";
		echo "      <td height=\"25\" align=\"center\">{$i}.</td>\n";
		echo "      <td height=\"25\" align=\"left\"><a href=\"admin.php?job=edit&type=$type&num={$row['ID']}\" title=\"Click to Edit\">{$row['Name']}</a></td>\n";
		echo "      <td height=\"25\" align=\"center\"><input type=\"checkbox\" name=\"c[]\" value=\"{$row['ID']}\"></td>\n";
		echo "    </tr>\n";
		} elseif ($type == "content") {
		echo "    <tr>\n";
		echo "      <td height=\"25\" align=\"center\">{$i}.</td>\n";
		echo "      <td height=\"25\" align=\"left\"><a href=\"admin.php?job=edit&type=$type&num={$row['ID']}\" title=\"Click to Edit\">{$row['Module']}</a></td>\n";
		echo "      <td height=\"25\" align=\"center\">{$row['Code']}</td>\n";
		echo "      <td height=\"25\" align=\"center\">{$row['ID']}</td>\n";
		echo "      <td height=\"25\" align=\"center\"><input type=\"checkbox\" name=\"c[]\" value=\"{$row['ID']}\"></td>\n";
		echo "    </tr>\n";
		}
	$i++;
	}
	echo "    <tr>\n";
	echo "      <td width=\"100%\" height=\"1\" colspan=\"5\" bgcolor=\"#808080\"></td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "      <td width=\"100%\" height=\"20\" colspan=\"5\"><input type=\"submit\" value=\"  Delete Selected  \"></td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "      <td width=\"100%\" height=\"20\" colspan=\"5\"><small>Found <b>$totalRecords</b> records, now displaying <b>$recStart</b> - <b>$recEnd</b></small></td>\n";
	echo "    </tr>\n";
	echo "  </form>\n";
	echo "  </table>\n";
	echo "  </center>\n";
	echo "</div>\n";
	echo "<div align=\"center\">";
	echo "Page: ";
	if( $page != 1 ) echo "<a href=\"$PHP_SELF?job=view&type=$type&page=$prePage\">&lt;&lt;</a> ";
	for( $i = 1; $i <= $totalPages; $i++ )
	{
	if( $page == $i ) echo "<b>$i</b> ";
	else echo "<a href=\"$PHP_SELF?job=view&type=$type&page=$i\">$i</a> ";
	}
	if( $page != $totalPages ) echo " <a href=\"$PHP_SELF?job=view&type=$type&page=$nextPage\">&gt;&gt;</a>";
	echo "</div>\n";
	echo "<div width=\"500\" align=\"center\"><form method=\"post\" action=\"admin.php?job=edit&type=$type\"><small>If you want to edit the properties of a specific record, use it's ID number right here:</small><br><b>Enter ID: </b><input type=\"text\" name=\"num\" length=\"20\" size=\"20\"><input type=\"submit\" value=\" Edit \"></form></div>\n";

	echo "<p align=\"center\"><a href=\"admin.php\"><b>Back to Menu</b></a> | <a href=\"admin.php?action=logout\"><b>Logout</b></a></p>\n";
	cpFooter();
}

function cpEdit()
{
	global $job, $type, $webpages, $templates, $modules, $contents, $num;
	if(!$type) error( "Don't know what to edit!" );

	if ($type == "page") {
	$dbtable = $webpages;
	$pagetitle = "Update Webpage";
	} elseif ($type == "template") {
	$dbtable = $templates;
	$pagetitle = "Update Template";
	} elseif ($type == "module") {
	$dbtable = $modules;
	$pagetitle = "Update Module";
	} elseif ($type == "content") {
	$dbtable = $contents;
	$pagetitle = "Update Content";
	}
	$showrec = mysql_query( "SELECT * FROM $dbtable WHERE ID=$num" ) or error( mysql_error() );
	$row = mysql_fetch_array( $showrec );

	cpHeader( "Light CMS: " . $pagetitle );
	echo "<p align=\"center\"><font size=\"4\">$pagetitle</font></p>\n";

	echo "<div align=\"center\">\n";
	echo "  <center>\n";
	echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n";
	echo "    <tr>\n";
	echo "      <td width=\"100%\">\n";
	echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"100%\">\n";
	echo "        <form enctype=\"multipart/form-data\" method=\"post\" action=\"admin.php\">\n";
	echo "          <input type=\"hidden\" name=\"job\" value=\"editok\">\n";
	echo "          <input type=\"hidden\" name=\"type\" value=\"$type\">\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>ID number</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">{$row['ID']} <input type=\"hidden\" name=\"num\" value=\"{$row['ID']}\"></td>\n";
	echo "          </tr>\n";
	if ($type == "page") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" value=\"{$row['Name']}\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Template</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; selTemplate($row['Template']); echo "</td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Content</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; selContent($row['Template'],$row['Content']); echo "</td>\n";
	echo "          </tr>\n";
	}
	if ($type == "template") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" value=\"{$row['Name']}\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Modules</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">"; checkModules($row['Modules']); echo "</td>\n";
	echo "          </tr>\n";
	}
	if ($type == "module") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Name</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><input type=\"text\" name=\"Name\" value=\"{$row['Name']}\" size=\"20\" maxlength=\"200\"></td>\n";
	echo "          </tr>\n";
	}
	if ($type == "content") {
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Module</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\">{$row['Module']} <input type=\"hidden\" name=\"Module\" value=\"{$row['Module']}\"></td>\n";
	echo "          </tr>\n";
	echo "          <tr>\n";
	echo "            <td width=\"30%\" height=\"25\"><b>Code</b></td>\n";
	echo "            <td width=\"70%\" height=\"25\"><textarea name=\"Code\" cols=\"50\" rows=\"10\">{$row['Code']}</textarea></td>\n";
	echo "          </tr>\n";
	}
	echo "          <tr>\n";
	echo "            <td width=\"100%\" height=\"25\" colspan=\"3\"><input type=\"submit\" value=\"  Update Record  \"><input type=\"reset\" value=\"  Reset  \"></td>\n";
	echo "          </tr>\n";
	echo "        </form>\n";
	echo "        </table>\n";
       		echo "      </td>\n";
	echo "    </tr>\n";
	echo "  </table>\n";
	echo "  </center>\n";
	echo "</div>\n";

	echo "<p align=\"center\"><a href=\"admin.php\"><b>Back to Menu</b></a> | <a href=\"admin.php?action=logout\"><b>Logout</b></a></p>\n";
	cpFooter();

}

function cpDelete()
{
	global $job, $type, $webpages, $templates, $modules, $contents;
	if(!$type) error( "Don't know what to delete!" );

}

function actCreate()
{
	global $job, $type, $webpages, $templates, $modules, $contents, $num, $Name, $Module, $Template, $Content, $Code;
	if(!$type) error( "Don't know what to create!" );

	if ($type == "page") {
		$dbtable = $webpages;
		mysql_query( "INSERT INTO $dbtable ( Name, Template, Content ) VALUES ( '$Name', '$Template', '$Content' )" ) or error( mysql_error() );
	} elseif ($type == "template") {
		$dbtable = $templates;
		mysql_query( "INSERT INTO $dbtable ( Name, Modules ) VALUES ( '$Name', '$Module' )" ) or error( mysql_error() );
	} elseif ($type == "module") {
		$dbtable = $modules;
		mysql_query( "INSERT INTO $dbtable ( Name ) VALUES ( '$Name' )" ) or error( mysql_error() );
	} elseif ($type == "content") {
		$dbtable = $contents;
		mysql_query( "INSERT INTO $dbtable ( Module, Code ) VALUES ( '$Module', '$Code' )" ) or error( mysql_error() );
	}

	header( "Location: ./admin.php?job=create&type=$type&finish=yes" );

}

function actEdit()
{
	global $job, $type, $webpages, $templates, $modules, $contents, $num, $Name, $Module, $Template, $Content, $Code;
	if(!$num) error( "Don't know what to create!" );

	if ($type == "page") {
		$dbtable = $webpages;
		$Listed=implode(",",$Content);
		mysql_query( "UPDATE $dbtable SET Name='$Name', Template='$Template', Content='$Listed' WHERE ID=$num" ) or error( mysql_error() );
	} elseif ($type == "template") {
		$dbtable = $templates;
		$Listed=implode(",",$Module);
		mysql_query( "UPDATE $dbtable SET Name='$Name', Modules='$Listed' WHERE ID=$num" ) or error( mysql_error() );
	} elseif ($type == "module") {
		$dbtable = $modules;
		mysql_query( "UPDATE $dbtable SET Name='$Name' WHERE ID=$num" ) or error( mysql_error() );
	} elseif ($type == "content") {
		$dbtable = $contents;
		mysql_query( "UPDATE $dbtable SET Module='$Module', Code='$Code' WHERE ID=$num" ) or error( mysql_error() );
	}

	header( "Location: ./admin.php?job=view&type=$type&finish=yes" );

}

function actDelete()
{
	global $job, $type, $webpages, $templates, $modules, $contents, $num, $Name, $Module, $Template, $Content, $Code;
	if(!$num) error( "Don't know what to delete!" );

}



function dbConnect()
{
	global $db_host, $db_name, $db_user, $db_pass;
	mysql_connect( $db_host, $db_user, $db_pass ) or error( mysql_error() );
	mysql_select_db( $db_name );
}

function cpHeader( $title = "" )
{
	global $ADVT_NAME, $PAGE_BG_COLOR, $PAGE_BG_IMAGE;
	echo "\n<html>\n";
	echo "<head>\n";
	echo "<title>$title</title>\n";
	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=ISO-8859-7\">\n";
	echo "<link rel=\"stylesheet\" href=\"../scripts/style.css\" type=\"text/css\">\n";
	echo "</head>\n\n";
	echo "<body bgcolor=\"$PAGE_BG_COLOR\" background=\"$PAGE_BG_IMAGE\">\n\n";
}

function cpFooter()
{
	echo "</body>\n";
	echo "</html>\n";
	exit;
}


function checkModules($themodules)
{
	global $modules;

	$modulenum = explode(",", $themodules);

	$querychar = "SELECT * FROM $modules";
	$result = mysql_query($querychar) or error( mysql_error() );
	$countmods = mysql_num_rows( $result );
	for($i = 0; $i < $countmods; $i++) {
		$modvalues[$i] = mysql_fetch_array( $result );
		echo "<input type=\"checkbox\" name=\"Module[]\" value=\"{$modvalues[$i]['ID']}\"";
		for($j = 0; $j < count($modulenum); $j++) {
			if ( $modvalues[$i]['ID'] == $modulenum[$j] ) {
			echo " checked";
			}
		}
		echo "> {$modvalues[$i]['Name']} <br>";
	}
}

function selModule($pickname, $picknum)
{
	global $modules;
	$querychar = "SELECT * FROM $modules";
	if( $pickname ) { $querychar .= " WHERE Name='$pickname'"; }
	$result = mysql_query($querychar) or error( mysql_error() );
	$countmods = mysql_num_rows( $result );
	echo "<select name=\"Module\" size=\"1\">";
	for($i = 0; $i < $countmods; $i++) {
		$modvalues[$i] = mysql_fetch_array( $result );
		echo "<option value=\"{$modvalues[$i]['ID']}\""; if ( $modvalues[$i]['ID'] == $pickname ) { echo " selected";} echo "> {$modvalues[$i]['Name']} </option>";
	}
	echo "</select>";
}

function selTemplate($picked)
{
	global $templates;
	$querychar = "SELECT * FROM $templates";
	$result = mysql_query($querychar) or error( mysql_error() );
	$countemplates = mysql_num_rows( $result );
	echo "<select name=\"Template\" size=\"1\">";
	for($i = 0; $i < $countemplates; $i++) {
		$alltemplates[$i] = mysql_fetch_array( $result );
		echo "<option value=\"{$alltemplates[$i]['Name']}\""; if ( $alltemplates[$i]['Name'] == $picked ) { echo " selected";} echo "> {$alltemplates[$i]['Name']} </option>";
	}
	echo "</select>";
}

function selContent($thepage,$themodules)
{
	global $templates, $contents, $modules;

	$modulenum = explode(",", $themodules);

	$querychar = "SELECT * FROM $templates WHERE Name='$thepage'";
	$result = mysql_query($querychar) or error( mysql_error() );
	$temproperties = mysql_fetch_array( $result );
	$defaultmods = $temproperties['Modules'];
	$defaultnum = explode(",", $defaultmods);

	for($i = 0; $i < count($defaultnum); $i++) {
		$querychar = "SELECT * FROM $modules WHERE ID='$defaultnum[$i]'";
		$result = mysql_query($querychar) or error( mysql_error() );
		$modproperties = mysql_fetch_array( $result );
		$modname = $modproperties['Name'];
		$modnum = $defaultnum[$i];
		echo "$modname <br>";
		selCode($modname, $modnum);
		echo "<br><br>";
	}
}

function selCode($pickname, $picknum)
{
	global $contents;
	$querychar = "SELECT * FROM $contents";
	if( $pickname ) { $querychar .= " WHERE Module='$pickname'"; }
	$result = mysql_query($querychar) or error( mysql_error() );
	$countmods = mysql_num_rows( $result );
	echo "<select name=\"Content[]\" size=\"1\">";
	for($i = 0; $i < $countmods; $i++) {
		$modvalues[$i] = mysql_fetch_array( $result );
		echo "<option value=\"{$modvalues[$i]['ID']}\""; if ( $modvalues[$i]['ID'] == $pickname ) { echo " selected";} echo "> ID # {$modvalues[$i]['ID']} </option>";
	}
	echo "</select>";
}

function verifyAdmin()
{
	session_start();
	global $username, $password, $LightCMSPass, $LightCMSName;
	if( session_is_registered( "LightCMSName" ) && session_is_registered( "LightCMSPass" ) )
	{
		if( $LightCMSName == $username && $LightCMSPass == $password )
		return true;
	}
	return false;
}

function error( $error ) {
	cpHeader( "Error Page" );
	echo "<p align=\"center\"><font size=\"4\">Error: $error</font></center>\n";
	echo "<p align=\"center\"><a href=\"javascript:history.back()\"><b>Back</b></a></p>\n";
	cpFooter();
	mysql_close();
	exit;
}


?>