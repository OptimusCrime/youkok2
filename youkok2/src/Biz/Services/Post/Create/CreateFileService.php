<?php
namespace Youkok\Biz\Services\Post\Create;

use Exception;

use Carbon\Carbon;
use Psr\Http\Message\UploadedFileInterface;

use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Helpers\Configuration\Configuration;

class CreateFileService
{
    const int MIN_VALID_NAME_LENGTH = 2;
    const int CRYPTO_STRING_LENGTH = 32;

    private ElementService $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * @throws CreateException
     * @throws ElementNotFoundException
     */
    public function run(int $id, UploadedFileInterface $file): void
    {
        $fileName = $file->getClientFilename();
        if (mb_strlen($fileName) < static::MIN_VALID_NAME_LENGTH) {
            throw new CreateException('File name too short. Found: ' . $fileName);
        }

        $extension = static::extractAndValidateExtension($fileName);

        $course = $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                ElementService::FLAG_ENSURE_VISIBLE,
                ElementService::FLAG_ENSURE_IS_COURSE
            ]
        );

        $checksum = static::generateChecksum($extension);
        static::moveFile($file, $checksum);

        $newElement = new Element();
        $newElement->parent = $course->id;
        $newElement->name = $file->getClientFilename();
        $newElement->slug = ElementService::createSlug($fileName);
        $newElement->pending = 1;
        $newElement->deleted = 0;
        $newElement->empty = 1;
        $newElement->directory = 0;
        $newElement->size = $file->getSize();
        $newElement->checksum = $checksum;
        $newElement->added = Carbon::now();

        $success = $newElement->save();

        if (!$success) {
            throw new CreateException('Failed to create a new link element');
        }
    }

    /**
     * @throws CreateException
     */
    private static function extractAndValidateExtension(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!in_array($extension, Configuration::getInstance()->getFileUploadAllowedTypes())) {

            throw new CreateException('Found invalid extension of type ' . $extension . ' in upload.');
        }

        return $extension;
    }

    /**
     * @throws CreateException
     */
    private static function generateChecksum(string $extension): string
    {
        try {
            while (true) {
                $randomBytes = random_bytes(static::CRYPTO_STRING_LENGTH);

                // We need the random filename to be only a-zA-Z0-9 formatted. The easiest way to
                // do this is to use md5. The previous implementation used md5_file (checksum), but this is no longer
                // possible with the Slim framework.
                $checksum = md5($randomBytes) . '.' . $extension;

                $existingMatch = Element::where('checksum', $checksum)->get();
                if (count($existingMatch) === 0) {
                    return $checksum;
                }
            }
        } catch (Exception $ex) {
            throw new CreateException('Failed to create a checksum for file.', $ex->getCode(), $ex);
        }
    }

    /**
     * @throws CreateException
     */
    private static function moveFile(UploadedFileInterface $file, string $checksum): void
    {
        $targetDir = static::getFileTargetDir($checksum);
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir)) {
                throw new CreateException('Failed to create directory for upload in: ' . $targetDir);
            }
        }

        try {
            $file->moveTo($targetDir . $checksum);
        } catch (Exception $ex) {
            throw new CreateException('Failed to move uploaded file with exception', $ex->getCode(), $ex);
        }
    }

    private static function getFileTargetDir(string $checksum): string
    {
        return Configuration::getInstance()->getDirectoryFiles()
            . substr($checksum, 0, 1)
            . DIRECTORY_SEPARATOR
            . substr($checksum, 1, 1)
            . DIRECTORY_SEPARATOR;
    }
}
