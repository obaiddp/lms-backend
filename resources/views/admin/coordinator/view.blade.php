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
                                <h2>{{__('Coordinator Profile')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item"><a href="{{route('coordinator.index')}}">{{__('All Coordinators')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Coordinator Profile')}}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="profile__item bg-style">
                        <div class="profile__item__top">
                            <div class="user-img">
                                <img src="{{asset($coordinator->user ? $coordinator->user->image_path  : '')}}" alt="img">
                            </div>
                            <div class="user-text">
                                <h2>{{$coordinator->name}}</h2>
                                <h3>{{$coordinator->professional_title}}</h3>
                            </div>
                        </div>
                        <div class="profile__item__content">
                            <h2>{{__('Personal Information')}}</h2>
                            <p>
                                {{$coordinator->about_me}}
                            </p>
                        </div>
                        <ul class="profile__item__list">
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Name')}}:</h2>
                                    <p>{{$coordinator->name}}</p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Phone')}}:</h2>
                                    <p>{{$coordinator->phone_number}}</p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Email')}}:</h2>
                                    <p>{{$coordinator->user ? $coordinator->user->email : '' }}</p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{ __('Address') }}:</h2>
                                    <p>{{$coordinator->address}} </p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{ __('Location') }}:</h2>
                                    <p>{{$coordinator->city ?  $coordinator->city->name.', ' : ''}}  {{$coordinator->state ?  $coordinator->state->name.', ' : ''}} {{$coordinator->country ? $coordinator->country->country_name : ''}} </p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{ __('CV') }}:</h2>
                                    <span><a href="{{ getVideoFile($coordinator->cv_file) }}">{{ $coordinator->cv_filename }}</a> </span>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{ __('Auto Content Approval') }}:</h2>
                                    <p>{{ $coordinator->auto_content_approval == 1 ? ' Enable' : ' Disable' }} </p>
                                </div>
                            </li>

                        </ul>
                    </div>
                    <div class="profile__inbox bg-style">
                        <div class="item-title">
                            <h2>{{__('Social Links')}}</h2>
                        </div>
                        @php
                            $social_link = json_decode($coordinator->social_link);

                        @endphp
                        <div class="profile__inbox__table">
                            <table class="text-black">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-img">
                                                {{ __('Facebook') }}:
                                            </div>
                                            <div class="user-text">
                                                {{@$coordinator->social_link ? $social_link->facebook : ''}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-img">
                                                {{ __('Twitter') }}:
                                            </div>
                                            <div class="user-text">
                                                {{@$coordinator->social_link ? $social_link->twitter : ''}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-img">
                                                {{ __('Linkedin') }}:
                                            </div>
                                            <div class="user-text">
                                                {{@$coordinator->social_link ? $social_link->linkedin : ''}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-img">
                                                {{ __('Pinterest') }}:
                                            </div>
                                            <div class="user-text">
                                                {{@$coordinator->social_link ? $social_link->pinterest ?? '' : ''}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-lg-8 col-md-7">
                    <div class="profile__status__area">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="status__item bg-style">
                                    <div class="status__box__img">
                                        <img src="{{ asset('admin') }}/images/admin-dashboard-icons/laptop.png" alt="icon">
                                    </div>
                                    <div class="status-text">
                                        <h2>{{$total_instructors}}</h2>
                                        <p>{{ __('Total Instructors') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="status__item bg-style">
                                    <div class="status-img">
                                        <img src="{{asset('admin/images/status-icon/done.png')}}" alt="icon">
                                    </div>
                                    <div class="status-text">
                                        <h2>{{$published_courses}}</h2>
                                        <p>{{ __('Published Courses') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="status__item bg-style">
                                    <div class="status-img">
                                        <img src="{{asset('admin/images/status-icon/pending.png')}}" alt="icon">
                                    </div>
                                    <div class="status-text">
                                        <h2>{{$pending_courses}}</h2>
                                        <p>{{ __('Pending Courses') }}</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-4 col-md-6">
                                <div class="status__item bg-style">
                                    <div class="status-img">
                                        <img src="{{asset('admin/images/status-icon/revenue.png')}}" alt="icon">
                                    </div>
                                    <div class="status-text">
                                        <h2>
                                            @if(get_currency_placement() == 'after')
                                                {{ @$total_earning }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ @$total_earning }}
                                            @endif
                                        </h2>
                                        <p>{{ __('Total Earning') }}</p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

{{-- 
                    <div class="profile__timeline__area bg-style">
                        <div class="item-title">
                            <h2>{{__('Certifications')}}</h2>
                        </div>
                        <div class="profile__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Title of the Certificate')}}</th>
                                    <th>{{__('Year')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($instructor->certificates as $certificate)
                                    <tr>
                                        <td>
                                            <span class="data-text">{{$certificate->name}}</span>
                                        </td>
                                        <td>
                                            <span class="data-text">{{$certificate->passing_year}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
{{-- 
                    <div class="profile__table__area bg-style">
                        <div class="item-title">
                            <h2>{{__('Awards')}}</h2>
                        </div>
                        <div class="profile__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Title of the Award')}}</th>
                                    <th>{{__('Year')}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($instructor->awards as $award)
                                <tr>

                                    <td>
                                        <span class="data-text">{{$award->name}}</span>
                                    </td>
                                    <td>
                                        <span class="data-text">{{$award->winning_year}}</span>
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div> --}}

                </div>



            </div>
        </div>
    </div>
    <!-- Page content area end -->
@endsection
