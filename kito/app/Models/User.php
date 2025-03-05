<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan',
        'is_admin',
        'is_leader',
        'is_hamukti',
        'is_active',
        'username',
        'gambar',
    ];

    // public function casts(): array
    // {
    //     return [
    //         'is_admin' => 'boolean',
    //         'is_leader' => 'boolean',
    //     ];
    // }
    // Bisa juga menambahkan fungsi untuk memudahkan pengecekan role
    //  public function hasRole($is_leader)
    //  {
    //      return $this->is_leader === 1;
    //  }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_leader' => 'boolean',
    ];

    public function izinkeluars()
    {
        return $this->hasMany(Izinkeluar::class);
    }

    public function surattugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'surattugasuser');
    }

    public function permintaanbarang()
    {
        return $this->hasMany(PermintaanBarang::class);
    }
}
