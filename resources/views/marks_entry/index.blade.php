@extends('layouts.dashboard')

@section('title', 'Marks Entry')

@section('content')
<style>
/* Existing styles retained */
.filter-input { background:#ffffff; border:1px solid #679767; height:38px; transition:border 0.2s, box-shadow 0.2s; }
.filter-input:focus, input[type="number"]:focus { border:2px solid #679767; outline:none; box-shadow:0 0 5px #679767; }
.horizontal-panel { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:1rem; }
.horizontal-panel select, .horizontal-panel button { flex:1 1 0; min-width:120px; max-width:100%; height:38px; }
.selected-filters { display:grid; grid-template-columns:repeat(3,1fr); gap:0.5rem 1rem; margin-bottom:1rem; color:#555; }
.selected-filters span { background:#B2FFFF; padding:0.3rem 0.6rem; border-radius:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.table-fixed { max-height:520px; overflow-y:auto; overflow-x:hidden; }
input[type="number"] { width:100px; border:2px solid #679767; }
.total-cell { min-width:80px; text-align:center; }
.grade-cell, .remark-cell { text-align:center; }
</style>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>

<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fa-solid fa-pen-to-square"></i> Marks Entry
            </h5>
            <button id="downloadPdfBtn" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf"></i> Download Mark Sheet (PDF)
            </button>
        </div>

        <div class="card-body">
            <div class="horizontal-panel">
                <select id="section_id" class="form-select filter-input">
                    <option value="">-- Select Section --</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->section_id }}">{{ $s->section_name }}</option>
                    @endforeach
                </select>

                <select id="class_id" class="form-select filter-input">
                    <option value="">-- Select Class --</option>
                </select>

                <select id="arm_id" class="form-select filter-input">
                    <option value="">-- Select Arm --</option>
                </select>

                <select id="subject_id" class="form-select filter-input">
                    <option value="">-- Select Subject --</option>
                </select>

                <button id="loadStudentsBtn" class="btn btn-success">
                    <i class="fa-solid fa-eye"></i> Load Students
                </button>
            </div>

            <input type="hidden" id="term_id" value="{{ $activeTerm?->term_id }}">
            <input type="hidden" id="session_id" value="{{ $activeSession?->session_id }}">

            <div class="selected-filters" id="selectedFilters">
                <span id="displaySection">Section: -</span>
                <span id="displayClass">Class: -</span>
                <span id="displayArm">Arm: -</span>
                <span id="displayTerm">Term: {{ $activeTerm?->term_name ?? '-' }}</span>
                <span id="displaySession">Session: {{ $activeSession?->session_name ?? '-' }}</span>
                <span id="displaySubject">Subject: -</span>
            </div>

            <div class="table-fixed">
                <table class="table table-striped table-bordered" id="marksTable">
                    <thead class="table-success" style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Reg Number</th>
                            <th>Full Name</th>
                            <th>CA1 (0)</th>
                            <th>CA2 (0)</th>
                            <th>Exam (0)</th>
                            <th>Total (0)</th>
                            <th>Grade</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-3 text-end">
                <button id="saveResultsBtn" class="btn btn-success" disabled>
                    <i class="fa-solid fa-save"></i> Save Results
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
@endsection

@section('scripts')
<script>
$(function() {
    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });

    // Global variables for max marks (initialized to 0, updated by AJAX)
    let maxCa1 = 0;
    let maxCa2 = 0;
    let maxExam = 0;

    // Function to update the table headers based on fetched marks settings
    function updateTableHeaders() {
        const totalMax = maxCa1 + maxCa2 + maxExam;
        
        $('#marksTable thead tr th:nth-child(4)').text(`CA1 (${maxCa1})`);
        $('#marksTable thead tr th:nth-child(5)').text(`CA2 (${maxCa2})`);
        $('#marksTable thead tr th:nth-child(6)').text(`Exam (${maxExam})`);
        $('#marksTable thead tr th:nth-child(7)').text(`Total (${totalMax})`);
    }
    
    // Call once to ensure headers display default values (Total 0)
    updateTableHeaders();


    // Format score for input display
    const formatScoreForInput = (score) => {
        if (score === null || score === undefined || score === '') return '';
        const num = parseFloat(score);
        return isNaN(num) ? '' : (num % 1 === 0 ? num.toString() : num.toString());
    };

    function enforceLimit(value, maxValue){
        value = parseFloat(value);
        if (isNaN(value) || value < 0) return 0;
        if (value > maxValue) return maxValue;
        return value;
    }

    function showToast(type, message){
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>');
        }
        const id='toastAjax'+Date.now();
        const bgClass=(type==='danger' || type==='error')?'text-bg-danger':(type==='warning'?'text-bg-warning':'text-bg-success');
        const $toast=$(`<div class="toast align-items-center ${bgClass} border-0" id="${id}" role="alert">
                            <div class="d-flex">
                                <div class="toast-body">${message}</div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        </div>`);
        $('.toast-container').append($toast);
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const bs = new bootstrap.Toast(document.getElementById(id), { delay: 5000 });
            bs.show();
            document.getElementById(id).addEventListener('hidden.bs.toast', function(){ $(this).remove(); });
        } else console.log(`[${type.toUpperCase()}]: ${message}`);
    }

    function updateSelectedFilters(){
        $('#displaySection').text('Section: ' + ($('#section_id option:selected').text() || '-'));
        $('#displayClass').text('Class: ' + ($('#class_id option:selected').text() || '-'));
        $('#displayArm').text('Arm: ' + ($('#arm_id option:selected').text() || '-'));
        $('#displaySubject').text('Subject: ' + ($('#subject_id option:selected').text() || '-'));
        $('#saveResultsBtn').prop('disabled', true);
    }

    // Dependent dropdowns
    $('#section_id').change(function(){
        const id=$(this).val();
        $('#marksTable tbody').empty();
        $('#class_id').html('<option>Loading...</option>');
        $('#arm_id').html('<option value="">-- Select Arm --</option>');
        $('#subject_id').html('<option value="">-- Select Subject --</option>');
        updateSelectedFilters();
        if(!id){ $('#class_id').html('<option value="">-- Select Class --</option>'); return;}
        $.get(`/marks-entry/get-classes/${id}`, function(data){
            $('#class_id').html('<option value="">-- Select Class --</option>');
            data.forEach(c=> $('#class_id').append(`<option value="${c.class_id}">${c.class_name}</option>`));
        }).fail(()=>showToast('error','Failed to load classes.'));
    });

    $('#class_id').change(function(){
        const id=$(this).val();
        $('#marksTable tbody').empty();
        $('#arm_id').html('<option>Loading...</option>');
        $('#subject_id').html('<option value="">-- Select Subject --</option>');
        updateSelectedFilters();
        if(!id){ $('#arm_id').html('<option value="">-- Select Arm --</option>'); return;}
        $.get(`/marks-entry/get-arms/${id}`, function(data){
            $('#arm_id').html('<option value="">-- Select Arm --</option>');
            data.forEach(a=> $('#arm_id').append(`<option value="${a.arm_id}">${a.arm_name}</option>`));
        }).fail(()=>showToast('error','Failed to load arms.'));
    });

    $('#arm_id').change(function(){
        const id=$(this).val();
        $('#marksTable tbody').empty();
        $('#subject_id').html('<option>Loading...</option>');
        updateSelectedFilters();
        if(!id){ $('#subject_id').html('<option value="">-- Select Subject --</option>'); return;}
        $.get('/marks-entry/get-subjects',{ arm_id:id },function(data){
            $('#subject_id').html('<option value="">-- Select Subject --</option>');
            data.forEach(s=> $('#subject_id').append(`<option value="${s.subject_id}">${s.subject_name}</option>`));
        }).fail(()=>showToast('error','Failed to load subjects.'));
    });

    $('#subject_id').change(()=>{ $('#marksTable tbody').empty(); updateSelectedFilters(); });

    // *** UPDATED: calcRow function to pass section_id ***
    function calcRow($tr){
        const section_id = $('#section_id').val();
        
        let ca1=enforceLimit($tr.find('.input-ca1').val(), maxCa1);
        let ca2=enforceLimit($tr.find('.input-ca2').val(), maxCa2);
        let exam=enforceLimit($tr.find('.input-exam').val(), maxExam);
        
        // Update input values after enforcing limits
        $tr.find('.input-ca1').val(formatScoreForInput(ca1));
        $tr.find('.input-ca2').val(formatScoreForInput(ca2));
        $tr.find('.input-exam').val(formatScoreForInput(exam));
        
        let total=ca1+ca2+exam;
        $tr.find('.total-cell').text(total.toString());
        
        if (!section_id) {
            $tr.find('.grade-cell').text('-'); $tr.find('.remark-cell').text('Select Section');
            return;
        }

        // Pass section_id to the grade calculation endpoint
        $.get('/marks-entry/get-grade',{ total:total, section_id:section_id },function(resp){
            $tr.find('.grade-cell').text(resp.grade ?? '');
            $tr.find('.remark-cell').text(resp.remark ?? '');
        }).fail(()=>{ 
            $tr.find('.grade-cell').text('-'); 
            $tr.find('.remark-cell').text('Grade Error'); 
        });
    }

    $('#marksTable').on('input','.input-ca1,.input-ca2,.input-exam', function(){ calcRow($(this).closest('tr')); });

    // *** UPDATED: Load Students handler to fetch marksSetting and students ***
    $('#loadStudentsBtn').click(function(){
        const section=$('#section_id').val(), classID=$('#class_id').val(), arm=$('#arm_id').val(),
              term=$('#term_id').val(), session=$('#session_id').val(), subject=$('#subject_id').val();
        
        if(!section||!classID||!arm||!subject){ 
            showToast('warning','Please select Section, Class, Arm, and Subject.'); 
            return;
        }
        
        $('#marksTable tbody').html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading students...</td></tr>');
        $('#saveResultsBtn').prop('disabled',true); 
        updateSelectedFilters();

        $.ajax({
            url: '/marks-entry/load-students',
            method: 'GET',
            data: {
                section_id:section, class_id:classID, arm_id:arm, session_id:session, term_id:term, subject_id:subject
            },
            success: function(res) {
                // 1. Update global max marks variables and headers
                if (res.marksSetting) {
                    maxCa1 = parseFloat(res.marksSetting.max_ca1) || 0;
                    maxCa2 = parseFloat(res.marksSetting.max_ca2) || 0;
                    maxExam = parseFloat(res.marksSetting.max_exam) || 0;
                    updateTableHeaders();
                } else {
                    maxCa1 = maxCa2 = maxExam = 0;
                    updateTableHeaders();
                    showToast('warning', 'Marks setting not found for this section. Using 0 max score.');
                }
                
                // 2. Populate student rows
                const students = res.students;
                if(!students||students.length===0){ 
                    $('#marksTable tbody').html('<tr><td colspan="9" class="text-center text-danger fw-bold">No students found enrolled for the selected criteria.</td></tr>');
                    $('#saveResultsBtn').prop('disabled',true); return; 
                }
                
                let rows='';
                students.forEach((st,i)=>{
                    rows+=`<tr data-enrollment="${st.enrollment_id}" data-reg="${st.reg_number}">
                            <td>${i+1}</td>
                            <td>${st.reg_number}</td>
                            <td>${st.full_name}</td>
                            <td><input type="number" class="form-control input-ca1" value="${formatScoreForInput(st.ca1)}" min="0" max="${maxCa1}"></td>
                            <td><input type="number" class="form-control input-ca2" value="${formatScoreForInput(st.ca2)}" min="0" max="${maxCa2}"></td>
                            <td><input type="number" class="form-control input-exam" value="${formatScoreForInput(st.exam)}" min="0" max="${maxExam}"></td>
                            <td class="total-cell">${st.total?parseFloat(st.total).toString():'0'}</td>
                            <td class="grade-cell">${st.grade??''}</td>
                            <td class="remark-cell">${st.remark??''}</td>
                        </tr>`;
                });
                $('#marksTable tbody').html(rows); 
                $('#saveResultsBtn').prop('disabled',false);
            },
            error: function(xhr) {
                let message='Failed to load students.';
                if(xhr.status===422){ const errors=xhr.responseJSON.errors; message=Object.values(errors).flat()[0]||message; }
                else if(xhr.responseJSON?.message){ message=xhr.responseJSON.message; }
                showToast('error', message);
                $('#marksTable tbody').html('<tr><td colspan="9" class="text-center text-danger fw-bold">'+message+'</td></tr>');
                $('#saveResultsBtn').prop('disabled',true);
            }
        });
    });

    $('#saveResultsBtn').click(function(){
        const term=$('#term_id').val(), session=$('#session_id').val(), subject=$('#subject_id').val(),
              classID=$('#class_id').val(), arm=$('#arm_id').val(), sectionID=$('#section_id').val();
              
        let entries=[];
        $('#marksTable tbody tr').each(function(){ 
            let d=$(this); 
            entries.push({ 
                enrollment_id:d.data('enrollment'), 
                reg_number:d.data('reg'), 
                ca1:d.find('.input-ca1').val(), 
                ca2:d.find('.input-ca2').val(), 
                exam:d.find('.input-exam').val() 
            }); 
        });
        
        if(entries.length===0){ showToast('warning','No data to save.'); return; }
        $('#saveResultsBtn').prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url:"{{ route('marks_entry.save') }}", 
            method:'POST', 
            contentType:'application/json',
            data:JSON.stringify({
                section_id:sectionID, // Passed to controller for limit and grade logic
                subject_id:subject,class_id:classID,arm_id:arm,term_id:term,session_id:session,entries:entries
            }),
            success:function(res){ 
                showToast('success',res.message||'Results saved successfully'); 
                $('#saveResultsBtn').html('<i class="fa-solid fa-save"></i> Save Results'); 
                // Re-load students to see updated grades/remarks after server-side save/positioning
                $('#loadStudentsBtn').click(); 
            },
            error:function(xhr){ 
                let message='Failed to save results.';
                if(xhr.status===422){ const errors=xhr.responseJSON.errors; message=Object.values(errors).flat()[0]||message; }
                else if(xhr.responseJSON?.message){ message=xhr.responseJSON.message; }
                showToast('error',message);
                $('#saveResultsBtn').prop('disabled',false).html('<i class="fa-solid fa-save"></i> Save Results');
            }
        });
    });

    updateSelectedFilters();

    // Download PDF
    $('#downloadPdfBtn').click(function(){
        const section=$('#section_id').val(), classID=$('#class_id').val(), arm=$('#arm_id').val(), subject=$('#subject_id').val(),
              term=$('#term_id').val(), session=$('#session_id').val();
        if(!section||!classID||!arm||!subject){ showToast('warning','Please select Section, Class, Arm, and Subject before downloading the mark sheet.'); return; }
        $('#downloadPdfBtn').prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> Preparing PDF...');
        const url=`/marks-entry/download-marks-sheet?section_id=${section}&class_id=${classID}&arm_id=${arm}&subject_id=${subject}&term_id=${term}&session_id=${session}`;
        window.open(url,'_blank');
        setTimeout(()=>{ $('#downloadPdfBtn').prop('disabled',false).html('<i class="fa-solid fa-file-pdf"></i> Download Mark Sheet (PDF)'); },1500);
    });

});
</script>

@endsection