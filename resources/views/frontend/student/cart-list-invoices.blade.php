@extends('frontend.layouts.app')

@section('content')
<div  class="bg-page">
      <header class="page-banner-header blank-page-banner-header gradient-bg position-relative">
        <div class="section-overlay">
            <div class="blank-page-banner-wrap">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="page-banner-content text-center">
                                <h3 class="page-banner-heading color-heading pb-15">{{ __('Live Invoices') }}</h3>

                                <!-- Breadcrumb Start-->
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb justify-content-center">
                                        <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{__('Home')}}</a>
                                        </li>
                                        <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Live Invoices') }}
                                        </li>
                                    </ol>
                                </nav>
                                <!-- Breadcrumb End-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <section class="wishlist-page-area cart-page-area">
        <div class="container">
            @if($invoices->isEmpty())
                <div class="alert alert-info">{{ __('No pending invoices found.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Course') }}</th>
                                <th>{{ __('Invoice Date') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $invoice->course->title ?? 'N/A' }}</td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('student.checkoutInvoice', $invoice->id) }}" 
                                       class="btn btn-success btn-sm">
                                        {{ __('Checkout') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
  
</div>
@endsection