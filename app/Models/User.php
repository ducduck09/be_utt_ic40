<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * ====================================================================
 * USER MODEL
 * ====================================================================
 * 
 * Model User hỗ trợ:
 * - Đăng nhập thông thường (email/password)
 * - Đăng nhập bằng Google OAuth
 * - API Token authentication (Sanctum)
 * 
 * ====================================================================
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * 
     * Các trường có thể được gán hàng loạt:
     * - google_id: ID từ Google (dùng cho OAuth)
     * - name: Tên người dùng
     * - email: Email
     * - password: Mật khẩu (nullable cho user Google)
     * - avatar: Ảnh đại diện từ Google
     *
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'password',
        'avatar',
        'email_verified_at',
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
     * ----------------------------------------------------------------
     * HELPER METHODS
     * ----------------------------------------------------------------
     */

    /**
     * Kiểm tra user có đăng nhập bằng Google không
     *
     * @return bool
     */
    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }

    /**
     * Kiểm tra user có đặt password không
     * (User Google có thể không có password)
     *
     * @return bool
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }
}
