<?php


namespace Bixie\DfmApi\Helpers;


use Hashids\Hashids;
use Lime\Helper;

class KeyGenerator extends Helper
{
    const MIN_HASH_LENGTH = 12;

    const ALPHABETH = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    protected $hashids;

    public function initialize ()
    {
        $this->hashids = new Hashids($this->app['sec-key'], self::MIN_HASH_LENGTH, self::ALPHABETH);
    }

    public function generateKeyFromId (int $id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Id for key must not be empty!');
        }
        $hashid = $this->hashids->encode($id);
        return implode('-', str_split($hashid . strtoupper(hash('crc32b', $hashid)), 4));
    }
}
