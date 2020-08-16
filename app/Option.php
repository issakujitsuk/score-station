<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model {

    use CreateModelsByText;

    protected $fillable = ["poll_id", "label"];

    public static function getOpenAttributes(\ArrayAccess $options) {
        $list = [];
        foreach ($options as $item) {
            $attributes = [];
            foreach (["id", "label"] as $k) {
                $attributes[$k] = $item->getAttribute($k);
            }
            $list[] = $attributes;
        }
        return $list;
    }

}
