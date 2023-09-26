@component('mail::message')
<h1
 style='
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center;
'>
       Welcome to Mercahnt Application<br/>
       New Password
</h1>
<h4
 style='
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center;
'>
       New Password...
</h4>
    <p style='
       letter-spacing: 10px;
       display: flex;
       justify-content: center;
       align-items: center;
       text-align:center;
    '>
    {{$randPassword}}
    </p>
{{-- {{ config('app.name') }} --}}
{{-- @endcomponent --}}

