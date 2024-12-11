<?php

namespace App\Http\Controllers;

use App\Classes\DTO\ZohoCRMAccountDTO;
use App\Classes\DTO\ZohoCRMDealDTO;
use App\Classes\ZohoCRMService;
use App\Exceptions\ZohoCRMExceptions\ZohoCRMRequestFailedException;
use App\Exceptions\ZohoCRMExceptions\ZohoCRMRequestValidationFailedException;
use App\Http\Requests\ZohoCRMCreateRecordsRequest;
use Illuminate\Http\JsonResponse;

class ZohoCRMController extends Controller
{
    /**
     * @param ZohoCRMCreateRecordsRequest $request
     * @return JsonResponse
     */
    public function createRecords(ZohoCRMCreateRecordsRequest $request): JsonResponse
    {
        /** @var ZohoCRMService $service */
        $service = app(ZohoCRMService::class);

        $accountDto = new ZohoCRMAccountDTO(...$request->only(['accountName', 'accountWebsite', 'accountPhone']));
        $dealDto = new ZohoCRMDealDTO(...$request->only(['dealName', 'dealStage']));

        try {
            $result = $service->createAccount($accountDto);
            $service->createDeal($dealDto, $result[0]['details']['id']);

            return response()->json(['result' => 'success', 'message' => 'Records successfully created.']);
        } catch (ZohoCRMRequestValidationFailedException $e) {
            return response()->json(['result' => 'failure', 'message' => 'Zoho validation not passed. ' . $e->getMessage()], $e->getCode());
        } catch (ZohoCRMRequestFailedException $e) {
            return response()->json(['result' => 'failure', 'message' => 'Zoho request failed. ' . $e->getMessage()], $e->getCode());
        }
    }
}
