@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#mobilenumber">Phone Number Verified?</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane container active" id="home">
                                {!! Form::model($user, ['method' => 'POST','route' => ['user.update'], 'role'=>'form','class'=>'form-horizontal']) !!}
                                @csrf
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        {{ Form::text('name', Request::old('name'), array('class'=>'form-control ng-untouched ng-dirty ng-valid ng-valid-required', 'placeholder'=>'name', 'required'=>'required') ) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-6">
                                        {{ Form::email('email', Request::old('email'), array('class'=>'form-control ng-untouched ng-dirty ng-valid ng-valid-required', 'placeholder'=>'Email', 'required'=>'required') ) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input type="text" readonly="true" class="form-control" value="{{ $user->phone_number }}"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-6">
                                        <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane container fade" id="mobilenumber">
                                @csrf
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        @if ($user->is_verified)
                                            <div class="alert alert-success alert-block">
                                                {{ $user->phone_number }} verified
                                            </div>
                                        @else
                                        <h4>Didn't you receive the code?</h4>
                                        <form method="post" action="{{ url('user/resend') }}">
                                            @csrf
                                            <div class="form-group row mb-0">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-danger">
                                                        {{ __('Resend') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/app.js') }}" defer></script>
