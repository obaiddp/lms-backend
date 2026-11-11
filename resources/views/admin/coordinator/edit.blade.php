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
                                <h2>{{ __('Edit Coordinator') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Coordinator') }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                    <div class="item-title d-flex justify-content-between">
                        <h2>{{ __('Edit Instructor') }}</h2>
                    </div>
                    <form action="{{route('coordinator.update', $coordinator->uuid)}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label>{{__('First Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" value="{{ $coordinator->first_name }}" placeholder="{{__('First Name')}} " class="form-control" required>
                                    @if ($errors->has('first_name'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('first_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Last Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" value="{{$coordinator->last_name}}" placeholder="{{__('Last Name')}}" class="form-control" required>
                                    @if ($errors->has('last_name'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('last_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Email')}} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{@$coordinator->user->email}}" placeholder="{{__('Email')}}" class="form-control" required>
                                    @if ($errors->has('email'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Password') }} </label>
                                    <input type="password" name="password" value="" placeholder="{{ __('Password') }}" class="form-control">
                                    @if ($errors->has('password'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Professional Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="professional_title" value="{{$coordinator->professional_title}}" placeholder="{{__('Professional Title')}}" class="form-control"
                                           required>
                                    @if ($errors->has('professional_title'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('professional_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Area
                                        Code')
                                        }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="area_code">
                                        <option value>{{ __("Select Code") }}</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->phonecode }}" @if(old('area_code', @$coordinator->
                                            user->area_code)==$country->
                                            phonecode) selected @endif>{{ $country->short_name.'('.$country->phonecode.')' }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area_code'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('area_code') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Phone Number')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" value="{{$coordinator->phone_number}}" placeholder="{{__('Phone Number')}}" class="form-control">
                                    @if ($errors->has('phone_number'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('phone_number') }}</span>
                                    @endif
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Time Zone')
                                        }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="time_zone" required>
                                        <option value ="">{{ __("Select Time Zone") }}</option>
                                        <option value="America/Adak"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Adak") selected @endif>(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)</option>
                                        <option value="America/Atka"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Atka") selected @endif>(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)</option>
                                        <option value="America/Anchorage"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Anchorage") selected @endif>(GMT-9:00) America/Anchorage (Alaska Standard Time)</option>
                                        <option value="America/Juneau"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Juneau") selected @endif>(GMT-9:00) America/Juneau (Alaska Standard Time)</option>
                                        <option value="America/Nome"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Nome") selected @endif>(GMT-9:00) America/Nome (Alaska Standard Time)</option>
                                        <option value="America/Yakutat"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yakutat") selected @endif>(GMT-9:00) America/Yakutat (Alaska Standard Time)</option>
                                        <option value="America/Dawson"@if(old('time_zone', @$coordinator->
                                            user->area_code)=="America/Dawson") selected @endif>(GMT-8:00) America/Dawson (Pacific Standard Time)</option>
                                        <option value="America/Ensenada"@if(old('time_zone', @$coordinator->
                                            user->area_code)=="America/Ensenada") selected @endif>(GMT-8:00) America/Ensenada (Pacific Standard Time)</option>
                                        <option value="America/Los_Angeles"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Los_Angeles") selected @endif>(GMT-8:00) America/Los_Angeles (Pacific Standard Time)</option>
                                        <option value="America/Tijuana"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Tijuana") selected @endif>(GMT-8:00) America/Tijuana (Pacific Standard Time)</option>
                                        <option value="America/Vancouver"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Vancouver") selected @endif>(GMT-8:00) America/Vancouver (Pacific Standard Time)</option>
                                        <option value="America/Whitehorse"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Whitehorse") selected @endif>(GMT-8:00) America/Whitehorse (Pacific Standard Time)</option>
                                        <option value="Canada/Pacific"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Pacific") selected @endif>(GMT-8:00) Canada/Pacific (Pacific Standard Time)</option>
                                        <option value="Canada/Yukon"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Yukon") selected @endif>(GMT-8:00) Canada/Yukon (Pacific Standard Time)</option>
                                        <option value="Mexico/BajaNorte"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Mexico/BajaNorte") selected @endif>(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)</option>
                                        <option value="America/Boise"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Boise") selected @endif>(GMT-7:00) America/Boise (Mountain Standard Time)</option>
                                        <option value="America/Cambridge_Bay"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Cambridge_Bay") selected @endif>(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)</option>
                                        <option value="America/Chihuahua"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Chihuahua") selected @endif>(GMT-7:00) America/Chihuahua (Mountain Standard Time)</option>
                                        <option value="America/Dawson_Creek"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Dawson_Creek") selected @endif>(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)</option>
                                        <option value="America/Denver"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Denver") selected @endif>(GMT-7:00) America/Denver (Mountain Standard Time)</option>
                                        <option value="America/Edmonton"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Edmonton") selected @endif>(GMT-7:00) America/Edmonton (Mountain Standard Time)</option>
                                        <option value="America/Hermosillo"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Hermosillo") selected @endif>(GMT-7:00) America/Hermosillo (Mountain Standard Time)</option>
                                        <option value="America/Inuvik"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Inuvik") selected @endif>(GMT-7:00) America/Inuvik (Mountain Standard Time)</option>
                                        <option value="America/Mazatlan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Mazatlan") selected @endif>(GMT-7:00) America/Mazatlan (Mountain Standard Time)</option>
                                        <option value="America/Phoenix"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Phoenix") selected @endif>(GMT-7:00) America/Phoenix (Mountain Standard Time)</option>
                                        <option value="America/Shiprock"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Shiprock") selected @endif>(GMT-7:00) America/Shiprock (Mountain Standard Time)</option>
                                        <option value="America/Yellowknife"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yellowknife") selected @endif>(GMT-7:00) America/Yellowknife (Mountain Standard Time)</option>
                                        <option value="Canada/Mountain"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Mountain") selected @endif>(GMT-7:00) Canada/Mountain (Mountain Standard Time)</option>
                                        <option value="Mexico/BajaSur"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Mexico/BajaSur") selected @endif>(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)</option>
                                        <option value="America/Belize"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Belize") selected @endif>(GMT-6:00) America/Belize (Central Standard Time)</option>
                                        <option value="America/Cancun"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Cancun") selected @endif>(GMT-6:00) America/Cancun (Central Standard Time)</option>
                                        <option value="America/Chicago"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Chicago") selected @endif>(GMT-6:00) America/Chicago (Central Standard Time)</option>
                                        <option value="America/Costa_Rica"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Costa_Rica") selected @endif>(GMT-6:00) America/Costa_Rica (Central Standard Time)</option>
                                        <option value="America/El_Salvador"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/El_Salvador") selected @endif>(GMT-6:00) America/El_Salvador (Central Standard Time)</option>
                                        <option value="America/Guatemala"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Guatemala") selected @endif>(GMT-6:00) America/Guatemala (Central Standard Time)</option>
                                        <option value="America/Knox_IN"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Knox_IN") selected @endif>(GMT-6:00) America/Knox_IN (Central Standard Time)</option>
                                        <option value="America/Managua"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Managua") selected @endif>(GMT-6:00) America/Managua (Central Standard Time)</option>
                                        <option value="America/Menominee"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Menominee") selected @endif>(GMT-6:00) America/Menominee (Central Standard Time)</option>
                                        <option value="America/Merida"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Merida") selected @endif>(GMT-6:00) America/Merida (Central Standard Time)</option>
                                        <option value="America/Mexico_City"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Mexico_City") selected @endif>(GMT-6:00) America/Mexico_City (Central Standard Time)</option>
                                        <option value="America/Monterrey"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Monterrey") selected @endif>(GMT-6:00) America/Monterrey (Central Standard Time)</option>
                                        <option value="America/Rainy_River"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Rainy_River") selected @endif>(GMT-6:00) America/Rainy_River (Central Standard Time)</option>
                                        <option value="America/Rankin_Inlet"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Rankin_Inlet") selected @endif>(GMT-6:00) America/Rankin_Inlet (Central Standard Time)</option>
                                        <option value="America/Regina"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Regina") selected @endif>(GMT-6:00) America/Regina (Central Standard Time)</option>
                                        <option value="America/Swift_Current"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Swift_Current") selected @endif>(GMT-6:00) America/Swift_Current (Central Standard Time)</option>
                                        <option value="America/Tegucigalpa"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yakutat") selected @endif>(GMT-6:00) America/Tegucigalpa (Central Standard Time)</option>
                                        <option value="America/Winnipeg"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yakutat") selected @endif>(GMT-6:00) America/Winnipeg (Central Standard Time)</option>
                                        <option value="Canada/Central"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Central") selected @endif>(GMT-6:00) Canada/Central (Central Standard Time)</option>
                                        <option value="Canada/East-Saskatchewan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/East-Saskatchewan") selected @endif>(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)</option>
                                        <option value="Canada/Saskatchewan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Saskatchewan") selected @endif>(GMT-6:00) Canada/Saskatchewan (Central Standard Time)</option>
                                        <option value="Chile/EasterIsland"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Chile/EasterIsland") selected @endif>(GMT-6:00) Chile/EasterIsland (Easter Is. Time)</option>
                                        <option value="Mexico/General"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Mexico/General") selected @endif>(GMT-6:00) Mexico/General (Central Standard Time)</option>
                                        <option value="America/Atikokan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Atikokan") selected @endif>(GMT-5:00) America/Atikokan (Eastern Standard Time)</option>
                                        <option value="America/Bogota"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Bogota") selected @endif>(GMT-5:00) America/Bogota (Colombia Time)</option>
                                        <option value="America/Cayman"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Cayman") selected @endif>(GMT-5:00) America/Cayman (Eastern Standard Time)</option>
                                        <option value="America/Coral_Harbour"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Coral_Harbour") selected @endif>(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)</option>
                                        <option value="America/Detroit"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Detroit") selected @endif>(GMT-5:00) America/Detroit (Eastern Standard Time)</option>
                                        <option value="America/Fort_Wayne"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Fort_Wayne") selected @endif>(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)</option>
                                        <option value="America/Grand_Turk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Grand_Turk") selected @endif>(GMT-5:00) America/Grand_Turk (Eastern Standard Time)</option>
                                        <option value="America/Guayaquil"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Guayaquil") selected @endif>(GMT-5:00) America/Guayaquil (Ecuador Time)</option>
                                        <option value="America/Havana"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Havana") selected @endif>(GMT-5:00) America/Havana (Cuba Standard Time)</option>
                                        <option value="America/Indianapolis"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Indianapolis") selected @endif>(GMT-5:00) America/Indianapolis (Eastern Standard Time)</option>
                                        <option value="America/Iqaluit"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Iqaluit") selected @endif>(GMT-5:00) America/Iqaluit (Eastern Standard Time)</option>
                                        <option value="America/Jamaica"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Jamaica") selected @endif>(GMT-5:00) America/Jamaica (Eastern Standard Time)</option>
                                        <option value="America/Lima"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Lima") selected @endif>(GMT-5:00) America/Lima (Peru Time)</option>
                                        <option value="America/Louisville"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Louisville") selected @endif>(GMT-5:00) America/Louisville (Eastern Standard Time)</option>
                                        <option value="America/Montreal"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Montreal") selected @endif>(GMT-5:00) America/Montreal (Eastern Standard Time)</option>
                                        <option value="America/Nassau"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Nassau") selected @endif>(GMT-5:00) America/Nassau (Eastern Standard Time)</option>
                                        <option value="America/New_York"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/New_York") selected @endif>(GMT-5:00) America/New_York (Eastern Standard Time)</option>
                                        <option value="America/Nipigon"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Nipigon") selected @endif>(GMT-5:00) America/Nipigon (Eastern Standard Time)</option>
                                        <option value="America/Panama"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Panama") selected @endif>(GMT-5:00) America/Panama (Eastern Standard Time)</option>
                                        <option value="America/Pangnirtung"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Pangnirtung") selected @endif>(GMT-5:00) America/Pangnirtung (Eastern Standard Time)</option>
                                        <option value="America/Port-au-Prince"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Port-au-Prince") selected @endif>(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)</option>
                                        <option value="America/Resolute"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Resolute") selected @endif>(GMT-5:00) America/Resolute (Eastern Standard Time)</option>
                                        <option value="America/Thunder_Bay"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Thunder_Bay") selected @endif>(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)</option>
                                        <option value="America/Toronto"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Toronto") selected @endif>(GMT-5:00) America/Toronto (Eastern Standard Time)</option>
                                        <option value="Canada/Eastern"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Canada/Eastern") selected @endif>(GMT-5:00) Canada/Eastern (Eastern Standard Time)</option>
                                        <option value="America/Caracas"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Caracas") selected @endif>(GMT-4:-30) America/Caracas (Venezuela Time)</option>
                                        <option value="America/Anguilla"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Anguilla") selected @endif>(GMT-4:00) America/Anguilla (Atlantic Standard Time)</option>
                                        <option value="America/Antigua"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Antigua") selected @endif>(GMT-4:00) America/Antigua (Atlantic Standard Time)</option>
                                        <option value="America/Aruba"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Aruba") selected @endif>(GMT-4:00) America/Aruba (Atlantic Standard Time)</option>
                                        <option value="America/Asuncion"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Asuncion") selected @endif>(GMT-4:00) America/Asuncion (Paraguay Time)</option>
                                        <option value="America/Barbados"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Barbados") selected @endif>(GMT-4:00) America/Barbados (Atlantic Standard Time)</option>
                                        <option value="America/Blanc-Sablon"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Blanc-Sablon") selected @endif>(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)</option>
                                        <option value="America/Boa_Vista"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Boa_Vista") selected @endif>(GMT-4:00) America/Boa_Vista (Amazon Time)</option>
                                        <option value="America/Campo_Grande"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Campo_Grande") selected @endif>(GMT-4:00) America/Campo_Grande (Amazon Time)</option>
                                        <option value="America/Cuiaba"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Cuiaba") selected @endif>(GMT-4:00) America/Cuiaba (Amazon Time)</option>
                                        <option value="America/Curacao"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Curacao") selected @endif>(GMT-4:00) America/Curacao (Atlantic Standard Time)</option>
                                        <option value="America/Dominica"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Dominica") selected @endif>(GMT-4:00) America/Dominica (Atlantic Standard Time)</option>
                                        <option value="America/Eirunepe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Eirunepe") selected @endif>(GMT-4:00) America/Eirunepe (Amazon Time)</option>
                                        <option value="America/Glace_Bay"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Glace_Bay") selected @endif>(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)</option>
                                        <option value="America/Goose_Bay"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Goose_Bay") selected @endif>(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)</option>
                                        <option value="America/Grenada"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Grenada") selected @endif>(GMT-4:00) America/Grenada (Atlantic Standard Time)</option>
                                        <option value="America/Guadeloupe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Guadeloupe") selected @endif>(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)</option>
                                        <option value="America/Guyana"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Guyana") selected @endif>(GMT-4:00) America/Guyana (Guyana Time)</option>
                                        <option value="America/Halifax"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Halifax") selected @endif>(GMT-4:00) America/Halifax (Atlantic Standard Time)</option>
                                        <option value="America/La_Paz"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/La_Paz") selected @endif>(GMT-4:00) America/La_Paz (Bolivia Time)</option>
                                        <option value="America/Manaus"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Manaus") selected @endif>(GMT-4:00) America/Manaus (Amazon Time)</option>
                                        <option value="America/Marigot"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Marigot") selected @endif>(GMT-4:00) America/Marigot (Atlantic Standard Time)</option>
                                        
                                        
                                        
                                      
                                        
                                        
                                        <option value="Asia/Nicosia"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Nicosia") selected @endif>(GMT+2:00) Asia/Nicosia (Eastern European Time)</option>
                                        <option value="Asia/Tel_Aviv"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Tel_Aviv") selected @endif>(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)</option>
                                        <option value="Europe/Athens"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Athens") selected @endif>(GMT+2:00) Europe/Athens (Eastern European Time)</option>
                                        <option value="Europe/Bucharest"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Bucharest") selected @endif>(GMT+2:00) Europe/Bucharest (Eastern European Time)</option>
                                        <option value="Europe/Chisinau"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Chisinau") selected @endif>(GMT+2:00) Europe/Chisinau (Eastern European Time)</option>
                                        <option value="Europe/Helsinki"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Helsinki") selected @endif>(GMT+2:00) Europe/Helsinki (Eastern European Time)</option>
                                        <option value="Europe/Istanbul"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Istanbul") selected @endif>(GMT+2:00) Europe/Istanbul (Eastern European Time)</option>
                                        <option value="Europe/Kaliningrad"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Kaliningrad") selected @endif>(GMT+2:00) Europe/Kaliningrad (Eastern European Time)</option>
                                        <option value="Europe/Kiev"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Kiev") selected @endif>(GMT+2:00) Europe/Kiev (Eastern European Time)</option>
                                        <option value="Europe/Mariehamn"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Mariehamn") selected @endif>(GMT+2:00) Europe/Mariehamn (Eastern European Time)</option>
                                        <option value="Europe/Minsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Minsk") selected @endif>(GMT+2:00) Europe/Minsk (Eastern European Time)</option>
                                        <option value="Europe/Nicosia"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Nicosia") selected @endif>(GMT+2:00) Europe/Nicosia (Eastern European Time)</option>
                                        <option value="Europe/Riga"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Riga") selected @endif>(GMT+2:00) Europe/Riga (Eastern European Time)</option>
                                        <option value="Europe/Simferopol"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Simferopol") selected @endif>(GMT+2:00) Europe/Simferopol (Eastern European Time)</option>
                                        <option value="Europe/Sofia"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Sofia") selected @endif>(GMT+2:00) Europe/Sofia (Eastern European Time)</option>
                                        <option value="Europe/Tallinn"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Tallinn") selected @endif>(GMT+2:00) Europe/Tallinn (Eastern European Time)</option>
                                        <option value="Europe/Tiraspol"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Tiraspol") selected @endif>(GMT+2:00) Europe/Tiraspol (Eastern European Time)</option>
                                        <option value="Europe/Uzhgorod"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Uzhgorod") selected @endif>(GMT+2:00) Europe/Uzhgorod (Eastern European Time)</option>
                                        <option value="Europe/Vilnius"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Vilnius") selected @endif>(GMT+2:00) Europe/Vilnius (Eastern European Time)</option>
                                        <option value="Europe/Zaporozhye"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Zaporozhye") selected @endif>(GMT+2:00) Europe/Zaporozhye (Eastern European Time)</option>
                                        <option value="Africa/Addis_Ababa"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Addis_Ababa") selected @endif>(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)</option>
                                        <option value="Africa/Asmara"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Asmara") selected @endif>(GMT+3:00) Africa/Asmara (Eastern African Time)</option>
                                        <option value="Africa/Asmera"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Asmera") selected @endif>(GMT+3:00) Africa/Asmera (Eastern African Time)</option>
                                        <option value="Africa/Dar_es_Salaam"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Dar_es_Salaam") selected @endif>(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)</option>
                                        <option value="Africa/Djibouti"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Djibouti") selected @endif>(GMT+3:00) Africa/Djibouti (Eastern African Time)</option>
                                        <option value="Africa/Kampala"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Kampala") selected @endif>(GMT+3:00) Africa/Kampala (Eastern African Time)</option>
                                        <option value="Africa/Khartoum"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Khartoum") selected @endif>(GMT+3:00) Africa/Khartoum (Eastern African Time)</option>
                                        <option value="Africa/Mogadishu"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Mogadishu") selected @endif>(GMT+3:00) Africa/Mogadishu (Eastern African Time)</option>
                                        <option value="Africa/Nairobi"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Africa/Nairobi") selected @endif>(GMT+3:00) Africa/Nairobi (Eastern African Time)</option>
                                        <option value="Antarctica/Syowa"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/Syowa") selected @endif>(GMT+3:00) Antarctica/Syowa (Syowa Time)</option>
                                        <option value="Asia/Aden"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Aden") selected @endif>(GMT+3:00) Asia/Aden (Arabia Standard Time)</option>
                                        <option value="Asia/Baghdad"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Baghdad") selected @endif>(GMT+3:00) Asia/Baghdad (Arabia Standard Time)</option>
                                        <option value="Asia/Bahrain"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Bahrain") selected @endif>(GMT+3:00) Asia/Bahrain (Arabia Standard Time)</option>
                                        <option value="Asia/Kuwait"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kuwait") selected @endif>(GMT+3:00) Asia/Kuwait (Arabia Standard Time)</option>
                                        <option value="Asia/Qatar"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Qatar") selected @endif>(GMT+3:00) Asia/Qatar (Arabia Standard Time)</option>
                                        <option value="Europe/Moscow"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Moscow") selected @endif>(GMT+3:00) Europe/Moscow (Moscow Standard Time)</option>
                                        <option value="Europe/Volgograd"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yakutat") selected @endif>(GMT+3:00) Europe/Volgograd (Volgograd Time)</option>
                                        <option value="Indian/Antananarivo"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Antananarivo") selected @endif>(GMT+3:00) Indian/Antananarivo (Eastern African Time)</option>
                                        <option value="Indian/Comoro"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Comoro") selected @endif>(GMT+3:00) Indian/Comoro (Eastern African Time)</option>
                                        <option value="Indian/Mayotte"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Mayotte") selected @endif>(GMT+3:00) Indian/Mayotte (Eastern African Time)</option>
                                        <option value="Asia/Tehran"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Tehran") selected @endif>(GMT+3:30) Asia/Tehran (Iran Standard Time)</option>
                                        <option value="Asia/Baku"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Baku") selected @endif>(GMT+4:00) Asia/Baku (Azerbaijan Time)</option>
                                        <option value="Asia/Dubai"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Dubai") selected @endif>(GMT+4:00) Asia/Dubai (Gulf Standard Time)</option>
                                        <option value="Asia/Muscat"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Muscat") selected @endif>(GMT+4:00) Asia/Muscat (Gulf Standard Time)</option>
                                        <option value="Asia/Tbilisi"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Tbilisi") selected @endif>(GMT+4:00) Asia/Tbilisi (Georgia Time)</option>
                                        <option value="Asia/Yerevan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Yerevan") selected @endif>(GMT+4:00) Asia/Yerevan (Armenia Time)</option>
                                        <option value="Europe/Samara"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Europe/Samara") selected @endif>(GMT+4:00) Europe/Samara (Samara Time)</option>
                                        <option value="Indian/Mahe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Mahe") selected @endif>(GMT+4:00) Indian/Mahe (Seychelles Time)</option>
                                        <option value="Indian/Mauritius"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Mauritius") selected @endif>(GMT+4:00) Indian/Mauritius (Mauritius Time)</option>
                                        <option value="Indian/Reunion"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Reunion") selected @endif>(GMT+4:00) Indian/Reunion (Reunion Time)</option>
                                        <option value="Asia/Kabul"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kabul") selected @endif>(GMT+4:30) Asia/Kabul (Afghanistan Time)</option>
                                        <option value="Asia/Aqtau"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Aqtau") selected @endif>(GMT+5:00) Asia/Aqtau (Aqtau Time)</option>
                                        <option value="Asia/Aqtobe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Aqtobe") selected @endif>(GMT+5:00) Asia/Aqtobe (Aqtobe Time)</option>
                                        <option value="Asia/Ashgabat"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="America/Yakutat") selected @endif>(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)</option>
                                        <option value="Asia/Ashkhabad"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Ashkhabad") selected @endif>(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)</option>
                                        <option value="Asia/Dushanbe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Dushanbe") selected @endif>(GMT+5:00) Asia/Dushanbe (Tajikistan Time)</option>
                                        <option value="Asia/Karachi"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Karachi") selected @endif>(GMT+5:00) Asia/Karachi (Pakistan Time)</option>
                                        <option value="Asia/Oral"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Oral") selected @endif>(GMT+5:00) Asia/Oral (Oral Time)</option>
                                        <option value="Asia/Samarkand"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Samarkand") selected @endif>(GMT+5:00) Asia/Samarkand (Uzbekistan Time)</option>
                                        <option value="Asia/Tashkent"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Tashkent") selected @endif>(GMT+5:00) Asia/Tashkent (Uzbekistan Time)</option>
                                        <option value="Asia/Yekaterinburg"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Yekaterinburg") selected @endif>(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)</option>
                                        <option value="Indian/Kerguelen"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Kerguelen") selected @endif>(GMT+5:00) Indian/Kerguelen (French Southern &amp; Antarctic Lands Time)</option>
                                        <option value="Indian/Maldives"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Maldives") selected @endif>(GMT+5:00) Indian/Maldives (Maldives Time)</option>
                                        <option value="Asia/Calcutta"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Calcutta") selected @endif>(GMT+5:30) Asia/Calcutta (India Standard Time)</option>
                                        <option value="Asia/Colombo"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Colombo") selected @endif>(GMT+5:30) Asia/Colombo (India Standard Time)</option>
                                        <option value="Asia/Kolkata"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kolkata") selected @endif>(GMT+5:30) Asia/Kolkata (India Standard Time)</option>
                                        <option value="Asia/Katmandu"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Katmandu") selected @endif>(GMT+5:45) Asia/Katmandu (Nepal Time)</option>
                                        <option value="Antarctica/Mawson"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/Mawson") selected @endif>(GMT+6:00) Antarctica/Mawson (Mawson Time)</option>
                                        <option value="Antarctica/Vostok"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/Vostok") selected @endif>(GMT+6:00) Antarctica/Vostok (Vostok Time)</option>
                                        <option value="Asia/Almaty"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Almaty") selected @endif>(GMT+6:00) Asia/Almaty (Alma-Ata Time)</option>
                                        <option value="Asia/Bishkek"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Bishkek") selected @endif>(GMT+6:00) Asia/Bishkek (Kirgizstan Time)</option>
                                        <option value="Asia/Dacca"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Dacca") selected @endif>(GMT+6:00) Asia/Dacca (Bangladesh Time)</option>
                                        <option value="Asia/Dhaka"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Dhaka") selected @endif>(GMT+6:00) Asia/Dhaka (Bangladesh Time)</option>
                                        <option value="Asia/Novosibirsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Novosibirsk") selected @endif>(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)</option>
                                        <option value="Asia/Omsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Omsk") selected @endif>(GMT+6:00) Asia/Omsk (Omsk Time)</option>
                                        <option value="Asia/Qyzylorda"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Qyzylorda") selected @endif>(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)</option>
                                        <option value="Asia/Thimbu"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Thimbu") selected @endif>(GMT+6:00) Asia/Thimbu (Bhutan Time)</option>
                                        <option value="Asia/Thimphu"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Thimphu") selected @endif>(GMT+6:00) Asia/Thimphu (Bhutan Time)</option>
                                        <option value="Indian/Chagos"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Chagos") selected @endif>(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)</option>
                                        <option value="Asia/Rangoon"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Rangoon") selected @endif>(GMT+6:30) Asia/Rangoon (Myanmar Time)</option>
                                        <option value="Indian/Cocos"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Cocos") selected @endif>(GMT+6:30) Indian/Cocos (Cocos Islands Time)</option>
                                        <option value="Antarctica/Davis"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/Davis") selected @endif>(GMT+7:00) Antarctica/Davis (Davis Time)</option>
                                        <option value="Asia/Bangkok"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Bangkok") selected @endif>(GMT+7:00) Asia/Bangkok (Indochina Time)</option>
                                        <option value="Asia/Ho_Chi_Minh"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Ho_Chi_Minh") selected @endif>(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)</option>
                                        <option value="Asia/Hovd"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Hovd") selected @endif>(GMT+7:00) Asia/Hovd (Hovd Time)</option>
                                        <option value="Asia/Jakarta"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Jakarta") selected @endif>(GMT+7:00) Asia/Jakarta (West Indonesia Time)</option>
                                        <option value="Asia/Krasnoyarsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Krasnoyarsk") selected @endif>(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)</option>
                                        <option value="Asia/Phnom_Penh"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Phnom_Penh") selected @endif>(GMT+7:00) Asia/Phnom_Penh (Indochina Time)</option>
                                        <option value="Asia/Pontianak"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Pontianak") selected @endif>(GMT+7:00) Asia/Pontianak (West Indonesia Time)</option>
                                        <option value="Asia/Saigon"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Saigon") selected @endif>(GMT+7:00) Asia/Saigon (Indochina Time)</option>
                                        <option value="Asia/Vientiane"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Vientiane") selected @endif>(GMT+7:00) Asia/Vientiane (Indochina Time)</option>
                                        <option value="Indian/Christmas"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Indian/Christmas") selected @endif>(GMT+7:00) Indian/Christmas (Christmas Island Time)</option>
                                        <option value="Antarctica/Casey"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/Casey") selected @endif>(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))</option>
                                        <option value="Asia/Brunei"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Brunei") selected @endif>(GMT+8:00) Asia/Brunei (Brunei Time)</option>
                                        <option value="Asia/Choibalsan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Choibalsan") selected @endif>(GMT+8:00) Asia/Choibalsan (Choibalsan Time)</option>
                                        <option value="Asia/Chongqing"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Chongqing") selected @endif>(GMT+8:00) Asia/Chongqing (China Standard Time)</option>
                                        <option value="Asia/Chungking"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Chungking") selected @endif>(GMT+8:00) Asia/Chungking (China Standard Time)</option>
                                        <option value="Asia/Harbin"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Harbin") selected @endif>(GMT+8:00) Asia/Harbin (China Standard Time)</option>
                                        <option value="Asia/Hong_Kong"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Hong_Kong") selected @endif>(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)</option>
                                        <option value="Asia/Irkutsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Irkutsk") selected @endif>(GMT+8:00) Asia/Irkutsk (Irkutsk Time)</option>
                                        <option value="Asia/Kashgar"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kashgar") selected @endif>(GMT+8:00) Asia/Kashgar (China Standard Time)</option>
                                        <option value="Asia/Kuala_Lumpur"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kuala_Lumpur") selected @endif>(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)</option>
                                        <option value="Asia/Kuching"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kuching") selected @endif>(GMT+8:00) Asia/Kuching (Malaysia Time)</option>
                                        <option value="Asia/Macao"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Macao") selected @endif>(GMT+8:00) Asia/Macao (China Standard Time)</option>
                                        <option value="Asia/Macau"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Macau") selected @endif>(GMT+8:00) Asia/Macau (China Standard Time)</option>
                                        <option value="Asia/Makassar"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Makassar") selected @endif>(GMT+8:00) Asia/Makassar (Central Indonesia Time)</option>
                                        <option value="Asia/Manila"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Manila") selected @endif>(GMT+8:00) Asia/Manila (Philippines Time)</option>
                                        <option value="Asia/Shanghai"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Shanghai") selected @endif>(GMT+8:00) Asia/Shanghai (China Standard Time)</option>
                                        <option value="Asia/Singapore"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Singapore") selected @endif>(GMT+8:00) Asia/Singapore (Singapore Time)</option>
                                        <option value="Asia/Taipei"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Taipei") selected @endif>(GMT+8:00) Asia/Taipei (China Standard Time)</option>
                                        <option value="Asia/Ujung_Pandang"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Ujung_Pandang") selected @endif>(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)</option>
                                        <option value="Asia/Ulaanbaatar"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Ulaanbaatar") selected @endif>(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)</option>
                                        <option value="Asia/Ulan_Bator"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Ulan_Bator") selected @endif>(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)</option>
                                        <option value="Asia/Urumqi"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Urumqi") selected @endif>(GMT+8:00) Asia/Urumqi (China Standard Time)</option>
                                        <option value="Australia/Perth"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Perth") selected @endif>(GMT+8:00) Australia/Perth (Western Standard Time (Australia))</option>
                                        <option value="Australia/West"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/West") selected @endif>(GMT+8:00) Australia/West (Western Standard Time (Australia))</option>
                                        <option value="Australia/Eucla"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Eucla") selected @endif>(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))</option>
                                        <option value="Asia/Dili"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Dili") selected @endif>(GMT+9:00) Asia/Dili (Timor-Leste Time)</option>
                                        <option value="Asia/Jayapura"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Jayapura") selected @endif>(GMT+9:00) Asia/Jayapura (East Indonesia Time)</option>
                                        <option value="Asia/Pyongyang"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Pyongyang") selected @endif>(GMT+9:00) Asia/Pyongyang (Korea Standard Time)</option>
                                        <option value="Asia/Seoul"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Seoul") selected @endif>(GMT+9:00) Asia/Seoul (Korea Standard Time)</option>
                                        <option value="Asia/Tokyo"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Tokyo") selected @endif>(GMT+9:00) Asia/Tokyo (Japan Standard Time)</option>
                                        <option value="Asia/Yakutsk"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Yakutsk") selected @endif>(GMT+9:00) Asia/Yakutsk (Yakutsk Time)</option>
                                        <option value="Australia/Adelaide"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Adelaide") selected @endif>(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))</option>
                                        <option value="Australia/Broken_Hill"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Broken_Hill") selected @endif>(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))
                                        </option>
                                        <option value="Australia/Darwin"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Darwin") selected @endif>(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))</option>
                                        <option value="Australia/North"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/North") selected @endif>(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))</option>
                                        <option value="Australia/South"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/South") selected @endif>(GMT+9:30) Australia/South (Central Standard Time (South Australia))</option>
                                        <option value="Australia/Yancowinna"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Yancowinna") selected @endif>(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))</option>
                                        <option value="Antarctica/DumontDUrville"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/DumontDUrville") selected @endif>(GMT+10:00) Antarctica/DumontDUrville (Dumont-d'Urville Time)</option>
                                        <option value="Asia/Sakhalin"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Sakhalin") selected @endif>(GMT+10:00) Asia/Sakhalin (Sakhalin Time)</option>
                                        <option value="Asia/Vladivostok"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Vladivostok") selected @endif>(GMT+10:00) Asia/Vladivostok (Vladivostok Time)</option>
                                        <option value="Australia/ACT"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/ACT") selected @endif>(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))</option>
                                        <option value="Australia/Brisbane"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Brisbane") selected @endif>(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))</option>
                                        <option value="Australia/Canberra"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Canberra") selected @endif>(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))</option>
                                        <option value="Australia/Currie"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Currie") selected @endif>(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))</option>
                                        <option value="Australia/Hobart"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Hobart") selected @endif>(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))</option>
                                        <option value="Australia/Lindeman"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Lindeman") selected @endif>(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))</option>
                                        <option value="Australia/Melbourne"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Melbourne") selected @endif>(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))</option>
                                        <option value="Australia/NSW"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/NSW") selected @endif>(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))</option>
                                        <option value="Australia/Queensland"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Queensland") selected @endif>(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))</option>
                                        <option value="Australia/Sydney"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Sydney") selected @endif>(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))</option>
                                        <option value="Australia/Tasmania"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Tasmania") selected @endif>(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))</option>
                                        <option value="Australia/Victoria"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Victoria") selected @endif>(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))</option>
                                        <option value="Australia/LHI"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/LHI") selected @endif>(GMT+10:30) Australia/LHI (Lord Howe Standard Time)</option>
                                        <option value="Australia/Lord_Howe"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Australia/Lord_Howe") selected @endif>(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)</option>
                                        <option value="Asia/Magadan"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Magadan") selected @endif>(GMT+11:00) Asia/Magadan (Magadan Time)</option>
                                        <option value="Antarctica/McMurdo"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/McMurdo") selected @endif>(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)</option>
                                        <option value="Antarctica/South_Pole"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Antarctica/South_Pole") selected @endif>(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)</option>
                                        <option value="Asia/Anadyr"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Anadyr") selected @endif>(GMT+12:00) Asia/Anadyr (Anadyr Time)</option>
                                        <option value="Asia/Kamchatka"@if(old('time_zone', @$coordinator->
                                            user->time_zone)=="Asia/Kamchatka") selected @endif>(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)</option>
                                    </select>
                                    @if ($errors->has('time_zone'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('time_zone') }}</span>
                                    @endif
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Address') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="address" value="{{$coordinator->address}}" placeholder="{{ __('Address') }}" class="form-control"
                                           required>
                                    @if ($errors->has('address'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('address') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Postal Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" value="{{$coordinator->postal_code}}" placeholder="{{ __('Postal Code') }}" class="form-control"
                                           required>
                                    @if ($errors->has('postal_code'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('postal_code') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Country')}}</label>
                                    <select name="country_id" id="country_id" class="form-select">
                                        <option value="">{{__('Select Country')}}</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" @if(old('country_id'))
                                                {{old('country_id') == $country->id ? 'selected' : '' }}
                                                @else
                                                {{$coordinator->country_id == $country->id ? 'selected' : '' }}
                                                @endif >{{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('State')}}</label>
                                    <select name="state_id" id="state_id" class="form-select">
                                        <option value="">{{__('Select State')}}</option>
                                        @if(old('country_id'))
                                            @foreach($states as $state)
                                                <option value="{{$state->id}}" {{old('state_id') == $state->id ? 'selected' : ''}} >{{$state->name}}</option>
                                            @endforeach
                                        @else
                                            @if($coordinator->country)
                                                @foreach($coordinator->country->states as $selected_state)
                                                    <option
                                                        value="{{$selected_state->id}}" {{$coordinator->state_id == $selected_state->id ? 'selected' : '' }} >{{$selected_state->name}}</option>
                                                @endforeach
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('City')}}</label>
                                    <select name="city_id" id="city_id" class="form-select">
                                        <option value="">{{__('Select City')}}</option>
                                        @if(old('state_id'))
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}" {{old('city_id') == $city->id ? 'selected' : '' }} >{{$city->name}}</option>
                                            @endforeach
                                        @else
                                            @if($coordinator->state)
                                                @foreach($coordinator->state->cities as $selected_city)
                                                    <option value="{{$selected_city->id}}" {{$coordinator->city_id == $selected_city->id ? 'selected' : '' }} >{{$selected_city->name}}</option>
                                                @endforeach
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Gender')}}<span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="">{{__('Select Option')}}</option>
                                        <option value="Male" {{ $coordinator->gender == 'Male' ? 'selected' : '' }} >{{ __('Male') }}</option>
                                        <option value="Female" {{ $coordinator->gender == 'Female' ? 'selected' : '' }} >{{ __('Female') }}</option>
                                        <option value="Others" {{ $coordinator->gender == 'Others' ? 'selected' : '' }} >{{ __('Others') }}</option>
                                    </select>
                                </div>
                            </div>
                            @php
                                $social_link = json_decode($coordinator->social_link);
                            @endphp
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Facebook') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="social_link[facebook]" value="{{$coordinator->social_link ? $social_link->facebook : ''}}" class="form-control"
                                    placeholder="https://facebook.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Twitter') }}</label>
                                    <input type="text" name="social_link[twitter]" value="{{$coordinator->social_link ? $social_link->twitter : ''}}" class="form-control"
                               placeholder="https://twitter.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Linkedin') }}</label>
                                    <input type="text" name="social_link[linkedin]" value="{{$coordinator->social_link ? $social_link->linkedin : ''}}" class="form-control"
                               placeholder="https://linkedin.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Pinterest') }}</label>
                                    <input type="text" name="social_link[pinterest]" value="{{$coordinator->social_link ? $social_link->pinterest : ''}}" class="form-control"
                               placeholder="https://pinterest.com">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Courses (Live)')
                                        }}</label>
                                    <select class="form-control" name="cour_id[]" multiple="multiple" id="course_selectpicker">
                                        <option disabled>Select Course</option>
                                            @if(!$courses->isEmpty())
                                                 @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}" <?php if( in_array($course->id, $check_courses)){ echo 'selected="selected"'; } ?>>
                                                        <?php 

                                                    echo "<strong>".$course->title."</strong>";
                                                    echo ' ('.$course->id.')';
                                                    ?>
                                                    </option>
                                                 @endforeach
                                            @endif
                                    </select>
                                    @if ($errors->has('cour_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('cour_id') }}</span>
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label>{{ __('About Instructor') }} <span class="text-danger">*</span></label>
                                    <textarea name="about_me" id="" cols="15" rows="5" required>{{ $coordinator->about_me }}</textarea>
                                    @if ($errors->has('about_me'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('about_me') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="upload-img-box mb-25">
                                    @if(@$coordinator->user->image)
                                        <img src="{{asset(@$coordinator->user->image_path)}}" alt="img">
                                    @else
                                        <img src="" alt="No img">
                                    @endif
                                    <input type="file" name="image" id="image" accept="image/*" onchange="previewFile(this)">
                                    <div class="upload-img-box-icon">
                                        <i class="fa fa-camera"></i>
                                        <p class="m-0">{{__('Image')}}</p>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('image'))
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('image') }}</span>
                            @endif
                            <p>{{ __('Accepted Image Files') }}: JPEG, JPG, PNG <br> {{ __('Accepted Size') }}: 300 x 300 (1MB)</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>

            <script>
            
            $(document).ready(function () {
                $('#course_selectpicker').select2({
                    width: '100%',
                    // allowClear: true,
                    placeholder: 'Select Course',
                    
                }); 
            });



            </script>


@endpush

