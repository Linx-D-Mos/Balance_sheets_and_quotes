<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    public function displayName(): Attribute
    {
        return Attribute::make(
            get: fn() => filled($this->company_name)
                ? $this->company_name
                : trim("{$this->first_name} {$this->last_name}"),
        );
    }
}
