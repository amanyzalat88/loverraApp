@extends('layouts.empty_layout')

@section("content")
<resetpasswordcomponent user_slack="{{ $user_slack }}" password_reset_token="{{ $password_reset_token }}"></resetpasswordcomponent>
@endsection