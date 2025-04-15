<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class TaskFilter extends ApiFilter
{
    protected $safeParams = [
        'userId' => ['eq'],
        'status' => ['eq'],
    ];

    protected $columnMap = [
        'userId' => 'user_id',
        'status' => 'status',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}
