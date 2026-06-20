@extends('layouts.admin')

@section('title')
    List Servers
@endsection

@section('content-header')
    <h1>Servers<small>All servers available on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Servers</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom nav-tabs-floating">
            <ul class="nav nav-tabs">
                <li class="{{ $type === 'all' ? 'active' : '' }}">
                    <a href="{{ route('admin.servers', ['type' => 'all', 'filter' => request()->input('filter')]) }}">All</a>
                </li>
                <li class="{{ $type === 'docker' ? 'active' : '' }}">
                    <a href="{{ route('admin.servers', ['type' => 'docker', 'filter' => request()->input('filter')]) }}">Game Servers</a>
                </li>
                <li class="{{ $type === 'qemu' ? 'active' : '' }}">
                    <a href="{{ route('admin.servers', ['type' => 'qemu', 'filter' => request()->input('filter')]) }}">VPS</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Server List</h3>
                <div class="box-tools search01">
                    <form action="{{ route('admin.servers') }}" method="GET">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="filter[*]" class="form-control pull-right" value="{{ request()->input()['filter']['*'] ?? '' }}" placeholder="Search Servers">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                @if($type === 'docker')
                                    <a href="{{ route('admin.servers.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                                @elseif($type === 'qemu')
                                    <a href="{{ route('admin.servers.new.vps') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;" data-toggle="modal" data-target="#newServerModal">Create New</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Server Name</th>
                            <th>Type</th>
                            <th>UUID</th>
                            <th>Owner</th>
                            <th>Node</th>
                            <th>Connection</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @foreach ($servers as $server)
                            <tr data-server="{{ $server->uuidShort }}">
                                <td><a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a></td>
                                <td>
                                    @if($server->isVm())
                                        <span class="label label-info">VPS</span>
                                    @else
                                        <span class="label label-default">Game</span>
                                    @endif
                                </td>
                                <td><code title="{{ $server->uuid }}">{{ $server->uuid }}</code></td>
                                <td><a href="{{ route('admin.users.view', $server->user->id) }}">{{ $server->user->username }}</a></td>
                                <td><a href="{{ route('admin.nodes.view', $server->node->id) }}">{{ $server->node->name }}</a></td>
                                <td>
                                    <code>{{ $server->allocation->alias }}:{{ $server->allocation->port }}</code>
                                </td>
                                <td class="text-center">
                                    @if($server->isSuspended())
                                        <span class="label bg-maroon">Suspended</span>
                                    @elseif(! $server->isInstalled())
                                        <span class="label label-warning">Installing</span>
                                    @else
                                        <span class="label label-success">Active</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-xs btn-default" href="/server/{{ $server->uuidShort }}"><i class="fa fa-wrench"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($servers->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $servers->appends(['type' => $type, 'filter' => Request::input('filter')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="newServerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">What would you like to create?</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ route('admin.servers.new') }}" class="btn btn-default btn-block" style="height:130px;display:flex;flex-direction:column;align-items:center;justify-content:center;white-space:normal;">
                            <i class="fa fa-gamepad" style="font-size:36px;margin-bottom:10px;"></i>
                            <strong>Game Server</strong>
                            <span class="text-muted small">A Docker container running a game or app from an egg.</span>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.servers.new.vps') }}" class="btn btn-default btn-block" style="height:130px;display:flex;flex-direction:column;align-items:center;justify-content:center;white-space:normal;margin-top:0;">
                            <i class="fa fa-server" style="font-size:36px;margin-bottom:10px;"></i>
                            <strong>VPS</strong>
                            <span class="text-muted small">A QEMU/KVM virtual machine built from a Pup template.</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $('.console-popout').on('click', function (event) {
            event.preventDefault();
            window.open($(this).attr('href'), 'Ruff Console', 'width=800,height=400');
        });
    </script>
@endsection
