<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class EduQueryService
{
    /**
     * Tìm sinh viên theo tên (LIKE) hoặc theo mã sinh viên (exact).
     */
    public function timSinhVienTheoTenHoacMSSV(string $q, int $limit = 15): array
    {
        $q = trim($q);

        $qb = DB::table('students as s')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->leftJoin('classes as c', 'c.id', '=', 's.class_id')
            ->select(
                's.id as student_id',
                's.student_code',
                's.fullname',
                's.dob',
                's.status',
                'c.code as class_code',
                'c.name as class_name',
                'u.email'
            );

        if ($this->looksLikeStudentCode($q)) {
            $qb->where('s.student_code', '=', $q);
        } else {
            $qb->where('s.fullname', 'like', '%' . $q . '%');
        }

        $qb->limit($limit);

        $this->logQueryInfo($qb, ['q' => $q, 'type' => 'timSinhVien']);

        $rows = $qb->get();

        Log::info('EduQueryService: timSinhVien count', ['q' => $q, 'count' => $rows->count()]);

        return $rows->map(function ($r) {
            return (array) $r;
        })->toArray();
    }

    /**
     * Lấy cảnh báo học vụ cho một sinh viên (theo id hoặc mã sinh viên)
     */
    public function layCanhBaoHocVu($studentIdentifier, int $limit = 50): array
    {
        $qb = DB::table('academic_warnings as aw')
            ->leftJoin('students as s', 's.id', '=', 'aw.student_id')
            ->leftJoin('semesters as sem', 'sem.id', '=', 'aw.semester_id')
            ->select(
                'aw.id as warning_id',
                's.student_code',
                's.fullname',
                'sem.name as semester',
                'aw.warning_level',
                'aw.gpa_term',
                'aw.gpa_cumulative',
                'aw.credits_owed',
                'aw.warning_count',
                'aw.reason',
                'aw.status',
                'aw.advisor_note',
                'aw.created_at'
            );

        if (is_numeric($studentIdentifier)) {
            $qb->where('s.id', '=', intval($studentIdentifier));
        } else {
            $qb->where('s.student_code', '=', $studentIdentifier);
        }

        $qb->orderBy('aw.created_at', 'desc')->limit($limit);

        $this->logQueryInfo($qb, ['studentIdentifier' => $studentIdentifier, 'type' => 'layCanhBao']);

        $rows = $qb->get();

        Log::info('EduQueryService: layCanhBao count', ['id' => $studentIdentifier, 'count' => $rows->count()]);

        return $rows->map(function ($r) {
            return (array) $r;
        })->toArray();
    }

    /**
     * Lấy kết quả học tập (gpa) cho sinh viên theo học kỳ (option).
     */
    public function layKetQuaHocTap($studentIdentifier, $semesterId = null): array
    {
        $qb = DB::table('academic_results as ar')
            ->leftJoin('students as s', 's.id', '=', 'ar.student_id')
            ->leftJoin('semesters as sem', 'sem.id', '=', 'ar.semester_id')
            ->select(
                'ar.id as result_id',
                's.student_code',
                's.fullname',
                'sem.name as semester',
                'ar.gpa_10',
                'ar.gpa_4',
                'ar.classification'
            );

        if (is_numeric($studentIdentifier)) {
            $qb->where('s.id', '=', intval($studentIdentifier));
        } else {
            $qb->where('s.student_code', '=', $studentIdentifier);
        }

        if ($semesterId) {
            $qb->where('ar.semester_id', '=', $semesterId);
        }

        $qb->orderBy('sem.start_date', 'desc');

        $this->logQueryInfo($qb, ['studentIdentifier' => $studentIdentifier, 'semesterId' => $semesterId, 'type' => 'layKetQua']);

        $rows = $qb->get();

        Log::info('EduQueryService: layKetQua count', ['id' => $studentIdentifier, 'count' => $rows->count()]);

        return $rows->map(function ($r) {
            return (array) $r;
        })->toArray();
    }

    /**
     * Lấy lịch sử tư vấn
     */
    public function layLichSuTuVan($studentIdentifier, int $limit = 30): array
    {
        $qb = DB::table('consultation_logs as cl')
            ->leftJoin('students as s', 's.id', '=', 'cl.student_id')
            ->leftJoin('users as u', 'u.id', '=', 'cl.advisor_id')
            ->select(
                'cl.id as log_id',
                's.student_code',
                's.fullname',
                'cl.created_at as meeting_date',
                'cl.topic',
                'cl.content',
                'cl.solution',
                'u.name as advisor_name'
            );

        if (is_numeric($studentIdentifier)) {
            $qb->where('s.id', '=', intval($studentIdentifier));
        } else {
            $qb->where('s.student_code', '=', $studentIdentifier);
        }

        $qb->orderBy('cl.created_at', 'desc')->limit($limit);

        $this->logQueryInfo($qb, ['studentIdentifier' => $studentIdentifier, 'type' => 'layLichSuTuVan']);

        $rows = $qb->get();

        Log::info('EduQueryService: layLichSuTuVan count', ['id' => $studentIdentifier, 'count' => $rows->count()]);

        return $rows->map(function ($r) {
            return (array) $r;
        })->toArray();
    }

    /**
     * Redact PII nếu user không có quyền xem.
     */
    public function redactIfNoPermission(array $rows, $canViewSensitive = false): array
    {
        if ($canViewSensitive) return $rows;

        return array_map(function ($r) {
            if (isset($r['email'])) $r['email'] = '***';
            if (isset($r['dob'])) $r['dob'] = null;
            return $r;
        }, $rows);
    }

    /**
     * Helper: xem xét chuỗi có thấy giống MSSV (mã) hay không.
     */
    protected function looksLikeStudentCode(string $q): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9\-]{4,20}$/', $q);
    }

    /**
     * Helper: log SQL và bindings (dùng để debug)
     */
    protected function logQueryInfo($queryBuilder, array $meta = []): void
    {
        try {
            $sql = $queryBuilder->toSql();
            $bindings = $queryBuilder->getBindings();
            Log::debug('EduQueryService SQL', [
                'sql' => $sql,
                'bindings' => $bindings,
                'meta' => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::debug('EduQueryService: cannot get SQL toSql()', ['error' => $e->getMessage(), 'meta' => $meta]);
        }
    }

    /**
     * Thực thi một "query spec" an toàn (do LLM tạo ra) và trả mảng kết quả.
     * Spec structure: see allowed entities below.
     */
    public function executeQuerySpec(array $spec): array
    {
        $allowed = [
            'students' => [
                'table' => 'students as s',
                'joins' => [
                    ['left', 'users as u', 'u.id', '=', 's.user_id'],
                    ['left', 'classes as c', 'c.id', '=', 's.class_id'],
                ],
                'fields' => [
                    'student_id' => 's.id',
                    'student_code' => 's.student_code',
                    'fullname' => 's.fullname',
                    'dob' => 's.dob',
                    'status' => 's.status',
                    'class_code' => 'c.code',
                    'class_name' => 'c.name',
                    'email' => 'u.email'
                ],
                'filters' => ['student_code', 'fullname_like', 'class_code', 'status', 'enrollment_year']
            ],
            'academic_warnings' => [
                'table' => 'academic_warnings as aw',
                'joins' => [
                    ['left', 'students as s', 's.id', '=', 'aw.student_id'],
                    ['left', 'semesters as sem', 'sem.id', '=', 'aw.semester_id']
                ],
                'fields' => [
                    'warning_id' => 'aw.id',
                    'student_code' => 's.student_code',
                    'fullname' => 's.fullname',
                    'semester' => 'sem.name',
                    'warning_level' => 'aw.warning_level',
                    'gpa_term' => 'aw.gpa_term',
                    'gpa_cumulative' => 'aw.gpa_cumulative',
                    'credits_owed' => 'aw.credits_owed',
                    'reason' => 'aw.reason',
                    'status' => 'aw.status',
                    'created_at' => 'aw.created_at'
                ],
                'filters' => ['student_code', 'warning_level', 'semester_id', 'status']
            ],
            'academic_results' => [
                'table' => 'academic_results as ar',
                'joins' => [
                    ['left', 'students as s', 's.id', '=', 'ar.student_id'],
                    ['left', 'semesters as sem', 'sem.id', '=', 'ar.semester_id']
                ],
                'fields' => [
                    'result_id' => 'ar.id',
                    'student_code' => 's.student_code',
                    'fullname' => 's.fullname',
                    'semester' => 'sem.name',
                    'gpa_10' => 'ar.gpa_10',
                    'gpa_4' => 'ar.gpa_4',
                    'classification' => 'ar.classification'
                ],
                'filters' => ['student_code', 'semester_id']
            ],
            'consultation_logs' => [
                'table' => 'consultation_logs as cl',
                'joins' => [
                    ['left', 'students as s', 's.id', '=', 'cl.student_id'],
                    ['left', 'users as u', 'u.id', '=', 'cl.advisor_id']
                ],
                'fields' => [
                    'log_id' => 'cl.id',
                    'student_code' => 's.student_code',
                    'fullname' => 's.fullname',
                    'meeting_date' => 'cl.created_at',
                    'topic' => 'cl.topic',
                    'content' => 'cl.content',
                    'solution' => 'cl.solution',
                    'advisor_name' => 'u.name'
                ],
                'filters' => ['student_code', 'advisor_id', 'topic_like']
            ],
        ];

        $entity = $spec['entity'] ?? null;
        if (! $entity || ! isset($allowed[$entity])) {
            throw new InvalidArgumentException("Entity không hợp lệ hoặc không được phép: " . ($entity ?? 'null'));
        }

        $cfg = $allowed[$entity];

        $qb = DB::table($cfg['table']);

        foreach ($cfg['joins'] as $join) {
            [$type, $table, $left, $op, $right] = $join;
            if ($type === 'left') $qb->leftJoin($table, $left, $op, $right);
            else $qb->join($table, $left, $op, $right);
        }

        $fieldsRequested = $spec['fields'] ?? array_keys($cfg['fields']);
        $selects = [];
        foreach ($fieldsRequested as $f) {
            if (! isset($cfg['fields'][$f])) continue;
            $selects[] = $cfg['fields'][$f] . " as {$f}";
        }
        if (empty($selects)) {
            $selects = [
                $cfg['fields'][array_key_first($cfg['fields'])] . ' as ' . array_key_first($cfg['fields'])
            ];
        }
        $qb->select($selects);

        $filters = $spec['filters'] ?? [];
        foreach ($filters as $k => $v) {
            if ($v === null || $v === '') continue;
            if (! in_array($k, $cfg['filters'])) continue;

            if (str_ends_with($k, '_like')) {
                $columnKey = substr($k, 0, -5);
                if (! isset($cfg['fields'][$columnKey])) continue;
                $col = $cfg['fields'][$columnKey];
                $qb->where($col, 'like', '%' . (string) $v . '%');
            } elseif (str_ends_with($k, '_gte')) {
                $columnKey = substr($k, 0, -4);
                if (! isset($cfg['fields'][$columnKey])) continue;
                $col = $cfg['fields'][$columnKey];
                $qb->where($col, '>=', $v);
            } elseif (str_ends_with($k, '_lte')) {
                $columnKey = substr($k, 0, -4);
                if (! isset($cfg['fields'][$columnKey])) continue;
                $col = $cfg['fields'][$columnKey];
                $qb->where($col, '<=', $v);
            } else {
                if (! isset($cfg['fields'][$k])) continue;
                $col = $cfg['fields'][$k];
                $qb->where($col, '=', $v);
            }
        }

        if (! empty($spec['order_by']) && is_array($spec['order_by'])) {
            $colKey = $spec['order_by']['column'] ?? null;
            $dir = strtolower($spec['order_by']['direction'] ?? 'desc');
            if ($colKey && isset($cfg['fields'][$colKey]) && in_array($dir, ['asc', 'desc'])) {
                $qb->orderBy($cfg['fields'][$colKey], $dir);
            }
        }

        $limit = isset($spec['limit']) && is_int($spec['limit']) ? max(1, min(200, $spec['limit'])) : 50;
        $qb->limit($limit);

        $this->logQueryInfo($qb, ['spec' => $spec, 'entity' => $entity]);

        $rows = $qb->get();

        Log::info('EduQueryService: executeQuerySpec result_count', ['entity' => $entity, 'count' => $rows->count()]);

        return $rows->map(function ($r) {
            return (array) $r;
        })->toArray();
    }
}
