<?php

namespace App\Services\Identification;

interface IdentificationServiceInterface
{
    public function getObservation($id);
    public function addIdentification($checklistId, array $data);
    public function agreeWithIdentification($checklistId, $identificationId);
    public function cancelAgreement($checklistId, $identificationId);
    public function withdrawIdentification($checklistId, $identificationId);
    public function disagreeWithIdentification($checklistId, $identificationId, array $data);
} 