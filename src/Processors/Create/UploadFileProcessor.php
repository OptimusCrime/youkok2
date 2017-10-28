<?php
namespace Youkok\Processors\Create;

use Carbon\Carbon;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\UploadedFileInterface as UploadedFile;
use Youkok\Models\Element;
use Youkok\Utilities\UriTranslator;

class UploadFileProcessor extends AbstractCreateProcessor
{
    const CRYPTO_STRING_LENGTH = 32;

    private $settings;

    public static function fromRequest(Request $request)
    {
        return new UploadFileProcessor($request);
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    private static function errorResponse()
    {
        return [
            'code' => 400
        ];
    }

    public function run()
    {
        if (!static::containsOneUploadedFile($this->request)) {
            return static::errorResponse();
        }

        $uploadedFile = $this->request->getUploadedFiles()['files'][0];
        $parent = static::getParentForUpload($this->request);

        if ($parent === null) {
            return static::errorResponse();
        }

        if (!static::uploadIsOfValidFileType($uploadedFile->getClientFilename(), $this->settings)) {
            return static::errorResponse();
        }

        if (static::uploadAndAddFile($uploadedFile, $parent, $this->settings)) {
            return [
                'code' => 200
            ];
        }

        return static::errorResponse();
    }

    private static function containsOneUploadedFile(Request $request)
    {
        $files = $request->getUploadedFiles();
        if ($files === null or !is_array($files)) {
            return false;
        }

        return count($files) === 1;
    }

    private static function getParentForUpload(Request $request)
    {
        if (!isset($request->getParams()['parent']) or !is_numeric($request->getParams()['parent'])) {
            return null;
        }

        if (static::parentIsValid($request->getParams()['parent'])) {
            return Element::fromId($request->getParams()['parent']);
        }

        return null;
    }

    private static function uploadIsOfValidFileType($fileName, $settings)
    {
        $pathInto = pathinfo(htmlentities($fileName));
        if (!isset($pathInto['extension']) or strlen($pathInto['extension']) === 0) {
            return false;
        }

        return in_array($pathInto['extension'], $settings['file_endings']);
    }

    private static function uploadAndAddFile(UploadedFile $uploadedFile, Element $parent, $settings)
    {
        $uri = static::generateUrlFriendly($uploadedFile, $parent);
        $name = static::generateName($uploadedFile, $parent);
        $checksum = static::generateChecksum($uploadedFile);

        if (!static::moveFile($uploadedFile, $settings, $checksum)) {
            return false;
        }

        $element = new Element();
        $element->name = $name;
        $element->slug = $uri;
        $element->parent = $parent->id;
        $element->empty = 1;
        $element->checksum = $checksum;
        $element->size = $uploadedFile->getSize();
        $element->directory = 0;
        $element->pending = 1;
        $element->deleted = 0;
        $element->added = Carbon::now();

        return $element->save();
    }

    private static function generateUrlFriendly(UploadedFile $uploadedFile, Element $parent)
    {
        $tempUri = UriTranslator::generate($uploadedFile->getClientFilename());
        $pathInfo = pathinfo($tempUri);

        $uriBase = $pathInfo['filename'];
        $uriEndfix = '.' . $pathInfo['extension'];

        $number = null;
        while (true) {
            $currentSlug = $uriBase . $uriEndfix;
            if ($number !== null) {
                $currentSlug = $uriBase . $number . $uriEndfix;
            }

            $existingMatch = Element
                ::where('parent', $parent->id)
                ->where('slug', $currentSlug)
                ->get();

            if (count($existingMatch) === 0) {
                break;
            }

            if ($number === null) {
                $number = 1;
                continue;
            }

            $number++;
        }

        if ($number === null) {
            return $uriBase . $uriEndfix;
        }

        return $uriBase . $number . $uriEndfix;
    }

    private static function generateName(UploadedFile $uploadedFile, Element $parent)
    {
        $tempUri = $uploadedFile->getClientFilename();
        $pathInfo = pathinfo($tempUri);

        $uriBase = $pathInfo['filename'];
        $uriEndfix = '.' . $pathInfo['extension'];

        $number = null;
        while (true) {
            $currentSlug = $uriBase . $uriEndfix;
            if ($number !== null) {
                $currentSlug = $uriBase . ' (' . $number . ')' . $uriEndfix;
            }

            $existingMatch = Element
                ::where('parent', $parent->id)
                ->where('name', $currentSlug)
                ->get();

            if (count($existingMatch) === 0) {
                break;
            }

            if ($number === null) {
                $number = 1;
                continue;
            }

            $number++;
        }

        if ($number === null) {
            return $uriBase . $uriEndfix;
        }

        return $uriBase . ' (' . $number . ')' . $uriEndfix;
    }

    private static function generateChecksum(UploadedFile $uploadedFile)
    {
        $pathInfo = pathinfo($uploadedFile->getClientFilename());
        $rand = '';

        while (true) {
            // Please do not judge. We need the random filename to be only a-zA-Z0-9 formatted. The easiest way to
            // do this is to use md5. The previous implementation used md5_file (checksum), but this is no longer
            // possible with the Slim framework.
            $checksum = md5(random_bytes(static::CRYPTO_STRING_LENGTH) . $rand) . '.' . $pathInfo['extension'];

            $existingMatch = Element::where('checksum', $checksum)->get();
            if (count($existingMatch) === 0) {
                return $checksum;
            }

            $rand = random_bytes(static::CRYPTO_STRING_LENGTH);
        }
    }

    private static function moveFile(UploadedFile $uploadedFile, $settings, $checksum)
    {
        if (!static::createTarget($settings, $checksum)) {
            return false;
        }

        try {
            $uploadedFile->moveTo(static::getTarget($settings, $checksum) . $checksum);
            return true;
        }
        catch (\Exception $e) {
            // TODO better handling here (and logging)
            return false;
        }
    }

    private static function createTarget($settings, $checksum)
    {
        $target = static::getTarget($settings, $checksum);
        if (!file_exists($target)) {
            return mkdir($target, 0777, true);
        }

        return true;
    }

    private static function getTarget($settings, $checksum)
    {
        return $settings['file_directory'] . substr($checksum, 0, 1)
            . DIRECTORY_SEPARATOR . substr($checksum, 1, 1) . DIRECTORY_SEPARATOR;
    }
}