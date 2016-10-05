<?php
namespace Youkok2\Models\Controllers;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;

class ChangePasswordController extends BaseController
{

    public function __construct($model) {
        parent::__construct($this, $model);
    }

    public function createByHash($hash) {
        $validate_hash  = "SELECT c.id, c.user, c.timeout, u.email" . PHP_EOL;
        $validate_hash .= "FROM changepassword c" . PHP_EOL;
        $validate_hash .= "LEFT JOIN user AS u ON c.user = u.id" . PHP_EOL;
        $validate_hash .= "WHERE c.hash = :hash" . PHP_EOL;
        $validate_hash .= "AND c.timeout > CURRENT_TIMESTAMP";
        
        $validate_hash_query = Database::$db->prepare($validate_hash);
        $validate_hash_query->execute([':hash' => $hash]);
        $row = $validate_hash_query->fetch(\PDO::FETCH_ASSOC);
        
        if (isset($row['id'])) {
            $this->model->setId($row['id']);
            $this->model->setUser($row['user']);
            $this->model->setHash($hash);
            $this->model->setTimeout($row['timeout']);
        }
    }
}
