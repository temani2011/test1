<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploader
{
    private $baseDirectory;
    private $targetDirectory;

    public function __construct($baseDirectory, $targetDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $fullDirPath = $this->getBaseDirectory() . $this->getTargetDirectory();

        try {
            $file->move($fullDirPath, $fileName);
        } catch (FileException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $fileName;
    }

    public function delete(string $fileName)
    {
        $fileSystem = new Filesystem();
        //$fullDirPath = $this->getBaseDirectory() . $this->getTargetDirectory();
        if(!$fileSystem->exists($this->getBaseDirectory() . $fileName))
            throw new BadRequestHttpException('file ' . $fileName .' in '. $this->getBaseDirectory() . ' dosen\'t exist');

        try {
            $fileSystem->remove($this->getBaseDirectory() . $fileName);
        } catch (FileException $e) {
             throw new BadRequestHttpException($e->getMessage());
        }

        return true;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function setTargetDirectory(string $newTargetDirectory)
    {
        $this->targetDirectory = $newTargetDirectory;
    }

    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

}