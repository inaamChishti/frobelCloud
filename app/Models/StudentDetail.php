<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $fillable = [
        'student_id','current_domain',
        'surname', 'first_name', 'dob', 'gender', 'address_line1', 'address_line2', 'postcode',
        'uci_number', 'contact_number', 'email', 'cc_email', 'emergency_contact', 'uln', 'medical_condition',
        'access_arrangement', 'access_arrangement_explanation', 'practical_endorsement',
        'need_predicted_grades', 'need_mock_exams',
        'course_work', 'course_work_uci', 'exam_board', 'course_type', 'subject_name',
        'subject_code', 'exams', 'tire', 'nea', 'photo_path', 'id_document_path',
        'supporting_documents_path', 'additional_docs_paths', 'student_request_id',
        'day','time', 'date' ,'duration','unit','exam_session','form_fill_date','sign','amount_received','receipt_path','referral'
    ];

    protected $casts = [

        'additional_docs_paths' => 'array',
    ];

    public function studentRequest()
    {
        return $this->belongsTo(StudentRequest::class, 'student_request_id');
    }
}
