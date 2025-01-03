<?php

namespace App\Models\Rule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'permissions';

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
     * Retorna as regras da permissão
     *
     * @return HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * Retorna os módulos da permissão
     *
     * @return BelongsToMany
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'rules')
            ->withPivot(['id'])
            ->wherePivotNull('deleted_at');
    }
}
