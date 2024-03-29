<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Traits\LastActivedAtHelper;
    use Traits\ActiveUserHelper;
    use MustVerifyEmailTrait, HasRoles;
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

    //修改器-密码
    public function setPasswordAttribute($value)
    {
        // 不等于 60，做密码加密处理
        if(strlen($value) != 60)
        {
             $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    //修改器-头像
    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! \Str::startsWith($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }

}


