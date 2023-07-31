<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConsultationModel;
use App\Models\SocModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class RequestConsultation extends Controller
{
    public function CreateConsultation(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "disease_history" => "nullable|string",
            "current_symptoms" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Fill the request correctly!"
            ], 401);
        }

        $token = SocModel::where("login_tokens", $req->token)->first();
        $req->merge([
            "society_id" => $token->id
        ]);
        $consultation = new ConsultationModel;
        $consultation->fill($req->except(["token"]));
        $consultation->save();
        return response()->json([
            "message" => "Request consultation sent successful!"
        ], 200);
    }

    public function GetConsultation()
    {
        $consultationData = ConsultationModel::all();
        return response($consultationData, 200);
    }
}
