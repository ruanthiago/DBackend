<?php

namespace App\Models\Rule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'modules';

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
     * Retorna as regras do módulo
     *
     * @return HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * Retorna as permissões do módulo
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'rules')
            ->withPivot(['id'])
            ->wherePivotNull('deleted_at');
    }
}
