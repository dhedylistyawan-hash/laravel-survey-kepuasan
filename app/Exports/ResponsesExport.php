<?php

namespace App\Exports;

use App\Models\Response;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResponsesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Response::with(['user', 'question.category'])->get()->map(function ($response) {
            return [
                'ID' => $response->id,
                'Survey ID' => $response->survey_id,
                'User ID' => $response->user_id ?? 'Anonim',
                'Nama Responden' => $response->user ? $response->user->name : 'Anonim',
                'Email Responden' => $response->user ? $response->user->email : 'Anonim',
                'Jenis Kelamin' => $response->jenis_kelamin ?? '-',
                'Umur' => $response->umur ?? '-',
                'Pendidikan' => $response->pendidikan ?? '-',
                'Unit Kerja' => $response->unit_kerja ?? '-',
                'Jabatan Fungsional' => $response->jabatan_fungsional ?? '-',
                'Kategori' => $response->question->category->name ?? '-',
                'Pertanyaan' => $response->question->question_text ?? '-',
                'Tipe Pertanyaan' => $response->question->type ?? '-',
                'Tingkat Kepuasan' => $response->satisfaction ?? '-',
                'Tingkat Kepentingan' => $response->importance ?? '-',
                'Saran' => $response->suggestion ?? '-',
                'IP Address' => $response->ip_address ?? '-',
                'User Agent' => $response->user_agent ?? '-',
                'Tanggal Jawaban' => $response->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $response->updated_at->format('Y-m-d H:i:s')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Survey ID', 
            'User ID',
            'Nama Responden',
            'Email Responden',
            'Jenis Kelamin',
            'Umur',
            'Pendidikan',
            'Unit Kerja',
            'Jabatan Fungsional',
            'Kategori',
            'Pertanyaan',
            'Tipe Pertanyaan',
            'Tingkat Kepuasan',
            'Tingkat Kepentingan',
            'Saran',
            'IP Address',
            'User Agent',
            'Tanggal Jawaban',
            'Updated At'
        ];
    }
}
