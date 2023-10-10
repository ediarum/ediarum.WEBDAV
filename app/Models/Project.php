<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'data_folder_location',
        'gitlab_url',
        'gitlab_username',
        'gitlab_personal_access_token',
        'ediarum_backend_url',
        'ediarum_backend_api_key',
        'exist_base_url',
        'exist_data_path',
        'exist_username',
        'exist_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'gitlab_personal_access_token',
        'ediarum_backend_api_key',
        'exist_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gitlab_personal_access_token' => 'encrypted',
        'ediarum_backend_api_key' => 'encrypted',
        'exist_password' => 'encrypted',
    ];


    public function users():BelongsToMany{
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
