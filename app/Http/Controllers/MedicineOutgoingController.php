<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Models\MedicineIncoming;
use App\Models\MedicineOutgoing;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMedicineOutgoingRequest;

class MedicineOutgoingController extends Controller
{
    public function index($lang, Request $request)
    {
        try {
            App::setLocale($lang);
            $user = Auth::user();
            $access = checkACL($user, "Can - Read Medicine Outgoing");
            if(!$access) {
                return response()->json([
                    "error" => true,
                    "message" => trans("messages.access_denied")
                ]);
            }
            $q = $request->input('q');
            $perPage = $request->input('per_page') ?: config('app.paginate_per_page');
            $isDpho = $request->has('is_dpho') ? ($request->input('is_dpho') === 'true') : null;  
            
            $query = MedicineOutgoing::with(['medicine' => function ($qr) {
                    $qr->withTrashed();
                }])
                ->select((new MedicineOutgoing)->getColumns())
                ->join('medicines', 'medicines.id', '=', 'medicine_outgoings.medicine_id')
                ->join('units', 'units.id', '=', 'medicine_outgoings.unit_id')
                ->where('medicines.clinic_id', '' . $user->clinic_id . '')
                ->where('medicines.name', 'LIKE', '%' . $q . '%');


            if (!is_null($isDpho)) {
                $query->whereNotNull('medicine.code_dpho', $isDpho);
            }

            $fromDate = $request->input('from_date', '0001-01-01');
            $toDate = $request->input('to_date', '9999-12-31');

            $query = $query->whereBetween('medicine_outgoings.date', [$fromDate, $toDate])
                ->where('medicine_outgoings.quantity', '<>', 0);

            $item = $query->orderBy('date', 'desc')->paginate($perPage);

            activity()
            ->causedBy($user)
            ->log('Viewed List medicine outgoing data');

           
            return response()->json([
                'total' => $item->total(),
                'per_page' => $item->perPage(),
                'current_page' => $item->currentPage(),
                'last_page' => $item->lastPage(),
                'from' => $item->firstItem(),
                'to' => $item->lastItem(),
                'data' => $item,
                'error' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'error_message' => "Sorry, something went wrong please try again later"
            ]);
        }
    }

    /**
     * Get medicine outgoing by id
     *
     */

    public function show($lang, $id)
    {
        try {
            App::setLocale($lang);
            $user = Auth::user();
            $access = checkACL($user, "Can - Read Medicine Outgoing");
            if(!$access) {
                return response()->json([
                    "error" => true,
                    "message" => trans("messages.access_denied")
                ]);
            }

            $medicineOutgoing = (new MedicineOutgoing())->getDataById($id);

            activity()
            ->causedBy($user)
            ->log('Viewed detail medicine outgoing data');

            if (empty($medicineOutgoing)) {
                return response()->json([
                    "error" => true,
                    "message" => trans("messages.data_not_found")
                ]);
            }
          
            return response()->json([
                'data' => $medicineOutgoing,
                'error' => false
            ]);
        } catch (\Exception $error) {
            return response()->json([
                'error' => true,
                'error_message' => "Sorry, something went wrong please try again later"
            ]);
        }

    }


    public function create($lang, StoreMedicineOutgoingRequest $request)
    {
    
        try {
            App::setLocale($lang);
            $user = Auth::user();
            $access = checkACL($user, "Can - Create Medicine Outgoing");
            if(!$access) {
                return response()->json([
                    "error" => true,
                    "message" => trans("messages.access_denied")
                ]);
            }

            $input = $request->all();
            $date = date('Y-m-d');
            $medicineId = $input['id_medicine'];
            $quantity = $input['quantity'];

            $incomingMedicines = MedicineIncoming::with('outgoings')
                ->where('id_medicine', $medicineId)
                ->whereRaw('
                    (quantity > COALESCE((SELECT SUM(quantity) FROM 
                        medicine_outgoings 
                        WHERE medicine_outgoings.medicine_id = medicine_incomings.id_medicine 
                        AND medicine_outgoings.batch_no = medicine_incomings.batch_no 
                        AND medicine_outgoings.deleted_at IS NULL), 0))'
                )->orderBy('date', 'ASC')
                ->get();


            $totalStock = $incomingMedicines->sum(function ($medicine) {
                return $medicine->quantity - $medicine->outgoings->sum('quantity');
            });
        
            $checkStock = ($totalStock >= $quantity);
            $data = $checkStock;
            if ($checkStock && count($incomingMedicines) > 0) {
                $prepareForBulkInsert = [];
                foreach ($incomingMedicines as $medicine) {
                    $availableStock = $medicine->quantity - $medicine->outgoings->sum('quantity');
                    $quantityOutgoing = min($quantity, $availableStock);

                    $existingMedicineOutgoing = MedicineOutgoing::where('medicine_id', $medicineId)
                        ->where('batch_no', $medicine->batch_no)
                        ->where('exp_date', $medicine->exp_date)
                        ->where('quantity', $medicine->quantity)
                        ->where('date', $date)
                        ->first();
                
                    if(!empty($existingMedicineOutgoing)) {
                        continue;
                    }

                    $prepareForBulkInsert [] = [
                        'medicine_id' => $medicineId,
                        'batch_no' => $medicine->batch_no,
                        'exp_date' => $medicine->exp_date,
                        'quantity' => $quantityOutgoing,
                        'date' => $date,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'unit_id' => $request->unit_id
                    ];
            
                    $quantity -= $quantityOutgoing;
            
                    if ($quantity <= 0) {
                        break;
                    }
                }

                MedicineOutgoing::insert($prepareForBulkInsert);
                activity()
                ->causedBy($user)
                ->log('Created medicine outgoing data');
            } else {
                $medicine = Medicine::findOrFail($medicineId);
                $data = trans('messages.medicine_stock_not_enough', ['name' => $medicine->name]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "error" => true,
                'message' => trans('messages.internal_server_error'),
            ], 500);
        }
        $response = $data;

        return response()->json($response);
    }
}
