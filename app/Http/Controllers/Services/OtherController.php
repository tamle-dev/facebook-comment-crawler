<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurlController;

use Session;

class OtherController extends CurlController
{
  public function getComment(Request $request){
    
    if($request->isMethod('POST')){
      if(isset($request->link)){
        if($this->checkTokenLive(DEFAULT_TOKEN)){
          $id = $this->getIDPostFromURL($request->link);
          if($id){
            $args['url'] = URL_GRAPH_V10 . $id . '?fields=comments.limit(1000)%7Bmessage%2Cfrom%7D&access_token=' . DEFAULT_TOKEN;
            $args['encoding'] = 'bz';
            $results = json_decode($this->curl($args)['exec'])->comments->data;
            foreach($results as $key => $comment){
              echo '<b>' . $comment->from->name . '</b>' . '  ' . $comment->message . '<br>';
            }
            dd('DONE');
          }
          else{
    //         Session::flash('danger', 'Sai định dạng! Vui lòng copy lại link'); 
            return \Redirect::back()->withWarning('Sai định dạng! Vui lòng thử lại. Cảm ơn.');
          } 
        }
        else{
          return \Redirect::back()->withWarning('[TOKEN EXPIRED] Vui lòng liên hệ hỗ trợ.');
        }
      }
      else{
        return \Redirect::back()->withWarning('Vui lòng không spam. Cảm ơn.');
      }
    }
    return view('services.get_comment');
  }

  private function getIDPostFromURL($link){
		return (preg_match('/posts\/([0-9]+)/', $link, $list) || preg_match('/fbid\=([0-9]+)\&set\=/', $link, $list) || preg_match('/videos\/([0-9]+)\//', $link, $list) || preg_match('/media_set\?set\=a\.([0-9]+)\.([0-9]+)\.([0-9]+)\&type/', $link, $list)) ? $list[1] : false;
  }
  
  private function checkTokenLive($token){
    $args['url'] = URL_GRAPH_V10 . 'me?access_token=' . $token;
    $args['encoding'] = 'bz';
    $results = json_decode($this->curl($args)['exec']);
    return (isset($results->error->code) && ($results->error->code == 190)) ? false : true;
  }
}
