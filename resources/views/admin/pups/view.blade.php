@extends('layouts.admin')

@section('title')
    Pups &rarr; View &rarr; {{ $pup->name }}
@endsection

@section('content-header')
    <h1>{{ $pup->name }}<small>{{ str_limit($pup->description, 75) }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.pups') }}">Pups</a></li>
        <li class="active">{{ $pup->name }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Template Details</h3>
            </div>
            <form action="{{ route('admin.pups.view', $pup->id) }}" method="POST">
                <div class="box-body">
                    <div class="form-group">
                        <label for="pName" class="form-label">Name</label>
                        <input type="text" id="pName" name="name" class="form-control" value="{{ $pup->name }}" />
                    </div>
                    <div class="form-group">
                        <label for="pDescription" class="form-label">Description</label>
                        <textarea id="pDescription" name="description" class="form-control" rows="3">{{ $pup->description }}</textarea>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="pOsType" class="form-label">Operating System</label>
                            <select id="pOsType" name="os_type" class="form-control">
                                <option value="linux" @if($pup->os_type === 'linux') selected @endif>Linux</option>
                                <option value="windows" @if($pup->os_type === 'windows') selected @endif>Windows</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="pFirmware" class="form-label">Firmware</label>
                            <select id="pFirmware" name="firmware" class="form-control">
                                <option value="" @if(is_null($pup->firmware)) selected @endif>Auto (let the daemon decide)</option>
                                <option value="bios" @if($pup->firmware === 'bios') selected @endif>BIOS</option>
                                <option value="uefi" @if($pup->firmware === 'uefi') selected @endif>UEFI</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pImageUrl" class="form-label">Image / Template</label>
                        <input type="text" id="pImageUrl" name="image_url" class="form-control" value="{{ $pup->image_url }}" />
                        <p class="text-muted small">An absolute path on the node, a filename under the daemon template directory, or an <code>http(s)://</code> URL.</p>
                    </div>
                </div>
                <div class="box-footer">
                    {!! csrf_field() !!}
                    {!! method_field('PATCH') !!}
                    <button name="action" value="edit" class="btn btn-sm btn-primary pull-right">Save</button>
                    <button name="action" value="delete" class="btn btn-sm btn-danger pull-left muted muted-hover"><i class="fa fa-trash-o"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Usage</h3>
            </div>
            <div class="box-body">
                <p>
                    There {{ $pup->servers_count === 1 ? 'is' : 'are' }}
                    <strong>{{ $pup->servers_count }}</strong>
                    {{ str_plural('server', $pup->servers_count) }} using this template.
                </p>
                <p class="text-muted small no-margin">A VPS template can only be deleted once no servers reference it.</p>
            </div>
        </div>
    </div>
</div>
@endsection
