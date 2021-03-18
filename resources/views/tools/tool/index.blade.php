@extends('layouts.app')

@section('pageTitle', 'Tools')

@section('content')
<div class="container">
  @content('tools.tool.index', 'main')
  <p>
    To book a tool click on the <span style="color: #195905"><i class="far fa-calendar-alt" aria-hidden="true"></i></span> or name.<br>
    Use of some tools is restricted and needs an induction first.
  </p>
  @foreach ($tools as $tool)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="d-none d-md-table-cell">&nbsp;</th>
          <th>Tool</th>
          <th>Status</th>
          <th class="d-none d-md-table-cell">Cost per hour</th>
          <th>Next booking</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td class="d-none d-md-table-cell" style="width:25px"><a href="{{ route('tools.bookings.index', $tool->getId()) }}"><span style="color: #195905"><i class="far fa-calendar-alt" aria-hidden="true"></i></span></a></td>
          <td data-title="Tool"><a href="{{ route('tools.bookings.index', $tool->getId()) }}"><span class="d-md-none" style="color: #195905"><i class="far fa-calendar-alt" aria-hidden="true"></i>&nbsp;</span>{{ $tool->getDisplayName() }}</a></td>
          <td data-title="Status">
            {{ $tool->getStatusString() }}
            @if ($tool->getStatus() == \HMS\Entities\Tools\ToolState::DISABLED && ! is_null($tool->getStatusText()))
            <br>{{ $tool->getStatusText() }}
            @endif
          </td>
          <td data-title="Cost per hour" class="d-none d-md-table-cell">@money($tool->getPph(), 'GBP')</td>
          <td data-title="Next booking">{{ $nextBookings[$tool->getId()] ? $nextBookings[$tool->getId()]->getStart()->format('jS F Y @ H:i') : "None" }}</td>
         <td data-title="Actions" class="actions">
            @canany(['tools.edit', 'tools.maintainer.grant', 'tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.inductor.grant', 'tools.' . $tool->getPermissionName() . '.induct'])
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('tools.show', $tool->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View Settings</a>
            @endcan
            @can('tools.maintainer.grant')
            <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getDisplayName() }}" grant-type="{{ HMS\Tools\ToolManager::MAINTAINER }}" :small="true"></tool-grant-modal>
            @endcan
            @canany(['tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.inductor.grant'])
            <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getDisplayName() }}" grant-type="{{ HMS\Tools\ToolManager::INDUCTOR }}" :small="true"></tool-grant-modal>
            @endcan
            @if ($tool->isRestricted())
            @cannot('tools.' . $tool->getPermissionName() . '.use')
            <a class="btn btn-primary btn-sm mb-1" href="{{ Meta::get('induction_request_html') }}" target="_blank">Request an Induction</a>
            @endcannot
            @endif
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @endforeach
</div>

@can('tools.create')
<br>
<div class="container">
  <a href="{{ route('tools.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new tool</a>
</div>
@endcan
@endsection
