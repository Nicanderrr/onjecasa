<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'login_bypass_enabled', 'pincode_hash', 'avatar_path'])]
#[Hidden(['password', 'pincode_hash', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'login_bypass_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function avatarUrl(): string
    {
        if (!empty($this->avatar_path)) {
            return asset('assets/admin/img/profiles/' . ltrim((string) $this->avatar_path, '/\\'));
        }

        return asset('assets/adminhmd/images/avatar/avatar.jpg');
    }
}
