<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{
    use HasFactory, Notifiable, HasApiTokens ;

    protected $table='posts';

    protected $fillable = [
        'nameAnimal',
        'typeAnimal',
        'description',
        "image",
        "user_id",
        "adopted",
        'vaccines_mask'
    ];

    protected $appends = ['vaccines'];

    protected function casts(): array
    {
        return [
            "adopted"=>  "boolean",
            "verificado"=>  "boolean"
        ];
    }

    public function getImageAttribute($value)
    {
        return base64_encode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function userAdopted()
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->hasOne(Contrato::class, "post_id");
    }

    const VACC_RABIA       = 1 << 0; // 00001 → 1
    const VACC_PARVOVIRUS  = 1 << 1; // 00010 → 2
    const VACC_MOQUILLO    = 1 << 2; // 00100 → 4
    const VACC_LEUCEMIA    = 1 << 3; // 01000 → 8
    const VACC_PARAINFLUEN = 1 << 4; // 10000 → 16


    public static $VACCINES = [
        'rabia'       => self::VACC_RABIA,
        'parvovirus'  => self::VACC_PARVOVIRUS,
        'moquillo'    => self::VACC_MOQUILLO,
        'leucemia'    => self::VACC_LEUCEMIA,
        'parainfluen' => self::VACC_PARAINFLUEN,
    ];

    /**
     * Accessor que construye un array con los nombres de vacunas activas.
     * Al serializar (a JSON), aparecerá "vaccines": ["rabia","moquillo",…].
     */
    public function getVaccinesAttribute(): array
    {
        $mask   = $this->attributes['vaccines_mask'] ?? 0;
        $result = [];

        foreach (self::$VACCINES as $label => $bitValue) {
            if ( ($mask & $bitValue) === $bitValue ) {
                $result[] = $label;
            }
        }

        return $result;
    }

    /**
     * Mutator que recibe un array con nombres de vacunas y asigna el bitmask apropiado.
     * Ejemplo: $post->vaccines = ['rabia','leucemia'];
     * Internamente hará 1 | 8 = 9 y guardará vaccines_mask = 9.
     */
    public function setVaccinesAttribute(array $vaccineNames): void
    {
        $mask = 0;
        foreach ($vaccineNames as $name) {
            if (isset(static::$VACCINES[$name])) {
                $mask |= static::$VACCINES[$name];
            }
        }
        $this->attributes['vaccines_mask'] = $mask;
    }

    /**
     * Método auxiliar: verificar si este post (animal) tiene X vacuna.
     * Uso: $post->hasVaccine('rabia') → bool
     */
    public function hasVaccine(string $vaccineName): bool
    {
        if (!isset(self::$VACCINES[$vaccineName])) {
            return false;
        }
        $bitValue = self::$VACCINES[$vaccineName];
        return (($this->attributes['vaccines_mask'] & $bitValue) === $bitValue);
    }

    /**
     * Agrega una vacuna (pone a 1 el bit correspondiente).
     * Uso: $post->addVaccine('moquillo'); $post->save();
     */
    public function addVaccine(string $vaccineName): void
    {
        if (isset(self::$VACCINES[$vaccineName])) {
            $this->attributes['vaccines_mask'] =
                ($this->attributes['vaccines_mask'] ?? 0)
                | self::$VACCINES[$vaccineName];
        }
    }

    /**
     * Quita una vacuna (pone a 0 el bit correspondiente).
     * Uso: $post->removeVaccine('rabia'); $post->save();
     */
    public function removeVaccine(string $vaccineName): void
    {
        if (isset(self::$VACCINES[$vaccineName])) {
            $this->attributes['vaccines_mask'] =
                ($this->attributes['vaccines_mask'] ?? 0)
                & ~self::$VACCINES[$vaccineName];
        }
    }
}
