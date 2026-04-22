<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Exports\StageOne\OpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Services\IpcrGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class OpcrPmtReviewController extends Controller
{

}
