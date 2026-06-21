@extends('layouts.admin')

@section('title')
    New VPS
@endsection

@section('content-header')
    <h1>Create VPS<small>Add a new virtual machine to the panel.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.servers') }}">Servers</a></li>
        <li class="active">Create VPS</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('admin.servers.new.vps') }}" method="POST">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Core Details</h3>
                </div>

                <div class="box-body row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pName">VPS Name</label>
                            <input type="text" class="form-control" id="pName" name="name" value="{{ old('name') }}" placeholder="VPS Name">
                            <p class="small text-muted no-margin">Character limits: <code>a-z A-Z 0-9 _ - .</code> and <code>[Space]</code>.</p>
                        </div>

                        <div class="form-group">
                            <label for="pUserId">Server Owner</label>
                            <select id="pUserId" name="owner_id" class="form-control" style="padding-left:0;"></select>
                            <p class="small text-muted no-margin">Email address of the Server Owner.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pDescription" class="control-label">Description</label>
                            <textarea id="pDescription" name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                            <p class="text-muted small">A brief description of this VPS.</p>
                        </div>

                        <div class="form-group">
                            <div class="checkbox checkbox-primary no-margin-bottom">
                                <input id="pStartOnCreation" name="start_on_completion" type="checkbox" {{ \Ruff\Helpers\Utilities::checked('start_on_completion', 1) }} />
                                <label for="pStartOnCreation" class="strong">Start VPS when Installed</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">VPS Template</h3>
                </div>

                <div class="box-body row">
                    <div class="form-group col-xs-12">
                        <label for="pPupId">Template (Pup)</label>
                        <select id="pPupId" name="pup_id" class="form-control">
                            @foreach($pups as $pup)
                                <option value="{{ $pup->id }}" @if($pup->id === old('pup_id')) selected @endif>
                                    {{ $pup->name }} ({{ ucfirst($pup->os_type) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="small text-muted no-margin">The operating-system template this VPS is built from. Manage templates under <a href="{{ route('admin.pups') }}">Pups</a>.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="overlay" id="allocationLoader" style="display:none;"><i class="fa fa-refresh fa-spin"></i></div>
                <div class="box-header with-border">
                    <h3 class="box-title">Allocation Management</h3>
                </div>

                <div class="box-body row">
                    <div class="form-group col-sm-12">
                        <label for="pNodeId">Node</label>
                        <select name="node_id" id="pNodeId" class="form-control">
                            @foreach($locations as $location)
                                <optgroup label="{{ $location->long }} ({{ $location->short }})">
                                @foreach($location->nodes as $node)
                                    <option value="{{ $node->id }}">{{ $node->name }}</option>
                                @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="small text-muted no-margin">The node which this VPS will be deployed to. It must be running the Dawg daemon with <code>qemu.enabled</code>.</p>
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="pAllocation">Default Allocation</label>
                        <select id="pAllocation" name="allocation_id" class="form-control"></select>
                        <p class="small text-muted no-margin">The primary allocation (forwarded into the guest).</p>
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="pAllocationAdditional">Additional Allocation(s)</label>
                        <select id="pAllocationAdditional" name="allocation_additional[]" class="form-control" multiple></select>
                        <p class="small text-muted no-margin">Additional ports forwarded into the guest.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Guest Login (cloud-init)</h3>
                </div>
                <div class="box-body row">
                    <div class="form-group col-sm-3">
                        <label for="pVmUser">Username</label>
                        <input type="text" id="pVmUser" name="vm_user" class="form-control" value="{{ old('vm_user') }}" placeholder="root" />
                        <p class="small text-muted no-margin">Initial login user (default <code>root</code>).</p>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="pVmPassword">Password</label>
                        <input type="text" id="pVmPassword" name="vm_password" class="form-control" value="{{ old('vm_password') }}" autocomplete="off" />
                        <p class="small text-muted no-margin">Leave blank to auto-generate (shown once after creation).</p>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="pVmHostname">Hostname</label>
                        <input type="text" id="pVmHostname" name="vm_hostname" class="form-control" value="{{ old('vm_hostname') }}" placeholder="vps-xxxxxxxx" />
                        <p class="small text-muted no-margin">Guest hostname (defaults from the server UUID).</p>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="pVmSshKeys">SSH Public Keys</label>
                        <textarea id="pVmSshKeys" name="vm_ssh_keys" rows="3" class="form-control" placeholder="ssh-ed25519 AAAA... (one key per line)">{{ old('vm_ssh_keys') }}</textarea>
                        <p class="small text-muted no-margin">One public key per line. Supplying a key lets you leave the password blank.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Resource Management</h3>
                </div>

                <div class="box-body row">
                    <div class="form-group col-xs-6">
                        <label for="pCPU">CPU Limit</label>
                        <div class="input-group">
                            <input type="text" id="pCPU" name="cpu" class="form-control" value="{{ old('cpu', 0) }}" />
                            <span class="input-group-addon">%</span>
                        </div>
                        <p class="text-muted small">Maps to virtual CPUs: <code>100</code> = 1 vCPU, <code>200</code> = 2 vCPUs, and so on. Set to <code>0</code> for a single unconstrained vCPU.</p>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="pThreads">CPU Pinning</label>
                        <div>
                            <input type="text" id="pThreads" name="threads" class="form-control" value="{{ old('threads') }}" />
                        </div>
                        <p class="text-muted small"><strong>Advanced:</strong> pin the guest to specific host CPU threads, or leave blank. Example: <code>0</code>, <code>0-1,3</code>.</p>
                    </div>
                </div>

                <div class="box-body row">
                    <div class="form-group col-xs-6">
                        <label for="pMemory">Memory</label>
                        <div class="input-group">
                            <input type="text" id="pMemory" name="memory" class="form-control" value="{{ old('memory') }}" />
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">The amount of RAM allocated to this virtual machine.</p>
                    </div>

                    <div class="form-group col-xs-6">
                        <label for="pDisk">Disk Space</label>
                        <div class="input-group">
                            <input type="text" id="pDisk" name="disk" class="form-control" value="{{ old('disk') }}" />
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">The size of the virtual disk created for this VPS. Set to <code>0</code> for unlimited.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Application Feature Limits</h3>
                </div>

                <div class="box-body row">
                    <div class="form-group col-xs-4">
                        <label for="pDatabaseLimit" class="control-label">Database Limit</label>
                        <input type="text" id="pDatabaseLimit" name="database_limit" class="form-control" value="{{ old('database_limit', 0) }}"/>
                        <p class="text-muted small">Databases the user may create for this VPS.</p>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="pAllocationLimit" class="control-label">Allocation Limit</label>
                        <input type="text" id="pAllocationLimit" name="allocation_limit" class="form-control" value="{{ old('allocation_limit', 0) }}"/>
                        <p class="text-muted small">Additional allocations the user may create.</p>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="pBackupLimit" class="control-label">Backup Limit</label>
                        <input type="text" id="pBackupLimit" name="backup_limit" class="form-control" value="{{ old('backup_limit', 0) }}"/>
                        <p class="text-muted small">Backups that can be created for this VPS.</p>
                    </div>
                </div>

                <div class="box-footer">
                    {{-- swap and io_weight are container concepts the VM backend ignores, but the
                         server model still requires them; submit sane defaults. --}}
                    <input type="hidden" name="swap" value="0" />
                    <input type="hidden" name="io" value="500" />
                    {!! csrf_field() !!}
                    <input type="submit" class="btn btn-success pull-right" value="Create VPS" />
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/admin/new-vps-server.js?v=20260620') !!}

    <script type="application/javascript">
        $(document).ready(function() {
            @if (old('owner_id'))
                $.ajax({
                    url: '/admin/users/accounts.json?user_id={{ old('owner_id') }}',
                    dataType: 'json',
                }).then(function (data) {
                    initUserIdSelect([ data ]);
                });
            @else
                initUserIdSelect();
            @endif

            @if (old('node_id'))
                $('#pNodeId').val('{{ old('node_id') }}').change();

                @if (old('allocation_id'))
                    $('#pAllocation').val('{{ old('allocation_id') }}').change();
                @endif

                @if (old('allocation_additional'))
                    const additional_allocations = [];
                    @for ($i = 0; $i < count(old('allocation_additional')); $i++)
                        additional_allocations.push('{{ old('allocation_additional.'.$i) }}');
                    @endfor
                    $('#pAllocationAdditional').val(additional_allocations).change();
                @endif
            @endif

            @if (old('pup_id'))
                $('#pPupId').val('{{ old('pup_id') }}').change();
            @endif
        });
    </script>
@endsection
