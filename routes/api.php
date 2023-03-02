<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/file', function(Request $request){
    $res = Http::post('https://hooks.zapier.com/hooks/catch/4664515/3bydcec/', $request);
    return "hel";
    function generateHeaders()
            {
                $key = "150e2b065b1b66b6846011e0f9006d789d5b4e2d127fce0a144e20add5885ec1";
                $secret = "6895f99743941378cb81b9f654de18ce30b1fc246c5115941081721e3bcacb3e";
                $nonce   = hash('sha512', uniqid(true));
                $created = date('r');
                $digest  = base64_encode(sha1(base64_decode($nonce) . $created . $secret, true));

                return [
                    'Authorization'  => 'WSSE profile="UsernameToken"',
                    'X-WSSE'         => "UsernameToken Username=\"{$key}\", PasswordDigest=\"$digest\", Nonce=\"$nonce\", Created=\"$created\"",
                    'Accept-charset' => 'utf-8',
                    "Accept"         => 'application/json',
                    'Content-Type'   =>'application/json'
                ];
            }
        Podio::setup("zapier-94jscy", "irjR8yRwSFarNbMNpva5yFcDbw0hGxZQh8rUfVsaINXkDdfWfyecBGVMsmjtdRUF");
        Podio::authenticate_with_app("28374470", "e789c952a24d25a5bb8ef35e72b6b545");
         $file = PodioFile::get("1641283400");
            $fileRaw = $file->get_raw();
            // Storage::disk('local')->put('file.pdf', $fileRaw);
            $data = base64_encode($fileRaw);
            $response = Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/casefiles', [
                'title' => 'file',
            ]);
            error_log($response);
            $response2 = Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/folders/3427004/casefiles/'.$response['id']);
            error_log($response2);
            $response3 = Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/documents', [
                "caseFileId"=> $response['id'],
                "title"=> "Contract",
                "pdfFile"=> $data,
                "type"=>'signable'
            ]);
            error_log($response3);
            // https://app.penneo.com/api/v3/casefiles/${caseData.id}/signers
            $response4 = Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/casefiles/'.$response['id'].'/signers', [
                   'name'=> "wassay hassan"
             ]);
             error_log($response4);
             $response5  =Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/documents/'.$response3['id'].'/signaturelines', [
                "role"=> 'MySignerRole',
                "signOrder"=> '1'
             ]);
             error_log($response5);
             $response6 = Http::withHeaders(generateHeaders())->post('https://app.penneo.com/api/v3/documents/'.$response3['id'].'/signaturelines/'.$response5['id'].'/signers/'.$response4['id']);
             error_log($response6);
             $response7 = Http::withHeaders(generateHeaders())->get('https://app.penneo.com/api/v3/casefiles/'.$response['id'].'/signers/'.$response4['id'].'/signingrequests');
             error_log($response7);
             $response8 = Http::withHeaders(generateHeaders())->put('https://app.penneo.com/api/v3/signingrequests/'.$response7[0]['id'], [
                "email"=>'wassaywarraich3@gmail.com',
    
                // The subject and text of the first email he gets.
                "emailSubject"=>"An offer you can't refuse",
                "emailText"=>"Hi Your contract is ready for you.",
                
                // Let's send him a reminder every 4 days.
                "reminderInterval"=>4,
                "reminderEmailSubject"=>"You forgot to sign your contract.",
                "reminderEmailText"=>"Dear You haven't signed your contract yet. We'll try not to take it personally.",
                
                // Let him sign with touch.
                "enableInsecureSigning"=>true
             ]);
             error_log($response8);
             $response9 = Http::withHeaders(generateHeaders())->patch('https://app.penneo.com/api/v3/casefiles/'.$response['id'].'/send');
             error_log($response9);

            // file_put_contents("file.pdf", $fileRaw);// make sure you have permission to save the file to the location
            return $data;
});
