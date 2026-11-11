<div class="customers__table">
    <table id="classes-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th>{{__('Sr No.')}}</th>
            <th>{{__('Total Students')}}</th>
            <th>{{__('Total Classes')}}</th>
            <th>{{__('Duration')}}</th>
            <th>{{__('Type')}}</th>
            <th>{{__('Learning Tool')}}</th>
            <th class="text-center">{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
            @php
            $i = 1;
        @endphp
        @foreach($classes as $class)
            <tr class="removable-item">
                <td>
                    {{$i++}}
                    @if ($class->free_class)
                        <span style="color:white; font-size:12px;padding:4px" class="badge bg-success">{{__('Free')}}</span>
                    @endif
                </td>
                <td>
                    {{$class->no_of_students}}
                </td>

                <td>
                    <a href="{{route('classes.view_detail', [$class->id])}}" class="btn-action mr-30" title="View Details">
                    {{$class->no_of_classes}}
                    </a>
                </td>
                <td>
                    {{$class->duration}}
                </td>
                <td>
                    {{$class->type}}
                </td>
         
                <td>
                    {{$class->learning_tool}}
                </td>
                <td>
                    <div class="action__buttons">
                        <a href="{{route('classes.view_detail', [$class->id])}}" class="btn-action mr-30" title="View Details">
                            <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                        </a>
                        @if ($class->free_class)
                        <a href="{{route('classes.edit_free_class', [$class->id])}}" class="btn-action mr-30" title="Edit Free Class">
                            <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                        </a>
                        @else
                        <a href="{{route('classes.edit', [$class->id])}}" class="btn-action mr-30" title="Edit Class">
                            <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                        </a>
                        @endif
                        {{-- <a href="javascript:void(0);" data-url="{{route('coordinator.delete', [$coordinator->uuid])}}" title="Delete" class="btn-action deleteBtn">
                            <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                        </a> --}}
                        <a href="javascript:void(0);" data-url="{{route('classes.delete', [$class->id])}}" title="Delete" class="btn-action deleteBtn">
                            <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{$classes->links()}}
    </div>
</div>