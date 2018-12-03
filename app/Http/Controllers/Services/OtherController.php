<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurlController;

class OtherController extends CurlController
{
  public function getComment(Request $request){
    if($request->isMethod('POST')){
      $id = $this->getIDPostFromURL($request->link);
      if($id){
        $args['url'] = 'https://graph.facebook.com/v1.0/' . $id . '?fields=comments.limit(1000)%7Bmessage%2Cfrom%7D&access_token=' . DEFAULT_TOKEN;
        $args['encoding'] = 'bz';
        $results = json_decode($this->curl($args)['exec'])->comments->data;
        foreach($results as $key => $comment){
          echo '<b>' . $comment->from->name . '</b>' . '  ' . $comment->message . '<br>';
        }
        dd('DONE');
      }
      else{
        dd('Sai định dạng, vui lòng copy link bỏ vào, ez');
      }
      
    }
    return view('services.get_comment');
  }

  private function getIDPostFromURL($link){
		return (preg_match('/posts\/([0-9]+)/', $link, $list) || preg_match('/fbid\=([0-9]+)\&set\=/', $link, $list) || preg_match('/videos\/([0-9]+)\//', $link, $list) || preg_match('/media_set\?set\=a\.([0-9]+)\.([0-9]+)\.([0-9]+)\&type/', $link, $list)) ? $list[1] : false;
  }
  
  public function test(Request $request){
    dd('a');
  }
}
