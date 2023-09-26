{{-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .header {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        background-color:red;
    }
</style>
<body>
<div class="header">
       <h1>Welcome TO NIKA Merchant Application<h1>
        <div>{{$otp}}</div>
</div>
</body>
</html>
 --}}

@component('mail::message')
<h1
 style='
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center;
'>
       Welcome to Mercahnt Application
</h1>
<h4
 style='
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center;
'>
       Verify Code Here
</h4>
    <p style='
       letter-spacing: 10px;
       display: flex;
       justify-content: center;
       align-items: center;
       text-align:center;
    '>
    {{$otp}}
    </p>
{{-- {{ config('app.name') }} --}}
{{-- @endcomponent --}}

