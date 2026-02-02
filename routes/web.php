<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ArmController;
use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ArmSubjectController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentViewController;
use App\Http\Controllers\MoveStudentsController;
use App\Http\Controllers\MarksEntryController;
use App\Http\Controllers\MarksSettingController;
use App\Http\Controllers\BroadSheetController;
use App\Http\Controllers\FinalAverageCommentController;
use App\Http\Controllers\Admin\TraitController;


// ===========================
// Guest Routes (Before Login)
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store']);
});

// ===============================
// Authenticated Routes (After Login)
// ===============================
Route::middleware(['auth', 'verified'])->group(function () {

    // ===============================
    // Dashboard
    // ===============================
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // ===============================
    // School Management
    // ===============================
    Route::get('/school', [SchoolController::class, 'index'])->name('school.index');
    Route::post('/school', [SchoolController::class, 'store'])->name('school.store');
    Route::delete('/school', [SchoolController::class, 'destroy'])->name('school.destroy');

    // ===============================
    // Section Management
    // ===============================
    Route::resource('sections', SectionController::class)->except(['show', 'create', 'edit']);

    // ===============================
    // Classes Management
    // ===============================
    Route::controller(ClassController::class)->group(function () {
        Route::get('/classes', 'index')->name('classes.index');
        Route::post('/classes/store', 'store')->name('classes.store');
        Route::put('/classes/update/{id}', 'update')->name('classes.update');
        Route::delete('/classes/delete/{id}', 'destroy')->name('classes.destroy');

        // AJAX route for dependent dropdown
        //Route::get('/get-classes/{section_id}', 'getClassesBySection')->name('get.classes.by.section');
    });

    // ===============================
    // Arms Management
    // ===============================
    Route::controller(ArmController::class)->group(function () {
        Route::get('/arms', 'index')->name('arms.index');
        Route::post('/arms', 'store')->name('arms.store');
        Route::put('/arms/{id}', 'update')->name('arms.update');
        Route::delete('/arms/{id}', 'destroy')->name('arms.destroy');
    });
    

    // ===============================
    // Academic Sessions Management
    // ===============================
    Route::controller(AcademicSessionController::class)->group(function () {
        Route::get('/academic_sessions', 'index')->name('academic_sessions.index');
        Route::post('/academic_sessions', 'store')->name('academic_sessions.store');
        Route::put('/academic_sessions/{id}', 'update')->name('academic_sessions.update');
        Route::post('/academic_sessions/activate/{id}', 'activate')->name('academic_sessions.activate');
        Route::delete('/academic_sessions/{id}', 'destroy')->name('academic_sessions.destroy');
    });

    // ===============================
    // Terms Management
    // ===============================
    Route::controller(TermController::class)->group(function () {
        Route::get('/terms', 'index')->name('terms.index');
        Route::post('/terms', 'store')->name('terms.store');
        Route::put('/terms/{id}', 'update')->name('terms.update');
        Route::post('/terms/activate/{id}', 'activate')->name('terms.activate');
        Route::delete('/terms/{id}', 'destroy')->name('terms.destroy');
    });

    // ===============================
    // Students Management
    // ===============================
    Route::controller(StudentController::class)->group(function () {
        Route::get('/students', 'index')->name('students.index');             // Page + DataTable
        Route::post('/students', 'store')->name('students.store');            // Add new or update
        Route::get('/students/{id}', 'show')->name('students.show');          // Fetch single student for edit
        Route::delete('/students/{id}', 'destroy')->name('students.destroy'); // Delete
    });

    
    // ===============================
    // Subjects Management
    // ===============================
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects/store', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{id}', [SubjectController::class, 'show']);
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
    
    // ===============================
    // Arms Subjects Management
    // ===============================
    Route::get('/arm_subjects', [ArmSubjectController::class, 'index'])->name('arm_subjects.index');
    Route::post('/arm_subjects/store', [ArmSubjectController::class, 'store'])->name('arm_subjects.store');
    Route::get('/arm_subjects/{id}', [ArmSubjectController::class, 'show']);
    Route::delete('/arm_subjects/{id}', [ArmSubjectController::class, 'destroy']);



    // ===============================
    // User Profile (from Breeze)
    // ===============================
    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ===============================
    // Custom Profile Page
    // ===============================
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    
    // Update Profile
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Update Password (custom route)
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // ===============================
    // Student Enrollment Page
    // ===============================
    Route::get('/student_enrollments', [StudentEnrollmentController::class, 'index'])->name('student_enrollments.index');    

    // ===============================
    // AJAX Routes
    // ===============================
    Route::get('/ajax/classes/{section_id}', [StudentEnrollmentController::class, 'getClasses'])->name('ajax.getClasses');

    Route::get('/ajax/arms/{class_id}', [StudentEnrollmentController::class, 'getArms'])->name('ajax.getArms');

    Route::post('/ajax/enroll-student', [StudentEnrollmentController::class, 'store'])->name('ajax.enrollStudent');

    
    // ===============================
    // Grade Management Page
    // ===============================

    // Grade Index (GET) - No change
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');

    // Grade Data Fetch (GET) - No change
    Route::get('/grades/fetch', [GradeController::class, 'fetchGrades'])->name('grades.fetch');

    // Grade Store (POST) - No change
    Route::post('/grades/store', [GradeController::class, 'store'])->name('grades.store');

    // --- FIX APPLIED FOR UPDATE ---
    Route::post('/grades/update/{id}', [GradeController::class, 'update'])->name('grades.update'); 

    // Grade Delete (DELETE) - No change needed, as the JS fetch correctly uses the DELETE method 
    // and the URL structure /grades/delete/{id} matches the route definition.
    Route::delete('/grades/delete/{id}', [GradeController::class, 'destroy'])->name('grades.delete');
    
    // ===============================
    // Manage Class Members / Enrolled Students (View Only)
    // ===============================

    // View class members page
    Route::get('/class_members', [StudentViewController::class, 'index'])
       ->name('class_members.index');

    // AJAX: Fetch students for DataTable
    Route::get('/ajax/class_members', [StudentViewController::class, 'fetchStudents'])
       ->name('ajax.class_members');


    // ===============================
    // Move Students
    // ===============================
    Route::prefix('move-students')->group(function() {
    	Route::get('/', [MoveStudentsController::class, 'index'])->name('move_students.index');
    	Route::get('/get-classes/{section_id}', [MoveStudentsController::class, 'getClasses']);
    	Route::get('/get-arms/{class_id}', [MoveStudentsController::class, 'getArms']);
    	Route::get('/load-students', [MoveStudentsController::class, 'loadStudents']);
    	Route::post('/move', [MoveStudentsController::class, 'moveStudents'])->name('move_students.move');
    });

    // ===============================
    // Marks Entry
    // ===============================
    Route::prefix('marks-entry')->group(function() {

    	// Main marks entry page
    	Route::get('/', [MarksEntryController::class, 'index'])
            ->name('marks_entry.index');

    	// AJAX: get classes by section
    	Route::get('/get-classes/{section_id}', [MarksEntryController::class, 'getClasses']);

    	// AJAX: get arms by class
    	Route::get('/get-arms/{class_id}', [MarksEntryController::class, 'getArms']);

    	// AJAX: get subjects for arm
    	Route::get('/get-subjects', [MarksEntryController::class, 'getSubjects']);

    	// AJAX: load enrolled students + previous marks
    	Route::get('/load-students', [MarksEntryController::class, 'loadStudents'])
            ->name('marks_entry.load_students');

    	// AJAX: save results
    	Route::post('/save-results', [MarksEntryController::class, 'saveResults'])
            ->name('marks_entry.save');

    	// AJAX: get grade based on total
    	Route::get('/get-grade', [MarksEntryController::class, 'getGrade']);

        // PDF DOWNLOAD ROUTE
        Route::get('/download-marks-sheet', [MarksEntryController::class, 'downloadMarksSheet'])
           ->name('marks_entry.download_pdf');
    
    });

   
   // ===============================
   // Marks Settings
   // ===============================
   Route::prefix('marks-setting')->name('marks_settings.')->group(function () {

    	// Display page
    	Route::get('/', [MarksSettingController::class, 'index'])
            ->name('index');

        // Fetch settings by section (AJAX)
        Route::get('/fetch/{section}', [MarksSettingController::class, 'fetch'])
           ->name('fetch');

        // Create (store)
        Route::post('/store', [MarksSettingController::class, 'store'])
           ->name('store');

        // Update (PUT)
        Route::put('/update/{id}', [MarksSettingController::class, 'update'])
            ->name('update');

        // Delete
        Route::delete('/delete/{id}', [MarksSettingController::class, 'destroy'])
           ->name('destroy');
    });

   // ===============================
   // BroadSheets
   // ===============================
   Route::get('/broadsheet', [BroadSheetController::class, 'index'])->name('broadsheet.index');
   Route::get('/broadsheet/load', [BroadSheetController::class, 'load'])->name('broadsheet.load');

   // ===============================
   // Final Average Comments
   // ===============================  
   Route::prefix('final-average-comments')->group(function () {
    	Route::get('/', [FinalAverageCommentController::class, 'index'])->name('final-average-comments.index');
    	Route::get('/fetch', [FinalAverageCommentController::class, 'fetch']);
    	Route::post('/store', [FinalAverageCommentController::class, 'store']);
    	Route::put('/update/{id}', [FinalAverageCommentController::class, 'update']);
    	Route::delete('/delete/{id}', [FinalAverageCommentController::class, 'destroy']);
    	Route::patch('/toggle/{id}', [FinalAverageCommentController::class, 'toggleStatus']);
   });

   // ===============================
   // Affective & Psychomotor Traits
   // ===============================
   Route::prefix('admin')->name('admin.')->group(function () {
    	Route::get('/traits', [TraitController::class, 'index'])->name('traits.index');
    	Route::get('/traits/list', [TraitController::class, 'list'])->name('traits.list');
    	Route::post('/traits/store', [TraitController::class, 'store'])->name('traits.store');
    	Route::put('/traits/{id}', [TraitController::class, 'update'])->name('traits.update');
    	Route::delete('/traits/{id}', [TraitController::class, 'destroy'])->name('traits.destroy');
    	Route::patch('/traits/{id}/toggle', [TraitController::class, 'toggle'])->name('traits.toggle');
   });


});

// Laravel Breeze Authentication
require __DIR__.'/auth.php';
