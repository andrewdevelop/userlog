<?php 

namespace Andrewdevelop\Userlog;

class Info {

    public $ip;
    public $ua;
    public $url;
    public $http_referer;

    public function __construct()
    {
        $this->ip = app('request')->ip();
        $this->ua = app('request')->server('HTTP_USER_AGENT');
        $this->url = app('request')->fullUrl();
        $this->http_referer = app('request')->server('HTTP_REFERER');
    }

    public function __toString()
    {
        $strval = '';
        $props = get_object_vars($this);
        foreach ($props as $prop => $val) {
            $strval.= $prop.': '.$val.'; ';
        }
        return rtrim($strval,' ');
    }


}