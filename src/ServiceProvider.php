<?php

namespace ChastePhp\LaravelWheres;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('wheres', function (array $where, $method = 'where') {
            foreach ($where as $key => $value) {
                $type = gettype($value);
                // recursive
                if ($type === 'array' && preg_match("/^(AND|OR)(\s+#.*)?$/", $key, $matches)) {
                    $this->where(function ($query) use ($value, $matches) {
                        $query->wheres($value, ['AND' => 'where', 'OR' => 'orWhere'][$matches[1]]);
                    });
                    continue;
                }

                // fallback
                if ($type === 'array' && is_int($key)) {
                    $this->{$method}(...$value);
                    continue;
                }

                preg_match('/([a-zA-Z0-9_\.]+)(\[(?<operator>\>\=?|\<\=?|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);
                $column = $match[1];

                if (isset($match['operator'])) {
                    $operator = $match['operator'];

                    // custom
                    $appends = ['<>' => 'Between', '><' => 'NotBetween'];
                    if (isset($appends[$operator])) {
                        $this->{$method.$appends[$operator]}($column, $value);
                        continue;
                    }

                    if ($operator === '!' && is_array($value)) {
                        $this->{$method.'NotIn'}($column, $value);
                        continue;
                    }

                    $_operator = ['!' => '!=', '~' => 'like', '!~' => 'not like'][$operator] ?? $operator;
                    if (!$this->invalidOperator($_operator)) {
                        $this->{$method}($column, $_operator, $value);
                        continue;
                    }

                    throw new \InvalidArgumentException('Not support operator '.$operator);

                } else {
                    switch ($type) {
                        case 'array':
                            $this->{$method.'In'}($column, $value);
                            break;
                        default:
                        {
                            $this->{$method}($column, $value);
                        }
                    }
                }
            }

            return $this;
        });
    }
}
