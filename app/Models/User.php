<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use OpenAdmin\Admin\Auth\Database\HasPermissions;
use OpenAdmin\Admin\Traits\DefaultDatetimeFormat;

class User extends  Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasPermissions;
    use DefaultDatetimeFormat;
    protected $table='admin_users';
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (url()->isValidUrl($avatar)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/open-admin/open-admin/gfx/user.svg';

        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }
    // public function roles()
    // {
    //     $pivotTable = config('admin.database.role_users_table');

    //     $relatedModel = config('admin.database.roles_model');

    //     return $this->belongsTo($relatedModel, $pivotTable, 'user_id', 'role_id');
    // }
    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'status','can_edit_order_date',
        'user_type',
        'location_id'
    ];
    public $timestamps = false; // Assuming you don't need timestamps for this model

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];
   
    
    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
   
     

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }
}
