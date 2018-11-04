@extends('email')
@section('content')
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-bottom: 0px;">
          <tr>
          <td style="padding: 40px; padding-bottom: 0px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 16px">
                Hi : {{$name}}
          </td>
          </tr>
          <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
            You have been invited by <b>{{$sent_by_name}}</b> to join the project called <b>{{$project}}</b>. Please follow the link to set your credentials and join the project.<br> Note that you must use as 'E-Mail Address' the current one.
              <br>
            </td>
          </tr>
          <tr>
          <tr>
            <td valign="middle" align="center" style="-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-image: url(http://tedgoas.github.io/Cerberus/assets/bg-btn.png); background-position: top left; background-repeat: repeat-x; background-color: #fff; padding-bottom: 30px">
              <a href="{{config('app.url')}}/password/reset/{{$token}}" target="blank" style="color: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #333333; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #575959; width: 200px; border: 0px">
                <b>Click to join this project</b>
              </a>
            </td>
</table>
@endsection

