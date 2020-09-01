<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GenreCategoryRelationship implements Rule
{
    private $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function passes($attribute, $value)
    {
        $genres = array_unique($value);
        foreach ($genres as $genre_id) {
            $result = DB::table('category_genre')->whereIn('category_id',$this->categories)
            ->where('genre_id','=',$genre_id)->get()->toArray();
            if (sizeof($result) == 0){
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'The genre must be related to the category';
    }
}
