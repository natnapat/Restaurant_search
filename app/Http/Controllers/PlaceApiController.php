<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PlaceApiController extends Controller
{
    //function to return restaurant list
    function restaurant($keyword){
        $restaurants = [];

        //check if cahce with the keyword exists
        if(Cache::has($keyword)){
            $restaurants = Cache::get($keyword);
        } else {
            $API_key = 'AIzaSyByNZyJmwVZiHJ7af4Y37n9C-C9iUZppVc';

            //get data from google place API
            $datas = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurants%20'.$keyword.'&type=restaurant&key='.$API_key);
            
            //create restaurant list
            foreach($datas['results'] as $data){
                $restaurant = [
                    'name' => $data['name'],
                    'location' => [ 'lat' => $data['geometry']['location']['lat'],'lng' => $data['geometry']['location']['lng']],
                    'address' => $data['formatted_address'],
                    'rating' => $data['rating'],
                    'user_ratings_total' => isset($data['user_ratings_total'])?$data['user_ratings_total']:0, //check if json field exists or not and define default value
                    'is_open' => isset($data['opening_hours']['open_now'])?$data['opening_hours']['open_now']:0,
                    'price_level' => isset($data['price_level'])?$data['price_level']:0,
                    'photo_reference' => isset($data['photos'])?$data['photos'][0]['photo_reference']:'',
                ];
                array_push($restaurants, $restaurant);   
            }
            
            //cache the new restaurant list and set expiry
            Cache::put($keyword,$restaurants, $seconds = 60);
        }

        //return restaurant list as response
        return response()->json($restaurants,200);
    }
}
