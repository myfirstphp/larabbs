<?php

use App\Models\Topic;
use Illuminate\Support\Collection;

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

Route::get('/', 'PagesController@root')->name('root');


//the frame auto create route
//Email Verification Routes 默认是不展开,要展开的话需要加上['verify' => true]
Auth::routes(['verify' => true]);

Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'edit']]);


Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);


Route::resource('categories', 'CategoriesController', ['only' => ['show']]);

Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');
Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);

Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);

Route::get('test/{topic}', function (Topic $topic){
    dump($topic->replies);
    foreach($topic->replies as $reply)
    {
        dump($reply);
    }

});

Route::get('test', function (){
    $obj = new Bar;
    #$obj = new Collection(array(0=>'a',1=>'b',2=>'c',3=>'d',4=>'e',5=>'f',6=>'g',7=>'h',8=>'i',9=>'j'));
    #dump($obj);
    foreach($obj as $reply)
    {
        dump($reply);
    }
});

class Foo
{
    protected $item = [];
    public function __construct()
    {
        $this->item[0] = new Bar;
        $this->item[1] = new Bar;
    }
}

class Bar
{
    protected $a = 1;
    protected $b = 2;
    protected $item = array(0=>'a',1=>'b',2=>'c',3=>'d',4=>'e',5=>'f',6=>'g',7=>'h',8=>'i',9=>'j');
}

class obj implements arrayaccess {
    protected $container = array();
    public function __construct() {
        $this->container = array(
            "one"   => 1,
            "two"   => 2,
            "three" => 3,
        );
    }
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}




