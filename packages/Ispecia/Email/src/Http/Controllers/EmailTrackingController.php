<?php

namespace Ispecia\Email\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ispecia\Email\Repositories\EmailRepository;

class EmailTrackingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmailRepository $emailRepository
    ) {}

    /**
     * Track email open.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function track($hash)
    {
        $email = $this->emailRepository->findOneWhere(['tracking_hash' => $hash]);

        if ($email) {
            if (!$email->opened_at) {
                // First open
                $this->emailRepository->update([
                    'opened_at' => now(),
                    'open_count' => 1,
                ], $email->id);
            } else {
                // Subsequent opens - update both timestamp and count
                $this->emailRepository->update([
                    'opened_at' => now(),
                    'open_count' => ($email->open_count ?? 0) + 1,
                ], $email->id);
            }
        }

        // Return a 1x1 transparent PNG
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

        return response($pixel, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
