<div class="customers__table all-course">
    <table id="resources-table" class="row-border data-table-filter table-style">
        {{-- <input type="hidden" name="resourceGetDataRoute" id="resourceGetDataRoute" value="{{route('admin.resource.getData')}}"> --}}
        <thead>
        <tr>
            <th>{{__('Sr. #')}}</th>
            <th>{{__('Resource ID')}}</th>
            <th>{{__('Resource Name')}}</th>
            <th>{{__('Resource Category')}}</th>
            <th>{{__('Resource Type')}}</th>
            <th>{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
            @php
                $i = 0
            @endphp
        @foreach($resources as $resource)
            <tr class="removable-item">
                <td>
                   {{++$i}}
                </td>
                <td>
                    {{'LMS-R-'}}{{$resource->id}}
                </td>
                <td>
                       @if($resource->type == 'Text' || $resource->type == 'Youtube')
                            <a target="_blank"  href="{{route('admin.resource.viewfullother', ['resource_id'=>$resource->id])}}" class="btn-action mr-30" title="View Resource">
                                {{$resource->name}}
                            </a>
                        @else
                             @if ($resource->file_extension && $resource->file_name)
                                <a target="_blank"  href="{{route('admin.resource.viewfull', ['file_ext'=>$resource->file_extension , 'file_path'=> $resource->file_name])}}" class="btn-action mr-30" title="View Resource">
                                    {{$resource->name}}
                                </a>
                            @endif
                        @endif
                   
                </td>
                <td>
                    {{$resource->category->name}}
                </td>
                <td>
                    @if($resource->type == 'Text' || $resource->type == 'Youtube')
                        {{$resource->type}}
                    @else
                        {{$resource->file_extension}}
                    @endif
                </td>
                <td>

                    <div class="action__buttons">
                      
                        @if($resource->type == 'Text' || $resource->type == 'Youtube')
                            <a target="_blank"  href="{{route('admin.resource.viewfullother', ['resource_id'=>$resource->id])}}" class="btn-action mr-30" title="View Resource">
                                <img src="{{asset('admin/images/icons/eye.svg')}}" alt="view">
                            </a>
                        @else
                            @if ($resource->file_extension && $resource->file_name)
                                <a target="_blank"  href="{{route('admin.resource.viewfull', ['file_ext'=>$resource->file_extension , 'file_path'=> $resource->file_name])}}" class="btn-action mr-30" title="View Resource">
                                    <img src="{{asset('admin/images/icons/eye.svg')}}" alt="view">
                                </a>
                            @endif
                          
                        @endif
                      

                        <a  href="{{route('admin.resource.edit', [$resource->id])}}" class="btn-action mr-30" title="Edit Details">
                            <img src="{{asset('admin/images/icons/edit.svg')}}" alt="edit">
                        </a>
                      
                        <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_{{$resource->id}}">
                            <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                        </button>
                      
                        <form action="{{route('admin.resource.delete', [$resource->id])}}" method="post" id="delete_row_form_{{ $resource->id }}">
                            @csrf
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{$resources->links()}}
    </div>
</div>