<!-- Error container for student form -->
{{-- <div id="student-errors" style="{{ session('active_form', 'student') === 'student' && $errors->any() ? 'display:block' : 'display:none' }}"> --}}
<div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

