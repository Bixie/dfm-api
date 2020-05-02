<?php


namespace Bixie\DfmApi;


class PreviewZip
{

    /**
     * @var string
     */
    protected $previewimagesPath;

    /**
     * @param string $previewimagesPath
     * @throws \Exception
     */
    public function __construct(string $previewimagesPath) {
        $this->previewimagesPath = realpath($previewimagesPath);
        if ($this->previewimagesPath === false) {
            throw new \Exception('No previewimages path available!');
        }
    }

    /**
     * @param string $preview_id
     * @return bool
     */
    public function saveZipResponse($preview_id) {
        $size = 0;
        /* PUT data comes in on the stdin stream */
        $putdata = fopen("php://input", "r");

        /* Open a file for writing */
        $fp = fopen($this->getZipFilepath($preview_id), 'w');

        /* Read the data 1 KB at a time and write to the file */
        while ($data = fread($putdata, 1024)) {
            $size += fwrite($fp, $data);
        }
        /* Close the streams */
        fclose($fp);
        fclose($putdata);

        return $size > 0;
    }

    /**
     * @param string $preview_id
     * @return array|bool
     */
    public function getPreviewFilesContents ($preview_id) {
        $filepath = $this->getZipFilepath($preview_id);
        if (!file_exists($filepath)) {
            return false;
        }
        //unzip and base64 decode the images
        $files = $this->readZipToAscii($filepath);
        return $files;
    }

    /**
     * @param string $preview_id
     */
    public function removeTempZip ($preview_id) {
        unlink($this->getZipFilepath($preview_id));
    }

    /**
     * @param string $preview_id
     * @return string
     */
    protected function getZipFilepath ($preview_id) {
        return sprintf('%s/%s.zip', $this->previewimagesPath, $preview_id);
    }

    /**
     * @param $filepath
     * @return array
     */
    protected function readZipToAscii ($filepath) {
        $files = [];
        $zip = new \ZipArchive;
        if ($zip->open($filepath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $contents = $zip->getFromIndex($i);
                if (preg_match('/\.(?:png|jpg|gif)$/', $name)) {
                    $contents = base64_encode($contents);
                }
                $files[$name] = $contents;
            }
            $zip->close();
        }
        return $files;
    }
}
