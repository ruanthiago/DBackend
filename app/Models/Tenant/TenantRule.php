<?php

namespace App\Models\Tenant;

use App\Models\Rule\Rule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantRule extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'tenant_rule';

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
    protected $fillable = ['tenant_id', 'rule_id'];

    /**
     * Os atributos que não são atribuíveis em massa
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Retorna o inquilino
     *
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Retorna a regra
     *
     * @return BelongsTo
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
