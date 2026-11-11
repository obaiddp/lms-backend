<div class="tab-pane fade" id="Resources" role="tabpanel" aria-labelledby="Resources-tab">
    <div class="row">
        <div class="col-12">
            {{-- <div class="after-purchase-course-watch-tab bg-white p-40"> --}}
                <!-- If there is no data Show Empty Design Start -->
                {{-- <div class="empty-data d-none">
                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                    <h5 class="my-3">{{ __('Empty Resources') }}</h5>
                </div> --}}
                <!-- If there is no data Show Empty Design End -->
                @if(count($categories) > 0)

                <div class="table-responsive">
                    <div class="back-btn" id="back-btn" style="margin-top:5px; display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" class="bi bi-arrow-left"
                            viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                        </svg>
                    </div>
                    <br>

                    <table id="resourcesCategoriesContainer" class="table">
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div class="course-box resource-list-text" onclick="showResources({{$category->id}},{{$courseID}})">
                                        {{-- <span class="iconify" data-icon="akar-icons:link-chain"></span> --}}
                                        {{ @$category->name }}
                                        {{-- <a href="{{ getVideoFile($category->id) }}"
                                           class="text-decoration-underline">{{ @$category->name }}</a> --}}
                                    </div>
                                </td>
                              
                            </tr>
                        @endforeach
                    </table>
                </div>

                
                @else
                    <!-- If there is no data Show Empty Design Start -->
                    <div class="empty-data">
                        <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                        <h5 class="my-3">{{ __('Empty Resource Categories') }}</h5>
                    </div>
                    <!-- If there is no data Show Empty Design End -->
                @endif


                <div hidden id="resourcesContainer" class="resources-container">
                    <!-- Resources for the selected course will be displayed here -->
                </div>

            {{-- </div> --}}
        </div>

     

        
    </div>


  

</div>
