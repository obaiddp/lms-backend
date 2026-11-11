<!-- Error container for student form -->
<div id="student-errors" style="{{ session('active_form', 'student') === 'student' && $errors->any() ? 'display:block' : 'display:none' }}">
    @if(session('active_form', 'student') === 'student' && $errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<!-- Error container for teacher form -->
<div id="teacher-errors" style="{{ session('active_form') === 'teacher' && $errors->any() ? 'display:block' : 'display:none' }}">
    @if(session('active_form') === 'teacher' && $errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>