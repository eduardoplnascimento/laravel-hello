<?php

namespace App\Services;

class CalculadoraService
{
    public function sum($num1, $num2)
    {
        try {
            $soma = $num1 + $num2;
        } catch (\Throwable $th) {
            logger()->error($th);
            return [
                'success' => false,
                'message' => 'Erro ao fazer soma'
            ];
        }
        return [
            'success' => true,
            'message' => 'Soma feita com sucesso',
            'data' => $soma
        ];
    }

    public function sub($num1, $num2)
    {
        try {
            $sub = $num1 - $num2;
        } catch (\Throwable $th) {
            logger()->error($th);
            return [
                'success' => false,
                'message' => 'Erro ao fazer sub'
            ];
        }
        return [
            'success' => true,
            'message' => 'Sub feita com sucesso',
            'data' => $sub
        ];
    }

    public function mult($num1, $num2)
    {
        try {
            $mult = $num1 * $num2;
        } catch (\Throwable $th) {
            logger()->error($th);
            return [
                'success' => false,
                'message' => 'Erro ao fazer mult'
            ];
        }
        return [
            'success' => true,
            'message' => 'Mult feita com sucesso',
            'data' => $mult
        ];
    }

    public function div($num1, $num2)
    {
        try {
            if ($num2 == 0) {
                return [
                    'success' => false,
                    'message' => 'Divisão por zero'
                ];
            }

            $div = $num1 / $num2;
        } catch (\Throwable $th) {
            logger()->error($th);
            return [
                'success' => false,
                'message' => 'Erro ao fazer div'
            ];
        }
        return [
            'success' => true,
            'message' => 'Div feita com sucesso',
            'data' => $div
        ];
    }

    public function maxMult($num)
    {
        # code...
    }

    public function maxPrime($limit)
    {
        try {
            $max = 1;
            for ($num = 1; $num < $limit; $num++) {
                for ($div = 2; $div < $num; $div++) {
                    if ($num % $div === 0) {
                        break;
                    }
                }
                if ($div === $num) {
                    $max = $num;
                }
            }
        } catch (\Throwable $th) {
            logger()->error($th);
            return [
                'success' => false,
                'message' => 'Erro ao buscar número primo'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Maior número primo encontrado',
            'data' => $max
        ];
    }
}
