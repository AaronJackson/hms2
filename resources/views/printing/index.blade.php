@extends('layouts.app')

@section('pageTitle', 'Printing')

@section('content')
<div class="container">
  <p>Here you can see the printers available for general printing in the space. Each one has a unique printer link which can be installed on your laptop as a printer. These can only be printed to while you are inside the space. The total cost per print job is automatically drawn from your Snackspace balance.</p>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>£/A4 BW</th>
          <th>£/A4 Colour</th>
          <th>£/A3 BW</th>
          <th>£/A3 Colour</th>
          <th>IPP URI</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($printers as $printer)
        <tr>
          <td data-title="Name">

            {{ $printer->getPrinterName() }}
          </td>
          <td>
            @if ($printer->getCostA4Black())
            @money($printer->getCostA4Black(), 'GBP')
            @endif
          </td>
          <td>
            @if ($printer->getCostA4Colour())
            @money($printer->getCostA4Colour(), 'GBP')
            @endif
          </td>
          <td>
            @if ($printer->getCostA3Black())
            @money($printer->getCostA3Black(), 'GBP')
            @endif
          </td>
          <td>
            @if ($printer->getCostA3Colour())
            @money($printer->getCostA3Colour(), 'GBP')
            @endif
          </td>
          <td data-title="IPP URI">
            <div class="input-group">
              <div class="input-group-prepend">
                 <span class="input-group-text" id="basic-addon1"><i class="far fa-print"></span></i>
              </div>
              <input type="url" class="form-control" id="ippUri{{ $printer->getPrinterId() }}" value="{{ route('ipp.user', $printer->getUserEndpoint($user)) }}" disabled>
               <div class="input-group-append">
                 <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('#ippUri{{ $printer->getPrinterId() }}')"><i class="far fa-copy"></i></button>
               </div>
            </div>
          </td>
          <td calss='actions'>
            {{ $printer->getStatus() }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
