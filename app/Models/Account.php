<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * ====================================================================
 * ACCOUNT MODEL - Dùng cho Authentication
 * ====================================================================
 * 
 * Model Account dùng cho:
 * - Đăng nhập bằng Google OAuth
 * - Quản lý API Token (Sanctum)
 * 
 * Bảng: accounts
 * 
 * Tách biệt với bảng users cũ để không ảnh hưởng đến dữ liệu hiện có.
 * 
 * ====================================================================
 */
class Account extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'microsoft_id',
        'name',
        'email',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * ----------------------------------------------------------------
     * RELATIONSHIPS
     * ----------------------------------------------------------------
     */

    // Không cần relationships vì đã xóa user_id

    /**
     * ----------------------------------------------------------------
     * HELPER METHODS
     * ----------------------------------------------------------------
     */

    /**
     * Kiểm tra account có đăng nhập bằng Google không
     *
     * @return bool
     */
    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }

    /**
     * Kiểm tra account có đăng nhập bằng Microsoft không
     *
     * @return bool
     */
    public function isMicrosoftUser(): bool
    {
        return !empty($this->microsoft_id);
    }
}
