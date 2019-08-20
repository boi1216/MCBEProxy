<?php


namespace proxy\utils;


class LoginCredentials
{

    /** @var string $email */
    private $email;

    /** @var string $password */
    private $password;

    /**
     * LoginCredentials constructor.
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function matchPassword(string $password) : bool{
        return $password == $this->password;
    }

}