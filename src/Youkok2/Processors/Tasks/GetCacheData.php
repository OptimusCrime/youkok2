<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\CacheManager;

class GetCacheData extends BaseProcessor
{

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        if (isset($_GET['id']) and is_numeric($_GET['id'])) {
            $element = new Element();
            
            $this->setData('pre', [
                'id' => $element->getId(),
                'name' => $element->getName(),
                'url_friendly' => $element->getUrlFriendly(),
                'owner' => $element->getOwner(),
                'parent' => $element->getParent(),
                'empty' => $element->isEmpty(),
                'checksum' => $element->getChecksum(),
                'mime_type' => $element->getMimeType(),
                'missing_image' => $element->getMissingImage(),
                'size' => $element->getSize(),
                'directory' => $element->isDirectory(),
                'link' => $element->isLink(),
                'file' => $element->isFile(),
                'accepted' => $element->isAccepted(),
                'visible' => $element->isVisible(),
                'exam' => $element->getExam(),
                'url' => $element->getUrl(),
                'added' => $element->getAdded()
            ]);
            
            $element->createById($_GET['id']);
            
            if ($element->wasFound()) {
                $cache = CacheManager::getCache($element->getId(), 'i');
                
                if ($cache === null) {
                    $this->setData('code', 300);
                    $this->setData('msg', 'Not cached');
                }
                else {
                    $this->setData('code', 200);
                    $this->setData('cache', $cache);
                }
            }
            
            $this->setData('cache_format', $element->cacheFormat());
            
            $this->setData('data', [
                'id' => $element->getId(),
                'name' => $element->getName(),
                'url_friendly' => $element->getUrlFriendly(),
                'owner' => $element->getOwner(),
                'parent' => $element->getParent(),
                'empty' => $element->isEmpty(),
                'checksum' => $element->getChecksum(),
                'mime_type' => $element->getMimeType(),
                'missing_image' => $element->getMissingImage(),
                'size' => $element->getSize(),
                'directory' => $element->isDirectory(),
                'link' => $element->isLink(),
                'file' => $element->isFile(),
                'accepted' => $element->isAccepted(),
                'visible' => $element->isVisible(),
                'exam' => $element->getExam(),
                'url' => $element->getUrl(),
                'added' => $element->getAdded()
            ]);
        }
        else {
            $this->setData('code', 500);
            $this->setData('msg', 'No id');
        }
    }
}
