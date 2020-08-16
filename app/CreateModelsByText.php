<?php

namespace App;

trait CreateModelsByText {

    public static function createMulti($text, array $common) {
        /** @var Illuminate\Database\Eloquent\Model */
        $class = static::class;
        $labels = preg_split("/[\r\n]+/", trim($text), -1, PREG_SPLIT_NO_EMPTY);
        if ($labels) {
            foreach ($labels as $label) {
                $class::create(array_merge($common, [
                    "label" => $label,
                ]));
            }
        }
    }

}
