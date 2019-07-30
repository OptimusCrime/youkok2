<?php
namespace Youkok\Biz\Services\Post\Create;

use Carbon\Carbon;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Youkok\Biz\Exceptions\CreateException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\FileTypesHelper;
use Youkok\Common\Utilities\SelectStatements;

class CreateFileService
{
    const MIN_VALID_NAME_LENGTH = 2;
    const CRYPTO_STRING_LENGTH = 32;

    private $elementService;

    public function __construct(ElementService $elementService) {
        $this->elementService = $elementService;
    }

    public function run(int $id, UploadedFileInterface $file): void
    {
        $fileName = $file->getClientFilename();
        if (mb_strlen($fileName) < static::MIN_VALID_NAME_LENGTH) {
            throw new CreateException('File name too short. Found: ' . $fileName);
        }

        $extension = static::extractAndValidateExtension($fileName);

        $course = $this->elementService->getElement(
            new SelectStatements('id', $id),
            ['id'],
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
        $newElement->slug = static::createSlug($fileName);
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

    private static function extractAndValidateExtension(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($extension === null) {
            throw new CreateException('Found no file type in upload.');
        }

        if (!in_array($extension, FileTypesHelper::getValidFileTypes())) {
            throw new CreateException('Found invalid extension of type ' . $extension . ' in upload.');
        }

        return $extension;
    }

    private static function createSlug(string $fileName): string
    {
        // Replace first here to keep "norwegian" names in a way
        $fileName = str_replace(['Æ', 'Ø', 'Å'], ['ae', 'o', 'aa'], $fileName);
        $fileName = str_replace(['æ', 'ø', 'å'], ['ae', 'o', 'aa'], $fileName);

        // Replace multiple spaces to dashes and remove special chars
        $fileName = preg_replace('!\s+!', '-', $fileName);

        // Remove all but last dot
        $fileName = preg_replace('/\.(?![^.]+$)|[^-_a-zA-Z0-9\s]$/', '-', $fileName);

        // Remove multiple dashes in a row
        $fileName = preg_replace('!-+!', '-', $fileName);

        // Remove all but these characters
        return preg_replace('![^-_a-zA-Z0-9\s.]+!', '', $fileName);
    }

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
        }
        catch (Exception $ex) {
            throw new CreateException('Failed to create a checksum for file.', $ex->getCode(), $ex);
        }

        // Guard
        throw new CreateException('Failed to create a checksum for file.');
    }

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
        }
        catch (Exception $ex) {
            throw new CreateException('Failed to move uploaded file with exception', $ex->getCode(), $ex);
        }
    }

    private static function getFileTargetDir(string $checksum): string
    {
        return getenv('FILE_DIRECTORY')
            . substr($checksum, 0, 1)
            . DIRECTORY_SEPARATOR
            . substr($checksum, 1, 1)
            . DIRECTORY_SEPARATOR;
    }
}
