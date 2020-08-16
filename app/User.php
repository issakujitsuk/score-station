<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $fillable = ["name"];

    public static function getByName($name) {
        return isset($name) ? static::where("name", $name)->first() : null;
    }

    public static function createOrGet($name) {
        $user = null;
        try {
            $user = User::create(["name" => $name]);
        } catch (\PDOException $ex) {
            // Ignore unique constraint failures
            if ($ex->getCode() != 23000) {
                throw $ex;
            }
            $user = self::getByName($name);
        }
        if (!isset($user)) {
            throw new \Exception("Failed to get User");
        }
        return $user;
    }

}
