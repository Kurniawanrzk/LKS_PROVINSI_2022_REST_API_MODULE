<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConsultationModel;
use App\Models\MedicalModel;
use App\Models\RegionalModel;
use App\Models\SocModel;
use App\Models\SpotsModel;
use App\Models\VaccinationModel;
use App\Models\VaccineModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VaccinationController extends Controller
{
    public function GetAllVaccination(Request $req)
    {
        // Get The Society Data
        $Society = SocModel::where("login_tokens", $req->token)->first();

        // Find Society Vaccination 
        $SocietyVaccination =  VaccinationModel::where("society_id", $Society->id);

        // Get The First Vaccination From Society
        $FirstVaccination = $SocietyVaccination->first();

        // Get The Second Vaccination From Society
        // $SecondVaccination variable when theres no second vaccination it gonna return the first vaccination
        $SecondVaccination = $SocietyVaccination->latest("date")->first();

        // Get The First Society Vaccination Queue Number And Add It To FirstVacciantion Object
        $FirstVaccinationQueue = VaccinationModel::where("date", $FirstVaccination->date)->where("spot_id", $FirstVaccination->spot_id)->get();
        $FirstVaccination->queue = count($FirstVaccinationQueue);

        // Get The First Society Vaccination Spot
        $FirstSpotVaccination = SpotsModel::where("id", $FirstVaccination->spot_id)->first();
        $FirstSpotVaccination->regional = RegionalModel::where("id", $FirstSpotVaccination->regional_id)->first();

        // Get The Second Society Vaccination Queue Number And Add It To SecondVacciantion Object
        $SecondVaccinationQueue = VaccinationModel::where("date", $SecondVaccination->date)->where("spot_id", $SecondVaccination->spot_id)->get();
        $SecondVaccination->queue = count($SecondVaccinationQueue);

        // Get The Secind Society Vaccination Spot
        $SecondSpotVaccination = SpotsModel::where("id", $SecondVaccination->spot_id)->first();
        $SecondVaccination->regional = RegionalModel::where("id", $SecondSpotVaccination->regional_id)->first();
        if (!$SocietyVaccination->exists()) {
            return response()->json([
                "message" => "Theres No Vaccination For You"
            ], 401);
        }

        return response()->json([
            "first" => [
                "queue" => $FirstVaccination->queue,
                "dose" => $FirstVaccination->dose,
                "vaccination_date" => $FirstVaccination->date,
                "spot" => $FirstSpotVaccination->makeHidden(["regional_id"]),
                "status" =>
                Carbon::create($FirstVaccination->date)->lessThan(date("Y-m-d"))
                    ? "Done" : "Undone",
                "vaccine" => VaccineModel::where("id", $FirstVaccination->vaccine_id)->first(),
                "vaccinator" =>  MedicalModel::where("id", $FirstVaccination->doctor_id)
                    ->first() !== null ? MedicalModel::where("id", $FirstVaccination->doctor_id)
                    ->first()
                    ->makeHidden(["spot_id", "user_id"]) : null
            ],

            "second" => $SecondVaccination->date === $FirstVaccination->date ? NULL : [
                "queue" => $SecondVaccination->queue,
                "dose" => $SecondVaccination->dose,
                "vaccination_date" => $SecondVaccination->date,
                "spot" => $SecondSpotVaccination->makeHidden(["regional_id"]),
                "status" =>
                Carbon::create($SecondVaccination->date)->lessThan(date("Y-m-d"))
                    ? "Done" : "Undone",
                "vaccine" => VaccineModel::where("id", $SecondVaccination->vaccine_id)->first(),
                "vaccinator" =>  MedicalModel::where("id", $SecondVaccination->doctor_id)
                    ->first() !== null ? MedicalModel::where("id", $SecondVaccination->doctor_id)
                    ->first()
                    ->makeHidden(["spot_id", "user_id"])  : null
            ]

        ], 200);
    }

    public function CheckDateVaccination($first, $second)
    {
        $a = Carbon::create($first);
        $b = Carbon::create($second)->subDays(30);

        return $b->lessThan($a);
    }
    public function RegisterVaccination(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "spot_id" => "integer|required",
            "date" => "date|required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Fill body correctly!"
            ], 401);
        }

        $RegisterVaccination = new VaccinationModel;
        // Tambahin society_id di dalam array Request
        $req->merge([
            "society_id" => SocModel::where("login_tokens", $req->token)->first()->id
        ]);

        // Menghitung jumlah vaksinasi society sebelum ditambahkan
        $SocietyVaccineCountBefore = VaccinationModel::where("society_id", $req->society_id);
        if (ConsultationModel::where("society_id", $req->society_id)->first()->status !== "accepted") {
            return response()->json([
                "message" => "Your consultation must be accepted by doctor before"
            ], 401);
        }
        if (count($SocietyVaccineCountBefore->get()) === 2) {
            return response()->json([
                "message" => "Society has been 2x vaccinated"
            ], 401);
        }

        if (
            count($SocietyVaccineCountBefore->get()) === 1
            &&  $this->CheckDateVaccination($SocietyVaccineCountBefore->first()->date, $req->date)
        ) {
            return response()->json([
                "message" => "Wait at least +30 days from 1st Vaccination"
            ], 401);
        }
        // Tambahkan data Vaccination
        $RegisterVaccination->fill([
            "date" => $req->date,
            "spot_id" => $req->spot_id,
            "society_id" => $req->society_id
        ])->save();

        // Menghitung jumlah vaksinasi society sesudah ditambahkan
        $SocietyVaccineCountAfter = count(VaccinationModel::where("society_id", $req->society_id)->get());

        if ($SocietyVaccineCountAfter === 1) {
            return response()->json([
                "message" => "first vaccination registered successful"
            ], 200);
        } else if ($SocietyVaccineCountAfter === 2) {
            return response()->json([
                "message" => "second vaccination registered successful"
            ], 200);
        }
    }
}
