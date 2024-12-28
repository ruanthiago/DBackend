<?php

namespace App\Models\Profile;

use App\Models\Rule\Rule;
use App\Models\Tenant\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * Indica se o modelo deve ter carimbo de data/hora
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Os atributos que são atribuíveis em massa
     *
     * @var array
     */
    protected $fillable = ['name', 'tenant_id'];

    /**
     * Os atributos que não são atribuíveis em massa
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Retorna o inquilino
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Retorna as regras do perfil
     *
     * @return HasMany
     */
    public function profileRules(): HasMany
    {
        return $this->hasMany(ProfileRule::class);
    }

    /**
     * Retorna as regras
     *
     * @return BelongsToMany
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, 'profile_rule')
            ->withPivot(['id'])
            ->wherePivotNull('deleted_at');
    }

    /**
     * Condição para filtrar pelo inquilino
     *
     * @param Builder $query
     * @param Tenant $tenant
     * @return void
     */
    public function scopeWhereTenant(Builder $query, Tenant $tenant): void
    {
        $query->where('tenant_id', $tenant->id);
    }
}
