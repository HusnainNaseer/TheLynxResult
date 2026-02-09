<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles; // Add this line
    
    // ... rest of your User model

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // In App\Models\User.php

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'branch_name',
        'branch_email',
        'branch_phone',
        'branch_address',
    ];
    protected $attributes = [
    'role_id' => 2,
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
