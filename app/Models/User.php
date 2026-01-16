<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profile_photo',
        'role',
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

    /**
     * Scope a query to only include users of a given role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the profile photo URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getProfilePhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If the value is already a data URI, return it as is.
        if (str_starts_with($value, 'data:image')) {
            return $value;
        }

        $path = storage_path('app/public/' . $value);

        if (!file_exists($path)) {
            return null;
        }

        $type = mime_content_type($path);
        $data = file_get_contents($path);

        return 'data:' . $type . ';base64,' . base64_encode($data);
    }
}
