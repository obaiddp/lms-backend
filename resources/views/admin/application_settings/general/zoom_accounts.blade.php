
@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Application Settings') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __(@$title) }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    @include('admin.application_settings.sidebar')
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{ __('Zoom Accounts') }}</h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-zoom-account-modal">
                                <i class="fa fa-plus"></i> {{ __('Add Zoom Account') }}
                            </button>
                        </div>
                        <div class="customers__table">
                                <table id="customers-table" class="row-border data-table-filter table-style">
                                    <thead>
                                    <tr>
                                        <th>{{__('SR')}}</th>
                                        <th>{{__('API Key')}}</th>
                                        <th>{{__('SDK Key')}}</th>
                                        <th>{{__('Minutes Used')}}</th>
                                        <th>{{__('Last Renewed At')}}</th>
                                        <th>{{__('Days Passed')}}</th>
                                        <th>{{__('Next Renewel On')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (!$zoomAccounts->isEmpty())
                                    @foreach($zoomAccounts as $zoom)
                                        <tr class="removable-item">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{$zoom->api_key}}</td>
                                            <td>{{$zoom->sdk_key}}</td>
                                            <td>{{$zoom->meeting_minutes_used}}</td>
                                            <td>
                                                @php
                                                    // if($zoom->days_passed){
                                                    //     if($zoom->days_passed == 1){
                                                    //         $daysPassed = '( '.$zoom->days_passed.' Day Ago )';
                                                    //     }
                                                    //     else if($zoom->days_passed > 1){
                                                    //         $daysPassed = '( '.$zoom->days_passed.' Days Ago )';
                                                    //     }
                                                    //     else{
                                                    //         $daysPassed = null;  
                                                    //     }
                                                    // }
                                                    // else{
                                                    //     $daysPassed = null; 
                                                    // }
                                                    if($zoom->last_renewed_on){
                                                        $last = \Carbon\Carbon::parse($zoom->last_renewed_on)->format('d M Y h:i A');
                                                    }
                                                    else{
                                                        $last = '';
                                                    }
                                                  
                                                @endphp
                                                {{$last}}
                                            </td>
                                            <td>{{$zoom->days_passed ? $zoom->days_passed : 0}}</td>
                                            <td>
                                                @php
                                                    if($zoom->last_renewed_on){
                                                        $next = \Carbon\Carbon::parse($zoom->next_renewel_on)->format('d M Y h:i A');
                                                    }
                                                    else{
                                                        $next = '';
                                                    }
                                                @endphp
                                                {{$next}}
                                            </td>
                                            <td>
                                                @if ($zoom->status == 1)
                                                      {{-- <a href="{{route('settings.zoom_account.status_change', [$zoom->id])}}" class="btn btn-success btn-sm hold-btn mr-30" title="{{__('Active')}}">
                                                            {{__('Active')}}
                                                      </a> --}}
                                                    <span  class="status label-inline font-weight-bolder mb-1 btn btn-success btn-sm">{{__('Active')}}</span>
                                                @else
                                                      {{-- <a href="{{route('settings.zoom_account.status_change', [$zoom->id])}}" class="btn btn-danger btn-sm hold-btn mr-30" title="{{__('Disabled')}}">
                                                            {{__('Disabled')}}
                                                      </a> --}}
                                                <span  class="status label-inline font-weight-bolder mb-1 btn btn-danger btn-sm">{{__('Disabled')}}</span>
                                                @endif
                                          
                                            </td>
                                         
                                            <td>
                                                <div class="action__buttons">
                                                    <a href="{{ route('settings.zoom_account.edit', [$zoom->id]) }}" class=" btn-action mr-1 edit" data-toggle="tooltip" title="Edit">
                                                        <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                    </a>
                                                    {{-- <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_{{$zoom->id}}">
                                                        <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                    </button>

                                                    <form action="{{ route('settings.currency.delete', $zoom->id) }}" method="post" id="delete_row_form_{{ $zoom->id }}">
                                                        {{ method_field('DELETE') }}
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </form> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->

    <!-- Add Modal section start -->
    <div class="modal fade" id="add-zoom-account-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div style="width:1000px; max-height: 550px; overflow-y: auto;" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Zoom Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('settings.zoom_account.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">



                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('SDK Key') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input required type="text" name="sdk_key" value="{{ old('sdk_key')}}" class="form-control" placeholder="{{__('Zoom SDK Key')}}" required>
                        @if ($errors->has('sdk_key'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('sdk_key') }}</span>
                        @endif
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('SDK Secret') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="sdk_secret" value="{{old('sdk_secret')}}" class="form-control" placeholder="{{__('Zoom SDK Secret')}}" required>
                        @if ($errors->has('sdk_secret'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('sdk_secret') }}</span>
                        @endif</div>
                        </div>

                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('API Key') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_key" value="{{old('api_key')}}" class="form-control" placeholder="{{__('Zoom API Key')}}" required>
                        @if ($errors->has('api_key'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_key') }}</span>
                        @endif</div>
                        </div>


                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('API Secret') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_secret" value="{{old('api_secret')}}" class="form-control" placeholder="{{__('Zoom API Secret')}}" required>
                        @if ($errors->has('api_secret'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_secret') }}</span>
                        @endif</div>
                        </div>


                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('JWT Token') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_jwt_token" value="{{old('api_jwt_token')}}" class="form-control" placeholder="{{__('Zoom API JWT TOKEN')}}" required>
                        @if ($errors->has('api_jwt_token'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_jwt_token') }}</span>
                        @endif</div>
                        </div>

                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Status') }}<span
                                    class="text-danger"></span></label>
                            <div class="col-lg-9">
                            <select name="status" id="status" class="" required>
                            <option value="">{{__('Select Option')}}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0" selected>{{ __('Disable') }}</option>
                        </select>    
                        </div>
                        </div>







                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-purple">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
@endpush
