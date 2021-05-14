<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function store(ReplyRequest $request, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->user_id = Auth::id();
        $reply->topic_id = $request->topic_id;
        $ret = $reply->save();
        #dd($ret);
        // xss 过滤后回复内容长度小于2，则评论创建失败

        if (!$ret) {
            return redirect()->to($reply->topic->link())->with('danger', '最少两个字符！');
        }
        return redirect()->to($reply->topic->link())->with('success', '评论创建成功！');

    }

	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->to($reply->topic->link())->with('success', '评论删除成功！');
	}
}