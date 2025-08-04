<?php

namespace App\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',
        'name',
        'password',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id', "id");
    }

    // Implementing Authenticatable methods
    // public function getAuthIdentifierName()
    // {
    //     return 'username';
    // }

    // public function getAuthIdentifier()
    // {
    //     return $this->username;
    // }

    // public function getAuthPassword()
    // {
    //     return $this->password;
    // }

    // public function getAuthPasswordName()
    // {
    //     return 'password';
    // }

    // public function getRememberToken()
    // {
    //     return 'token';
    // }

    // public function setRememberToken($value)
    // {
    //     $this->token = $value;
    // }

    // public function getRememberTokenName()
    // {
    //     return 'token';
    // }
}


