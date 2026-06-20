@extends('layouts.admin')

@section('title')
    Pups
@endsection

@section('content-header')
    <h1>Pups<small>VPS templates that virtual machines can be created from.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Pups</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">VPS Template List</h3>
                <div class="box-tools">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newPupModal">Create New</button>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>OS</th>
                            <th>Firmware</th>
                            <th>Image</th>
                            <th class="text-center">Servers</th>
                        </tr>
                        @foreach ($pups as $pup)
                            <tr>
                                <td><code>{{ $pup->id }}</code></td>
                                <td><a href="{{ route('admin.pups.view', $pup->id) }}">{{ $pup->name }}</a></td>
                                <td>
                                    @if($pup->os_type === 'windows')
                                        <i class="fa fa-windows"></i> Windows
                                    @else
                                        <i class="fa fa-linux"></i> Linux
                                    @endif
                                </td>
                                <td>{{ $pup->firmware ? strtoupper($pup->firmware) : 'Auto' }}</td>
                                <td><code title="{{ $pup->image_url }}">{{ str_limit($pup->image_url, 48) }}</code></td>
                                <td class="text-center">{{ $pup->servers_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="newPupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.pups') }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create VPS Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="pName" class="form-label">Name</label>
                            <input type="text" name="name" id="pName" class="form-control" />
                            <p class="text-muted small">A friendly name for this template, for example <code>Ubuntu 22.04 LTS</code>.</p>
                        </div>
                        <div class="col-md-12">
                            <label for="pDescription" class="form-label">Description</label>
                            <textarea name="description" id="pDescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="pOsType" class="form-label">Operating System</label>
                            <select name="os_type" id="pOsType" class="form-control">
                                <option value="linux">Linux</option>
                                <option value="windows">Windows</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pFirmware" class="form-label">Firmware</label>
                            <select name="firmware" id="pFirmware" class="form-control">
                                <option value="">Auto (let the daemon decide)</option>
                                <option value="bios">BIOS</option>
                                <option value="uefi">UEFI</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="pImageUrl" class="form-label">Image / Template</label>
                            <input type="text" name="image_url" id="pImageUrl" class="form-control" />
                            <p class="text-muted small">A base qcow2 image: an absolute path on the node, a filename under the daemon template directory, or an <code>http(s)://</code> URL downloaded on first boot.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! csrf_field() !!}
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
