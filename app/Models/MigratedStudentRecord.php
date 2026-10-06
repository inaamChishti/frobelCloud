<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigratedStudentRecord extends Model
{
    use HasFactory;
     protected $table = 'migrated_student_record';

    protected $fillable = [
        'current_domain',
        'form_fill_date',
        'sign',
        'student_id',
        'surname',
        'first_name',
        'dob',
        'gender',
        'address_line1',
        'address_line2',
        'postcode',
        'uci_number',
        'contact_number',
        'email',
        'emergency_contact',
        'uln',
        'medical_condition',
        'access_arrangement',
        'access_arrangement_explanation',
        'practical_endorsement',
        'course_work',
        'course_work_uci',
        'day',
        'time',
        'date',
        'duration',
        'exam_board',
        'course_type',
        'subject_name',
        'subject_code',
        'exams',
        'tire',
        'nea',
        'photo_path',
        'id_document_path',
        'supporting_documents_path',
        'additional_docs_paths',
        'student_request_id',
        'unit',
        'exam_session',
    ];
}
