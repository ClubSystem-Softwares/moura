<?php

namespace CSWeb\Moura;

use Carbon\Carbon;
use CSWeb\Moura\DTO\{Dependente, Socio};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\{Collection, Fluent};
/**
 * Moura
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package CSWeb\Moura
 */
class Moura
{
    protected string $endpoint;

    protected string $user;

    protected string $password;

    public function __construct(string $endpoint, string $user, string $password)
    {
        $this->endpoint = $endpoint;
        $this->user     = $user;
        $this->password = $password;
    }

    public function getSocio(string $cpf, string $password): Socio
    {
        $client = $this->getClient();

        $request = new Request(
            'POST',
            '/pessoas.api/api/v1/MouraMais/Login',
            ['Content-Type' => 'application/json'],
            json_encode(['Cpf' => $cpf, 'Senha' => $password])
        );

        $response = $client->send($request)->getBody()->getContents();

        $response = json_decode($response, true);

        if ($response['Sucesso'] == false) {
            throw new UserNotFoundException('Sócio não encontrado', 401);
        }

        return new Socio([
            'nome'       => data_get($response, 'Nome'),
            'nascimento' => Carbon::parse(data_get($response, 'Nascimento')),
            'cpf'        => data_get($response, 'Cpf'),
        ]);
    }

    public function getDependentes(string $cpf): Collection
    {
        $client = $this->getClient();

        $request = new Request(
            'POST',
            '/pessoas.api/api/v1/Dependentes/PorCPFColaborador',
            ['Content-Type' => 'application/json'],
            json_encode(['Cpf' => $cpf])
        );

        try {
            $response = $client->send($request)->getBody()->getContents();
            $response = json_decode($response, true);

            return collect($response)
                ->map(function (array $dependente) {
                    return new Dependente([
                        'id'              => (int)$dependente['IdDependente'],
                        'nome'            => $dependente['Nome'],
                        'nascimento'      => Carbon::parse($dependente['Nascimento']),
                        'grau_parentesco' => (int)$dependente['GrauParentesco'],
                    ]);
                });
        } catch (ClientException $e) {
            if ($e->getCode() === 400) {
                throw new UserNotFoundException('Sócio não encontrado', 401);
            }

            throw $e;
        }
    }

    protected function getClient(): Client
    {
        return new Client([
            'base_uri' => $this->endpoint,
            'auth'     => [
                $this->user,
                $this->password,
            ],
        ]);
    }
}