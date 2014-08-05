@extends('layouts.base')

@section('main')
    <header class="page-header">
        <hgroup>
            <h1>{{ $title }}</h1>
        </hgroup>
    </header>

    <article>
        {{ $content }}
    </article>
@stop