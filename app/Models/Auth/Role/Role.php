<?php

namespace App\Models\Auth\Role;

use App\Models\Auth\Role\Traits\Scopes\RoleScopes;
use App\Models\Auth\Role\Traits\Relations\RoleRelations;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * App\Models\Auth\Role\Role
 *
 * @property int $id
 * @property string $name
 * @property int $weight
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 * @property \Illuminate\Database\Eloquent\Collection $permission_documents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\User\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role sort($direction = 'asc')
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\Role\Role whereWeight($value)
 * @mixin \Eloquent
 */
class Role extends Eloquent
{
    use RoleScopes,
        RoleRelations;

    /**
     * The database table used by the model.
     *
     * @var string
     */
//    protected $table = 'roles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weight',
        'description'];

    public function permission_documents()
    {
        return $this->hasMany(\App\Models\PermissionDocument::class);
    }
}
