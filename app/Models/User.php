<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Hash;

use App\Models\DirectSponsorBaseModel;


class User extends DirectSponsorBaseModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('username', 'email', 'account_type', 'password');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $hashService;

    public static $rules = array(
        'username' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed'
    );

    public function __construct() {
        parent::__construct();
    }

    public function setFillable($fields) {
        $this->fillable = $fields;
    }

    public function coordinator() {
        return $this->hasOne('App\Models\Coordinator');
    }

    public function recipient() {
        return $this->hasOne('App\Models\Recipient');
    }

    public function sponsor() {
        return $this->hasOne('App\Models\Sponsor');
    }

    public function sentPayments() {
        return $this->hasMany('App\Models\Payment', 'sender_id');
    }

    public function receivedPayments() {
        return $this->hasMany('App\Models\Payment', 'receiver_id');
    }

    public function account() {
        switch ($this->account_type) {
            case 'Admin':
                return null;
            case 'Coordinator':
                return $this->coordinator();
            case 'Recipient':
                return $this->recipient();
            case 'Sponsor':
                return $this->sponsor();
        }
        return null;
    }

    public function getReminderEmail() {
        return $this->email;
    }

    public function createNewUser(array $data=array(), $accountType='') {
        $this->fill($data);
        $this->password = Hash::make($this->password);
        $this->account_type = $accountType;

        $this->save();
        return $this;
    }
}
