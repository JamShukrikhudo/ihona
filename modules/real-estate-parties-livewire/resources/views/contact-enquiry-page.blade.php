@extends('layouts.app')

@section('content')
    <main>
        <h1>Contact us</h1>
        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif
        <livewire:contact-enquiry-form />
    </main>
@endsection
