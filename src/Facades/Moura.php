<?php

namespace CSWeb\Moura\Facades;

use CSWeb\Moura\DTO\{Socio, Dependente};
use Illuminate\Support\{Collection, Facades\Facade};

/**
 * Moura
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package CSWeb\Moura
 * @method static Socio getSocio(string $cpf, string $password)
 * @method static Collection|Dependente[] getDependentes(string $cpf)
 */
class Moura extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CSWeb\Moura\Moura::class;
    }
}