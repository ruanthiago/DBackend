<?php

namespace App\Models\Tenant;

use App\Models\Rule\Rule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'tenants';

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
    protected $fillable = ['name'];

    /**
     * Os atributos que não são atribuíveis em massa
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Retorna as regras do inquilino
     *
     * @return HasMany
     */
    public function tenantRules(): HasMany
    {
        return $this->hasMany(TenantRule::class);
    }

    /**
     * Retorna as regras
     *
     * @return BelongsToMany
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, 'tenant_rule')
            ->withPivot(['id'])
            ->wherePivotNull('deleted_at');
    }
}
