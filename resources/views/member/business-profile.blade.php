@extends('layouts.app')

@section('content')

@php
$business = $business ?? [
'name' => isset($id) ? ('Member Business #' . $id) : 'Member Business',
'about' => 'Business details are currently unavailable. Please check back later for updated profile information.',
'services' => ['Business Service 1', 'Business Service 2', 'Business Service 3'],
'phone' => 'Not available',
'email' => 'Not available',
'address' => 'Address not available',
'hours' => [
'Monday - Friday' => 'Not available',
'Saturday' => 'Not available',
'Sunday' => 'Not available',
],
'map' => 'Valenzuela+City',
];
@endphp

{{-- HERO SECTION --}}
<div class="w-100" style="background:#1f2330; min-height: 350px;">
    <div class="container">
        <div class="row align-items-center justify-content-center justify-content-md-start g-4"
            style="padding-top:60px; padding-bottom:60px; @media (min-width: 768px) { padding-top:120px; padding-bottom:80px; }">

            {{-- LOGO --}}
            <div class="col-12 col-md-auto d-flex justify-content-center">
                <div class="rounded-4 bg-light d-flex align-items-center justify-content-center shadow-lg"
                    style="width:120px;height:120px;">
                    <span class="fw-bold fs-2 text-danger">
                        {{ strtoupper(substr($business['name'], 0, 3)) }}
                    </span>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="col-12 col-md text-center text-md-start">
                <h1 class="fw-bold text-white mb-2 fs-2 fs-md-1">
                    {{ $business['name'] }}
                </h1>
                <span class="badge rounded-pill mb-3" style="background:#2e5aac;">
                    Manufacturing
                </span>

                <p class="text-light mb-4 mx-auto mx-md-0" style="max-width:720px; opacity:.9;">
                    {{ $business['about'] }}
                </p>

                <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-md-start">
                    <a href="tel:{{ $business['phone'] }}" class="btn btn-danger px-4 fw-bold">
                        CONTACT US
                    </a>
                    <a href="mailto:{{ $business['email'] }}" class="btn btn-outline-light px-4 fw-bold">
                        CALL NOW
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4 mt-md-5">
    <div class="row g-4 g-lg-5">

        {{-- LEFT CONTENT --}}
        <div class="col-lg-8">

            {{-- ABOUT --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-buildings text-danger fs-3"></i>
                    <h4 class="fw-bold text-danger mb-0">About Our Company</h4>
                </div>
                <p class="mb-0">{{ $business['about'] }}</p>
            </div>

            {{-- SERVICES --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <h4 class="fw-bold text-danger mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i>
                    <span>Products & Services</span>
                </h4>

                <div class="row g-3">
                    @foreach ($business['services'] as $service)
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-box bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-3 h-100 shadow-sm">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-danger"></i>
                                <h6 class="fw-bold mb-0 text-danger">{{ $service }}</h6>
                            </div>
                            <p class="small mb-0 opacity-75">
                                Reliable solutions tailored to meet your business goals.
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- MAP --}}
            <div class="card border border-danger shadow-sm p-3 p-md-4 rounded-4 mb-4">
                <h4 class="fw-bold text-danger mb-3">Our Location</h4>
                <p class="mb-3">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                    {{ $business['address'] }}
                </p>
                <div class="rounded-3 overflow-hidden">
                    <iframe
                        src="https://maps.google.com/maps?q={{ $business['map'] }}&t=m&z=15&output=embed"
                        width="100%"
                        height="300"
                        style="border:0;"
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-lg-4">

            {{-- CONTACT INFO --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <h5 class="fw-bold text-danger mb-4">Contact Information</h5>
                <div class="d-flex flex-column gap-4">
                    {{-- Phone --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px; flex-shrink:0;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="fw-bold text-uppercase opacity-75">Phone</small><br>
                            <span class="text-break">{{ $business['phone'] }}</span>
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px; flex-shrink:0;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="fw-bold text-uppercase opacity-75">Email</small><br>
                            <span class="text-break">{{ $business['email'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HOURS --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-clock text-danger fs-4"></i>
                    <h5 class="fw-bold text-danger mb-0">Business Hours</h5>
                </div>
                @foreach ($business['hours'] as $day => $time)
                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                    <span>{{ $day }}</span>
                    <span class="fw-bold">{{ $time }}</span>
                </div>
                @endforeach
            </div>

            {{-- ACTIONS --}}
            <div class="card border border-danger shadow-sm p-4 rounded-4 mb-5">
                <div class="d-grid gap-2">
                    <button class="btn btn-danger fw-bold">Request Quote</button>
                    <a href="{{ url('/membership') }}" class="btn btn-outline-danger fw-bold">
                        Browse Other Members
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection