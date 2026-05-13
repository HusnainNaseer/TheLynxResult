<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        'type',
        'branch_name',
        'branch_email',
        'branch_address',
        'branch_phone',
        'profile_picture',
        'erp_picture',
        'branch_id',   
        'teacher_id',        // add this
        'erp_employee_id',     // add this
        'storage_limit',
        'avatar',
        'lang',
        'mode',
        'delete_status',
        'plan',
        'email_verified_at',
        'plan_expire_date',
        'requested_plan',
        'is_active',
        'last_login_at',
        'owned_by',
        'created_by',
        'company_id',
    ];
//     protected $attributes = [
//     'role_id' => 2,
// ];

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
    public static function getBranchAttribute($branchId)
    {
        $branchId = $branchId; // use the provided branch ID or fallback to the user's branch_id

        try {
            $response = Http::get(env('API_URL') . "get-branch/{$branchId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching branch: ' . $e->getMessage());
            return null;
        }
    }
}
