<?php

namespace App\Helpers;

use App\Models\EmailLog;
use App\Models\StudentRequest;
use Illuminate\Support\Facades\Log;

class EmailLogHelper
{
    /**
     * Store a sent-email log. Failures are caught so email delivery is never blocked.
     *
     * Identifiers (use the correct field per source — do not mix UCI into student_id):
     * - student_id: approved StudentDetail student_id only
     * - candidate_id: CandidateAndUciNumber candidate_id (send-results)
     * - uci_number: UCI from student_requests or related record when student_id not assigned yet
     *
     * @param  array{
     *     candidate_name?: string|null,
     *     email_address?: string|null,
     *     student_id?: string|null,
     *     candidate_id?: string|null,
     *     uci_number?: string|null,
     *     subject_line: string,
     *     message: string,
     *     message_date?: \DateTimeInterface|string|null,
     *     session?: string|null,
     *     sent_status?: string|null,
     *     tracking_token?: string|null,
     * }  $data
     * @return \App\Models\EmailLog|null
     */
    public static function log(array $data): ?\App\Models\EmailLog
    {
        try {
            return EmailLog::create([
                'candidate_name'  => self::normalizeName($data['candidate_name'] ?? null),
                'email_address'   => self::normalizeIdentifier($data['email_address'] ?? null),
                'student_id'      => self::normalizeIdentifier($data['student_id'] ?? null),
                'candidate_id'    => self::normalizeIdentifier($data['candidate_id'] ?? null),
                'uci_number'      => self::normalizeIdentifier($data['uci_number'] ?? null),
                'subject_line'    => $data['subject_line'] ?? '',
                'message'         => $data['message'] ?? '',
                'message_date'    => $data['message_date'] ?? now(),
                'session'         => $data['session'] ?? session('current_session'),
                'sent_status'     => $data['sent_status'] ?? 'sent',
                'tracking_token'  => $data['tracking_token'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Email log failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a unique tracking token suitable for use in a pixel URL.
     */
    public static function generateTrackingToken(): string
    {
        return \Illuminate\Support\Str::random(40);
    }

    public static function nameFromStudentRequest(?StudentRequest $request, ?string $fallback = null): ?string
    {
        if ($request) {
            $name = trim(($request->first_name ?? '') . ' ' . ($request->surname ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return self::normalizeName($fallback);
    }

    private static function normalizeName(?string $name): ?string
    {
        $name = trim((string) $name);

        return $name !== '' ? $name : null;
    }

    private static function normalizeIdentifier(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
