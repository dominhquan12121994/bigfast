@php
    $item_count = 0;
    if ( $count ) {
        $item_count = $count;
    }
@endphp
@if ( is_array($val) )
    <li @if( $item_count > 5) class="hidden-request" @endif >{{$key}}</li>
    @foreach ($val as $index => $item )
        <ul class="hidden-request">
            @include('Systems::logs.shared.item-children', ['count' => $item_count, 'key' => $index, 'val' => $item])
        </ul>
    @endforeach
@else 
    <li @if( $item_count > 5) class="hidden-request" @endif >{{$key}}: {{ $val }}</li>
@endif