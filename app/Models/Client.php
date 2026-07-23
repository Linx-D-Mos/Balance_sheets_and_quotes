<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    //Accesor

    /**
     * Obtiene el nombre de visualización unificado (Persona Natural o Empresa).
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->company_name)) {
            return $this->company_name;
        }

        return trim("{$this->first_name} {$this->last_name}");
    }
}
