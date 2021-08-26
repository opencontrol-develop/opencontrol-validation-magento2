<?php

namespace OpenControl\Integration\Model\Http;

class AuthDto{
    public $user;
    public $password;

    public function toBase64() {
        return base64_encode($this->user.':'.$this->password);
    }
}