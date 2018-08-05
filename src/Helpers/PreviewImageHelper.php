<?php


namespace Bixie\DfmApi\Helpers;


use Lime\Helper;

class PreviewImageHelper extends Helper {
    /**
     * @var string
     */
    protected $previewimagesPath;

    /**
     * @throws \Exception
     */
    public function initialize() {
        $this->previewimagesPath = realpath($this->app['path.root'] . '/' . $this->app['temppath.previewimages']);
        if ($this->previewimagesPath === false) {
            throw new \Exception('No previewimages path available!');
        }
    }

    /**
     * @param string $preview_id
     * @param string $imageData
     * @return bool
     */
    public function saveTempImage($preview_id, $imageData) {
        $size = file_put_contents($this->getFilename($preview_id), $imageData);
        return $size > 0;
    }

    /**
     * @param string $preview_id
     * @return string|bool
     */
    public function getTempImageContents ($preview_id) {
        $filename = $this->getFilename($preview_id);
        if (!file_exists($filename)) {
            return false;
        }
        return file_get_contents($filename);
    }

    /**
     * @param string $preview_id
     */
    public function removeTempImage ($preview_id) {
        unlink($this->getFilename($preview_id));
    }

    /**
     * @param string $preview_id
     * @return string
     */
    protected function getFilename ($preview_id) {
        return sprintf('%s/%s.b64', $this->previewimagesPath, $preview_id);
    }
}