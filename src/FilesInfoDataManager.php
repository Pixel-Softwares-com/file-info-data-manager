<?php

namespace FilesInfoDataManagers;

use Exception;
use FilesInfoDataManagers\Helpers\Helpers;
use Illuminate\Support\Facades\File;

abstract class FilesInfoDataManager
{
    protected string $FilesInfoJSONFilePath ;
    protected array $InfoData = [];

    abstract protected function getDataFilesInfoPath() : string;

    protected function openJSONFileToUpdate() : self
    {
        $this->InfoData = json_decode(File::get($this->FilesInfoJSONFilePath) , true) ?? [];
        return $this;
    }

    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    protected function checkJsonExtension(string $filePath) : void
    {
        if(strtolower(File::extension($filePath)) != "json")
        {
            $exceptionClass = Helpers::getExceptionClass();
            throw new $exceptionClass("The given json info file's extension in invalid !");
        }
    }

    protected function checkFileFolderPath(string $filePath) : void
    {
        $fileFolderPath = File::dirname($filePath);
        if(!File::exists($fileFolderPath))
        {
            File::makeDirectory($fileFolderPath);
        }
    }
    protected function createFileIfNotExists(string $filePath) : void
    {
        if(!File::exists($filePath))
        {
            $this->checkFileFolderPath($filePath);
            File::put($filePath , "");
        }
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function setDataFilesInfoPath(): self
    {
        $filePath = $this->getDataFilesInfoPath();
        $this->checkJsonExtension($filePath);
        $this->createFileIfNotExists($filePath);
        $this->FilesInfoJSONFilePath = $filePath;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setDataFilesInfoPath()->openJSONFileToUpdate();
    }

    /**
     * @param array | string $fileInfo
     * @param string|int $fileKey
     * @param bool $overwriteIfKeyExists
     * @return bool
     *
     * It Is A General Method .... Where $fileInfo is An Array Of File Details (Info)
     */
    public function addFileInfo(array | string $fileInfo , string | int $fileKey = -1 , bool $overwriteIfKeyExists = true) : bool
    {
        if(array_key_exists($fileKey , $this->InfoData))
        {
            if(!$overwriteIfKeyExists){ return false; }
            $this->InfoData[$fileKey] = $fileInfo;
            return true;
        }
        if($fileKey < 0 || $fileKey == ""){$fileInfo = count($this->InfoData);}
        $this->InfoData[$fileKey] = $fileInfo;
        return true;
    }

    /**
     * @param string | int $fileKey
     * @return $this
     */
    public function removeFileInfo(string | int $fileKey) : self
    {
        if( isset( $this->InfoData[$fileKey]) )
        {
            unset($this->InfoData[$fileKey]);
        }
        return $this;
    }

    protected function restartData() : void
    {
        $this->InfoData = [];
    }

    public function SaveChanges() : bool
    {
        $fileContent = json_encode($this->InfoData , JSON_PRETTY_PRINT);
        $this->restartData();

        return File::put($this->FilesInfoJSONFilePath , $fileContent);
    }

}
