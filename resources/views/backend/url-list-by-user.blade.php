@extends('layouts.backend')

@section('title', 'Links  >  '. $authorName)

@section('content')
<div class="container-alt max-w-340">
    <div class="content-container card card-master">
        <div class="content-header">
            <p class="text-2xl">Links created by {{ $authorName }}</p>
        </div>

        @livewire('table.UrlTableByUser', ['user_id' => $authorId])
    </div>
</div>
@endsection
