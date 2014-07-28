@extends('layouts.base')

@section('main')
    <header class="page-header">
        <hgroup>
            <h1>{{ $title }}</h1>
        </hgroup>
    </header>

    <div class="article-container">
        <article>
            <div class="inner">
                @if (Session::has('error'))
                    <div class="error">
                        <h4>{{ Session::get('error') }}</h4>
                    </div>
                @endif

                {{ $content }}
            </div>
        </article>
    </div>
@stop