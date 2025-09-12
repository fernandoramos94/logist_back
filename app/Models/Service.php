<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AddressService;
// use App\Models\Logs;
use App\Models\ServiceNote;

class Service extends Model
{
    protected $table = 'service';

    /**
     * Filter services with the same logic previously handled in the controller.
     * Returns an array with services data and counters.
     *
     * @param array $params
     * @return array{services: array, servicesCount: array, total: int}
     */
    public static function filterServices(array $params)
    {
        // Pagination params
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $page = $page > 0 ? $page : 1;
        $pageSize = isset($params['pageSize']) ? (int) $params['pageSize'] : 50;
        if ($pageSize <= 0) { $pageSize = 50; }
        if ($pageSize > 1000) { $pageSize = 1000; }
        $offset = ($page - 1) * $pageSize;

        // Subquery for evidences aggregation
        $evidencesSub = DB::table('evidences')
            ->select('service_id')
            ->selectRaw("SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) AS status_id_one")
            ->selectRaw("SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS status_id_two")
            ->groupBy('service_id');

        // Base query with joins
        $baseQuery = DB::table('service')
            ->join('client', 'client.id', '=', 'service.client_id')
            ->join('status', 'status.id', '=', 'service.status_id')
            ->join('unit', 'unit.id', '=', 'service.unit_id')
            ->join('driver', 'driver.id', '=', 'service.driver_id')
            ->join('assistant', 'assistant.id', '=', 'service.assistant_id')
            ->leftJoinSub($evidencesSub, 'e', function ($join) {
                $join->on('e.service_id', '=', 'service.id');
            });

        // Apply filters safely
        $applyFilters = function ($query) use ($params) {
            $query->when(!empty($params['client']), function ($q) use ($params) {
                $q->where('service.client_id', (int) $params['client']);
            });
            $query->when(!empty($params['status']), function ($q) use ($params) {
                $q->where('service.status_id', (int) $params['status']);
            });
            $query->when(!empty($params['upload_date']), function ($q) use ($params) {
                $q->whereDate('service.upload_date', $params['upload_date']);
            });
            $query->when(!empty($params['download_date']), function ($q) use ($params) {
                $q->whereDate('service.download_date', $params['download_date']);
            });
            $query->when(!empty($params['created_at']), function ($q) use ($params) {
                $q->where('service.created_at', 'like', '%' . $params['created_at'] . '%');
            });
        };

        $selectQuery = (clone $baseQuery);
        $applyFilters($selectQuery);
        $selectQuery->select([
            'unit.unit',
            DB::raw('status.name as status'),
            DB::raw('status.color as status_color'),
            DB::raw("CONCAT(assistant.name, ' ', assistant.last_name) as assistant"),
            DB::raw("CONCAT(driver.name, ' ', driver.last_name) as driver"),
            'service.*',
            DB::raw("concat_ws('_', unit.unit, service.unified) as unified_concat"),
            DB::raw("concat_ws(' ', DATE_FORMAT(upload_date, '%d/%m/%Y'), charging_hour) as date_start"),
            DB::raw("concat_ws(' ', DATE_FORMAT(download_date, '%d/%m/%Y'), download_time) as date_end"),
            DB::raw('e.status_id_one'),
            DB::raw('e.status_id_two'),
            DB::raw('client.name as client'),
        ])
        ->orderByDesc('service.created_at')
        ->orderBy('status.id')
        ->offset($offset)
        ->limit($pageSize);

        $services = $selectQuery->get();

        // Unfiltered counters (as original logic)
        $servicesCount = DB::table('service')->selectRaw('
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status_id in (2,3,4,5)  THEN 1 ELSE 0 END) AS in_route,
            SUM(CASE WHEN status_id = 6 THEN 1 ELSE 0 END) AS good
        ')->get();

        // Total filtered rows
        $totalQuery = (clone $baseQuery);
        $applyFilters($totalQuery);
        $total = $totalQuery->count('service.id');

        // Optimize enrichment to avoid N+1 queries
        if ($services->count() > 0) {
            $serviceIds = $services->pluck('id')->all();

            $logs = DB::table('logs')
                ->select('logs.service_id', 'logs.ip', 'logs.event', 'logs.created_at', 'users.name')
                ->join('users', 'users.id', '=', 'logs.user_id')
                ->whereIn('logs.service_id', $serviceIds)
                ->get()
                ->groupBy('service_id');

            $assistants = DB::table('service_assitant')
                ->select('service_assitant.order_id as service_id', 'assistant.*')
                ->join('assistant', 'assistant.id', '=', 'service_assitant.assistant_id')
                ->whereIn('service_assitant.order_id', $serviceIds)
                ->get()
                ->groupBy('service_id');

            $notes = ServiceNote::whereIn('service_id', $serviceIds)
                ->get()
                ->groupBy('service_id');

            $addresses = AddressService::query()
                ->select(
                    DB::raw('address_services.service_id as service_id'),
                    'address_services.id',
                    'address_services.origin',
                    'address_services.destination',
                    'address_services.status_id',
                    DB::raw('status.name as status'),
                    DB::raw('status.color as status_color')
                )
                ->leftJoin('status', 'status.id', '=', 'address_services.status_id')
                ->whereIn('address_services.service_id', $serviceIds)
                ->orderBy('address_services.id' ,'asc')
                ->get()
                ->groupBy('service_id');

            foreach ($services as $item) {
                $sid = $item->id;
                $item->logs = $logs->get($sid, collect())->values();
                $item->assistants = $assistants->get($sid, collect())->values();
                $item->notes = $notes->get($sid, collect())->values();
                $item->created_at = Carbon::parse($item->created_at)->format('d/m/Y H:i');
                $item->address_services = $addresses->get($sid, collect())->values();
            }
        }

        return [
            'services' => $services,
            'servicesCount' => $servicesCount,
            'total' => $total,
        ];
    }
}

