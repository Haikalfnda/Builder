@extends('layouts.app')
@section('content')
<div class="entry-page"><div class="entry-title"><h1>{{ __('messages.edit_financial_entry') }}</h1><p>{{ __('messages.edit_entry_description') }}</p></div>@include('transactions._form')</div>
@endsection
