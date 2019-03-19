<div class="panel panel-info">
    <ul class="add-shopnav">

        @foreach(config('menu')[Yunshop::$currentItems[0]]['child'][Yunshop::$currentItems[1]]['child'] as $key=>$value)

            @if(isset($value['menu']) && $value['menu'] == 1 && can($key))
                @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))


                    <li>
                        <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                            {{$value['name']}}
                        </a>
                    </li>


                @elseif($value['menu'] == 1)
                    <li>
                        <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                            {{$value['name']}}
                        </a>
                    </li>
                @endif
            @endif
        @endforeach

    </ul>
</div>