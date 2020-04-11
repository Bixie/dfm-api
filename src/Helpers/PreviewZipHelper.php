<?php


namespace Bixie\DfmApi\Helpers;


use Bixie\DfmApi\PreviewZip;
use Lime\Helper;

class PreviewZipHelper extends Helper {

    protected $previewZip;

    /**
     * @throws \Exception
     */
    public function initialize() {
        $this->previewZip = new PreviewZip($this->app['path.root'] . '/' . $this->app['temppath.previewimages']);
    }

    public function __call ($name, $arguments)
    {
        if (is_callable([$this->previewZip, $name])) {
            return call_user_func_array([$this->previewZip, $name], $arguments);
        }
        return parent::__call($name, $arguments);
    }
}
