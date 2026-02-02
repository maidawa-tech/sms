<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="dashboard-link" id="dashboardLink"> 
       <i class="fas fa-tachometer-alt"></i> Dashboard 
    </a>
    <div class="accordion" data-accordion-id="school">
        <div class="accordion-header">
            <div><i class="fa fa-school"></i> Manage School</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">
                <a href="{{ route('school.index') }}">School</a>
            </div>
            <div class="accordion-item">
                <a href="{{ route('sections.index') }}">Sections</a>
            </div>
            <div class="accordion-item">
                <a href="{{ route('classes.index') }}">Classes</a>
            </div>
            <div class="accordion-item">
                <a href="{{ route('arms.index') }}">Arms</a>
            </div>
            <div class="accordion-item">
                <a href="{{ route('subjects.index') }}">Subjects</a>
            </div>
            <div class="accordion-item">
                <a href="{{ route('arm_subjects.index') }}">Arms Subjects</a>
            </div>
        </div>
    </div>

    <div class="accordion" data-accordion-id="terms">
        <div class="accordion-header">
            <div><i class="fa fa-calendar-alt"></i> Terms & Sessions</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">
              <a href="{{ route('academic_sessions.index') }}">Academic Sessions</a>
            </div>
            <div class="accordion-item">
              <a href="{{ route('terms.index') }}">Add Term</a>
            </div>
        </div>
    </div>

    <div class="accordion" data-accordion-id="students">
        <div class="accordion-header">
            <div><i class="fa fa-users"></i> Manage Students</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">
              <a href="{{ route('students.index') }}"> Add Student</a>
            </div>
            <div class="accordion-item">
              <a href="{{ route('student_enrollments.index') }}"> Enroll Student</a>
            </div>
            <div class="accordion-item">
               <a href="{{ route('class_members.index') }}">  View Enrolled Students</a>
            </div>
            <div class="accordion-item">
               <a href="{{ route('move_students.index') }}">  Change Class Member</a>
            </div>
        </div>
    </div>

    <div class="accordion" data-accordion-id="teachers">
        <div class="accordion-header">
            <div><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">Add Teachers</div>
            <div class="accordion-item">Assign Subjects</div>
            <div class="accordion-item">Assign Role</div>
        </div>
    </div>

    <div class="accordion" data-accordion-id="marks">
        <div class="accordion-header">
            <div><i class="fa-solid fa-pen-to-square"></i> Marks Entry</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">
            <a href="{{ route('marks_entry.index') }}"> Enter Results</a>
            </div>
            <div class="accordion-item">
            <a href="{{ route('marks_settings.index') }}"> Marks Settings</a>
            </div>
            <div class="accordion-item">
            <a href="{{ route('grades.index') }}"> Add Grades</a>
            </div>
            <div class="accordion-item">
            <a href="{{ route('broadsheet.index') }}"> BroadSheets</a>
            </div>
        </div>
    </div>
    
    <div class="accordion" data-accordion-id="comments">
        <div class="accordion-header">
            <div><i class="fas fa-commenting"></i> Comments</div>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="accordion-content">
            <div class="accordion-item">
            <a href="{{ route('final-average-comments.index') }}"> Comment Templates</a>
            </div>
            <div class="accordion-item">
            <a href="{{ route('admin.traits.index') }}"> Trait Management </a>
            </div>
            <div class="accordion-item">
            <a href="#"> Teacher's comment</a>
            </div>         
            <div class="accordion-item">
            <a href="#"> Affective Traits</a>
            </div>
            <div class="accordion-item">
            <a href="#"> Psychomotor</a>
            </div>
        </div>
    </div>
    <a href="#" class="dashboard-link" id="dashboardLink"> 
       <i class="fa fa-book"></i> Generate Report 
    </a>
</aside>
