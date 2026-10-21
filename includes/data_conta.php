<?php

// valida e formata a data de criacao da conta sem alterar o fuso horario e retorna nao informada quando o valor e invalido

function formatarDataCriacaoConta($valor) // exibe o horario do banco sem conversao de fuso
{
    if (!is_string($valor) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:\.\d{1,6})?$/D', $valor)) {
        return 'Não informada';
    }
    $formato = strpos($valor, '.') === false ? '!Y-m-d H:i:s' : '!Y-m-d H:i:s.u';
    $data = DateTimeImmutable::createFromFormat($formato, $valor);
    $erros = DateTimeImmutable::getLastErrors();
    if (!$data || ($erros !== false && ($erros['warning_count'] || $erros['error_count']))) { // rejeita datas corrigidas automaticamente pelo php
        return 'Não informada';
    }
    return $data->format('d/m/Y \à\s H:i');
}
