@extends('layout_home.app')
@section('content')
    <section id="privacy-policy" class="py-10 bg-white text-gray-800"
        style="padding-top: 5rem; padding-bottom: 5rem; margin-top: 6rem">
        @if ($policy)
            <div class="container mx-auto px-4">
                {!! $policy->content !!}
            </div>
        @else
            <p>No privacy policy found.</p>
        @endif

    </section>
@endsection
