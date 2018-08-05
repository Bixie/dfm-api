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
        $filename = $this->previewimagesPath . '/' . $preview_id . '.b64';
        $size = file_put_contents($filename, $imageData);
        return $size > 0;
    }
}