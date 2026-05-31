<?php

namespace App\Models;

use App\Enums\Roles\Entities;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Override;

class User extends BaseModel implements Authenticatable, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, AuthAuthenticatable;

    const FIELD_NAME = 'name';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';
    const FIELD_ROLE_ID = 'role_id';

    const FIELD_BALANCE = 'balance';
    const FIELD_ORDERS = 'orders';

    protected $guarded = ['id'];

    protected $hidden = [
        self::FIELD_PASSWORD,
    ];

    protected $casts = [
        'role_id' => Entities::class,
        'password' => 'hashed',
    ];

    public static function getUser(string $email): ?self
    {
        return self::where(self::FIELD_EMAIL, $email)->first();
    }

    public function getNewToken(): string
    {
        return $this->createToken('api-token')->plainTextToken;
    }

    public function checkPassword(string $password): bool
    {
        $currentPass = $this->{self::FIELD_PASSWORD};

        return Hash::check($password, $currentPass);
    }

    public function deleteToken(): void
    {
        $this->tokens()->delete();
    }

    public function getRole(): ?Entities
    {
        return $this->role_id;
    }

    public function getLastOrder(): ?Order
    {
        return $this->{self::FIELD_ORDERS}->last();
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class, Balance::FIELD_USER_ID);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, Order::FIELD_USER_ID);
    }

    #[Override]
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role_id === Entities::Admin;
    }
}
