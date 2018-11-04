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
            It looks like the currrent revision for project <b>{{$project_name}} is complete</b>. The client is ready for a new revision. To see the final comments for this revision click the button below
              <br>
            </td>
          </tr>
          <tr>
          <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
              <b>Project:</b> {{$project_name}}<br>
              <b>Company:</b> {{$company_name}}<br><br>
              <b>Sent by:</b> {{$sent_by_name}}
            </td>
          </tr>
          <tr>
            <td valign="middle" align="center" style="-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-image: url(http://tedgoas.github.io/Cerberus/assets/bg-btn.png); background-position: top left; background-repeat: repeat-x; background-color: #fff; padding-bottom: 30px">
              <a href="{{config('app.url')}}/loadProofer/{{$project_id}}/{{$revision_id}}" target="blank" style="color: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #333333; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #575959; width: 100px; border: 0px">
                <b>Go to proofer</b>
              </a>
            </td>
</table>
@endsection