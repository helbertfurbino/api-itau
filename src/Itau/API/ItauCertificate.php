<?php

namespace Itau\API;

use Exception;
use Itau\API\Exception\ItauException;

/**
 * Class Itau
 *
 * @package Itau\API
 */
class ItauCertificate
{
    public function requestCertificate(string $token, string $certificadoCSR)
    {
        $endpoint = 'https://sts.itau.com.br/seguranca/v1/certificado/solicitacao';
        $headers = [
            'Content-Type: text/plain',
            'Authorization: Bearer ' . $token
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $certificadoCSR,
        ]);
        echo 'ANTES DA RESONSE';
        try {
            $response = curl_exec($curl);
        } catch (Exception $e) {
            throw new ItauException($e->getMessage(), 100);
        }
echo 'Passou da response';
var_dump($response);
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new ItauException('CURL Error: ' . $error, 100);
        }

        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
var_dump($statusCode);
        // Verifica status HTTP
        if ($statusCode >= 400) {
            $obj = json_decode($response);
            $acao = '';
            $mensagem = $obj->mensagem;
            if(isset($obj->acao)){
                $acao = "- {$obj->acao}";
            }
            throw new ItauException("HTTP Error: $statusCode - $mensagem {$acao}", $statusCode);
        }

        // Lógica para 204
        if ($statusCode === 204) {
            return [
                'status_code' => 204
            ];
        }

        // Verifica resposta vazia
        if (empty($response)) {
            throw new ItauException('Empty response received from server.', $statusCode);
        }

        return $response;
    }
}
