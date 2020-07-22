@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Perfil do Usuário') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <image-upload 
                                user-id="{{ auth()->id() }}" 
                                initial-original-path="{{ auth()->user()->profile->image ? asset('storage'.'/'. auth()->user()->profile->image) : '' }}" 
                                initial-image="{{ auth()->user()->profile->image_thumb ? asset('storage').'/'. auth()->user()->profile->image_thumb : 'img/user.png' }}">
                            </image-upload>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
