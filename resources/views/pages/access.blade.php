@extends('layouts.app')

@section('pageTitle', 'Access Codes')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm">
      <div class="card mb-3">
        <h3 class="card-header"><i class="fas fa-key" aria-hidden="true"></i> Door Codes</h3>
        <div class="card-body">
          <dl>
            <dt class="h5">Street Door</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_street_door') }}</span></h4>
            <hr>
            <dt class="h5">Inner Door</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_inner_door') }}</span></h4>
            @if ($roden = Meta::get('access_roden_street_door'))
            <hr>
            <dt class="h5">Roden Street Door</dt>
            <h4><span class="badge badge-primary">{{  $roden }}</span></h4>
            @endif
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card mb-3">
        <h3 class="card-header"><i class="fas fa-wifi" aria-hidden="true"></i> Member&apos;s WiFi</h3>
        <div class="card-body">
          <dl>
            <dt class="h5">SSID</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_wifi_ssid') }}</span></h4>
            <hr>
            <dt class="h5">Password (PSK)</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_wifi_password') }}</span></h4>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card mb-3">
        <h3 class="card-header"><i class="fas fa-wifi" aria-hidden="true"></i> Guest WiFi</h3>
        <div class="card-body">
          <dl>
            <dt class="h5">SSID</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_guest_wifi_ssid') }}</span></h4>
            <hr>
            <dt class="h5">Password (PSK)</dt>
            <h4><span class="badge badge-primary">{{  Meta::get('access_guest_wifi_password') }}</span></h4>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
