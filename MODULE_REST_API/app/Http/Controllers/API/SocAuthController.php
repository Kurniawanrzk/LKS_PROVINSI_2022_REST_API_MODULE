<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegionalModel;
use App\Models\SocModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{ Validator };

class SocAuthController extends Controller
{
    public function login(Request $req) {
        $validator = Validator::make($req->all(), [
                "id_card_number" => "required",
                "password" => "required"
        ]);

        if($validator->fails()) {
            return response()->json([
                "message" => "Fill data correctly!"
            ], 401);
        }

        if(SocModel::where("id_card_number", $req->id_card_number)->first() && SocModel::where("password", $req->password)->first()) {
            // Ambil data dari table 
            $soc = SocModel::where("id_card_number", $req->id_card_number)->first();
            // Buat Token Nya
            $token =  md5($soc->id_card_number + $soc->password);
            // Setelah dibuat, update langsung token societies
            $soc->update(["login_tokens" => $token]);
            // Sesuai dengan module, Ganti login_tokens ke token
            unset($soc->login_tokens);
            $soc->token = $token;
            // Sesuai dengan moudule, Ganti regional_id dengan data asli regionalnya
            $soc->regional = RegionalModel::where("id", $soc->regional_id)->first();
            unset($soc->regional_id);
            return response()->json($soc, 401);
        } else {
            return response()->json([
                "message" => "ID Card Number or Password incorrect"
            ], 401);
        }
    }

    public function logout(Request $req) {
        $validator = Validator::make($req->all(), [
            "token" => "required"
        ]);

        if($validator->fails()) {
            return response()->json([
                "message" => "Invalid token"
            ], 401);
        }

        if(SocModel::where("login_tokens", $req->token)->first()) {
            $soc = SocModel::where("login_tokens", $req->token)->first();
            $soc->update(["login_tokens" => NULL]);
            return response()->json([
                "message" => "Logout Success"
            ], 200);
        } else {
            return response()->json([
                "message" => "Invalid token"
            ], 401);
        }


    }
}
