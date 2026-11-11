<div class="customers__table">
    <table class="row-border table-style w-100">
        <thead>
            <tr>
                <th>{{ __('Student') }}</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Issue Date') }}</th>
                <th>{{ __('Days Overdue') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overdues as $row)
                <tr>
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['course_name'] }}</td>
                    <td>{{ $row['issue_date'] }}</td>
                    <td>{{ $row['days_overdue'] }}</td>
                    <td>
                        @if(get_currency_placement() == 'after')
                            {{ number_format($row['amount'], 0) }} {{ get_currency_symbol() }}
                        @else
                            {{ get_currency_symbol() }} {{ number_format($row['amount'], 0) }}
                        @endif
                    </td>
                    <td>
                        @if($row['student_uuid'])
                            <a href="{{ route('student.view', $row['student_uuid']) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('View') }}
                            </a>
                        @endif

                        <a href="{{ route('student_payment.edit_payment_2', $row['id']) }}" class="btn-action mr-30" title="Edit Details">
                            <img src="{{ asset('admin/images/icons/edit-2.svg') }}" alt="edit">
                        </a>

                        <button class="ms-3">
                            <span data-formid="delete_row_form_{{$row['id']}}" class="deleteItem">
                                <img src="{{ asset('admin/images/icons/trash-2.svg') }}" alt="trash">
                            </span>
                        </button>

                        <form action="{{ route('student_payment.delete_payment', $row['id']) }}" method="post" id="delete_row_form_{{ $row['id'] }}">
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">{{ __('No overdue payments found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {!! $overdues->links() !!}
    </div>
</div>