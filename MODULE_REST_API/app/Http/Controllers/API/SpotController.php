<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocModel;
use App\Models\SpotsModel;
use App\Models\SpotVaccineModel;
use App\Models\VaccinationModel;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class SpotController extends Controller
{
    public function GetSpot(Request $req) {
        $societie = SocModel::where("login_tokens", $req->token)->first();
        $spots = SpotsModel::where("regional_id", $societie->regional_id)->get();
        foreach($spots as $data) {
           $spotsFixedData[] = [
            "id" => $data->id,
            "name" => $data->name,
            "address" => explode(", ", $data->address)[1],
            "serve" => $data->serve,
            "capacity" => $data->capacity,
            'available_vaccines' => [
                "Sinovac" =>  $this->GetVaccineBySpot($data->id, 1),
                "AstraZaneca" =>  $this->GetVaccineBySpot($data->id, 2),
                "Moderna" => $this->GetVaccineBySpot($data->id, 3),
                "Pfizer" => $this->GetVaccineBySpot($data->id, 4),
                "Sinnopharm" => $this->GetVaccineBySpot($data->id, 6)
            ]
           ];
        }
        return response($spotsFixedData);
    }

    public function GetVaccineBySpot($spot_id, $vaccine_id) {
        return SpotVaccineModel::where("spot_id", $spot_id)
                ->where("vaccine_id",$vaccine_id)
                ->exists();
    }

    public function GetDetailSpot(Request $req, $spot_id) {
        $vaccination_date = VaccinationModel::Where("date",  date("Y-m-d"))
        ->where("spot_id", $spot_id)
        ->orWhere("date", $req->date)
        ->where("spot_id", $spot_id);

        if(!$vaccination_date->exists()) {
            return response()->json([
                "message" => "Spot detail not exists in this date!",
            ], 401);
        }

        $spot_detail = SpotsModel::where("id", $vaccination_date->first()->spot_id)
        ->first();
        $spot_detail->makeHidden(["regional_id"]);
        $spot_detail->address = explode(",", $spot_detail->address)[0]; 

        return response()->json([
            "date" => !$req->date ? date('F d, Y') : date('F d, Y',strtotime($req->date)),
            "spot" =>  $spot_detail,
            "vaccinations_count" => count(VaccinationModel::all())
        ], 200);
    }
    
}

// date('F d, Y',strtotime($req->date))