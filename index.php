<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Berlin');

// --- Static Values ---

// PHPExcel
require_once dirname(__FILE__) . '/excel/PHPExcel.php';

// Database
include 'include/db.php';

// Registration
$reg_min_name = 3;
$reg_min_email = 5;
$reg_min_password = 6;

// General settings
$delimiter = '^';
$standardX = 0;
$standardY = 0;
$standardZ = 0;
$dateFormat = 'd.m.Y';
$standard_csv_delimiter = ',';

// --- Set up ---

// Set up Smarty
define('SMARTY_DIR','smarty/');
require(SMARTY_DIR.'Smarty.class.php');
$smarty = new Smarty;
$smarty->template_dir = 'templates/';
$smarty->compile_dir = 'templates_c/';
$smarty->config_dir = 'configs/';
$smarty->cache_dir = 'cache/'; 
$smarty->caching = false;
$smarty->assign('app_name','uncode');

// Set up Session
session_name('uncode');
if (!isset($_SESSION)) session_start();
if (isset($_SESSION['uid']))
{
	$smarty->assign('uid', $_SESSION['uid']);
	$smarty->assign('name', $_SESSION['name']);
	$smarty->assign('reg_msg', 'logged');
}
else $smarty->assign('reg_msg', 'notlogged');

// Common functions
function mysql_fail($db_link) {
	$error = $db_link->error;
	$errno = $db_link->errno;
	echo "Error while accessing Database! Should the problem persist, please contact support. Error Message: ".$errno." - ".$error;
	exit;
}
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   foreach ($array as $row) {
      fputcsv($df, $row, $_SESSION['delimiter']);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename)
{
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

// Color-Hex to Color-RGB
function hex2rgb($hex)
{
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

// RGB to Hex
function rgb2hex($rgb)
{
	return sprintf('%02x', $rgb[0]) . sprintf('%02x', $rgb[1]) . sprintf('%02x', $rgb[2]);
}

// Truncates string after 30 characters, not cutting words out
function truncate($string, $length=27, $dots = "...") {
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function saveViaTempFile($objWriter)
{
	global $tmp_directory;
	$filePath = $tmp_directory . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
	$objWriter->save($filePath);
	readfile($filePath);
	unlink($filePath);
}

function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

function cleanstr($string) {
   return preg_replace('/[^A-Za-z0-9\ \']/', '', $string); // Removes special chars.
}

// --- Catching site requests ---
// Requests for the internal area (logged in)
if (isset($_SESSION['uid']))
{
	if (isset($_GET['p']))
	{
		switch ($_GET['p'])
		{
			case 'logout':
			logout();
			break;
		
			case 'settings':
			settings();
			break;
			
			case 'savesettings':
			saveSettings();
			break;
			
			case 'project':
			project();
			break;
			
			case 'data':
			data();
			break;
			
			case 'save':
			save();
			break;
			
			case 'add':
			add();
			break;

			case 'delete':
			delete();
			break;
			
			case 'edit_element':
			editElement();
			break;

			case 'copy_element':
			copyElement();
			break;
			
			case 'exportcsv':
			exportcsv();
			break;
			
			case 'persistence':
			persistence();
			break;
			
			case 'exportdef':
			exportDef();
			break;
			
			case 'createproject':
			createProject();
			break;
			
			case 'createstudy':
			createStudy();
			break;
			
			case 'editstudy':
			editStudy();
			break;
			
			case 'editproject':
			editProject();
			break;
			
			case 'copyproject':
			copyProject();
			break;
			
			case 'deleteproject':
			deleteProject();
			break;
			
			case 'deletestudy':
			deleteStudy();
			break;
			
			case 'rawdata':
			rawData();
			break;
			
			case 'addsource':
			addSource();
			break;
			
			case 'downloadsource':
			downloadSource();
			break;
			
			case 'deletesource':
			deleteSource();
			break;
			
			case 'recalculate':
			reCalculate(null, null, null);
			break;
			
			case 'visualize':
			visualize();
			break;
			
			case 'visualizedata':
			visualizeData();
			break;
			
			case 'connections':
			connections();
			break;
			
			case 'connections_save':
			connections_save();
			break;
			
			default:
			overview();
		}
	}
	else overview();
}

// Requests for the public area (logged out)
else
{
	if (isset($_GET['p']))
	{
		switch ($_GET['p'])
		{	
			case 'login':
			login();
			break;
			
			case 'register':
			register();
			break;
			
			default:
			start();
		}
	}
	else start();
}






// --- Internal Area: Functions ---

// Overview Window
function overview()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$query = 'SELECT * FROM projects WHERE `uid` = "'.$_SESSION['uid'].'" ORDER BY `project_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	
	while ($row = $result->fetch_assoc())
	{
		$query = 'SELECT COUNT(*) AS count FROM `'.$row['project_id'].'.timeslides`';
		$result2 = $db_link->query($query) or mysql_fail($db_link);
		$data = $result2->fetch_assoc();
		$row['timeslides'] = $data['count'];
		$query = 'SELECT COUNT(*) AS count FROM `'.$row['project_id'].'.actors`';
		$result2 = $db_link->query($query) or mysql_fail($db_link);
		$actors = $result2->fetch_assoc();
		$row['actors'] = $actors['count'];
		$query = 'SELECT COUNT(*) AS count FROM `'.$row['project_id'].'.elements`';
		$result2 = $db_link->query($query) or mysql_fail($db_link);
		$elements = $result2->fetch_assoc();
		$row['elements'] = $elements['count'];
		
		$projects[] = $row;
	}
	if (isset($projects)) $smarty->assign('projects', $projects);
	
	$query = 'SELECT * FROM studies WHERE `uid` = "'.$_SESSION['uid'].'" ORDER BY `study_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc())
	{
		$studies[] = $row;
	}
	if (isset($studies)) $smarty->assign('studies', $studies);
	
	$db_link->close();
	$smarty->display('overview.tpl');
}

// Project Screen
function project()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	// If timeslide_id is set, the project screen will start with this one
	if (isset($_GET['timeslide_id']))
	{
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
		$smarty->assign('timeslide_id', $timeslide_id);
	}

	// General query for navigation
	$query = 'SELECT uid, study_id FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	if ($data['uid'] != $_SESSION['uid']) exit;
	$query = 'SELECT project_id, project_name FROM `projects` WHERE `study_id` = "'.$data['study_id'].'" ORDER BY `project_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc()) { $projects[] = $row; }
	$smarty->assign('projects', $projects);
	
	// Query for this project
	$query = "SELECT * FROM projects WHERE project_id = '$project_id'";
	$result = $db_link->query($query) or mysql_fail($db_link);
	$row = $result->fetch_assoc();
	$smarty->assign('thisproject', $row);
	
	// Time Slides
	$query = "SELECT timeslide_id, timeslide_name FROM `".$project_id.".timeslides` ORDER BY `start_date` ASC";
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc())
	{
		$row['timeslide_name'] = (strlen($row['timeslide_name']) > 20) ? substr($row['timeslide_name'], 0, 20) . '...' : $row['timeslide_name'];
		$timeslides[] = $row;
	}
	$smarty->assign('timeslides', $timeslides);
	
	// Actors
	$query2 = 'SELECT actor_id, dead FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslides[0]['timeslide_id'].'"';
	$result2 = $result = $db_link->query($query2) or mysql_fail($db_link);
	while ($row2 = $result2->fetch_assoc())
	{
		$dead[$row2['actor_id']] = $row2['dead'];
	}
	
	$query = "SELECT actor_id, actor_name FROM `".$project_id.".actors` ORDER BY `actor_name` ASC";
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc())
	{
		if ($dead[$row['actor_id']] == 0) $row['dead'] = 0; else $row['dead'] = 1;
		$actors[] = $row;
	}
	$smarty->assign('actors', $actors);
	
	$db_link->close();
	$smarty->display('project.tpl');
}

// Project Screen: Retrieve data
function data()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter, $dateFormat;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	
	// If both a timeslide and an actor are selected, get the data
	if ((isset($_GET['actor_id'])) && (isset($_GET['timeslide_id'])))
	{
		$actor_id = $db_link->real_escape_string($_GET['actor_id']);
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
	
		// Time Slide Data
		$query = 'SELECT * FROM `'.$project_id.'.timeslides` WHERE timeslide_id = '.$timeslide_id;
		$result = $db_link->query($query) or mysql_fail($db_link);
		$timeslide_data = $result->fetch_assoc();
		$dataReturned = $timeslide_data['timeslide_name'];
		$dataReturned .= $delimiter.$timeslide_data['timeslide_information'];
		$dataReturned .= $delimiter.date($dateFormat, $timeslide_data['start_date']);
		$dataReturned .= $delimiter.date($dateFormat, $timeslide_data['end_date']);
	
		// Actor Data
		$query = 'SELECT * FROM `'.$project_id.'.actors` WHERE actor_id = '.$actor_id;
		$result = $db_link->query($query) or mysql_fail($db_link);
		$actor_data = $result->fetch_assoc();
		$dataReturned .= $delimiter.$actor_data['actor_name'];
		$dataReturned .= $delimiter.$actor_data['actor_information'];
		
		// X, Y, Z, num_con, alive
		$query = 'SELECT dead, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from x)) AS x, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from y)) AS y, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from z)) AS z, elements, num_con FROM `'.$project_id.'.xyz` WHERE timeslide_id = '.$timeslide_id.' AND actor_id = '.$actor_id;
		$result = $db_link->query($query) or mysql_fail($db_link);
		$xyz_data = $result->fetch_assoc();
		
		// Recalculate X and Y to 0-1 decimals relative to highest number ("ceiling")
		$queryMAX = 'SELECT MAX(x) AS x, MAX(y) AS y FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `dead` = "0"';
		$resultMAX = $db_link->query($queryMAX) or mysql_fail($db_link);
		$dataMAX = $resultMAX->fetch_assoc();
		if ($dataMAX['x'] == 0) $x = 0; else $x = $xyz_data['x'] / $dataMAX['x'];
		if ($dataMAX['y'] == 0) $y = 0; else $y = $xyz_data['y'] / $dataMAX['y'];
		$dataReturned .= $delimiter.round($x, 5).'  (abs:'.$xyz_data['x'].')';
		$dataReturned .= $delimiter.round($y, 5).'  (abs:'.$xyz_data['y'].')';
		
		$dataReturned .= $delimiter.$xyz_data['z'];
		$dataReturned .= $delimiter.$xyz_data['num_con'];
		$dataReturned .= $delimiter.$xyz_data['dead'];
		
		
		// ----------- BEGINNING OF C-SCORE ELEMENTS CALCULATION PROCESS (C-Score itself is stored in database) -----------
			
			// Calculate "actual # sim ele" (# elements of this actor that are shared by at least one other actor at this moment)
			$act_num_sim_ele = 0;
			$query = 'SELECT elements, num_con FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$xyz_data = $result->fetch_assoc();
			$num_con = $xyz_data['num_con'];
			if ($xyz_data['elements'] != '') // Bugfix
			{
				$elements = explode($delimiter, $xyz_data['elements']);
				for ($i = 0; $i < count($elements); $i++)
				{
					$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` != "'.$actor_id.'" AND `dead` = "0" AND ((`elements` LIKE "%^'.$elements[$i].'^%") OR (`elements` LIKE "%^'.$elements[$i].'") OR (`elements` LIKE "'.$elements[$i].'^%") OR (`elements` LIKE "'.$elements[$i].'"))';
					$result = $db_link->query($query) or mysql_fail($db_link);
					$count_data = $result->fetch_assoc();
					if ($count_data['count'] >= 1) $act_num_sim_ele++;
				}
			}
			else $act_num_sim_ele = 0;
			$dataReturned .= $delimiter.$act_num_sim_ele;
			
			// Calculate "max # sim ele" (# maximum elements that could be shared by at least one other actor at this moment)
			$max_num_sim_ele = 0;
			$elements = [];
			$query = 'SELECT element_id FROM `'.$project_id.'.elements`';
			$result = $db_link->query($query) or mysql_fail($db_link);
			while ($row = $result->fetch_assoc()) { $elements[] = $row['element_id']; }
			for ($i = 0; $i < count($elements); $i++)
			{
				$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `dead` = "0" AND ((`elements` LIKE "%^'.$elements[$i].'^%") OR (`elements` LIKE "%^'.$elements[$i].'") OR (`elements` LIKE "'.$elements[$i].'^%") OR (`elements` LIKE "'.$elements[$i].'"))';
				$result = $db_link->query($query) or mysql_fail($db_link);
				$count_data = $result->fetch_assoc();
				if ($count_data['count'] >= 1) $max_num_sim_ele++;
			}
			$dataReturned .= $delimiter.$max_num_sim_ele;
			
			// Calculate "weight"
			if ($max_num_sim_ele == 0) $weight = 0; // !!!
			else $weight = $act_num_sim_ele / $max_num_sim_ele;
			$dataReturned .= $delimiter.$weight;
						
			// Calculate "max # con" (# maximum connections that could be had by an actor at this moment)
			$max_num_con = 0;
			$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.actors` LEFT JOIN `'.$project_id.'.xyz` ON `'.$project_id.'.actors`.`actor_id` = `'.$project_id.'.xyz`.`actor_id` WHERE `'.$project_id.'.xyz`.`timeslide_id` = "'.$timeslide_id.'" AND `'.$project_id.'.xyz`.`dead` = "0"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$con_data = $result->fetch_assoc();
			$max_num_con = $con_data['count'] - 1;
			$dataReturned .= $delimiter.$max_num_con;
		
		// ----------- END OF C-SCORE ELEMENTS CALCULATION PROCESS (C-Score itself is stored in database) -----------
		
		
		// Actors PDef and SDef
		$elements_data = [];
		$pdef = [];
		$sdef = [];
		$elements = explode($delimiter, $xyz_data['elements']);
		for ($i = 0; $i < count($elements); $i++)
		{
			if ($i == 0) $elements_query = '`element_id` = "'.$elements[$i].'" '; else $elements_query .= 'OR `element_id` = "'.$elements[$i].'" ';
		}
		$query = 'SELECT * FROM `'.$project_id.'.elements` WHERE '.$elements_query.' ORDER BY `element_name` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while($row = $result->fetch_assoc()) { $elements_data[] = $row; }
		for ($i = 0; $i < count($elements_data); $i++)
		{
			if ($elements_data[$i]['element_type'] == 'problem')
			{
				$pdef[] = ['element_id' => $elements_data[$i]['element_id'], 'element_name' => $elements_data[$i]['element_name']];
			}
			elseif ($elements_data[$i]['element_type'] == 'solution')
			{
				$sdef[] = ['element_id' => $elements_data[$i]['element_id'], 'element_name' => $elements_data[$i]['element_name']];
			}
		}
		$dataReturned .= $delimiter.count($pdef);
		for ($i = 0; $i < count($pdef); $i++)
		{
				$dataReturned .= $delimiter.$pdef[$i]['element_id'];
				$dataReturned .= $delimiter.$pdef[$i]['element_name'];
		}
		$dataReturned .= $delimiter.count($sdef);
		for ($i = 0; $i < count($sdef); $i++)
		{
				$dataReturned .= $delimiter.$sdef[$i]['element_id'];
				$dataReturned .= $delimiter.$sdef[$i]['element_name'];
		}
		
		$dataReturned .= $delimiter.count($pdef);
		$dataReturned .= $delimiter.count($sdef);
		
		// All PDef and SDef
		$allelements_data = [];
		$pdef = [];
		$sdef = [];
		$query = 'SELECT * FROM `'.$project_id.'.elements` ORDER BY `element_name` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while($row = $result->fetch_assoc()) { $allelements_data[] = $row; }
		for ($i = 0; $i < count($allelements_data); $i++)
		{
			// Search for duplicate entry in actors lists and remove them from the all lists
			$duplicate = false;
			for ($l = 0; $l < count($elements_data); $l++)
			{
				if ($elements_data[$l]['element_id'] == $allelements_data[$i]['element_id']) $duplicate = true;
			}
			
			if (!$duplicate)
			{
				if ($allelements_data[$i]['element_type'] == 'problem')
				{
					$pdef[] = ['element_id' => $allelements_data[$i]['element_id'], 'element_name' => $allelements_data[$i]['element_name']];
				}
				elseif ($allelements_data[$i]['element_type'] == 'solution')
				{
					$sdef[] = ['element_id' => $allelements_data[$i]['element_id'], 'element_name' => $allelements_data[$i]['element_name']];
				}
			}
		}
		
		$dataReturned .= $delimiter.count($pdef);
		for ($i = 0; $i < count($pdef); $i++)
		{
				$dataReturned .= $delimiter.$pdef[$i]['element_id'];
				$dataReturned .= $delimiter.$pdef[$i]['element_name'];
		}
		$dataReturned .= $delimiter.count($sdef);
		for ($i = 0; $i < count($sdef); $i++)
		{
				$dataReturned .= $delimiter.$sdef[$i]['element_id'];
				$dataReturned .= $delimiter.$sdef[$i]['element_name'];
		}
		
		// Sources
		$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.sources` WHERE `timeslide_id` = "'.$timeslide_id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$counterino = $result->fetch_assoc();
		$dataReturned .= $delimiter.$counterino['count'];
		
		$query = 'SELECT source_id, source_name FROM `'.$project_id.'.sources` WHERE `timeslide_id` = "'.$timeslide_id.'" ORDER BY `source_name` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		
		while ($data = $result->fetch_assoc())
		{
			$dataReturned .= $delimiter.$data['source_id'];
			$dataReturned .= $delimiter.$data['source_name'];
		}
		
		// Time Slides
		$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.timeslides`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$counterino = $result->fetch_assoc();
		$dataReturned .= $delimiter.$counterino['count'];
		
		$query = 'SELECT timeslide_id, timeslide_name FROM `'.$project_id.'.timeslides` ORDER BY `start_date` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		
		while ($data = $result->fetch_assoc())
		{
			$dataReturned .= $delimiter.$data['timeslide_id'];
			$dataReturned .= $delimiter.$data['timeslide_name'];
		}
		
		// Actors
		$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.actors`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$counterino = $result->fetch_assoc();
		$dataReturned .= $delimiter.$counterino['count'];
		$dead_actors = [];
		$query = 'SELECT actor_id FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `dead` = "1"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc()) { $dead_actors[] = $row['actor_id']; }
		$query = 'SELECT actor_id, actor_name FROM `'.$project_id.'.actors` ORDER BY `actor_name` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($data = $result->fetch_assoc())
		{
			$found_death = false; // Find dead actors and mark them (for different color in list)
			for ($i = 0; $i < count($dead_actors); $i++)
			{
				if ($dead_actors[$i] == $data['actor_id']) $found_death = true;
			}
			$dataReturned .= $delimiter.$data['actor_id'];
			$dataReturned .= $delimiter.$data['actor_name'];
			if ($found_death) $dataReturned .= $delimiter.'d'; else $dataReturned .= $delimiter.'a';
		}
		
		echo $dataReturned;
		$db_link->close();		
	}
}

// Project_Screen: Recalculate C-Score
function reCalculate($p_id, $t_id,$a_id)
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	
	if ($p_id == null)
	{
		if (isset($_GET['project_id']))
		{
			$project_id = $db_link->real_escape_string($_GET['project_id']);
		}
		else exit;
	}
	else $project_id = $p_id;
	
	if ($t_id == null)
	{
		$timeslides = [];
		$query = 'SELECT `timeslide_id` FROM `'.$project_id.'.timeslides`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($rowT = $result->fetch_assoc()) { $timeslides[]= $rowT['timeslide_id']; }
	}
	else $timeslides = [$t_id];
	
	if ($a_id == null)
	{
		$actors = [];
		$query = 'SELECT `actor_id` FROM `'.$project_id.'.actors`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($rowA = $result->fetch_assoc()) { $actors[]= $rowA['actor_id']; }
	}
	else $actors = [$a_id];
	
	for ($time_count = 0; $time_count < count($timeslides); $time_count++)
	{
	
		$timeslide_id = $timeslides[$time_count];
		
		for ($act_count = 0; $act_count < count($actors); $act_count++)
		{
		
			$actor_id = $actors[$act_count];
			
			// ----------- BEGINNING OF C-SCORE CALCULATION PROCESS -----------
			
			// Calculate "actual # sim ele" (# elements of this actor that are shared by at least one other actor at this moment)
			$act_num_sim_ele = 0;
			$query = 'SELECT elements, con FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$xyz_data = $result->fetch_assoc();
			$con = $xyz_data['con'];
			
			if ($xyz_data['elements'] != '') // Bugfix
			{
				$elements = explode($delimiter, $xyz_data['elements']);
				for ($i = 0; $i < count($elements); $i++)
				{
					$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` != "'.$actor_id.'" AND `dead` = "0" AND ((`elements` LIKE "%^'.$elements[$i].'^%") OR (`elements` LIKE "%^'.$elements[$i].'") OR (`elements` LIKE "'.$elements[$i].'^%") OR (`elements` LIKE "'.$elements[$i].'"))';
					$result = $db_link->query($query) or mysql_fail($db_link);
					$count_data = $result->fetch_assoc();
					if ($count_data['count'] >= 1) $act_num_sim_ele++;
				}
			}
			else $act_num_sim_ele = 0;
			
			// Calculate "max # sim ele" (# maximum elements that could be shared by at least one other actor at this moment)
			$max_num_sim_ele = 0;
			$elements = [];
			$query = 'SELECT element_id FROM `'.$project_id.'.elements`';
			$result = $db_link->query($query) or mysql_fail($db_link);
			while ($row = $result->fetch_assoc()) { $elements[] = $row['element_id']; }
			for ($i = 0; $i < count($elements); $i++)
			{
				$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `dead` = "0" AND ((`elements` LIKE "%^'.$elements[$i].'^%") OR (`elements` LIKE "%^'.$elements[$i].'") OR (`elements` LIKE "'.$elements[$i].'^%") OR (`elements` LIKE "'.$elements[$i].'"))';
				$result = $db_link->query($query) or mysql_fail($db_link);
				$count_data = $result->fetch_assoc();
				if ($count_data['count'] >= 1) $max_num_sim_ele++;
			}
			
			// Calculate "weight"
			if ($max_num_sim_ele == 0) $weight = 0; else $weight = $act_num_sim_ele / $max_num_sim_ele;
			
			// Calculate "max # con" (# maximum connections that could be had by an actor at this moment)
			$max_num_con = 0;
			$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.actors` LEFT JOIN `'.$project_id.'.xyz` ON `'.$project_id.'.actors`.`actor_id` = `'.$project_id.'.xyz`.`actor_id` WHERE `'.$project_id.'.xyz`.`timeslide_id` = "'.$timeslide_id.'" AND `'.$project_id.'.xyz`.`dead` = "0"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$con_data = $result->fetch_assoc();
			$max_num_con = $con_data['count'] - 1;
			
			// Calculate "num_con" (based on the connections table)
			if ($con == '') $num_con = 0;
			else
			{
				$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `dead` = "0" AND `actor_id` in ('.str_replace('^',',',$con).')';
				$result = $db_link->query($query) or mysql_fail($db_link);
				$brezel = $result->fetch_assoc();
				$num_con = $brezel['count'];
			}
						
			// Fix, if num_con > max_num_con, then num_con = max_num_con!
			if ($num_con > $max_num_con) $num_con = $max_num_con;
			
			// Calculate C-Score
			if ($max_num_con == 0) $cscore = 0; else $cscore = $weight * ($num_con / $max_num_con);
			//Debug: echo 'C-Score of Actor '.$actor_id.' in Time Slice '.$timeslide_id.': '.$cscore.'<br>';
			
			// ----------- END OF C-SCORE CALCULATION PROCESS -----------
			
			// Save C-Score
			$query = 'UPDATE `'.$project_id.'.xyz` SET `y` = "'.$cscore.'", `num_con` = "'.$num_con.'" WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
			$db_link->query($query) or mysql_fail($db_link);
		}
	}
	
	$db_link->close();
}

// Project Screen: Save data
function save()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter, $dateFormat;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);

	$start_date1 = $db_link->real_escape_string($_POST['start_date']);
	$end_date1 = $db_link->real_escape_string($_POST['end_date']);
	$timeslide_id = $db_link->real_escape_string($_POST['timeslide_id']);
	$timeslide_name = $db_link->real_escape_string($_POST['timeslide_name']);
	$timeslide_name = str_replace(']', ')', $timeslide_name);
	$timeslide_name = str_replace('[', '(', $timeslide_name);
	$timeslide_information = $db_link->real_escape_string($_POST['timeslide_information']);
	$actor_name = $db_link->real_escape_string($_POST['actor_name']);
	$actor_information = $db_link->real_escape_string($_POST['actor_information']);
	$actor_id = $db_link->real_escape_string($_POST['actor_id']);
	$fitness = $db_link->real_escape_string($_POST['fitness']);
	if (isset($_POST['dead'])) $dead = 1; else $dead = 0;
	
	// Format the dates to timestamps
	$dtime = DateTime::createFromFormat($dateFormat.' H:i:s', $start_date1.' 00:00:00');
	$start_date = date_timestamp_get($dtime);
	$dtime2 = DateTime::createFromFormat($dateFormat.' H:i:s', $end_date1.' 00:00:00');
	$end_date = date_timestamp_get($dtime2);
	
	// Check if End Date > Start Date
	if ($end_date < $start_date)
	{
		echo 'The starting date of the selected Time Slide must be before the end date.';
		$db_link->close();
		return;
	}
	
	// Check if time period overlaps an existing time period
	$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.timeslides`';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$counter = $result->fetch_assoc();
	$query = 'SELECT timeslide_id, timeslide_name, start_date, end_date FROM `'.$project_id.'.timeslides`';
	$result = $db_link->query($query) or mysql_fail($db_link);
	for ($i = 0; $i < $counter['count']; $i++)
	{
		$timeslide_data = $result->fetch_assoc();
		if ($timeslide_data['timeslide_id'] != $timeslide_id) // Obviously don't compare the time slide with itself
		{
			if ((($end_date >= $timeslide_data['start_date']) && ($end_date <= $timeslide_data['end_date'])) || (($start_date >= $timeslide_data['start_date']) && ($start_date <= $timeslide_data['end_date'])))
			{
				echo 'Sorry, the entered time period overlaps with the time period of Time Slide "'.$timeslide_data['timeslide_name'].'" (internal id '.$timeslide_data['timeslide_id'].')';
				$db_link->close();
				return;
			}
		}
	}

	// Save stuff
	$query = 'UPDATE `'.$project_id.'.timeslides` SET `timeslide_name` = "'.$timeslide_name.'", `timeslide_information` = "'.$timeslide_information.'", `start_date` = "'.$start_date.'", `end_date` = "'.$end_date.'" WHERE `timeslide_id` = "'.$timeslide_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'UPDATE `'.$project_id.'.actors` SET `actor_name` = "'.$actor_name.'", `actor_information` = "'.$actor_information.'" WHERE `actor_id` = "'.$actor_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'UPDATE `'.$project_id.'.xyz` SET `z` = "'.$fitness.'", `dead` = "'.$dead.'" WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
	$db_link->query($query) or mysql_fail($db_link);

	reCalculate($project_id, $timeslide_id, null); // Recalculate C-Score for this time slide (all actors)
	
	echo 'ok';
	$db_link->close();
}

// Project Screen: Add new entry
function add()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter, $standardX, $standardY, $standardZ;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$type = $db_link->real_escape_string($_GET['type']);

	switch($type)
	{
	
		// --------------------------------------ADD TIMESLIDE----------------------------------------
		case 'timeslide':
		// Add new timeslide
		$query = 'INSERT INTO `'.$project_id.'.timeslides` (timeslide_name, timeslide_information) VALUES ("(new field)", "")';
		$db_link->query($query) or mysql_fail($db_link);

		// Find out the timeslide-id of the just created timeslide
		$query = 'SELECT MAX(timeslide_id) AS timeslide_id FROM `'.$project_id.'.timeslides`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$timeslide_data = $result->fetch_assoc();
		$timeslide_id = $timeslide_data['timeslide_id'];
		
		// Find out the actor_ids of the existing actors
		$query = 'SELECT actor_id FROM `'.$project_id.'.actors`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc())
		{
			$actor_data[] = $row;
		}
		$numberActors = count($actor_data);
		
		// Add new xyz-data for the timeslide
		for ($i = 0; $i < $numberActors; $i++)
		{
			$query = 'INSERT INTO `'.$project_id.'.xyz` (timeslide_id, actor_id, x, y, z) VALUES ("'.$timeslide_id.'", "'.$actor_data[$i]['actor_id'].'", "'.$standardX.'", "'.$standardY.'", "'.$standardZ.'")';
			$db_link->query($query) or mysql_fail($db_link);
		}

		echo $timeslide_data['timeslide_id'];
		break;
		
		// --------------------------------------ADD ACTOR----------------------------------------
		case 'actor':
		// Add new actor
		$query = 'INSERT INTO `'.$project_id.'.actors` (actor_name, actor_information) VALUES ("(New actor)", "")';
		$db_link->query($query) or mysql_fail($db_link);

		// Find out the actor-id of the just created actor
		$query = 'SELECT MAX(actor_id) AS actor_id FROM `'.$project_id.'.actors`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$actor_data = $result->fetch_assoc();
		$actor_id = $actor_data['actor_id'];
		
		// Find out the timeslide_ids of the existing timeslides
		$query = 'SELECT timeslide_id FROM `'.$project_id.'.timeslides`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc())
		{
			$timeslide_data[] = $row;
		}
		$numberTimeslides = count($timeslide_data);
		
		// Add new xyz-data for the timeslide
		for ($i = 0; $i < $numberTimeslides; $i++)
		{
			$query = 'INSERT INTO `'.$project_id.'.xyz` (timeslide_id, actor_id, x, y, z) VALUES ("'.$timeslide_data[$i]['timeslide_id'].'", "'.$actor_id.'", "'.$standardX.'", "'.$standardY.'", "'.$standardZ.'")';
			$db_link->query($query) or mysql_fail($db_link);
		}
		
		reCalculate($project_id, null, null);
		
		echo $actor_data['actor_id'];
		break;
		
		// --------------------------------------ADD PDEF/SDEF (ACTOR) ----------------------------------------
		case 'pdef':
		case 'sdef':
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
		$actor_id = $db_link->real_escape_string($_GET['actor_id']);
		$element_id = $db_link->real_escape_string($_GET['id']);
		if ($element_id == 0) break;
		$query = 'SELECT entry_id, elements FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$xyz_data = $result->fetch_assoc();
		$elements = explode($delimiter, $xyz_data['elements']);
		for ($co = 0; $co < count($elements); $co++)
		{
			if ($elements[$co] == "") unset($elements[$co]);
		}
		$elements[] = $element_id;
		$query = 'UPDATE `'.$project_id.'.xyz` SET `x` = "'.count($elements).'", `elements` = "'.implode($delimiter, $elements).'" WHERE `entry_id` = "'.$xyz_data['entry_id'].'"';
		$db_link->query($query) or mysql_fail($db_link);
		
		reCalculate($project_id, $timeslide_id, null);
		
		echo 1;
		break;
		
		// --------------------------------------ADD PDEF/SDEF (ALL) ----------------------------------------
		case 'pdef_all':
		case 'sdef_all':
		$add_name = $db_link->real_escape_string($_GET['add_name']);
		if ($add_name == '') { echo 'Empty name!'; break; }
		if ($type == 'pdef_all') $element_type = 'problem'; elseif ($type == 'sdef_all') $element_type = 'solution';
		$query = 'INSERT INTO `'.$project_id.'.elements` (element_type, element_name) VALUE ("'.$element_type.'", "'.$add_name.'")';
		$db_link->query($query) or mysql_fail($db_link);
		echo 1;
		break;
	}
		$db_link->close();
}

// Project Screen: Copy Entry
function copyElement()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	// Copy Timeslide
	if (isset($_GET['timeslide_id']))
	{
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
		
		// Duplicate timeslide entry
		$query = 'SELECT * FROM `'.$project_id.'.timeslides` WHERE `timeslide_id` = "'.$timeslide_id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$data = $result->fetch_assoc();
		$query = 'INSERT INTO `'.$project_id.'.timeslides` (timeslide_name, timeslide_information, start_date, end_date) VALUES ("[copy] '.$data['timeslide_name'].'", "'.$data['timeslide_information'].'", "'.$data['start_date'].'", "'.$data['end_date'].'")';
		$db_link->query($query) or mysql_fail($db_link);
		
		// Duplicate data
		$query = 'SELECT * FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc()) { $xyz_data[] = $row; }
		$query = 'SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.$db_projectdb.'" AND TABLE_NAME = "'.$project_id.'.timeslides"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$data = $result->fetch_assoc();
		$new_timeslide_id = $data['AUTO_INCREMENT']-1;
		for ($i = 0; $i < count($xyz_data); $i++)
		{
			$query = 'INSERT INTO `'.$project_id.'.xyz` (timeslide_id, actor_id, dead, x, y, z, num_con, con) VALUES ("'.$new_timeslide_id.'", "'.$xyz_data[$i]['actor_id'].'", "'.$xyz_data[$i]['dead'].'", "'.$xyz_data[$i]['x'].'", "'.$xyz_data[$i]['y'].'", "'.$xyz_data[$i]['z'].'", "'.$xyz_data[$i]['num_con'].'", "'.$xyz_data[$i]['con'].'")';
			$db_link->query($query) or mysql_fail($db_link);
		}
		
		//Recalculate scores
		reCalculate($project_id, null, null);
	}
	
	echo 'ok';
	$db_link->close();
}

// Project Screen: Delete entry
function delete()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	$type = $db_link->real_escape_string($_GET['type']);
	$id = $db_link->real_escape_string($_GET['id']);
	
	switch ($type)
	{ 
		// -------------------- DELETE TIME SLIDE --------------------
		case 'timeslide_id':
		// Check for minimum of 1 timeslide
		$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.timeslides`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$counterino = $result->fetch_assoc();
		if ($counterino['count'] <= 1)
		{
			echo 'Cannot have less than one Time Slide in a project. Deleting not possible!';
			exit;
		}
		$query = 'DELETE FROM `'.$project_id.'.timeslides` WHERE `timeslide_id` = "'.$id.'"';
		$db_link->query($query) or mysql_fail($db_link);
		$query = 'DELETE FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$id.'"';
		$db_link->query($query) or mysql_fail($db_link);
		$query = 'DELETE FROM `'.$project_id.'.sources` WHERE `timeslide_id` = "'.$id.'"';
		$db_link->query($query) or mysql_fail($db_link);
		break;
		
		// -------------------- DELETE ACTOR --------------------
		case 'actor_id': 

		// Delete connections of this actor
		$query = 'SELECT timeslide_id, con FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc())
		{
			$t_id = $row['timeslide_id'];
			$con_exp = explode($delimiter, $row['con']);
			foreach ($con_exp as $key => $value)
			{
				$query_other = 'SELECT con FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$value.'" AND `timeslide_id` = "'.$t_id.'"';
				$result_other = $db_link->query($query_other) or mysql_fail($db_link);
				$data_other = $result_other->fetch_assoc();
				$con_other = explode($delimiter, $data_other['con']);
				foreach ($con_other as $key_other => $value_other)
				{
					if ($value_other == $id) unset($con_other[$key_other]);
				}
				$con_other = array_values($con_other);
				$query_other = 'UPDATE `'.$project_id.'.xyz` SET `con` = "'.implode($delimiter,$con_other).'" WHERE `actor_id` = "'.$value.'" AND `timeslide_id` = "'.$t_id.'"';
				$db_link->query($query_other) or mysql_fail($db_link);
			}
		}

		// Check for minimum of 1 actor
		$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.actors`';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$counterino = $result->fetch_assoc();
		if ($counterino['count'] <= 1)
		{
			echo 'Cannot have less than one Actor in a project. Deleting not possible!';
			exit;
		}
		$query = 'DELETE FROM `'.$project_id.'.actors` WHERE `actor_id` = "'.$id.'"';
		$db_link->query($query) or mysql_fail($db_link);
		$query = 'DELETE FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$id.'"';
		$db_link->query($query) or mysql_fail($db_link);
		
		reCalculate($project_id, null, null);
		
		break;

		// -------------------- DELETE PDEF/SDEF (ALL) --------------------
		case 'pdef_all':
		case 'sdef_all':
		$query = 'DELETE FROM `'.$project_id.'.elements` WHERE element_id = '.$id;
		$db_link->query($query) or mysql_fail($db_link);
		// Check all data sets for occurrence
		$query = 'SELECT entry_id, elements FROM `'.$project_id.'.xyz` WHERE elements LIKE "%'.$id.'%"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc()) 
		{
			$elements = explode($delimiter, $row['elements']);
			$found = false;
			for ($i = 0; $i < count($elements); $i++)
			{
				if ($elements[$i] == $id)
				{
					unset($elements[$i]);
					$found = true;
				}
			}
			if ($found == true)
			{
				$elements = array_values($elements);
				$query = 'UPDATE `'.$project_id.'.xyz` SET `x` = "'.count($elements).'", `elements` = "'.implode($delimiter,$elements).'" WHERE `entry_id` = "'.$row['entry_id'].'"';
				$db_link->query($query) or mysql_fail($db_link);
			}
		}
		
		reCalculate($project_id, null, null);
		
		break;
		
		// -------------------- DELETE PDEF/SDEF (ACTOR) --------------------
		case 'pdef_actor':
		case 'sdef_actor':
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
		$actor_id = $db_link->real_escape_string($_GET['actor_id']);
		$query = 'SELECT entry_id, elements FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" AND `actor_id` = "'.$actor_id.'"';
		$result = $db_link->query($query) or mysql_fail($db_link);
		$data = $result->fetch_assoc();
		$elements = explode($delimiter, $data['elements']);
		for ($i = 0; $i < count($elements); $i++)
		{
			if ($elements[$i] == $id)
			{
				unset($elements[$i]);
			}
		}
		$elements = array_values($elements);
		$query = 'UPDATE `'.$project_id.'.xyz` SET `x` = "'.count($elements).'", `elements` = "'.implode($delimiter,$elements).'" WHERE `entry_id` = "'.$data['entry_id'].'"';
		$db_link->query($query) or mysql_fail($db_link);
		
		reCalculate($project_id, $timeslide_id, null);
		
		break;
	}

	echo 'ok';
	$db_link->close();
}

// Project Screen: Edit an Element (PDef/SDef)
function editElement()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$element_id = $db_link->real_escape_string($_GET['element_id']);
	if (($element_id == null) || ($element_id == 0) || ($element_id == -1)) exit;
	$element_name = $db_link->real_escape_string($_GET['element_name']);
	if ($element_name == '') exit;
	$query = 'UPDATE `'.$project_id.'.elements` SET `element_name` = "'.$element_name.'" WHERE `element_id` = "'.$element_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	echo 'ok';
	$db_link->close();
}

// Project Screen: Export to CSV
function exportcsv()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	// Prepare outer loop
	$query = 'SELECT project_name FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$resultP = $db_link->query($query) or mysql_fail($db_link);
	$project_data = $resultP->fetch_assoc();
	$project_name = $project_data['project_name'];
	$csv_data[] = ['[UN-CODE]'];
	$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.actors`';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$counter = $result->fetch_assoc();
	$num_actors = $counter['count'];
	$query = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.timeslides`';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$counter = $result->fetch_assoc();
	$num_timeslides = $counter['count'];
	
	// First add Time Slide names - if timeslide_id is set then only one
	$type_query = '';
	$order_add = '';
	if (isset($_GET['timeslide_id']))
	{
		$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
		$type_query = 'WHERE `timeslide_id` = "'.$timeslide_id.'"';
		$order_add = ' DESC';
	}
	$query = 'SELECT timeslide_id, timeslide_name FROM `'.$project_id.'.timeslides` '.$type_query.' ORDER BY `start_date` ASC';
	$resultT = $db_link->query($query) or mysql_fail($db_link);
	$timeslide_data = [];
	while ($rowT = $resultT->fetch_assoc())
	{
		array_push($csv_data[0], $rowT['timeslide_name'], '', '');
		$timeslide_data[] = $rowT['timeslide_id'];
	}

	// Then add coordinates
	array_push($csv_data, [$project_name]);
	for ($f = 0; $f < count($timeslide_data); $f++)
	{
			array_push($csv_data[1], 'PSD', 'C-Score', 'Fitness');
	}
	
	// Get the max XYZ data
	for ($u = 0; $u < count($timeslide_data); $u++)
	{
		$queryMAX = 'SELECT MAX(x) AS x, MAX(y) AS y, MAX(z) AS z FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_data[$u].'" AND `dead` = "0"';
		$resultMAX = $db_link->query($queryMAX) or mysql_fail($db_link);
		$dataMAX[] = $resultMAX->fetch_assoc();
	}
	
	// Create complicated query so that xyz-data is in the same order as the timeslide-data
	$order_query = '';
	for ($q = 0; $q < count($timeslide_data); $q++)
	{
		if ($q == 0) $order_query .= $timeslide_data[0]; else $order_query .= ', '.$timeslide_data[$q];
	}
	
	// Loop of actors
	$query = 'SELECT actor_id, actor_name FROM `'.$project_id.'.actors` ORDER BY actor_name ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$act_no = 2;
	while ($actor_data = $result->fetch_assoc())
	{
	
		// Get this actor's XYZ data
		$query = 'SELECT TRIM(TRAILING "." FROM TRIM(TRAILING "0" from x)) AS x, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from y)) AS y, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from z)) AS z, dead FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$actor_data['actor_id'].'" ORDER BY FIELD (timeslide_id, '.$order_query.' )'.$order_add;
		$resultXYZ = $db_link->query($query) or mysql_fail($db_link);
		
		array_push($csv_data, [$actor_data['actor_name']]);

		// Loop of time slides
		for ($i = 0; $i < count($timeslide_data); $i++)
		{
			$row = $resultXYZ->fetch_assoc();
			if ($dataMAX[$i]['x'] == 0) $x = 0; else $x = $row['x'] / $dataMAX[$i]['x'];
			if ($dataMAX[$i]['y'] == 0) $y = 0; else $y = $row['y'] / $dataMAX[$i]['y'];
			if ($dataMAX[$i]['z'] == 0) $z = 0; else $z = $row['z'] / $dataMAX[$i]['z'];
			if ($row['dead'] == 1) array_push($csv_data[$act_no], 'not present', 'not present', 'not present'); else array_push($csv_data[$act_no], round($x, 5), round($y, 5), round($z, 5));
		}
		$act_no++;
	}

	download_send_headers("uncode_export_" . date("Y-m-d") . ".csv");
	echo array2csv($csv_data);
	
	$db_link->close();
}

// Project Screen: Show PSD persistence
function persistence()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	// Here we create an array for all Excel columns, ranging from A to ZZ!
	$letterspace = range('A', 'Z');
	$alphas = $letterspace;
	foreach ($letterspace as $letter)
	{
		foreach ($letterspace as $key => $val)
		{
			$alphas[] = $letter.$letterspace[$key];
		}
	}
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("UN-CODE")
							 ->setLastModifiedBy("UN-CODE")
							 ->setTitle("UN-CODE persistence of PSDs")
							 ->setSubject("persistence")
							 ->setDescription("")
							 ->setKeywords("un-code problem solution definitions complexity politics")
							 ->setCategory("Export");
	
	
	for ($sheet = 0; $sheet < 2; $sheet++)
	{
		
		if ($sheet != 0) $objPHPExcel->createSheet($sheet);
		$objPHPExcel->setActiveSheetIndex($sheet);
		if ($sheet == 0) $objPHPExcel->setActiveSheetIndex($sheet)->setTitle('Colored');
		if ($sheet == 1) $objPHPExcel->setActiveSheetIndex($sheet)->setTitle('Print Grey-Scale');
	
	// Style work
	$align_middle = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )
    );
	
	$borders = array(
		'borders' => array(
			'horizontal' => array(
				'style' => PHPExcel_Style_Border::BORDER_HAIR,
				'color' => array('rgb' => 'CDCDCD'),
			),
		)
	);

	$head_style = array(
	    'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'wrap' => (true)
		),
		'font' => array(
			'color' => array('rgb' => '000000'),
			'size' => 8,
			'bold' => true)
	);
	
	$result_style = array(
		'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'wrap' => (true)),
		'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 10,
				'bold' => true),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => '000000'),
				)
			)
        );
	
	if ($sheet == 0) // --------- STYLE FOR COLORED OUTPUT ---------
	{
		$legend_style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'FFC9C9C9',
				),
				'endcolor' => array(
					'argb' => 'FFFFFFFF',
				),
			),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);
		
		$legend_style_p = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'FFAFBA',
				),
				'endcolor' => array(
					'argb' => 'FFFFFFFF',
				),
			),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);

		$legend_style_s = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'BAFFA5',
				),
				'endcolor' => array(
					'argb' => 'FFFFFFFF',
				),
			),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);
		
		$persistence_style = array(
				'fill'  => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'startcolor' => array('argb' => 'FFFFFFF'),
					'endcolor' => array( 'rgb' => 'FFE05F')
				),
				'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true)
			);
			
		$totalshared_style = array(
				'fill'  => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'startcolor' => array('rgb' => 'D1E7FF'),
					'endcolor' => array( 'argb' => 'FFFFFFF'),
				'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true)
				)
			);
		
		$inactive_style = array(
				'fill'  => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'startcolor' => array('rgb' => 'F7F7F7'),
					'endcolor' => array( 'argb' => 'FFFFFFF')
				)
			);
	}
	else // --------- STYLE FOR GREY-SCALE/PRINT OUTPUT ---------
	{
			$legend_style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_NONE),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);
		
		$legend_style_p = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_NONE),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);

		$legend_style_s = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => (true)),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_NONE),
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true),
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
					)
				)
		);
		
		$persistence_style = array(
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true)
			);
			
		$totalshared_style = array(
			'font' => array(
					'color' => array('rgb' => '000000'),
					'size' => 10,
					'bold' => true)
				);
				
		$inactive_style = array(
				'fill'  => array(
					'type' => PHPExcel_Style_Fill::FILL_NONE)
			);
	}

		
				
	// Generate background colors
	$colors = [];
	for ($c = 0; $c < 128; $c++)
	{
		if ($sheet == 0) $rgb = [$c*2,128-($c/2),0]; else $rgb = [256-($c*2),256-($c*2),256-($c*2)]; // Only difference in terms of background colors between Colored Mode ($sheet == 0) and Grey-Scale/Print Mode ($sheet == 1)
		$color = rgb2hex($rgb);
		
		// Adjust color (white/black) of font in relation to background color
		$brightness = sqrt(($rgb[0] * $rgb[0] * 0.241) + ($rgb[1] * $rgb[1] * 0.691) + ($rgb[2] * $rgb[2] * 0.068));
		if ($brightness > 135)
			$styleArray = array(
			'fill'  => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => $color)),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
				'wrap' => (true)),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 10,
				'bold' => true
			));
		else 
			$styleArray = array(
			'fill'  => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => $color)),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
				'wrap' => (true)),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'size' => 10,
				'bold' => true
			));
		
		$colors[$c] = $styleArray;
	}

	// Loading and adding project name
	$query = 'SELECT project_name FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$resultP = $db_link->query($query) or mysql_fail($db_link);
	$project_data = $resultP->fetch_assoc();
	$project_name = $project_data['project_name'];

	// Loading and adding timeslide data
	$query = 'SELECT timeslide_id, timeslide_name FROM `'.$project_id.'.timeslides` ORDER BY `start_date` ASC';
	$resultT = $db_link->query($query) or mysql_fail($db_link);
	$timeslide_data = [];
	while ($rowT = $resultT->fetch_assoc())
	{
		$timeslide_data[] = $rowT;
	}
	for ($t = 0; $t < count($timeslide_data); $t++)
	{
			$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$t+1].'1', $timeslide_data[$t]['timeslide_name']);
			$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$t+1].'1')->applyFromArray($legend_style);
			$objPHPExcel->setActiveSheetIndex($sheet)->getColumnDimension($alphas[$t+1])->setWidth(20);
	}
	
	// Add the main headline with app title and lineage title
	$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue('A1', 'un-code.org: PSD persistence of lineage "'.$project_name.'"');
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A1')->applyFromArray($head_style);
	
	// Add persistence headlines
	$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[count($timeslide_data)+1].'1', 'Total Shared');
	$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[count($timeslide_data)+2].'1', 'Persistence');
	
	
	
	// ---------------- Beginning cycle ----------------
	
	$cycle_row = 2; // $cycle_row is the next free row!
	for ($cycle = 0; $cycle < 2; $cycle++)
	{
	// Add Cycle Headline
	$objPHPExcel->setActiveSheetIndex($sheet)->mergeCells('A'.($cycle_row).':'.$alphas[count($timeslide_data)+2].($cycle_row));
	if ($cycle == 0) {
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue('A'.($cycle_row), 'Problem Definitions');
		$objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A'.($cycle_row).':'.$alphas[count($timeslide_data)+2].($cycle_row))->applyFromArray($legend_style_p);
	}
	else {
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue('A'.($cycle_row), 'Solution Definitions');
		$objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A'.($cycle_row).':'.$alphas[count($timeslide_data)+2].($cycle_row))->applyFromArray($legend_style_s);
	}
	$cycle_row++;
	
	// Loading elements data and adding them to the table
	if ($cycle == 0) $queryE = 'SELECT element_id, element_type, element_name FROM `'.$project_id.'.elements` WHERE `element_type` = "problem" ORDER BY `element_name` ASC';
	else $queryE = 'SELECT element_id, element_type, element_name FROM `'.$project_id.'.elements` WHERE `element_type` = "solution" ORDER BY `element_name` ASC';
	$resultE = $db_link->query($queryE) or mysql_fail($db_link);
	$elements_data = [];
	$eli = 0; // to count the added elements
	while ($rowE = $resultE->fetch_assoc())
	{
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue('A'.($eli+$cycle_row), $rowE['element_name']);
		if ($cycle == 0) $objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A'.($eli+$cycle_row))->applyFromArray($legend_style);
		else $objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A'.($eli+$cycle_row))->applyFromArray($legend_style);
		$rowE['num_total'] = 0;
		$rowE['num_ts'] = [];
		$rowE['active_total'] = 0;
		$elements_data[] = $rowE;
		$eli++;
	}
	$objPHPExcel->setActiveSheetIndex($sheet)->getColumnDimension('A')->setWidth(40);
	
	// Loading xyz data for all timeslides
	for ($tsi = 0; $tsi < count($timeslide_data); $tsi++)
	{
		// How many actors are alive at this point in time?
		$queryCount = 'SELECT COUNT(*) AS count FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_data[$tsi]['timeslide_id'].'" AND `dead` = "0"';
		$resultCount = $db_link->query($queryCount) or mysql_fail($db_link);
		$dataCount = $resultCount->fetch_assoc();
		
		// Get the data
		$queryX = 'SELECT entry_id, timeslide_id, actor_id, dead, elements FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_data[$tsi]['timeslide_id'].'"';
		$resultX = $db_link->query($queryX) or mysql_fail($db_link);
		$xyz_data[$tsi] = [];
		while ($rowX = $resultX->fetch_assoc())
		{
			if ($rowX['dead'] == 0) {
				$new_elements = explode($delimiter, $rowX['elements']);
				$rowX['elements'] = $new_elements;
				$xyz_data[$tsi][] = $rowX;
				$xyz_data[$tsi]['alive_count'] = $dataCount['count']; // Number of alive actors at this point in time
			}
		}
	}
	
	$highest_num_ts = 0;
	// Go through timeslide by timeslide and then element by element and measure a) the number this element is used per time slide and b) the total number of used 
	for ($tsi = 0; $tsi < count($timeslide_data); $tsi++)
	{
		for ($eli = 0; $eli < count($elements_data); $eli++)
		{
			$num_ts = 0;
			$haystack = array_column($xyz_data[$tsi], 'elements');
			for ($ai = 0; $ai < count($haystack); $ai++)
			{
				if (is_numeric(recursive_array_search($elements_data[$eli]['element_id'], $haystack[$ai]))) 
				{
					$num_ts++; // Measures  the number this element is used per timeslide
					$elements_data[$eli]['num_total']++; // Measures the number this element is used in total
				}
			}
			$elements_data[$eli]['num_ts'][$tsi] = $num_ts;
			if ($num_ts > $highest_num_ts) $highest_num_ts = $num_ts;
		}
	}
	
	// Go through timeslide by timeslide and then element by element and use the data measured above to ADD table content
	for ($tsi = 0; $tsi < count($timeslide_data); $tsi++)
	{
		for ($eli = 0; $eli < count($elements_data); $eli++)
		{
			//if ($elements_data[$eli]['num_ts'] == 0) break; // If the element is never ever used, then just ignore it and don't waste table space.
			if ($elements_data[$eli]['num_ts'][$tsi] > 0) // Is the element actually used in this very time slide?
			{
				$elements_data[$eli]['active_total']++; // Save the activity performance of this element
				$style_choice = round(($elements_data[$eli]['num_ts'][$tsi] / $highest_num_ts) * 127); // Determine the right color of the cell, depending on the activity relative to the highest one.
				$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$tsi+1].($eli+$cycle_row))->applyFromArray($colors[$style_choice]);
				$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$tsi+1].($eli+$cycle_row), round($elements_data[$eli]['num_ts'][$tsi]/$xyz_data[$tsi]['alive_count']*100).'% ('.$elements_data[$eli]['num_ts'][$tsi].'/'.$xyz_data[$tsi]['alive_count'].')');
			}
			else $objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$tsi+1].($eli+$cycle_row))->applyFromArray($inactive_style);
		}
	}

	// "Total Shared" and "persistence"
	$resultColumn = count($timeslide_data)+1; // First column for the results
	$resultRow = $cycle_row; // First row for the results
	$objPHPExcel->setActiveSheetIndex($sheet)->getColumnDimension($alphas[$resultColumn])->setWidth(15);
	$objPHPExcel->setActiveSheetIndex($sheet)->getColumnDimension($alphas[$resultColumn+1])->setWidth(15);	
	
	// Style: Add the colors for the "shared" column and the "persistence" column
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$resultColumn].'1:'.$alphas[$resultColumn].($resultRow+count($elements_data)-1))->applyFromArray($totalshared_style);
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$resultColumn+1].'1:'.$alphas[$resultColumn+1].($resultRow+count($elements_data)-1))->applyFromArray($persistence_style);
	// Style: Add the borders for the "shared" headline and the "persistence" headline
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$resultColumn].'1:'.$alphas[$resultColumn+1].'2')->applyFromArray($result_style);
	// Style: Aligning scores at middle
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$resultColumn].$resultRow.':'.$alphas[$resultColumn+1].($resultRow+count($elements_data)))->applyFromArray($align_middle);
	
	// Add results
	for ($eli = 0; $eli < count($elements_data); $eli++)
	{
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$resultColumn].($resultRow+$eli), $elements_data[$eli]['num_total']); // Total shared
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$resultColumn+1].($resultRow+$eli), $elements_data[$eli]['active_total']); // persistence
	}

	
	// Adding borders
	$objPHPExcel->setActiveSheetIndex($sheet)->getStyle('B'.$cycle_row.':'.$alphas[count($timeslide_data)+2].(count($elements_data)+$cycle_row))->applyFromArray($borders);
	
	// Correcting $cycle_row for the next cycle
	$cycle_row += count($elements_data);
	}
	
	// ---------------- Ending cycle ----------------
	
	
	}
	
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="UNCODE_persistence_'.date("Y-m-d").'.xlsx"');
	
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	saveViaTempFile($objWriter);
	
	$db_link->close();
}

// Project: Definitions Overview
// If a project_id is given, for a project. If a study_id is given, for all projects of this study.
function exportDef()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	
	// Here we create an array for all Excel columns, ranging from A to ZZ!
	$letterspace = range('A', 'Z');
	$alphas = $letterspace;
	foreach ($letterspace as $letter)
	{
		foreach ($letterspace as $key => $val)
		{
			$alphas[] = $letter.$letterspace[$key];
		}
	}

	$objPHPExcel = new PHPExcel();
	if (isset($_GET['project_id'])) $task[0] = $db_link->real_escape_string($_GET['project_id']);
	elseif (isset($_GET['study_id']))
	{
		$study_id = $db_link->real_escape_string($_GET['study_id']);
		$query = 'SELECT project_id FROM `projects` WHERE `study_id` = "'.$study_id.'" ORDER BY `project_name` ASC';
		$result = $db_link->query($query) or mysql_fail($db_link);
		while ($row = $result->fetch_assoc()) { $task[] = $row['project_id']; }
	}
	
	$sheet = 0;
	
	$objPHPExcel->getProperties()->setCreator("UN-CODE")
							 ->setLastModifiedBy("UN-CODE")
							 ->setTitle("UN-CODE PSD Definitions")
							 ->setSubject("UN-CODE Problem-Solution-Definitions")
							 ->setDescription("")
							 ->setKeywords("un-code problem solution definitions politics complexity ")
							 ->setCategory("Export");
	$align_middle = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );
	
	for ($tc = 0; $tc < count($task); $tc++) // Beginning of Task
	{
	$project_id = $task[$tc];
	$actor_data = [];
	
	// Loading project name
	$query = 'SELECT project_name FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$resultP = $db_link->query($query) or mysql_fail($db_link);
	$project_data = $resultP->fetch_assoc();
	$project_name = $project_data['project_name'];
	
	// Loading elements data
	$query = 'SELECT element_id, element_type, element_name FROM `'.$project_id.'.elements`';
	$resultE = $db_link->query($query) or mysql_fail($db_link);
	$elements_data = [];
	while ($rowE = $resultE->fetch_assoc())
	{
		$elements_data[$rowE['element_id']] = $rowE;
	}
	
	// Generate background colors
	foreach ($elements_data as $key => $value)
	{
		$color = substr(md5($elements_data[$key]['element_name']), 0, 6);
		$rgb = hex2rgb($color);
		$brightness = sqrt(($rgb[0] * $rgb[0] * 0.241) + ($rgb[1] * $rgb[1] * 0.691) + ($rgb[2] * $rgb[2] * 0.068));
		if ($brightness > 125)
			$styleArray = array(
			'fill'  => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => $color)),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 10
			));
		else 
			$styleArray = array(
			'fill'  => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => $color)),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'size' => 10
			));
		$elements_data[$key]['style'] = $styleArray;
	}

	// Loading time slide data
	$query = 'SELECT timeslide_id, timeslide_name FROM `'.$project_id.'.timeslides` ORDER BY `start_date` ASC';
	$resultT = $db_link->query($query) or mysql_fail($db_link);
	$timeslide_data = [];
	while ($rowT = $resultT->fetch_assoc())
	{
		$timeslide_data[] = $rowT;
	}
	
	// Create complicated query so that elements-data is in the same order as the timeslide-data
	$order_query = '';
	for ($q = 0; $q < count($timeslide_data); $q++)
	{
		if ($q == 0) $order_query .= $timeslide_data[0]['timeslide_id']; else $order_query .= ', '.$timeslide_data[$q]['timeslide_id'];
	}

	// Loading Actors
	$query = 'SELECT actor_id, actor_name FROM `'.$project_id.'.actors` ORDER BY actor_name ASC';
	$resultA = $db_link->query($query) or mysql_fail($db_link);
	while ($rowA = $resultA->fetch_assoc())
	{
		
		// Loading Elements of each Actor
		$query = 'SELECT elements, dead FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$rowA['actor_id'].'" ORDER BY FIELD (timeslide_id, '.$order_query.' )';
		$resultE = $db_link->query($query) or mysql_fail($db_link);
		$time = 0;
		while ($rowE = $resultE->fetch_assoc())
		{
			// Sorts each actor's elements
			$unsorted_elements = [];
			$unsorted_elements = explode($delimiter, $rowE['elements']);
			$assoc_elements = [];
			for ($i = 0; $i < count($unsorted_elements); $i++)
			{
				if ($unsorted_elements[$i] == '') break;
				$assoc_elements[$unsorted_elements[$i]] = $elements_data[$unsorted_elements[$i]]['element_name'];
			}
			asort($assoc_elements);
		
			$sorted_elements = '';
			foreach ($assoc_elements as $key => $value) {
				if ($sorted_elements == '') $sorted_elements .= $key; else $sorted_elements.= $delimiter.$key;
			}
			
			$rowA['elements'][] = $sorted_elements;
			$rowA['dead'][] = $rowE['dead'];
			$time++;
		}
		
		$actor_data[] = $rowA; // merging both actor_id/actor_name and elements in the same array
	}

	// Loop of time slides
	for ($t = 0; $t < count($timeslide_data); $t++)
	{
		if ($sheet != 0) $objPHPExcel->createSheet($sheet);
		
		if (count($task) > 1) $objPHPExcel->setActiveSheetIndex($sheet)->setTitle(cleanstr(truncate('('.($t+1).') '.$project_name)));
		else $objPHPExcel->setActiveSheetIndex($sheet)->setTitle(cleanstr(truncate($timeslide_data[$t]['timeslide_name'])));
		$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue('A1', 'UN-CODE: Definitions of "'.$timeslide_data[$t]['timeslide_name'].'"');
		$objPHPExcel->setActiveSheetIndex($sheet)->mergeCells('A1:'.$alphas[count($actor_data)*2-1].'1');
		$objPHPExcel->setActiveSheetIndex($sheet)->getStyle('A1:'.$alphas[count($actor_data)*2-1].'1')->applyFromArray($align_middle);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		
		// Adding actor names
		for ($a = 0; $a < count($actor_data); $a++)
		{
			$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2].(2), $actor_data[$a]['actor_name']);
			$objPHPExcel->setActiveSheetIndex($sheet)->mergeCells($alphas[$a*2].'2:'.$alphas[$a*2+1].'2'); 
			$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2].'2:'.$alphas[$a*2+1].'2')->applyFromArray($align_middle);
			if ($actor_data[$a]['dead'][$t] == 0)
			{
				$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2].(3), 'Problem');
				$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2+1].(3), 'Solution');
			}
			else
			{
				$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2].(3), '[not present]');
				$objPHPExcel->setActiveSheetIndex($sheet)->mergeCells($alphas[$a*2].(3).':'.$alphas[$a*2+1].(3)); 
				$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2].(3).':'.$alphas[$a*2+1].(3))->applyFromArray($align_middle);
			}
		}
		
		// Loop of actors
		for ($a = 0; $a < count($actor_data); $a++)
		{
			$elements = explode($delimiter, $actor_data[$a]['elements'][$t]);
			$posY_P = 4; // Position of P-Def on Y grid
			$posY_S = 4; // Position of S-Def on Y grid
			
			// Loop of this actor's elements
			for ($e = 0; $e < count($elements); $e++)
			{
				if ($elements[$e] == '') break;
				if ($actor_data[$a]['dead'][$t] == 0)
				{
					if ($elements_data[$elements[$e]]['element_type'] == 'problem')
					{
						$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2].($posY_P), $elements_data[$elements[$e]]['element_name']);
						$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2].($posY_P))->applyFromArray($elements_data[$elements[$e]]['style']);
						$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2].($posY_P))->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); 
						$posY_P++;
					}
					elseif ($elements_data[$elements[$e]]['element_type'] == 'solution')
					{
						$objPHPExcel->setActiveSheetIndex($sheet)->setCellValue($alphas[$a*2+1].($posY_S), $elements_data[$elements[$e]]['element_name']);
						$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2+1].($posY_S))->applyFromArray($elements_data[$elements[$e]]['style']);
						$objPHPExcel->setActiveSheetIndex($sheet)->getStyle($alphas[$a*2+1].($posY_S))->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); 
						$posY_S++;
					}
				}
			}
		}
		
		$sheet++;
	}
	
	} // End of Task

	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="uncode_definitions_'.date("Y-m-d").'.xlsx"');
	
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);

	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	saveViaTempFile($objWriter);
		
	$db_link->close();
}

// Project Screen: Add a Source
function addSource()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
			
	if (isset($_FILES['source'])) {
		if($_FILES['source']['size'] > 0)
		{	
			$fileName = $_FILES['source']['name'];
			$tmpName  = $_FILES['source']['tmp_name'];
			$fileSize = $_FILES['source']['size'];
			$fileType = $_FILES['source']['type'];

			$fp      = fopen($tmpName, 'r');
			$content = fread($fp, filesize($tmpName));
			$content = addslashes($content);
			fclose($fp);

			if(!get_magic_quotes_gpc())
			{
				$fileName = addslashes($fileName);
			}

			$query = 'INSERT INTO `'.$project_id.'.sources` (timeslide_id, source_name, size, type, content ) VALUES ("'.$timeslide_id.'", "'.$fileName.'", "'.$fileSize.'", "'.$fileType.'", "'.$content.'")';
			$db_link->query($query) or mysql_fail($db_link);

			echo 'ok';
		}
		else 'File has size of 0';
	}
	else echo 'No file selected';
	
	$db_link->close();
}

// Project Screen: Download a Source
function downloadSource()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$source_id = $db_link->real_escape_string($_GET['source_id']);
	$query = 'SELECT * FROM `'.$project_id.'.sources` WHERE `source_id` = "'.$source_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$source_data = $result->fetch_assoc();

	header('Content-length: '.$source_data['size']);
	header('Content-type: '.$source_data['type']);
	header('Content-Disposition: attachment; filename="'.$source_data['source_name'].'"');
	echo $source_data['content'];
	
	$db_link->close();
}

// Project Screen: Delete a Source
function deleteSource()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$source_id = $db_link->real_escape_string($_GET['source_id']);
	
	$query = 'DELETE FROM `'.$project_id.'.sources` WHERE `source_id` = "'.$source_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	echo 'ok';
	$db_link->close();
}


// Overview: Create Project (= Lineage)
function createProject()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $standardX, $standardY, $standardZ;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);

	if (isset($_POST['study_id'])) $study_id = $db_link->real_escape_string($_POST['study_id']);
	if (isset($_POST['lineage_name'])) $project_name = $db_link->real_escape_string($_POST['lineage_name']);
	$project_name = str_replace(']', ')', $project_name);
	$project_name = str_replace('[', '(', $project_name);
	if (isset($_POST['lineage_description'])) $project_description = $db_link->real_escape_string($_POST['lineage_description']);
	if ($study_id == '' || $project_name == '' || $project_description == '') { echo 'Please fill out all form fields!'; exit; }
	$query = 'INSERT INTO projects (`uid`, `study_id`, `project_name`, `project_description`) VALUES ("'.$_SESSION['uid'].'", "'.$study_id.'","'.$project_name.'","'.$project_description.'")';
	$db_link->query($query) or mysql_fail($db_link);	
	
	$query = 'SELECT MAX(project_id) AS project_id FROM `projects`';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$project_data = $result->fetch_assoc();
	$project_id = $project_data['project_id'];
	
	$query = 'CREATE TABLE IF NOT EXISTS `'.$project_id.'.actors` (
  `actor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `actor_name` varchar(40) NOT NULL,
  `actor_information` text NOT NULL,
  PRIMARY KEY (`actor_id`),
  KEY `actor_id` (`actor_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$db_link->query($query) or mysql_fail($db_link);
	$query = "INSERT INTO `".$project_id.".actors` (`actor_id`, `actor_name`, `actor_information`) VALUES
	(1, '(new actor)', '')";
$db_link->query($query) or mysql_fail($db_link);
	$query = "CREATE TABLE IF NOT EXISTS `".$project_id.".timeslides` (
	  `timeslide_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `timeslide_name` varchar(40) NOT NULL,
	  `timeslide_information` text NOT NULL,
	  `start_date` int(10) NOT NULL,
	  `end_date` int(10) NOT NULL,
	  PRIMARY KEY (`timeslide_id`),
	  KEY `timeslide_id` (`timeslide_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
$db_link->query($query) or mysql_fail($db_link);
	$query = "INSERT INTO `".$project_id.".timeslides` (`timeslide_id`, `timeslide_name`, `timeslide_information`) VALUES
	(1, '(new field)', '')";
$db_link->query($query) or mysql_fail($db_link);
	$query = "CREATE TABLE IF NOT EXISTS `".$project_id.".xyz` (
  `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timeslide_id` int(10) unsigned NOT NULL,
  `actor_id` int(10) unsigned NOT NULL,
  `dead` tinyint(1) NOT NULL DEFAULT '0',
  `x` decimal(15,5) NOT NULL,
  `y` decimal(15,5) NOT NULL,
  `z` decimal(15,5) NOT NULL,
  `elements` text NOT NULL,
  `num_con` smallint(5) unsigned NOT NULL,
  `con` text NOT NULL,
  PRIMARY KEY (`entry_id`),
  KEY `timeslide_id` (`timeslide_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
$db_link->query($query) or mysql_fail($db_link);
	$query = "INSERT INTO `".$project_id.".xyz` (`entry_id`, `timeslide_id`, `actor_id`, `dead`, `x`, `y`, `z`) VALUES
	(1, 1, 1, 0, '".$standardX."', '".$standardY."', '".$standardZ."')";
$db_link->query($query) or mysql_fail($db_link);
	$query = "CREATE TABLE IF NOT EXISTS `".$project_id.".sources` (
  `source_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timeslide_id` int(10) unsigned NOT NULL,
  `source_name` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `size` int(11) NOT NULL,
  `content` mediumblob NOT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
	$db_link->query($query) or mysql_fail($db_link);
	$query = "CREATE TABLE IF NOT EXISTS `".$project_id.".elements` (
  `element_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `element_type` enum('problem','solution') NOT NULL,
  `element_name` varchar(250) NOT NULL,
  PRIMARY KEY (`element_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
	$db_link->query($query) or mysql_fail($db_link);
	
	echo 'ok';
	$db_link->close();
}

// Overview: Create Study
function createStudy()
{
	global $smarty, $db_host, $db_user, $db_password, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);

	if (isset($_POST['study_name'])) $study_name = $db_link->real_escape_string($_POST['study_name']);
	if (isset($_POST['study_description'])) $study_description = $db_link->real_escape_string($_POST['study_description']);
	if ($study_name == '' || $study_description == '') { echo 'Please fill out all form fields!'; exit; }
	$query = 'INSERT INTO studies (`uid`, `study_name`, `study_description`) VALUES ("'.$_SESSION['uid'].'", "'.$study_name.'", "'.$study_description.'")';
	$db_link->query($query) or mysql_fail($db_link);	
	
	echo 'ok';
	$db_link->close();
}

// Overview: Edit Study
function editStudy()
{
	global $smarty, $db_host, $db_user, $db_password, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);

	if (isset($_POST['estudy_id'])) $study_id = $db_link->real_escape_string($_POST['estudy_id']);
	if (isset($_POST['estudy_name'])) $study_name = $db_link->real_escape_string($_POST['estudy_name']);
	if (isset($_POST['estudy_description'])) $study_description = $db_link->real_escape_string($_POST['estudy_description']);
	if ($study_id == '' || $study_name == '' || $study_description == '') { echo 'Please fill out all form fields!'; exit; }
	$query = 'UPDATE studies SET `study_name` = "'.$study_name.'", `study_description` = "'.$study_description.'" WHERE `study_id` = "'.$study_id.'"';
	$db_link->query($query) or mysql_fail($db_link);	
	
	echo 'ok';
	$db_link->close();
}

// Overview: Edit Project (Lineage)
function editProject()
{
	global $smarty, $db_host, $db_user, $db_password, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);

	if (isset($_POST['elineage_id'])) $project_id = $db_link->real_escape_string($_POST['elineage_id']);
	if (isset($_POST['elineage_name'])) $project_name = $db_link->real_escape_string($_POST['elineage_name']);
	$project_name = str_replace(']', ')', $project_name);
	$project_name = str_replace('[', '(', $project_name);
	if (isset($_POST['elineage_description'])) $project_description = $db_link->real_escape_string($_POST['elineage_description']);
	if (isset($_POST['elineage_study_id'])) $study_id = $db_link->real_escape_string($_POST['elineage_study_id']);
	if ($project_id == '' || $project_name == '' || $project_description == '' || $study_id == '') { echo 'Please fill out all form fields!'; exit; }
	$query = 'UPDATE projects SET `project_name` = "'.$project_name.'", `project_description` = "'.$project_description.'", `study_id` = "'.$study_id.'" WHERE `project_id` = "'.$project_id.'"';
	$db_link->query($query) or mysql_fail($db_link);	
	
	echo 'ok';
	$db_link->close();
}

// Overview: Copy Project (Lineage)
function copyProject()
{
	global $smarty, $db_host, $db_user, $db_password, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	
	$query = 'SELECT * FROM projects WHERE `project_id` = "'.$project_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	$query = 'INSERT INTO projects (uid, study_id, project_name, project_description) VALUES ("'.$data['uid'].'", "'.$data['study_id'].'", "[Copy] '.$data['project_name'].'", "'.$data['project_description'].'")';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.$db_projectdb.'" AND TABLE_NAME = "projects"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	$new_project_id = $data['AUTO_INCREMENT']-1;
	
	$query = 'CREATE TABLE `'.$new_project_id.'.actors` LIKE `'.$project_id.'.actors`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'INSERT INTO `'.$new_project_id.'.actors` SELECT * FROM `'.$project_id.'.actors`';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'CREATE TABLE `'.$new_project_id.'.elements` LIKE `'.$project_id.'.elements`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'INSERT INTO `'.$new_project_id.'.elements` SELECT * FROM `'.$project_id.'.elements`';
	$db_link->query($query) or mysql_fail($db_link);

	$query = 'CREATE TABLE `'.$new_project_id.'.sources` LIKE `'.$project_id.'.sources`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'INSERT INTO `'.$new_project_id.'.sources` SELECT * FROM `'.$project_id.'.sources`';
	$db_link->query($query) or mysql_fail($db_link);

	$query = 'CREATE TABLE `'.$new_project_id.'.timeslides` LIKE `'.$project_id.'.timeslides`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'INSERT INTO `'.$new_project_id.'.timeslides` SELECT * FROM `'.$project_id.'.timeslides`';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'CREATE TABLE `'.$new_project_id.'.xyz` LIKE `'.$project_id.'.xyz`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'INSERT INTO `'.$new_project_id.'.xyz` SELECT * FROM `'.$project_id.'.xyz`';
	$db_link->query($query) or mysql_fail($db_link);
	
	echo 'ok';
	$db_link->close();
}

// Overview: Delete Project (Lineage)
function deleteProject()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);

	$query = 'DELETE FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	$query = 'DROP TABLE `'.$project_id.'.actors`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'DROP TABLE `'.$project_id.'.timeslides`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'DROP TABLE `'.$project_id.'.xyz`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'DROP TABLE `'.$project_id.'.sources`';
	$db_link->query($query) or mysql_fail($db_link);
	$query = 'DROP TABLE `'.$project_id.'.elements`';
	$db_link->query($query) or mysql_fail($db_link);
	
	echo 'ok';
	$db_link->close();
}

// Overview: Delete Study
function deleteStudy()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$study_id = $db_link->real_escape_string($_GET['study_id']);

	$query = 'DELETE FROM `studies` WHERE `study_id` = "'.$study_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	echo 'ok';
	$db_link->close();
}

// User Settings
function settings()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$user_link = new mysqli($db_host, $db_user, $db_password, $db_userdb);

	// Load settings
	$query = 'SELECT delimiter FROM users WHERE `uid` = "'.$_SESSION['uid'].'"';
	$result = $user_link->query($query) or mysql_fail($user_link);
	$settings_data = $result->fetch_assoc();
	$smarty->assign('delimiter', $settings_data['delimiter']);
	
	$db_link->close();
	$user_link->close();
	$smarty->display('settings.tpl');
}

// User Settings: Save settings
function saveSettings()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $reg_min_password;
	$user_link = new mysqli($db_host, $db_user, $db_password, $db_userdb);

	if (isset($_POST['delimiter'])) $delimiter = $user_link->real_escape_string($_POST['delimiter']);
	if (isset($_POST['oldpw'])) { if ($_POST['oldpw'] != '') $oldpw = hash('sha512', $_POST['oldpw']); else $oldpw = ''; } else $oldpw = '';
	if (isset($_POST['newpw'])) $newpw = $_POST['newpw']; else $newpw = '';
	
	// If no passwords are given, password changing process is omitted and other changes are saved directly
	if ($oldpw == '' && $newpw == '')
	{
		$query = 'UPDATE users SET `delimiter` = "'.$delimiter.'" WHERE `uid` = "'.$_SESSION['uid'].'"';
		if ($_SESSION['uid'] != 0) // No update for the Demo Account
		{
		    $result = $user_link->query($query) or mysql_fail($user_link);
		    $_SESSION['delimiter'] = $delimiter;
		}
		echo 'ok';
	}
	else
	{
		if (strlen($newpw) < $reg_min_password) echo 'Your password must consist of at least '.$reg_min_password.' characters.';
		else
		{
			// Checks for password
			$query = 'SELECT password FROM users WHERE `uid` = "'.$_SESSION['uid'].'"';
			$result = $user_link->query($query) or mysql_fail($user_link);
			$data = $result->fetch_assoc();
			if ($data['password'] != $oldpw) echo 'Please re-check the old password you entered.';
			else
				{
					// Save settings
					if ($_SESSION['uid'] != 0)
					{
    				    $newpw = hash('sha512', $newpw);
    					$query = 'UPDATE `users` SET `password` = "'.$newpw.'" WHERE `uid` = "'.$_SESSION['uid'].'"';
    					$user_link->query($query) or mysql_fail($db_link);			
    					$query = 'UPDATE users SET `delimiter` = "'.$delimiter.'" WHERE `uid` = "'.$_SESSION['uid'].'"';
    					$result = $user_link->query($query) or mysql_fail($user_link);
    					$_SESSION['delimiter'] = $delimiter;
					}
					echo 'ok';
			}
		}
	}

	$user_link->close();
}

// Logout process
function logout()
{
	global $smarty;
	
	session_destroy();
	start();
}


// 3D-Visualization
function visualize()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	if (isset($_GET['timeslide_id'])) $timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);

	// General query for navigation
	$query = 'SELECT uid, study_id FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	if ($data['uid'] != $_SESSION['uid']) exit;
	$query = 'SELECT project_id, project_name FROM `projects` WHERE `study_id` = "'.$data['study_id'].'" ORDER BY `project_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc()) { $projects[] = $row; }
	$smarty->assign('projects', $projects);
	
	// Query for this project
	$query = "SELECT * FROM projects WHERE project_id = '$project_id'";
	$result = $db_link->query($query) or mysql_fail($db_link);
	$row = $result->fetch_assoc();
	$smarty->assign('thisproject', $row);
	
	// Time Slides
	$query = "SELECT timeslide_id, timeslide_name FROM `".$project_id.".timeslides` ORDER BY `start_date` ASC";
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc())
	{
		$row['timeslide_name'] = (strlen($row['timeslide_name']) > 20) ? substr($row['timeslide_name'], 0, 20) . '...' : $row['timeslide_name'];
		$timeslides[] = $row;
	}
	$smarty->assign('timeslides', $timeslides);
	
	// Actors
	$query = "SELECT actor_id, actor_name FROM `".$project_id.".actors` ORDER BY `actor_name` ASC";
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc()) { $actors[] = $row; }
	$smarty->assign('actors', $actors);
	
	
	if (isset($timeslide_id)) $smarty->assign('timeslide_id', $timeslide_id);
	$smarty->display('visualize.tpl');
	$db_link->close();
}

// 3D-Visualization: Transmit Data to WebGL
function visualizeData()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$num_timeslides = $db_link->real_escape_string($_GET['num_timeslides']);
	$num_actors = $db_link->real_escape_string($_GET['num_actors']);
	
	for ($i = 1; $i <= $num_timeslides; $i++)
	{
		$timeslides_id[] = $db_link->real_escape_string($_GET['timeslide'.$i.'_id']);
	}
	
	for ($i = 1; $i <= $num_actors; $i++)
	{
		$actors_id[] = $db_link->real_escape_string($_GET['actor'.$i.'_id']);
	}

	// Prepare outer loop
	$query = 'SELECT project_name FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$resultP = $db_link->query($query) or mysql_fail($db_link);
	$project_data = $resultP->fetch_assoc();
	$project_name = $project_data['project_name'];
	$vis_data = [];
	
	// First add Time Slide names
	$type_query = '';
	for ($i = 0; $i < $num_timeslides; $i++)
	{
		if ($type_query == '') $type_query = '`timeslide_id` = "'.$timeslides_id[$i].'"';
		else $type_query .= ' OR `timeslide_id` = "'.$timeslides_id[$i].'"';
	}

	$query = 'SELECT timeslide_id, timeslide_name FROM `'.$project_id.'.timeslides` WHERE '.$type_query.' ORDER BY `start_date` ASC';
	$resultT = $db_link->query($query) or mysql_fail($db_link);
	$timeslide_data = [];
	while ($rowT = $resultT->fetch_assoc())
	{
		$vis_data[] = $rowT['timeslide_id'];
		$vis_data[] = $rowT['timeslide_name'];
		$timeslide_data[] = $rowT['timeslide_id'];
	}

	// Get the max XYZ data
	for ($u = 0; $u < count($timeslide_data); $u++)
	{
		// if 'psd_case' is set, then psd is calculated relative to whole case, else it is calculated as usual relative to the current time slide
		if (isset($_GET['psd_case']))
		{
			$queryMAX = 'SELECT MAX(y) AS y, MAX(z) AS z FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_data[$u].'" AND `dead` = "0"';
			$resultMAX = $db_link->query($queryMAX) or mysql_fail($db_link);
			$dataMAX[] = $resultMAX->fetch_assoc();
			$queryMAX = 'SELECT MAX(x) AS x FROM `'.$project_id.'.xyz` WHERE `dead` = "0"';
			$resultMAX = $db_link->query($queryMAX) or mysql_fail($db_link);
			$xMAX = $resultMAX->fetch_assoc();
			$dataMAX[count($dataMAX)-1]['x'] = $xMAX['x'];
		}
		else
		{
			$queryMAX = 'SELECT MAX(x) AS x, MAX(y) AS y, MAX(z) AS z FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_data[$u].'" AND `dead` = "0"';
			$resultMAX = $db_link->query($queryMAX) or mysql_fail($db_link);
			$dataMAX[] = $resultMAX->fetch_assoc();
		}
	}

	// Create query so that xyz-data is in the same order as the timeslide-data
	$order_query = '';
	for ($q = 0; $q < count($timeslide_data); $q++)
	{
		if ($q == 0) $order_query .= $timeslide_data[0]; else $order_query .= ', '.$timeslide_data[$q];
	}
	
	// Loop of actors
	$type_query_actor = '';
	for ($i = 0; $i < $num_actors; $i++)
	{
		if ($type_query_actor == '') $type_query_actor = '`actor_id` = "'.$actors_id[$i].'"';
		else $type_query_actor .= ' OR `actor_id` = "'.$actors_id[$i].'"';
	}

	$query = 'SELECT actor_id, actor_name FROM `'.$project_id.'.actors` WHERE '.$type_query_actor.' ORDER BY actor_name ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$act_no = 2;
	while ($actor_data = $result->fetch_assoc())
	{
	
		// Get this actor's XYZ data
		$query = 'SELECT TRIM(TRAILING "." FROM TRIM(TRAILING "0" from x)) AS x, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from y)) AS y, TRIM(TRAILING "." FROM TRIM(TRAILING "0" from z)) AS z, dead FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$actor_data['actor_id'].'" AND ( '.$type_query.' ) ORDER BY FIELD (timeslide_id, '.$order_query.' ) ASC';
		$resultXYZ = $db_link->query($query) or mysql_fail($db_link);
		
		$vis_data[] = $actor_data['actor_name'];

		// Loop of time slides
		for ($i = 0; $i < count($timeslide_data); $i++)
		{
			$row = $resultXYZ->fetch_assoc();
			if ($dataMAX[$i]['x'] == 0) $x = 0; else $x = $row['x'] / $dataMAX[$i]['x'];
			if ($dataMAX[$i]['y'] == 0) $y = 0; else $y = $row['y'] / $dataMAX[$i]['y'];
			if ($dataMAX[$i]['z'] == 0) $z = 0; else $z = $row['z']; // Fitness score is displayed absolute. If you want the Fitness displayed relative like the other scores, use: / $dataMAX[$i]['z'];
			if ($row['dead'] == 1) array_push($vis_data, 'not present', 'not present', 'not present'); else array_push($vis_data, round($x, 5), round($y, 5), round($z, 5));
		}
		$act_no++;
	}

	echo implode($delimiter, $vis_data);
	
	$db_link->close();
}


// Connections table overview
function connections()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);

	// General query for navigation
	$query = 'SELECT uid, study_id FROM `projects` WHERE `project_id` = "'.$project_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	if ($data['uid'] != $_SESSION['uid']) exit;
	$query = 'SELECT project_id, project_name FROM `projects` WHERE `study_id` = "'.$data['study_id'].'" ORDER BY `project_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	while ($row = $result->fetch_assoc()) { $projects[] = $row; }
	$smarty->assign('projects', $projects);
	
	// Query for this project
	$query = "SELECT * FROM projects WHERE project_id = '$project_id'";
	$result = $db_link->query($query) or mysql_fail($db_link);
	$row = $result->fetch_assoc();
	$smarty->assign('thisproject', $row);
	
	// Get Time Slice name
	$query = 'SELECT timeslide_name FROM `'.$project_id.'.timeslides` WHERE `timeslide_id` = "'.$timeslide_id.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$timeslide_data = $result->fetch_assoc();
	$smarty->assign('timeslide_id', $timeslide_id);
	$smarty->assign('timeslide_name', $timeslide_data['timeslide_name']);
	
	// Get Actor IDs, Names
	$query = 'SELECT actor_id, actor_name FROM `'.$project_id.'.actors` ORDER BY `actor_name` ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$actor_data = [];
	while ($row = $result->fetch_assoc())
	{
		$actor_data[] = $row;
	}

	// Synchronize order of actor/xyz queries
	$order_query = '';
	foreach ($actor_data as $key => $value)
	{
		if ($order_query == '') $order_query .= $value['actor_id']; else $order_query .= ', '.$value['actor_id'];
	}
	
	// Get connection data
	$query = 'SELECT actor_id, dead, con FROM `'.$project_id.'.xyz` WHERE `timeslide_id` = "'.$timeslide_id.'" ORDER BY FIELD (actor_id, '.$order_query.' ) ASC';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$counter = 0;
	while ($row = $result->fetch_assoc())
	{
		$actor_data[$counter]['dead'] = $row['dead'];
	
		$con_source = explode($delimiter, $row['con']);
		foreach ($con_source as $row2) { $con[$row['actor_id'].'^'.$row2] = true; }
		
		$counter++;
	}
	
	$smarty->assign('actors', $actor_data);
	$smarty->assign('con', $con);
	
	$smarty->display('connections.tpl');
	$db_link->close();
}

// Save for Connections Function
function connections_save()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb, $delimiter;
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
	$project_id = $db_link->real_escape_string($_GET['project_id']);
	$timeslide_id = $db_link->real_escape_string($_GET['timeslide_id']);
	
	// First empty all actor's connections. This is needed because of how check boxes behave.
	$query = 'UPDATE `'.$project_id.'.xyz` SET `con` = "", `num_con` = "0" WHERE `timeslide_id` = "'.$timeslide_id.'"';
	$db_link->query($query) or mysql_fail($db_link);
	
	if (isset($_POST['box']))
	{
		$con = $_POST['box'];
		foreach ($con as $row)
		{
			$exp = explode($delimiter, $row);
			
			// Get existing
			$query = 'SELECT con FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$exp[0].'" AND `timeslide_id` = "'.$timeslide_id.'"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$data = $result->fetch_assoc();
			$existing = explode($delimiter, $data['con']);
			if (!array_search($exp[1], $existing)) $existing[] = $exp[1];
			$existing = array_unique($existing);
			$found = false;
			for ($i = 0; $i < count($existing); $i++)
			{
				if ($existing[$i] == '')
				{
					unset($existing[$i]);
					$found = true;
				}
			}
			if ($found == true) $existing = array_values($existing);
			$query = 'UPDATE `'.$project_id.'.xyz` SET `con` = "'.implode($delimiter, $existing).'" WHERE `actor_id` = "'.$exp[0].'" AND `timeslide_id` = "'.$timeslide_id.'"';
			$db_link->query($query) or mysql_fail($db_link);
			
			$query = 'SELECT con FROM `'.$project_id.'.xyz` WHERE `actor_id` = "'.$exp[1].'" AND `timeslide_id` = "'.$timeslide_id.'"';
			$result = $db_link->query($query) or mysql_fail($db_link);
			$data = $result->fetch_assoc();
			$existing = explode($delimiter, $data['con']);
			if (!array_search($exp[0], $existing)) $existing[] = $exp[0];
			$existing = array_unique($existing);
			$found = false;
			for ($i = 0; $i < count($existing); $i++)
			{
				if ($existing[$i] == '')
				{
					unset($existing[$i]);
					$found = true;
				}
			}
			if ($found == true) $existing = array_values($existing);		
			$query = 'UPDATE `'.$project_id.'.xyz` SET `con` = "'.implode($delimiter, $existing).'" WHERE `actor_id` = "'.$exp[1].'" AND `timeslide_id` = "'.$timeslide_id.'"';
			$db_link->query($query) or mysql_fail($db_link);
		}
	}
	
	recalculate($project_id, $timeslide_id, null);
	
	echo 'ok';
	$db_link->close();
}




// --- Public Area: Functions ---

// Starting site
function start()
{
	global $smarty;

	$smarty->display('start.tpl');
}

// Login process
function login()
{
	global $smarty, $db_host, $db_user, $db_password, $db_userdb, $db_projectdb;
	
	$db_link = new mysqli($db_host, $db_user, $db_password, $db_userdb);
	$name = $db_link->real_escape_string($_POST['login_name']);
	$password_encrypted = hash('sha512', $db_link->real_escape_string($_POST['login_password']));
	$query = 'SELECT * FROM users WHERE name = "'.$name.'" AND password = "'.$password_encrypted.'"';
	$result = $db_link->query($query) or mysql_fail($db_link);
	$data = $result->fetch_assoc();
	if ($data != null)
	{
		$_SESSION['uid'] = $data['uid'];
		$_SESSION['name'] = $data['name'];
		$_SESSION['email'] = $data['email'];
		$_SESSION['delimiter'] = $data['delimiter'];
		echo 'ok';
		$db_link->close();
		if ($data['uid'] == 0) // Demo Account? Then reset the account.
		{
		    $db_link = new mysqli($db_host, $db_user, $db_password, $db_projectdb);
		    // Reset projects
		    $query = 'DELETE FROM `projects` WHERE `uid` = "0"';
		    $db_link->query($query) or mysql_fail($db_link);
		    $query = 'INSERT INTO `projects` (project_id, uid, study_id, project_name, project_description) VALUES ("0", "0", "0", "Stuttgart 21", "Stuttgart 21 is a railway infrastructure megaproject in Germany. Its goal is to increase railway '.
		              'connection quality in the whole Stuttgart region and to the airport by replacing Stuttgart Central Station with a subsurface replacement building. '.
		              'The project, first announced in 1994 and under construction since 2010, has undergone an erratic completion process, facing heavy political '.
		              'and civil resistance as well as major delays and cost increases. Planned opening has shifted from 2017 to 2024 with a cost increase from 2.5 billion Euro (1995) to 4 billion Euro (2010) to 7.6 billion Euro (2017).")';
		    $db_link->query($query) or mysql_fail($db_link);
		    $query = 'UPDATE `projects` SET `project_id` = "0" WHERE `uid` = "0"';
		    $db_link->query($query) or mysql_fail($db_link);
		    
		    // Reset studies
		    $query = 'DELETE FROM `studies` WHERE `uid` = "0"';
		    $db_link->query($query) or mysql_fail($db_link);
		    $query = 'INSERT INTO `studies` (study_id, uid, study_name, study_description) VALUES ("0", "0", "Demonstration", "This is a simplified version of the Stuttgart 21 case study for demonstrational purposes. '.
		  		     'Feel free to play around! Please note, that the Demo Account will be reset with every login, so no changes here will be permanent.")';
		    $db_link->query($query) or mysql_fail($db_link);
		    $query = 'UPDATE `studies` SET `study_id` = "0" WHERE `uid` = "0"';
		    $db_link->query($query) or mysql_fail($db_link);
		    $tableNames = ['actors', 'elements', 'sources', 'timeslides', 'xyz'];
		    for ($i = 0; $i < count($tableNames); $i++)
		    {
		        $query = 'DROP TABLE IF EXISTS `0.'.$tableNames[$i].'`';
		        $db_link->query($query) or mysql_fail($db_link);
    		    $query = 'CREATE TABLE `0.'.$tableNames[$i].'` LIKE `demo.'.$tableNames[$i].'`';
    		    $db_link->query($query) or mysql_fail($db_link);
    		    $query = 'INSERT `0.'.$tableNames[$i].'` SELECT * FROM `demo.'.$tableNames[$i].'`';
    		    $db_link->query($query) or mysql_fail($db_link);
		    }
		    $db_link->close();
		}
	}
	else echo '-1';
}

// Registration process
function register()
{
	global $smarty, $reg_min_name, $reg_min_email, $reg_min_password, $db_host, $db_user, $db_password, $db_userdb, $standard_csv_delimiter;
	
	// Checks for invalid information
	if (($_POST['register_name'] == '') || ($_POST['register_email'] == '') || ($_POST['register_password'] == '')) { start(); exit; }
	elseif (strlen($_POST['register_name']) < $reg_min_name) echo '-1';
	elseif ((strlen($_POST['register_email']) < $reg_min_email) || (!strstr($_POST['register_email'], '@')) || (!strstr($_POST['register_email'], '.'))) echo 'reg_email_invalid';
	elseif (strlen($_POST['register_password']) < $reg_min_password) echo '-1';
	else
	{
		$db_link = new mysqli($db_host, $db_user, $db_password, $db_userdb);
		$name = $db_link->real_escape_string($_POST['register_name']);
		$email = $db_link->real_escape_string($_POST['register_email']);
		$password =  $db_link->real_escape_string($_POST['register_password']);

		// Checks for duplicate name
		$query = "SELECT name FROM users WHERE name = '$name'";
		$result = $db_link->query($query) or mysql_fail($db_link);
		if(mysqli_fetch_object($result)) echo 'reg_name_exists';
		
		// Checks for duplicate email
		else
		{
			$query = "SELECT email FROM users WHERE email = '$email'";
			$result = $db_link->query($query) or mysql_fail($db_link);
			if (mysqli_fetch_object($result)) echo 'reg_email_exists';
		
			// Actual registration process
			else
			{
				$password_encrypted = hash('sha512', $password);
				$query = "INSERT INTO users (name,email,password,delimiter) VALUES ('$name', '$email', '$password_encrypted','$standard_csv_delimiter')";
				$db_link->query($query) or mysql_fail($db_link);
				echo 'ok';
			}
		}
		$db_link->close();
	}
}

?>