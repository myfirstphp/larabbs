<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, MustVerifyEmailTrait;
    use Notifiable {
        notify as protected laravelNotify;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
//截取数据库中的日期，去掉了时间部分
    public function date_limit()
    {
        return($this->created_at->toDateString());
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $model->user_id == $this->id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function scopeRecent($query)
    {
        return $query->orderby('id', 'desc');
    }

    //重写notify
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }
        //这个就是原来的notify方法的alias
        $this->laravelNotify($instance);
    }


    //把所有消息通知标记为已读
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        //这个是框架自带的markAsRead；而我们现在定义的是对框架的重写markAsRead
        $this->unreadNotifications->markAsRead();
    }

}


