@extends('layouts.role-help')

@section('role-help-content')
<div class=\
card
shadow-sm\>
    <div class=\card-header\>
        <h4 class=\mb-0\>{{ ucfirst(str_replace('-', ' ', '')) }} Guide</h4>
    </div>
    <div class=\card-body\>
        <div class=\alert
alert-info\>
            <i class=\fas
fa-info-circle
me-2\></i>
            <strong>{{ ucfirst(str_replace('-', ' ', '')) }} Guide</strong>
            <p class=\mb-0
mt-2\>This guide is being prepared. Please check back later for detailed documentation.</p>
        </div>
        <a href=\
{ route('help.role.dashboard') }
\ class=\btn
btn-kp-primary\>
            <i class=\fas
fa-arrow-left\></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
