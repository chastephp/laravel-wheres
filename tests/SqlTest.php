<?php
/**
 * User: wangkuan
 * Date: 2022/5/7
 * Time: 10:50
 */

namespace ChastePhp\LaravelWheres\Tests;

use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;

class SqlTest extends TestCase
{

    public function testBase()
    {
        $expected = 'select * from `users` where `email` = ?';
        $actual = DB::table('users')->wheres([
            "email" => "foo@bar.com"
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testGt()
    {
        $expected = 'select * from `users` where `user_id` > ?';
        $actual = DB::table('users')->wheres([
            "user_id[>]" => 200
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testGte()
    {
        $expected = 'select * from `users` where `user_id` >= ?';
        $actual = DB::table('users')->wheres([
            "user_id[>=]" => 200
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testNotEqual()
    {
        $expected = 'select * from `users` where `user_id` != ?';
        $actual = DB::table('users')->wheres([
            "user_id[!]" => 200
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testBetween()
    {
        $expected = 'select * from `users` where `age` between ? and ?';
        $actual = DB::table('users')->wheres([
            "age[<>]" => [20, 50]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testDateBetween()
    {
        $expected = 'select * from `users` where `birthday` between ? and ?';
        $actual = DB::table('users')->wheres([
            "birthday[<>]" => [date("Y-m-d", strtotime('-30 days')), date("Y-m-d")]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testDateNotBetween()
    {
        $expected = 'select * from `users` where `birthday` not between ? and ?';
        $actual = DB::table('users')->wheres([
            "birthday[><]" => [date("Y-m-d", strtotime('-30 days')), date("Y-m-d")]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testOr()
    {
        $expected = 'select * from `users` where (`user_id` in (?, ?) or `email` in (?, ?, ?))';
        $actual = DB::table('users')->wheres([
            "OR" => [
                "user_id" => [2, 123],
                "email" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"]
            ]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testAnd()
    {
        $expected = 'select * from `users` where (`user_name` != ? and `user_id` != ? and `email` not in (?, ?, ?) and `city` is not null and `promoted` != ?)';
        $actual = DB::table('users')->wheres([
            "AND" => [
                "user_name[!]" => "foo",
                "user_id[!]" => 1024,
                "email[!]" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"],
                "city[!]" => null,
                "promoted[!]" => true
            ]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testAndOr()
    {
        $expected = 'select * from `users` where ((`user_name` = ? or `email` = ?) and (`user_name` = ? or `email` = ?))';
        $actual = DB::table('users')->wheres([
            "AND #1" => [
                "OR #the first condition" => [
                    "user_name" => "foo",
                    "email" => "foo@bar.com"
                ],
                "OR #the second condition" => [
                    "user_name" => "bar",
                    "email" => "bar@foo.com"
                ]
            ]
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testLike()
    {
        $expected = 'select * from `users` where `nickname` like ? and `nickname` like ?';
        $actual = DB::table('users')->wheres([
            "nickname[~]#1" => '%foo%',
            "nickname[~]#2" => '%bar%',
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testNotLike()
    {
        $expected = 'select * from `users` where `nickname` not like ? and `nickname` not like ?';
        $actual = DB::table('users')->wheres([
            "nickname[!~]#1" => '%foo%',
            "nickname[!~]#2" => '%bar%',
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testQuickOr()
    {
        $expected = 'select * from `users` where (`province` like ? or `city` like ?)';
        $actual = DB::table('users')->wheres([
            "province|city[~]" => "%stan%"
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    public function testQuickAnd()
    {
        $expected = 'select * from `users` where (`province` like ? and `city` like ?)';
        $actual = DB::table('users')->wheres([
            "province&city[~]" => "%stan%"
        ])->toSql();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'ChastePhp\LaravelWheres\ServiceProvider',
        ];
    }
}