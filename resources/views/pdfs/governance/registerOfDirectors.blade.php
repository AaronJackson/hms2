<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Register Of Directors for {{ config('branding.company_name') }} as of {{ today()->toDateString() }}</title>

  <style>
    body {
      background: #fff none;
      font-family: DejaVu Sans, 'sans-serif';
      font-size: 12px;
    }

    @page {
      margin: 50px 50px;
    }

    #header {
      position: fixed;
      left: -50px; top: -50px; right: -50px; height: 50px;
      /*background-color: orange;*/
    }

    #footer {
      position: fixed;
      left: -50px; bottom: -10px; right: -50px; height: 50px;
      /*background-color: orange;*/
    }

    h2 {
      font-size: 15px;
      margin-bottom: 0;
      /*color: #ccc;*/
    }

    .container {
      padding-top: 30px;
    }

    .invoice-head td {
      padding: 0 8px;
    }

    .table th {
      vertical-align: bottom;
      font-weight: bold;
      padding: 8px;
      line-height: 14px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .table tr.row td {
      border-bottom: 1px solid #ddd;
    }

    .table td {
      padding: 8px;
      line-height: 14px;
      text-align: left;
      vertical-align: top;
    }

    input[type=checkbox]
    {
      /* Double-sized Checkboxes */
      -ms-transform: scale(2); /* IE */
      -moz-transform: scale(2); /* FF */
      -webkit-transform: scale(2); /* Safari and Chrome */
      -o-transform: scale(2); /* Opera */
      transform: scale(2);
      /*padding: 10px;*/
    }
    .page-break {
      page-break-before: auto;
    }

    .page_number:before {
      content: "Page " counter(page);
    }

    .font-medium {
      font-weight: 500;
    }
    .font-semibold {
      font-weight: 600;
    }
    .font-bold {
      font-weight: 700;
    }
    .text-red-700 {;
      color: rgb(185 28 28);
    }
    .text-orange-700 {;
      color: rgb(194 65 12);
    }
    .text-yellow-400 {
      color: rgb(250 204 21);
    }
  </style>
</head>
<body>
  <div id="header" align="center">
    <h2>Register Of Directors for {{ config('branding.company_name') }} as of {{ today()->toDateString() }}</h2>
  </div>

  <div id="footer" align="center">
    <p><span class="page_number"></span> of {{ $numPagesTotal }}</p>
  </div>

  <table width="100%" class="table" border="0">
    <thead>
      <tr>
        <th class="w-20" scope="col">Name</th>
        <th class="w-25" scope="col">Service Address</th>
        <th class="w-25" scope="col">Residential Address</th>
        <th class="w-15" scope="col">Date Started</th>
        <th class="w-15" scope="col">Date Ended</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($registerOfDirectors as $record)
      <tr>
        <td>{{ $record->getFullname() }}</td>
        <td><small>{!! $serviceAddress !!}</small></td>
        <td>
          <small>
            {{ $record->getAddress1() }}<br>
            @if ($record->getAddress2())
            {{ $record->getAddress2() }}<br>
            @endif
            @if ($record->getAddress3())
            {{ $record->getAddress3() }}<br>
            @endif
            {{ $record->getAddressCity() }}<br>
            @if ($record->getAddressCounty())
            {{ $record->getAddressCounty() }}<br>
            @endif
            {{ $record->getAddressPostCode() }}
          </small>
        </td>
        <td>{{ $record->getStartedAt()->toDateString() }}</td>
        <td>{{ $record->getEndedAt()?->toDateString() }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div id="footer" align="center">
    <p><span class="page_number"></span> of {{ $numPagesTotal }}</p>
  </div>
</body>
</html>