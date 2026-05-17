<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\ErpHttp;

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
        'session_id',
        'erp_session_id',
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
        'erp_access_token',
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
            'erp_access_token' => 'encrypted',
            'erp_token_expires_at' => 'datetime',
        ];
    }

    public function getDisplayPictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . ltrim($this->profile_picture, '/'));
        }

        if ($this->erp_picture) {
            if (filter_var($this->erp_picture, FILTER_VALIDATE_URL)) {
                return $this->erp_picture;
            }

            $baseUrl = rtrim(config('services.erp.web_url'), '/');
            $path = ltrim($this->erp_picture, '/');

            if (str_starts_with($path, 'storage/')) {
                return $baseUrl . '/' . $path;
            }

            return $baseUrl . '/storage/emp_profile_images/' . $path;
        }

        return asset('assets/auth/images/users/avatar.png');
    }

    public static function getBranchAttribute($branchId)
    {
        $branchId = $branchId; // use the provided branch ID or fallback to the user's branch_id

        try {
            $response = ErpHttp::get("get-branch/{$branchId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching branch: ' . $e->getMessage());
            return null;
        }
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schoolSession(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Session::class, 'session_id');
    }
}
