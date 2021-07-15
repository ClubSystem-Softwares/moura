<?php

namespace Tests;

use Carbon\Carbon;
use CSWeb\Moura\DTO\Dependente;
use CSWeb\Moura\DTO\Socio;
use CSWeb\Moura\Moura;
use CSWeb\Moura\UserNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Class MouraTest
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 */
class MouraTest extends TestCase
{
    public function testItShouldRetrieveAUserFromWebService()
    {
        $endpoint = getenv('MOURA_ENDPOINT');
        $username = getenv('MOURA_USERNAME');
        $password = getenv('MOURA_PASSWORD');

        $ws    = new Moura($endpoint, $username, $password);
        $socio = $ws->getSocio('12345678902', 'NAHXLWwuwmAidai6OQub');

        $this->assertInstanceOf(Socio::class, $socio);
        $this->assertEquals('Usuário de teste', $socio->nome);
        $this->assertInstanceOf(Carbon::class, $socio->nascimento);
        $this->assertEquals('01/01/1992', $socio->nascimento->format('d/m/Y'));
        $this->assertEquals('12345678902', $socio->cpf);
    }

    public function testItShouldThrowAnErrorWhenNoUserFound()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Sócio não encontrado');

        $endpoint = getenv('MOURA_ENDPOINT');
        $username = getenv('MOURA_USERNAME');
        $password = getenv('MOURA_PASSWORD');

        (new Moura($endpoint, $username, $password))
            ->getSocio('12345678909', 'NAHXLWwuwmAidai6OQub');
    }

    public function testItShouldGetDependentesOfSocios()
    {
        $endpoint = getenv('MOURA_ENDPOINT');
        $username = getenv('MOURA_USERNAME');
        $password = getenv('MOURA_PASSWORD');

        $ws = new Moura($endpoint, $username, $password);

        $dependentes = $ws->getDependentes('12345678902');

        $dependentes->each(function ($dependente) {
            $this->assertInstanceOf(Dependente::class, $dependente);
        });

        $dependente1 = $dependentes->offsetGet(0);
        $dependente2 = $dependentes->offsetGet(1);

        $this->assertEquals('Dependente de Teste 1', $dependente1->nome);
        $this->assertInstanceOf(Carbon::class, $dependente1->nascimento);
        $this->assertEquals('15/07/2011', $dependente1->nascimento->format('d/m/Y'));
        $this->assertEquals(119972001, $dependente1->id);
        $this->assertEquals(1, $dependente1->grau_parentesco);

        $this->assertEquals('Dependente de Teste 2', $dependente2->nome);
        $this->assertInstanceOf(Carbon::class, $dependente2->nascimento);
        $this->assertEquals('15/07/2001', $dependente2->nascimento->format('d/m/Y'));
        $this->assertEquals(119972002, $dependente2->id);
        $this->assertEquals(2, $dependente2->grau_parentesco);
    }

    public function testWhenInexistentUserForDependentesSearching()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Sócio não encontrado');

        $endpoint = getenv('MOURA_ENDPOINT');
        $username = getenv('MOURA_USERNAME');
        $password = getenv('MOURA_PASSWORD');

        (new Moura($endpoint, $username, $password))
            ->getDependentes('12345678909');
    }
}