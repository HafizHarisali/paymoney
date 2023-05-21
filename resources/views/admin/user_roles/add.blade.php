@extends('admin.layouts.master')

@section('title', __('Add User Group'))

@section('head_style')
  <!-- custom-checkbox -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/custom-checkbox.css') }}">
@endsection

@section('page_content')

  <div class="row">
    <div class="col-md-3 settings_bar_gap">
      @include('admin.common.settings_bar')
    </div>
    <div class="col-md-9">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">{{ __('Add User Group') }}</h3>
        </div>

        <!-- form start -->
        <form method="POST" action="{{ url(\Config::get('adminPrefix').'/settings/add_user_role') }}" class="form-horizontal" enctype="multipart/form-data" id="group_add_form">
          {{ csrf_field() }}

          <div class="box-body">
            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1">{{ __('Name') }}</label>
              <div class="col-sm-6">
                <input type="text" name="name" class="form-control f-14" value="{{ old('name') }}" placeholder="{{ __('Name') }}" id="name">
                @if($errors->has('name'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('name') }}</strong>
                </span>
                @endif
                <span id="name-error"></span>
                <span id="name-ok" class="text-success"></span>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1">{{ __('Display Name') }}</label>
              <div class="col-sm-6">
                <input type="text" name="display_name" class="form-control f-14" value="{{ old('display_name') }}" placeholder="{{ __('Display Name') }}" id="display_name">
                @if($errors->has('display_name'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('display_name') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1">{{ __('Description') }}</label>
              <div class="col-sm-6">
                <textarea name="description" placeholder="{{ __('Description') }}" rows="3" class="form-control f-14" value="{{ old('description') }}" id="description"></textarea>
                @if($errors->has('description'))
                <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('description') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1">{{ __('User Type') }}</label>
              <div class="col-sm-6">
                <select class="select2" name="customer_type" id="customer_type">
                    <option value='user'>{{ __('User') }}</option>
                    <option value='merchant'>{{ __('Merchant') }}</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1">{{ __('Default') }}</label>
              <div class="col-sm-6">
                <select class="select2" name="default" id="default">
                    <option value='No'>{{ __('No') }}</option>
                    <option value='Yes'>{{ __('Yes') }}</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 control-label mt-11 f-14 fw-bold text-end" for="exampleFormControlInput1"></label>

              <div class="col-sm-5">
                <div class="table-responsive">
                  <table class="table table-striped f-14">
                    <thead>
                      <tr>
                        <th>{{ __('Permissions') }}</th>
                        <th>{{ __('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody id="permissions-tbody">
                        @php
                          $arr=['Transaction','Dispute','Ticket','Settings']
                        @endphp
                        @if (isset($permissions))
                          @foreach ($permissions as $permission)
                            @if(in_array($permission->group,$arr))
                                <input class="d-none" type="checkbox" name="permission[]" id="permission" value="{{$permission->id}}" checked>
                            @else
                                <tr>
                                  <input type="hidden" value="{{ $permission->user_type }}" name="user_type" id="user_type">
                                  <td>{{ $permission->group }}</td>
                                  <td>
                                    <label class="checkbox-container">
                                      <input type="checkbox" name="permission[]" value="{{ $permission->id }}">
                                      <span class="checkmark"></span>
                                    </label>
                                  </td>
                                </tr>
                            @endif
                          @endforeach
                        @endif
                    </tbody>
                  </table>
                  <div class="d-none" id="error-message"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="box-footer">
            <a class="btn btn-theme-danger f-14" href="{{ url(\Config::get('adminPrefix').'/settings/user_role') }}">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-theme pull-right f-14">&nbsp; {{ __('Add') }} &nbsp;</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function checkUserRolePermissionsOnCreate()
    {
        let customer_type = $("#customer_type option:selected").val();
        $.ajax({
            method: "GET",
            url: SITE_URL+"/"+ADMIN_PREFIX+"/settings/roles/check-user-permissions",
            dataType: "json",
            data: {
                'customer_type': customer_type,
            }
        })
        .done(function(response)
        {
            // console.log(response);
            if (response.status == true)
            {
                let tr = '';
                $.each(response.permissions, function(key, val)
                {
                    let arr = ['Transaction','Dispute','Ticket','Settings'];
                    if (arr.includes(val.group))
                    {
                      `<input style="display: none" type="checkbox" name="permission[]" id="permission" value="${val.id}" checked>`
                    }
                    else
                    {
                      tr +=
                      '<tr>'+
                        `<input type="hidden" value="${val.user_type}" name="user_type" id="user_type">` +
                        '<td>'+ val.group +'</td>'+
                        '<td>'+
                          `<label class="checkbox-container">
                            <input type="checkbox" name="permission[]" value="${val.id}">
                            <span class="checkmark"></span>
                          </label>`
                        '</td>'+
                      '</tr>';
                    }
                });
                $('#permissions-tbody').html(tr);
            }
        });
    }

    $(window).on('load', function()
    {
      $(".select2").select2({});
      checkUserRolePermissionsOnCreate();
    });

    // Validate Role Name via Ajax
    $(document).ready(function()
    {
        $("#name").on('input', function(e)
        {
          var name = $('#name').val();
          var user_type = $('#user_type').val();
          $.ajax({
              headers:
              {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              method: "POST",
              url: SITE_URL+"/"+ADMIN_PREFIX+"/settings/roles/duplicate-role-check",
              dataType: "json",
              data: {
                  'name': name,
                  'user_type': user_type,
              }
          })
          .done(function(response)
          {
              // console.log(response);
              if (response.status == true)
              {
                  emptyName();
                  $('#name-error').show();
                  $('#name-error').addClass('error').html(response.fail).css("font-weight", "bold");
                  $('form').find("button[type='submit']").prop('disabled',true);
              }
              else if (response.status == false)
              {
                  $('#name-error').html('');
                  $('form').find("button[type='submit']").prop('disabled',false);
              }

              function emptyName() {
                  if( name.length === 0 )
                  {
                      $('#name-error').html('');
                  }
              }
          });
        });
    });

    //Check User Role Permissions
    $(document).on('change', "#customer_type", function(e)
    {
      checkUserRolePermissionsOnCreate();
    });

    //On Submit
    jQuery.validator.addMethod("letters_with_spaces", function(value, element)
    {
      return this.optional(element) || /^[A-Za-z ]+$/i.test(value); //only letters
    }, "Please enter letters only!");

    $.validator.setDefaults({
        highlight: function(element) {
          $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
         $(element).parent('div').removeClass('has-error');
       },
       errorPlacement: function (error, element) {
        if (element.prop('type') === 'checkbox') {
          $('#error-message').show().html(error);
        } else {
          $('#error-message').hide();
          error.insertAfter(element);
        }
      }
    });

    $('#group_add_form').validate({
      rules: {
        name: {
          required: true,
          maxlength: 30,
          letters_with_spaces: true,
        },
        display_name: {
          required: true,
          letters_with_spaces: true,
        },
        description: {
          required: true,
          letters_with_spaces: true,
        },
        "permission[]": {
          required: true,
          minlength: 1
        },
      },
      messages: {
        "permission[]": {
          required: "Please select at least one checkbox!",
        },
      },
    });
</script>

@endpush
