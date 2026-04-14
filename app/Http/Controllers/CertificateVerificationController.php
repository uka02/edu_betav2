<?php

namespace App\Http\Controllers;

use App\Http\Requests\Certificates\StoreCertificateVerificationRequest;
use App\Models\CertificateVerification;
use App\Services\CertificationRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class CertificateVerificationController extends Controller
{
    public function __construct(
        private readonly CertificationRequestService $certificationRequestService
    ) {
    }

    public function store(StoreCertificateVerificationRequest $request): RedirectResponse
    {
        $this->certificationRequestService->createRequest(
            $request->user(),
            $request->validated() + ['document' => $request->file('document')]
        );

        return redirect()
            ->route('certificates.index')
            ->with('success', __('certificates.request_submitted'));
    }

    public function download(CertificateVerification $verification)
    {
        $user = request()->user();

        abort_unless(
            $user && ($user->isAdmin() || (int) $verification->user_id === (int) $user->id),
            403
        );

        abort_unless(Storage::disk('local')->exists($verification->document_path), 404);

        return Storage::disk('local')->download(
            $verification->document_path,
            $verification->original_filename
        );
    }
}
