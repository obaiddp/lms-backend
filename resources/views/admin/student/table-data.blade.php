      <div class="customers__table">
                            <table id="students-table" class="row-border data-table-filter table-style">
                                <input type="hidden" name="studentGetDataRoute" id="studentGetDataRoute" value="{{route('student.getData')}}">
                          
                                <thead>
                                <tr class="removable-item">
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Details')}}</th>
                                    <th>{{__('Is Featured')}}</th>
                                    <th>{{__('Country')}}</th>
                                    <th>{{__('Address')}}</th>
                                    <th>{{ __('Total Course Enroll') }}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr class="removable-item">
                                        <td>
                                             <img src="{{getImageFile($student->user ? @$student->user->image_path : '')}}" width="80">
                                        </td>
                                        <td>
                                            <a href="{{route('student.view', [$student->uuid])}}" class="btn-action mr-30" title="View Details">
                                            {{__('Name')}}: {{$student->name}}<br>
                                            {{__('Email')}}: {{$student->user?->email}}<br>
                                            {{__('Phone')}}: {{$student->phone_number ?? @$student->user->phone_number}}<br>
                                            </a>
                                        </td>
                                        <td>
                                            @php
                                                if($student->is_featured === 1){
                                                    $isFeatured = __('Yes');
                                                }
                                                else if($student->is_featured === 0){
                                                    $isFeatured = __('No');
                                                }
                                                else{
                                                    $isFeatured = __('');
                                                }
                                            @endphp
                                            {{$isFeatured}}
                                        </td>
                                        <td>{{$student->country ? $student->country->country_name : '' }}</td>
                                        <td>{{$student->address}}</td>
                                        <td>{{ studentCoursesCount($student->user_id) }}</td>
                                        <td>
                                            <span id="hidden_id" style="display: none">{{$student->id}}</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="0" @if($student->status == 0) selected @endif>{{ __('Pending') }}</option>
                                                <option value="1" @if($student->status == 1) selected @endif>{{ __('Approved') }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="{{route('student.view', [$student->uuid])}}" class="btn-action mr-30" title="View Details">
                                                    <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                                </a>
                                                <a href="{{route('student.edit', [$student->uuid])}}" class="btn-action mr-30" title="Edit Details">
                                                    <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                </a>
                                                <button class="ms-3">
                                                    <span data-formid="delete_row_form_{{$student->id}}" class="deleteItem">
                                                        <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                    </span>
                                                </button>

                                                <form action="{{route('student.delete', [$student->uuid])}}" method="post" id="delete_row_form_{{ $student->id }}">
                                                    {{ method_field('DELETE') }}
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$students->links()}}
                            </div>
                        </div>