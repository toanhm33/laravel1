<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 
	'HomeController@index')->name('homepage');

use App\Category;
Route::get('get-list-category', function(){
	$cates = Category::all();
	$listSortedCate = get_options($cates, 0, "", 10);
	dd($listSortedCate);
});

Route::get('ajax-category', function(){
	$cate = Category::where('parent_id', 0)->get();
	return view('ajax-category', compact('cate'));
});

use Illuminate\Http\Request;
Route::post('get-child', function(Request $request){
	$childs = Category::where('parent_id', $request->parentId)->get();
	return response()->json($childs);
})->name('cate.child');

// Auth route
Route::get('cp-login', 'Auth\LoginController@login')->name('login');
Route::post('cp-login', 'Auth\LoginController@postLogin');
Route::any('logout', function(){
	Auth::logout();
	return redirect(route('login'));
})->name('logout');

Route::get('category/{cateName?}', 'Homecontroller@cate');
Route::get('tim-kiem', 'HomeController@search')->name('client.search');
Route::view('massive-tpl/something', 'layout.massive');

use Illuminate\Support\Facades\Mail;
Route::get('send-mail', function() {
    $username = 'thienth';
	Mail::send('mail_template.test-send-mail', compact('username'), function ($message) {
	    $message->to('thienth32@gmail.com', 'Thien tran');
	    $message->cc('kenjav96@gmail.com', 'Dũng thần dâm');
	    $message->replyTo('thienth@fpt.edu.vn', 'Mr.Thien');
	    $message->subject('test email');
	});
	return 'done!';
});


use App\PasswordReset;
use Carbon\Carbon;
use App\User;


Route::get(App\Category::CATE_URL.'{cateSlug}', 'HomeController@cate')->name('cate.detail');


Route::get('list-post', function(Request $request){
	$posts = App\Post::take(10)->get();
	$likedIds = $request->cookie('liked_id');
	$likedIds = explode('|', $likedIds);
	for ($i=0; $i < count($posts); $i++) { 
		if(in_array($posts[$i]->id, $likedIds)){
			$posts[$i]->liked = true;
		}else{
			$posts[$i]->liked = false;
		}
	}
	return view('check-cookie', compact('posts'));
});

Route::get('clicked-like/{id}', function(Request $request){
	// get all cookies
	$likedIds = $request->cookie('liked_id');
	$result = false;
	if(!$likedIds){
		$likedIds = "$request->id";
		$result = true;
	}else{
		$likedIds = explode('|', $likedIds);
		if(!in_array($request->id, $likedIds)){
			array_push($likedIds, $request->id);
			$likedIds = implode('|', $likedIds);
			$result = true;
		}else{
			for ($i=count($likedIds)-1; $i >= 0 ; $i--) { 
				if($likedIds[$i] == $request->id){
					array_splice($likedIds, $i, 1);
					break;
				}
			}
			$likedIds = implode('|', $likedIds);
		}
	}
	return response()->json($result)->cookie('liked_id', $likedIds, 60*24*365);
})->name('clicked-like');









Route::get('/{slugUrl}', 'HomeController@detail')->name('post.detail');

