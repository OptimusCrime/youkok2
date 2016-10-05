<?php
namespace Youkok2\Models\Controllers;

use Youkok2\Models\Download;
use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\TemplateHelper;

class ElementController extends BaseController
{
    
    public static $cacheKey = 'i';

    private $parent;
    private $rootParent;
    private $fullUrl;
    private $parents;
    private $aliasFor;

    private $query;
    private $loadFlagCount;
    private $loadIfRemoved;
    private $flagCount;
    private $downloadCount;
    private $ownerId;
    private $ownerUsername;
    private $flags;
    private $cache;
    private $children;
    
    public static $file = 0;
    public static $dir = 1;
    
    public static $accumulated = 0;
    public static $single = 1;
    
    public static $timeIntervals = [
        'mysql' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.pending = 0 AND a.deleted = 0',
        ],
        'sqlite' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= datetime("now", "-1 day") AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= datetime("now", "-7 days")  AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= datetime("now", "-1 month")  AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= datetime("now", "-1 year")  AND a.pending = 0 AND a.deleted = 0',
        ]
    ];
    
    public function __construct($model) {
        parent::__construct($this, $model);

        $this->parents = null;
        $this->flagCount = null;
        $this->downloadCount = [0 => null, 1 => null, 2 => null, 3 => null];
        $this->ownerId = null;
        $this->ownerUsername = null;
        $this->flags = null;
        $this->cache = true;
        $this->children = null;
    }
    
    public function createByUrl($url) {
        $url_pieces_temp = explode('/', $url);

        $url_pieces = [];
        foreach ($url_pieces_temp as $k => $v) {
            if ($v != 'last-ned' and $v != 'redirect' and strlen($v) > 0) {
                $url_pieces[] = $v;
            }
        }
        
        $num_pieces = count($url_pieces);
        
        if ($num_pieces > 0) {
            $temp_parent = null;
            
            foreach ($url_pieces as $k => $url_piece_single) {
                $get_reverse_url  = "SELECT id, alias" . PHP_EOL;
                $get_reverse_url .= "FROM archive " . PHP_EOL;
                
                if ($temp_parent === null) {
                    $get_reverse_url .= "WHERE parent IS NULL" . PHP_EOL;
                    $reverse_arr = [':url_friendly' => $url_piece_single];
                }
                else {
                    $get_reverse_url .= "WHERE parent = :parent" . PHP_EOL;
                    $reverse_arr = [':parent' => $temp_parent,
                        ':url_friendly' => $url_piece_single];
                }
                
                $get_reverse_url .= "AND url_friendly = :url_friendly" . PHP_EOL;
                $get_reverse_url .= "AND pending = 0" . PHP_EOL;
                $get_reverse_url .= "AND deleted = 0";
                
                $get_reverse_url_query = Database::$db->prepare($get_reverse_url);
                $get_reverse_url_query->execute($reverse_arr);
                $row = $get_reverse_url_query->fetch(\PDO::FETCH_ASSOC);
                
                if (isset($row['id'])) {
                    if ($k == ($num_pieces - 1)) {
                        $element_id = $row['id'];
                        
                        if ($temp_parent == null and $row['alias'] !== null) {
                            $element_id = $row['alias'];
                        }
                        
                        $this->createById($element_id);
                    }
                    else {
                        if ($temp_parent == null and $row['alias'] !== null) {
                            $temp_parent = $row['alias'];
                        }
                        else {
                            $temp_parent = $row['id'];
                        }
                        
                        $temp_item = Element::get($temp_parent);
                    }
                }
            }
        }
    }

    public function wasFound() {
        if ($this->model->getId() != null and is_numeric($this->model->getId())) {
            return true;
        }

        return false;
    }
    
    public function getChildren() {
        if ($this->children === null) {
            $this->children = [];

            $get_children_ids  = "SELECT id" . PHP_EOL;
            $get_children_ids .= "FROM archive" . PHP_EOL;
            $get_children_ids .= "WHERE parent = :parent" . PHP_EOL;
            $get_children_ids .= "AND pending = 0" . PHP_EOL;
            $get_children_ids .= "AND deleted = 0" . PHP_EOL;
            $get_children_ids .= "ORDER BY directory DESC, name ASC";
            
            $get_children_ids_query = Database::$db->prepare($get_children_ids);
            $get_children_ids_query->execute([':parent' => $this->model->getId()]);
            while ($row = $get_children_ids_query->fetch(\PDO::FETCH_ASSOC)) {
                $this->children[] = Element::get($row['id']);
            }
        }

        return $this->children;
    }
    
    public function getDownloadCount($delta) {
        $index = 0;
        foreach (self::$timeIntervals[DATABASE_ADAPTER] as $k => $v) {
            if ($k == $delta) {
                $index = $k;
                break;
            }
        }
        
        if ($this->downloadCount[$index] == null) {
            $get_download_count  = "SELECT COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
            $get_download_count .= "FROM download d" . PHP_EOL;
            $get_download_count .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
            $get_download_count .= self::$timeIntervals[DATABASE_ADAPTER][$index] . PHP_EOL;
            $get_download_count .= "AND d.file = :file";
            
            $get_download_count_query = Database::$db->prepare($get_download_count);
            $get_download_count_query->execute([':file' => $this->model->getId()]);
            $row = $get_download_count_query->fetch(\PDO::FETCH_ASSOC);

            $this->downloadCount[$index] = $row['downloaded_times'];
        }

        return $this->downloadCount[$index];
    }
    
    public function setDownloadCount($delta, $value) {
        $this->downloadCount[$delta] = $value;
    }

    public function addDownload($me) {
        $download = new Download();
        $download->setFile($this->model->getId());
        $download->setIp($_SERVER['REMOTE_ADDR']);
        $download->setAgent($_SERVER['HTTP_USER_AGENT']);

        if ($me != null && $me->isLoggedIn()) {
            $download->setUser($me->getId());
        }

        $download->save();
    }

    public function loadFlags() {
        $get_all_flags  = "SELECT *" . PHP_EOL;
        $get_all_flags .= "FROM flag" . PHP_EOL;
        $get_all_flags .= "WHERE file = :file" . PHP_EOL;
        $get_all_flags .= "AND active = 1";
        
        $get_all_flags_query = $this->controller->db->prepare($get_all_flags);
        $get_all_flags_query->execute([':file' => $this->id]);
        while ($row = $get_all_flags_query->fetch(PDO::FETCH_ASSOC)) {
            $flag = new Flag($this->controller);

            $flag->setAll($row);

            $this->flags[] = $flag;
        }

        $this->flagCount = count($this->flags);

        if ($this->cache) {
            $this->controller->cacheManager->setCache($this->id, 'i', $this->cacheFormat());
        }
    }

    public function getFlagCount() {
        return 1;
        if ($this->flagCount === null) {
            $this->loadFlags();
        }

        return $this->flagCount;
    }

    public function getFlags() {
        if ($this->flags === null) {
            $this->flags = [];
            $this->loadFlags();
        }
        
        return $this->flags;
    }
    
    public function getOwnerId() {
        if ($this->ownerId == null) {
            $this->getOwnerUsername();
        }
        
        return $this->ownerId;
    }
    
    public function getOwnerUsername() {
        if ($this->ownerUsername == null) {
            $get_owner  = "SELECT u.id, u.nick" . PHP_EOL;
            $get_owner .= "FROM user AS u" . PHP_EOL;
            $get_owner .= "LEFT JOIN flag AS f ON f.user = u.id" . PHP_EOL;
            $get_owner .= "WHERE f.file = :file" . PHP_EOL;
            $get_owner .= "AND f.type = 0" . PHP_EOL;
            $get_owner .= "LIMIT 1";
            
            $get_owner_query = $this->controller->db->prepare($get_owner);
            $get_owner_query->execute([':file' => $this->id]);
            $row = $get_owner_query->fetch(PDO::FETCH_ASSOC);
            
            if (isset($row['id'])) {
                $this->ownerId = $row['id'];
                
                if (!isset($row['nick']) or $row['nick'] == '') {
                    $this->ownerUsername = '<em>Anonym</em>';
                }
                else {
                    $this->ownerUsername = $row['nick'];
                }
            }
        }
        
        return $this->ownerUsername;
    }
    
    public function getGraphData($type = null) {
        if ($type === null) {
            $type = 0;
        }
        
        $output = [];
        $previous_num = 0;
        
        $get_all_downloads  = "SELECT COUNT(id) AS 'num', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "WHERE file = :file" . PHP_EOL;
        $get_all_downloads .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_all_downloads .= "ORDER BY downloaded_time ASC";
        
        $get_all_downloads_query = $this->controller->db->prepare($get_all_downloads);
        $get_all_downloads_query->execute([':file' => $this->id]);
        while ($row = $get_all_downloads_query->fetch(PDO::FETCH_ASSOC)) {
            if ($type == Item::$accumulated) {
                $previous_num += $row['num'];
                $num_count = $previous_num;
            }
            else {
                $num_count = $row['num'];
            }
            
            $ts_split = explode(' ', $row['downloaded_time']);
            $date_split = explode('-', $ts_split[0]);
            $time_split = explode(':', $ts_split[1]);
            
            $output[] = ['Date.UTC(' . $date_split[0] . ', ' . $date_split[1] . ', ' . $date_split[2] . ', ' .
                $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $num_count];
        }
        
        return json_encode($output);
    }
    
    public function getPhysicalLocation() {
        $checksum = $this->model->getChecksum();
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);
        
        return FILE_PATH . '/' . $folder1 . '/' . $folder2 . '/' . $this->model->getChecksum();
    }

    public function toArray($nest = true) {
        $arr = $this->model->toArrayInitial();
        
        $arr['added_pretty'] = $this->model->getAdded(true);
        
        if (!$this->hasParent()) {
            $arr['course_code'] = $this->getCourseCode();
            $arr['course_name'] = $this->getCourseName();
            unset($arr['name']);
        }
        
        foreach ($this->downloadCount as $v) {
            if ($v > 0) {
                $arr['download_count'] = $v;
            }
        }

        if ($nest) {
            if ($this->hasParent()) {
                $arr['parents'] = [];

                $root_parent = $this->getRootParent();
                if ($root_parent->getId() != $this->model->getParent()) {
                    $arr['parents'][] = $this->model->getParent(true)->toArray(false);
                }

                $arr['parents'][] = $root_parent->toArray(false);
            }
        }

        return $arr;
    }

    public function hasParent() {
        return $this->model->getParent() != null;
    }
    public function isLink() {
        return ($this->model->getUrl() != null);
    }
    public function isFile() {
        return ($this->model->getUrl() == null and !$this->model->isDirectory());
    }
    public function getParentObject() {
        if ($this->parent === null) {
            $this->parent = Element::get($this->model->getParent());
        }

        return $this->parent;
    }
    public function getParents() {
        $arr = [$this->model];

        if ($this->hasParent()) {
            $temp_obj = $this->getParentObject();
            while (true) {
                $arr[] = $temp_obj;

                if ($temp_obj->hasParent()) {
                    $temp_obj = $temp_obj->getParentObject();
                }
                else {
                    break;
                }
            }
        }

        return $arr;
    }
    public function getRootParent() {
        if ($this->rootParent === null) {
            $temp_element = $this->model;

            while (true) {
                if ($temp_element->hasParent()) {
                    $temp_element = $temp_element->getParent(true);
                }
                else {
                    $this->rootParent = $temp_element;
                    break;
                }
            }
        }

        return $this->rootParent;
    }
    public function getCourseName() {
        return htmlspecialchars(explode('||', $this->model->getName())[1]);
    }
    public function getCourseCode() {
        return htmlspecialchars(explode('||', $this->model->getName())[0]);
    }
    public function getFullUrl() {
        if ($this->fullUrl === null) {
            $temp_url = [];

            if ($this->model->isLink()) {
                $temp_url[] = $this->model->getId();
            }

            if (count($temp_url) == 0) {
                $temp_element = $this->model;
                $temp_url[] = $this->model->getUrlFriendly();

                while (true) {
                    if ($temp_element->hasParent()) {
                        $temp_element = $temp_element->getParent(true);
                    }
                    else {
                        break;
                    }

                    $temp_url[] = $temp_element->getUrlFriendly();
                }
            }

            $url_fragments = array_reverse($temp_url);
            $path = TemplateHelper::urlFor('download', $url_fragments);
            if ($this->model->isLink()) {
                $path = TemplateHelper::urlFor('redirect', $url_fragments);
            }
            elseif ($this->model->isDirectory()) {
                $path = TemplateHelper::urlFor('archive', $url_fragments);
            }

            $this->fullUrl = $path;
        }

        return $this->fullUrl;
    }
    public function setFullUrl($url) {
        $this->fullUrl = $url;
    }
    
    public function getAliasFor() {
        if ($this->aliasFor === null) {
            $aliases = [];
            
            $get_alias_elements  = "SELECT id" . PHP_EOL;
            $get_alias_elements .= "FROM archive" . PHP_EOL;
            $get_alias_elements .= "WHERE alias = :alias";
            
            $get_alias_elements_query = Database::$db->prepare($get_alias_elements);
            $get_alias_elements_query->execute([':alias' => $this->model->getId()]);
            while ($row = $get_alias_elements_query->fetch(\PDO::FETCH_ASSOC)) {
                $element = Element::get($row['id']);
                
                if ($element->wasFound() and !$element->isPending()) {
                    $aliases[] = $row['id'];
                }
            }

            $this->setAliasFor($aliases);
        }
        
        return $this->aliasFor;
    }
    public function setAliasFor($aliases) {
        $this->aliasFor = $aliases;
    }
    
    public function updateLastVisited() {
        $last_visited = date("Y-m-d H:i:s", time());
        
        $update_last_visited  = "UPDATE archive" . PHP_EOL;
        $update_last_visited .= "SET last_visited = :last_visited" . PHP_EOL;
        $update_last_visited .= "WHERE id = :id";
        
        $result = Database::$db->prepare($update_last_visited);
        $result->execute([
            ':last_visited' => $last_visited,
            ':id' => $this->model->getId(),
        ]);
        
        $this->model->setLastVisited($last_visited);
    }
}
