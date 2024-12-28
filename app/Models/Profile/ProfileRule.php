<?php

namespace App\Models\Profile;

use App\Models\Rule\Rule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileRule extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'profile_rule';

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
    protected $fillable = ['profile_id', 'rule_id'];

    /**
     * Os atributos que não são atribuíveis em massa
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Retorna o perfil
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
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
