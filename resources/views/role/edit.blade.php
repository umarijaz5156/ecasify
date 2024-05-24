{{ Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
                {{ Form::text('name', $role->name === 'advocate' ? 'Attorney' : null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Role Name'),
                    'readonly' => $role->name === 'advocate',
                ]) }}

                @error('name')
                    <span class="invalid-name text-danger text-xs" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="">
                @if (!empty($permissions))

                    <div class="table-border-style">
                        <label for="permissions" class="col-form-label">{{ __('Assign Permission to Roles') }}</label>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input align-middle" name="checkall"
                                                id="checkall">
                                        </th>
                                        <th class="text-dark">{{ __('Module') }} </th>
                                        <th class="text-dark ps-0">{{ __('Permissions') }} </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // $modules=['dashboard','advocate','document','doctype','member','group','court','highcourt','bench','cause','case','tasks','bill','tax','diary','timesheet','expense','feereceived','calendar','setting'];
                                        $modules = ['advocate', 'document', 'member', 'cause', 'case', 'tasks', 'setting'];
                                        
                                    @endphp
                                    @foreach ($modules as $module)
                                        <tr>
                                            <td width="10%"><input type="checkbox" class="form-check-input ischeck"
                                                    name="checkall" data-id="{{ str_replace(' ', '', $module) }}"
                                                    id="{{ str_replace(' ', '', $module) }}"></td>
                                            <td width="10%"><label class="ischeck"
                                                    data-id="{{ str_replace(' ', '', $module) }} ">{{ ucfirst($module == 'advocate' ? 'attorney' : $module) }}</label>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    @if (in_array('manage ' . $module, (array) $permissions))
                                                        @if ($key = array_search('manage ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module), 'data-field' => 'visible']) }}
                                                                {{ Form::label('permission' . $key, 'Visible', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('create ' . $module, (array) $permissions))
                                                        @if ($key = array_search('create ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Create', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('duplicate ' . $module, (array) $permissions))
                                                        @if ($key = array_search('duplicate ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'duplicate', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('edit ' . $module, (array) $permissions))
                                                        @if ($key = array_search('edit ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Edit', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('delete ' . $module, (array) $permissions))
                                                        @if ($key = array_search('delete ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Delete', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('show ' . $module, (array) $permissions))
                                                        @if ($key = array_search('show ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, Str::ucfirst($module).' View', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('view ' . $module, (array) $permissions))
                                                        @if ($key = array_search('view ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, Str::ucfirst($module).' View', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('move ' . $module, (array) $permissions))
                                                        @if ($key = array_search('move ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Move', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('client permission ' . $module, (array) $permissions))
                                                        @if ($key = array_search('client permission ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Client Permission', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('invite user ' . $module, (array) $permissions))
                                                        @if ($key = array_search('invite user ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Invite User ', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('timeline ' . $module, (array) $permissions))
                                                        @if ($key = array_search('timeline ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Timeline ', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('calendar ' . $module, (array) $permissions))
                                                        @if ($key = array_search('calendar ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module),'data-field' => 'calendar']) }}
                                                                {{ Form::label('permission' . $key, 'View Calendar ', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    {{-- create calendar --}}
                                                    @if (in_array('calendar create ' . $module, (array) $permissions))
                                                        @if ($key = array_search('calendar create ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module),'data-field' => 'calendar_create']) }}
                                                                {{ Form::label('permission' . $key, 'Create Calendar', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    {{-- edit calendar --}}
                                                    @if (in_array('calendar edit ' . $module, (array) $permissions))
                                                        @if ($key = array_search('calendar edit ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key,'data-field' => 'calendar_edit']) }}
                                                                {{ Form::label('permission' . $key, 'Edit Calendar', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    {{-- delete calendatr --}}
                                                    @if (in_array('calendar delete ' . $module, (array) $permissions))
                                                        @if ($key = array_search('calendar delete ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key,'data-field' => 'calendar_delete']) }}
                                                                {{ Form::label('permission' . $key, 'Delete Calendar', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('tasks ' . $module, (array) $permissions))
                                                        @if ($key = array_search('tasks ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Tasks', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('change password ' . $module, (array) $permissions))
                                                        @if ($key = array_search('change password ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Change Password ', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if (in_array('buy ' . $module, (array) $permissions))
                                                        @if ($key = array_search('buy ' . $module, $permissions))
                                                            <div class="col-md-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key, 'data-id' => str_replace(' ', '', $module)]) }}
                                                                {{ Form::label('permission' . $key, 'Buy', ['class' => 'form-check-label']) }}<br>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {

        if (!$('[data-field="calendar"]').prop('checked')) {
        $('[data-field*="calendar_"]').prop('checked', false).prop('disabled', true);
        }
        $("#checkall").on('click', function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
            if (this.checked) {
                $('[class*="isscheck_"]').prop('disabled', false);
            } else {
                $('[class*="isscheck_"]').prop('disabled', true);
                // where data-field == visiable make it disabled false
                $('[class*="isscheck_"][data-field="visible"]').prop('disabled', false);
            }
        });

        // $('[class*="isscheck_"]') each 
        // Disable all elements with class names starting with "isscheck_"
        // Enable elements with class names starting with "isscheck_" and data-field="visible"
        $('[class*="isscheck_"]').prop('disabled', true);
        $('[class*="isscheck_"]').each((i, e) => {
            if ($(e).prop('checked') == true && $(e).data('field') == 'visible') {

                var ischeck = $(e).data('id');
                $('.isscheck_' + ischeck).prop('disabled', false);
                // $(e).prop('disabled', true);
            }
            if ($(e).data('field') == 'visible') {

                $(e).prop('disabled', false);
            }
        });




        //     $('[class*="isscheck_"]').prop('disabled', true);

        // $('[class*="isscheck_"][data-field="visible"]').prop('disabled', false);
        $(".ischeck").on('click', function() {
            var ischeck = $(this).data('id');
            $('.isscheck_' + ischeck).prop('checked', this.checked);
            if (this.checked) {
                $('.isscheck_' + ischeck).prop('disabled', false);
            } else {
                $('.isscheck_' + ischeck).prop('disabled', true);
                // where data-field == visiable make it disabled false
                $('.isscheck_' + ischeck + '[data-field="visible"]').prop('disabled', false);
            }

        });
        // on click .isscheck_id => if all .isscheck_id is checked than checked the where class = isscheck and id = id
        $(".isscheck").on('click', function() {
            var ischeck = $(this).data('id');

            // check data-field == visable -> alert 
            if ($(this).data('field') == 'visible') {
                // if checkbox false disabled all other check box   '.isscheck_' + ischeck
                if ($(this).prop('checked') == false) {
                    $('.isscheck_' + ischeck).prop('checked', false);
                    $('.isscheck_' + ischeck).prop('disabled', true);
                    $(this).prop('disabled', false);
                } else {
                    $('.isscheck_' + ischeck).prop('disabled', false);

                }
            }
            var checkbox = $('#' + $(this).attr('id'));

            if ($('.isscheck_' + ischeck).length == $('.isscheck_' + ischeck + ':checked').length) {
                //checked where data-id=ischeck
                $('#' + ischeck).prop('checked', true);
            } else {
                $('#' + ischeck).prop('checked', false);
            }


        });

        $('[data-field="calendar"]').on('click', function() {
    if ($(this).prop('checked')) {
      // If "Calendar" is checked, enable and check the related checkboxes
      $('[data-field*="calendar_"]').prop('checked', true).prop('disabled', false);
    } else {
      // If "Calendar" is unchecked, uncheck and disable the related checkboxes
      $('[data-field*="calendar_"]').prop('checked', false).prop('disabled', true);
    }
  });
    });

    $(document).ready(function() {

if (!$('[data-field="calendar"]').prop('checked')) {
$('[data-field*="calendar_"]').prop('checked', false).prop('disabled', true);
}
    });
</script>
