<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use LaravelDoctrine\ACL\Roles\HasRoles;
use Illuminate\Auth\Passwords\CanResetPassword;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ORM\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRoleContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;

class User implements AuthenticatableContract, CanResetPasswordContract, HasRoleContract, HasPermissionsContract, AuthorizableContract
{
    use CanResetPassword, Notifiable, HasRoles, HasPermissions, SoftDeletable, Timestampable, Authorizable;

    const MIN_PASSWORD_LENGTH = 3;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string Users first name
     */
    protected $firstname;

    /**
     * @var string Users last name
     */
    protected $lastname;

    /**
     * @var string Users username for login
     */
    protected $username;

    /**
     * @var string Users email address
     */
    protected $email;

    /**
     * @var string Users remember me token for persisting login sessions
     */
    protected $rememberToken;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    protected $roles;

    /**
     * @var Profile The users profile
     */
    protected $profile;

    /**
     * User constructor.
     * @param string $firstname
     * @param string $lastname
     * @param string $username
     * @param string $email
     */
    public function __construct(string $firstname, string $lastname, string $username, string $email)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->email = $email;
        $this->roles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword()
    {
        throw new Exception('Not Supported');
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    /**
     * @return ArrayCollection|Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions()
    {
        // user's don't directly have permissions, only via their roles
        return [];
    }

    /**
     * @return Profile The users profile
     */
    public function getProfile() : Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }
}
