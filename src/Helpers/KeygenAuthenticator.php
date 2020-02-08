<?php


namespace Bixie\DfmApi\Helpers;


use Lime\Helper;

class KeygenAuthenticator extends Helper {

    protected $user;
    protected $password;

    public function initialize()
    {
        $this->user = $this->app['keygen.user'];
        $this->password = $this->app['keygen.password'];
    }

    public function authenticate()
    {
        return (!empty($this->user) && !empty($this->password)) &&
            $this->user === $_SERVER['PHP_AUTH_USER'] &&
            $this->password === $_SERVER['PHP_AUTH_PW'];
    }
}
