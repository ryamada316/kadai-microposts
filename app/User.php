<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }

    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
    }
    

    /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    public function unfollow($userId)
        {
            // すでにフォローしているかの確認
            $exist = $this->is_following($userId);
            // 対象が自分自身かどうかの確認
            $its_me = $this->id == $userId;
    
            if ($exist && !$its_me) {
                // すでにフォローしていればフォローを外す
                $this->followings()->detach($userId);
                return true;
            } else {
                // 未フォローであれば何もしない
                return false;
            }
        }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }

    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }

    /**
     * このユーザがお気に入り登録した投稿。（Micropostモデルとの関係を定義）
     */
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'microposts_id')->withTimestamps();
    }


    /**
     * お気に入り中であるか調べる。登録済みならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_favoriting($userId)
    {
        // 
        return $this->favoritings()->where('microposts_id', $userId)->exists();
    }

    /**
     * お気に入り追加機能
     */
    public function favorite($micropostId)
    {
        // すでにしているかの確認
        $exist = $this->is_favoriting($micropostId);

        if ($exist ) {
            // すでにしていれば何もしない
            return false;
        } else {
            // 未であればする
            $this->favoritings()->attach($micropostId);
            return true;
        }
    }

    /**
     * お気に入り解除機能
     */
    public function unfavorite($micropostId)
        {
            // すでにお気に入りしているかの確認
            $exist = $this->is_favoriting($micropostId);
    
            if ($exist ) {
                // すでにお気に入りしていれば外す
                $this->favoritings()->detach($micropostId);
                return true;
            } else {
                // 未であれば何もしない
                return false;
            }
        }


  /**
     * このユーザがお気に入り登録中の投稿。（ Micropostモデルとの関係を定義）
     */
    public function favoritings()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'microposts_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function favoriters()
    {
        return $this->belongsToMany(User::class, 'favorites', 'microposts_id', 'user_id')->withTimestamps();
    }


}
