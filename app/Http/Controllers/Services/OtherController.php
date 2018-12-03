<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurlController;

class OtherController extends CurlController
{
      public function getComment(Request $request){
    if($request->isMethod('POST')){
      $args['url'] = 'https://graph.facebook.com/v1.0/' . $request->id . '?fields=comments.limit(1000)%7Bmessage%2Cfrom%7D&access_token=' . DEFAULT_TOKEN;
      $args['encoding'] = 'bz';
//       dd($this->curl($args));
      $results = json_decode($this->curl($args)['exec'])->comments->data;
      foreach($results as $key => $comment){
        echo '<b>' . $comment->from->name . '</b>' . '  ' . $comment->message . '<br>';
      }
      dd('DONE');
    }
    return view('services.get_comment');
  }
}
