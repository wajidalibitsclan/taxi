@extends('layouts.user')
@section('head')
@endsection
@section('content')
    <div class="my_dashboard_review mt-3">
    <form action="{{ route("user.change_password_post") }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="my_profile_setting_input form-group">
                    <label>{{__("Current Password")}}</label>
                    <input type="password" name="current-password" placeholder="{{__("Current Password")}}" class="form-control">
                </div>
                <div class="my_profile_setting_input form-group">
                    <label>{{__("New Password")}}</label>
                    <input type="password" name="new-password" placeholder="{{__("New Password")}}" class="form-control">
                                   </div>
                <div class="my_profile_setting_input form-group">
                    <label>{{__("New Password Again")}}</label>
                    <input type="password" name="new-password_confirmation" placeholder="{{__("New Password Again")}}" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <hr>
                <input type="submit" class="btn btn-primary" value="{{__("Change Password")}}">
                <a href="{{ route("user.profile.index") }}" class="btn btn-default">{{__("Cancel")}}</a>
            </div>
        </div>
    </form>
    </div>
@endsection
@section('footer')

@endsection
