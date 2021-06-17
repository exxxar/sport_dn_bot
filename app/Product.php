<?php

namespace App;

use ElForastero\Transliterate\Map;
use ElForastero\Transliterate\Transliterator;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'mass',
        'price',
        'portion_count',
        'image_url',
        'site_url',
        'is_active'
    ];

    protected $appends = ['translit_category'];

    public static function getLatestProducts()
    {
        return Product::where("price",">","10")->get();//->unique('category');
    }

    public static function getAllCategroies()
    {
        $categroies = Product::all()->unique('category');
        $tmp = [];
        foreach ($categroies as $categroy) {
            $transliterator = new Transliterator(Map::LANG_RU, Map::GOST_7_79_2000);
            $trans = $transliterator->slugify($categroy->category);
            array_push($tmp, json_decode(json_encode(["trans"=>$trans,"title"=>$categroy->category])));
        }



        return $tmp;
    }

    public function getTranslitCategoryAttribute()
    {
        $transliterator = new Transliterator(Map::LANG_RU, Map::GOST_7_79_2000);
        $trans = $transliterator->slugify($this->category);
        return $trans;
    }
}
