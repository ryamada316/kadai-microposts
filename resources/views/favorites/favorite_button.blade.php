                        @if (Auth::id() != $micropost->user_id)
                            @if (Auth::user()->is_favoriting($micropost->id))
                                {{-- お気に入り追加・解除のフォーム --}}
                                {!! Form::open(['route' => ['microposts.unfavorite', $micropost->id], 'method' => 'delete']) !!}
                                    {!! Form::submit('UnFavorite', ['class' => 'btn btn-danger btn-sm']) !!}
                                {!! Form::close() !!}
                            @else
                                {!! Form::open(['route' => ['microposts.favorite', $micropost->id]]) !!}
                                    {!! Form::submit('Favorite', ['class' => 'btn btn-primary btn-sm']) !!}
                                {!! Form::close() !!}
                            @endif
                        @endif