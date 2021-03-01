<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'image',
        'fireBaseSaveREGID',
        'users/*/delete',
        'student/*/delete',
        'batch/*/delete',
        'grade/*/delete',
        'subject/*/delete',
        'school/*/delete',
        'teacher/*/delete',
        'create_school_process',
        'create_batch_process',
        'create_new_batch_process',
        'get_student_info_for_payment',
        'student_payment',
        'batch_update_process',
        'student/last_payment_date_update',
        'clear_due_payment',
        'student_admission_payment_process',
        'student_other_payment_process'
    ];
}
