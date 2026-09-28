@extends('layouts.app')
@section('content')
<div class="entry-page"><div class="entry-title"><h1>{{ __('messages.add_financial_entry') }}</h1><p>{{ __('messages.add_entry_description') }}</p></div>@include('transactions._form')</div>
@endsection
