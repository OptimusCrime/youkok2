<?php
/*
 * File: otherController.php
 * Holds: The OtherController-class for misc-stuff
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class OtherController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Checking what to call
        if ($_GET['q'] == 'nytt-fag') {
            // New course
            $this->newCourse();
        }
    }
    
    //
    // Create new course
    //
    
    private function newCourse() {
        if (!isset($_POST['send'])) {
            $this->template->display('other_new_course.tpl');
        }
        else {
            // Todo, security! Check for course that already exists
            // Insert course
            $insert_course = "INSERT INTO courses (code, name)
            VALUES (:code, :name)";
            
            $insert_course_query = $this->db->prepare($insert_course);
            $insert_course_query->execute(array(':code' => strtoupper($_POST['code']), ':name' => $_POST['course']));
            
            // Get the course-id
            $course_id = $this->db->lastInsertId();
            
            // Build empty archive
            $insert_archive = "INSERT INTO archive (name, url_friendly, parent, course, location, is_directory)
            VALUES (:name, :url_friendly, :parent, :course, :location, :is_directory)";
            
            $insert_archive_query = $this->db->prepare($insert_archive);
            $insert_archive_query->execute(array(':name' => $_POST['code'], ':url_friendly' => $_POST['course'], ':parent' => 1, ':course' => $course_id, ':location' => $_POST['course'], ':is_directory' => 1));
        }
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>