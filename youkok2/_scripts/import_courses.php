<?php
$url = 'https://www.ntnu.no/web/studier/emnesok?p_p_id=courselistportlet_WAR_courselistportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=fetch-courselist-as-json&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2';

$pageNo = 1;
$courses = [];
$lastCourseCodeSeen = null;

while(true) {
    echo 'Fetching page: ' . $pageNo . PHP_EOL;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "semester=2019&gjovik=false&trondheim=false&alesund=false&faculty=-1&institute=-1&multimedia=false&english=false&phd=false&courseAutumn=false&courseSpring=false&courseSummer=false&searchQueryString=&pageNo=" . $pageNo . "&season=autumn&sortOrder=%2Btitle&year=");

    $output = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($output, true);
    if (!isset($data['courses']) or count($data['courses']) === 0) {
        break;
    }

    $lastCourse = end($data['courses']);
    if ($lastCourse['courseCode'] === $lastCourseCodeSeen) {
        break;
    }
    else {
        $lastCourseCodeSeen = $lastCourse['courseCode'];
        $pageNo++;
    }

    foreach ($data['courses'] as $course) {
        $courses[] = [
            'code' => $course['courseCode'],
            'name' => $course['courseName']
        ];
    }
}

file_put_contents('debug_output.json', json_encode($courses));
