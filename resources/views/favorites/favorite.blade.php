@if (count($favorites) > 0)
    <ul class="list-unstyled">
        @foreach ($favorites as $favorite)
            <li class="media mb-3">
                {{-- 投稿の所有者のメールアドレスをもとにGravatarを取得して表示 --}}
                <img class="mr-2 rounded" src="{{ Gravatar::get($favorite->user->email, ['size' => 50]) }}" alt="">
                <div class="media-body">

                    <div>
                        {{-- 投稿の所有者のユーザ詳細ページへのリンク --}}
                        {!! link_to_route('users.show', $favorite->user->name, ['user' => $favorite->user->id]) !!}
                        <span class="text-muted">posted at {{ $favorite->created_at }}</span>
                    </div>
                    
                    <div>
                        {{-- 投稿内容 --}}
                        <p class="mb-0">{!! nl2br(e($favorite->content)) !!}</p>
                    </div>

                    <div>
                        {{--@if (Auth::id() != $favorite->user_id)--}}
                            @if (Auth::user()->is_favoriting($favorite->id))
                                {{-- お気に入り追加・解除のフォーム --}}
                                {!! Form::open(['route' => ['microposts.unfavorite', $favorite->id], 'method' => 'delete']) !!}
                                    {!! Form::submit('UnFavorite', ['class' => 'btn btn-danger btn-sm']) !!}
                                {!! Form::close() !!}
                            @else
                                {!! Form::open(['route' => ['microposts.favorite', $favorite->id]]) !!}
                                    {!! Form::submit('Favorite', ['class' => 'btn btn-primary btn-sm']) !!}
                                {!! Form::close() !!}
                            @endif
                        {{--@endif--}}
                    </div>

                </div>
            </li>
        @endforeach
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $favorites->links() }}
@endif