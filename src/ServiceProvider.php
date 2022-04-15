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
        /**
         * wheres
         * @param  $where array
         * @param  $method string where orWhere
         * @return static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
         */
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

                preg_match('/([a-zA-Z0-9_\.\|\&]+)(\[(?<operator>\>\=?|\<\=?|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);
                $column = $match[1];
                
                $columns = preg_split('/(\||\&)/', $column, -1, PREG_SPLIT_DELIM_CAPTURE);
                if (count($columns) >= 3) { // support quick &(and) |(or)
                    $full_operator = $match[2] ?? '';
                    $this->where(function ($query) use ($columns, $value, $full_operator) {
                        $delimiter = '&';
                        foreach ($columns as $col) {
                            if (in_array($col, ['|', '&'])) {
                                $delimiter = $col;
                                continue;
                            }
                            $query->wheres([$col . $full_operator => $value], ['&' => 'where', '|' => 'orWhere'][$delimiter]);
                        }
                    });
                    continue;
                }

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
                            $this->{$method.'In'}($column, (array) $value);
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
        
        /**
         * orders
         * @param  $orders array
         * @return static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
         */
        Builder::macro('ordersBy', function (array $orders = []) {
            foreach ($orders as $column => $direction) {
                if (is_int($column)) {
                    $column = $direction;
                    $direction = 'ASC';
                }
                $this->orderBy($column, $direction);
            }

            return $this;
        });
    }
}
