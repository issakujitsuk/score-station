<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {

    use CreateModelsByText;

    protected $fillable = ["poll_id", "label"];

    public static function getOpenAttributes(\ArrayAccess $questions) {
        $list = [];
        foreach ($questions as $item) {
            $attributes = [];
            foreach (["id", "label"] as $k) {
                $attributes[$k] = $item->getAttribute($k);
            }
            $list[] = $attributes;
        }
        return $list;
    }

}
