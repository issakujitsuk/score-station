<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model {

    const TOKEN_LENGTH = 10;

    protected $fillable = ["title", "password", "point_min", "point_max", "expiry"];
    protected $casts = ["expiry" => 'datetime:Y/m/d H:i'];

    /**
     * create new poll.token
     * 
     * @staticvar type $chars
     * @return string
     */
    public static function createToken() {
        static $chars = null;
        if (!isset($chars)) {
            $chars = array_flip(array_merge(
                            range('a', 'z'), range('A', 'Z'), range('0', '9')
            ));
        }
        $token = '';
        for ($i = 0; $i < self::TOKEN_LENGTH; ++$i) {
            $token .= array_rand($chars);
        }
        return $token;
    }

    /**
     * Check if $password match
     * 
     * @param string $password
     * @return bool match
     */
    public function checkPassword($password) {
        return password_verify($password, $this->getAttribute("password"));
    }

    public function storeToken() {
        for ($i = 3; $i; --$i) {
            $this->token = self::createToken();
            try {
                $this->save();
                return;
            } catch (\PDOException $ex) {
                // Ignore unique constraint failures
                if ($ex->getCode() != 23000) {
                    throw $ex;
                }
            }
        }
        throw new \Exception("Failed to create unique poll-token.");
    }

    public function getOpenAttributes() {
        $attributes = [];
        foreach (["title", "point_min", "point_max"] as $k) {
            $attributes[$k] = $this->getAttribute($k);
        }
        $attributes["expiry"] = $this->expiry->format("Y/m/d H:i");
        return $attributes;
    }
    
    public function isClosed() {
        return $this->expiry <= new \DateTime();
    }

}
