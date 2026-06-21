@extends('layouts.admin')

@section('title')
    Addons
@endsection

@section('content-header')
    <h1>Addons<small>Drop-in extensions discovered under the panel's <code>addons/</code> directory.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Addons</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Installed Addons</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Addon</th>
                            <th>Version</th>
                            <th>Author</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                        @forelse ($addons as $addon)
                            <tr>
                                <td>
                                    <strong>{{ $addon['name'] }}</strong>
                                    <p class="text-muted small no-margin">{{ $addon['description'] }}</p>
                                    <code class="small">{{ $addon['id'] }}</code>
                                </td>
                                <td>{{ $addon['version'] }}</td>
                                <td>{{ $addon['author'] ?: '—' }}</td>
                                <td class="text-center">
                                    @if($addon['enabled'])
                                        <span class="label label-success">Enabled</span>
                                    @else
                                        <span class="label label-default">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($addon['enabled'])
                                        <form action="{{ route('admin.addons.disable', $addon['id']) }}" method="POST" style="display:inline;">
                                            {!! csrf_field() !!}
                                            <button type="submit" class="btn btn-xs btn-danger">Disable</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.addons.enable', $addon['id']) }}" method="POST" style="display:inline;">
                                            {!! csrf_field() !!}
                                            <button type="submit" class="btn btn-xs btn-success">Enable</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="padding:24px;">
                                    No addons found. Drop an addon directory into <code>{{ $path }}</code> and refresh.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="box-footer with-border">
                <p class="text-muted small no-margin">
                    Enabling an addon runs its migrations and registers its routes/services on every boot.
                    Disabling rolls its migrations back, so the operation is reversible.
                    See <code>addons/README.md</code> for the addon format.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
