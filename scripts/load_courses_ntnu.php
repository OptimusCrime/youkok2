<?php
// Set headers
header('Content-Type: text/html; charset=utf-8');

// Includes
require_once $base_path . '../local.php';

// Variables
$base_path = dirname(dirname(__FILE__));
$clean = array();
$page = 1;
$db = null;
$to_json = array();
$log = array();

// Method for cleaning urls
function url_friendly($s) {
	$s = strtolower($s);
	$s = str_replace(array('Æ', 'Ø', 'Å'), array('ae', 'o', 'aa'), $s);
	$s = str_replace(array('æ', 'ø', 'å'), array('ae', 'o', 'aa'), $s);
	return $s;
}

// Connect to database
try {
    $db  = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $e) {
    $db  = null;
}

// Authenticate if database-connection was successful
if ($db) {
	// Fetch all!
	while (true) {
		// Load
		$file = file_get_contents('http://www.ntnu.no/web/studier/emnesok?p_p_id=courselistportlet_WAR_courselistportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=fetch-courselist-as-json&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&semester=2013&faculty=-1&institute=-1&multimedia=0&english=0&phd=0&courseAutumn=0&courseSpring=0&courseSummer=0&searchQueryString=&pageNo=' . $page . '&season=spring&sortOrder=%2Btitle&year=');
		$json_content = json_decode($file, true);

		// Clean
		foreach ($json_content['courses'] as $v) {
			$clean_url_path = url_friendly($v['courseCode']);
			$clean[] = array('code' => $v['courseCode'], 'name' => $v['courseName'], 'url_friendly' => $clean_url_path, 'directory' => $clean_url_path);
		}

		// Loop every single course
		foreach ($clean as $v) {
			// Check if course is in database
			$check_current_course = "SELECT id
	        FROM course 
	        WHERE code = :code
	        LIMIT 1";
	        
	        $check_current_course_query = $db->prepare($check_current_course);
	        $check_current_course_query->execute(array(':code' => $v['code']));
	        $row = $check_current_course_query->fetch(PDO::FETCH_ASSOC);

	        // Check if exists
	        if (isset($row['id'])) {
	        	$log[] = '<span style="color: red;"><b>' . $v['code'] . '</b> exists in the course table. Not created!</span>';
	        }
	        else {
	        	// Logging
	        	$log[] = '<span style="color: green;"><b>' . $v['code'] . '</b> was not found in the course table.</span>';

	        	// Check if url-friendly or name exists
	        	$check_current_course2 = "SELECT id
		        FROM archive 
		        WHERE (
		        	name = :name
		        	OR url_friendly = :url_friendly
		        )
				AND parent = 1
		        LIMIT 1";
		        
		        $check_current_course2_query = $db->prepare($check_current_course2);
		        $check_current_course2_query->execute(array(':name' => $v['code'], ':url_friendly' => $v['url_friendly']));
		        $row2 = $check_current_course2_query->fetch(PDO::FETCH_ASSOC);

		        // Check if exists
		        if (isset($row2['id'])) {
		        	$log[] = '<span style="color: red;"><b>' . $v['code'] . '</b> exists in the archive table. Not created!</span>';
		        }
		        else {
		        	$log[] = '<span style="color: green;"><b>' . $v['code'] . '</b> was not found in the archive table.</span>';
		        	
		        	// Check if the directory exists
		        	$directory_check = $base_path . FILE_ROOT . '/' . $v['directory'];
		        	if (is_dir($directory_check)) {
		        		$log[] = '<span style="color: red;"><b>' . $directory_check . '</b> directory exists in the file system. Not created!</span>';
		        	}
		        	else {
		        		$log[] = '<span style="color: green;"><b>' . $directory_check . '</b> directory does not exist in the file system.</span>';
		        		
		        		// Insert course
			            $insert_course = "INSERT INTO course (code, name)
			            VALUES (:code, :name)";
			            
			            $insert_course_query = $db->prepare($insert_course);
			            $insert_course_query->execute(array(':code' => $v['code'], ':name' => $v['name']));
			            
			            // Get the course-id
			            $course_id = $db->lastInsertId();

			            // Build empty archive
			            $insert_archive = "INSERT INTO archive (name, url_friendly, parent, course, location, is_directory)
			            VALUES (:name, :url_friendly, :parent, :course, :location, :is_directory)";
			            
			            $insert_archive_query = $db->prepare($insert_archive);
			            $insert_archive_query->execute(array(':name' => $v['code'], ':url_friendly' => $v['url_friendly'], ':parent' => 1, ':course' => $course_id, ':location' => $v['directory'], ':is_directory' => 1));
		        		
			            // Create directory
			            mkdir($directory_check);

			            // Log successful message :)
		        		$log[] = '<span style="color: green;">Everything went better than expected! (I think)</span>';
		        	}
		        }
	        }

	        // Append ruler
	        $log[] = '<hr />';
		}

		// Check if more results
		if (count($clean) == 100) {
			// More results, increase page
			$page++;

			// Clear array
			$clean = array();
		}
		else {
			// No more results, break all :)
			break;
		}
	}

	// Generate search file
    $get_all_courses = "SELECT c.code, c.name, a.url_friendly
    FROM course c
    LEFT JOIN archive AS a ON c.id = a.course
    ORDER BY c.code ASC";
    
    $get_all_courses_query = $db->prepare($get_all_courses);
    $get_all_courses_query->execute();
    while ($row = $get_all_courses_query->fetch(PDO::FETCH_ASSOC)) {
    	$to_json[] = array('course' => $row['code'] . ' - ' . $row['name'], 'url' => $row['url_friendly']);
    }

    // Put content to file
    file_put_contents($base_path . '/processor/search/courses.json', json_encode($to_json));

	// Clean db-connection
	$db = null;

	// Feedback
	echo '<h2>Log</h2>';
	echo implode('<br />', $log);
}
else {
	// Return error
	echo '<h2>Error</h2>';
	die('Could not connect to database!');
}
?>