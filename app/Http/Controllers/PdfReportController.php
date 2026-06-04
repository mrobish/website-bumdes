<?php

namespace App\Http\Controllers;

use App\Services\PdfReportService;
use Illuminate\Http\Request;

class PdfReportController extends Controller
{
    protected $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function neracaSaldo(Request $request)
    {
        return $this->pdfService->neracaSaldo(
            $request->fiscal_year_id,
            $request->month
        );
    }

    public function labaRugi(Request $request)
    {
        return $this->pdfService->labaRugi(
            $request->fiscal_year_id,
            $request->month
        );
    }

    public function neraca(Request $request)
    {
        return $this->pdfService->neraca(
            $request->fiscal_year_id,
            $request->month
        );
    }

    public function jurnalUmum(Request $request)
    {
        return $this->pdfService->jurnalUmum(
            $request->fiscal_year_id,
            $request->month
        );
    }
}
