<?php

namespace Ispecia\Admin\Http\Controllers\Mail;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Ispecia\Admin\DataGrids\Mail\EmailDataGrid;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Admin\Http\Requests\MassDestroyRequest;
use Ispecia\Admin\Http\Requests\MassUpdateRequest;
use Ispecia\Admin\Http\Resources\EmailResource;
use Ispecia\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Ispecia\Email\Mails\Email;
use Ispecia\Email\Repositories\AttachmentRepository;
use Ispecia\Email\Repositories\EmailRepository;
use Ispecia\Lead\Repositories\LeadRepository;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse|RedirectResponse
    {
        if (! request('route')) {
            return redirect()->route('admin.mail.index', ['route' => 'inbox']);
        }

        if (! bouncer()->hasPermission('mail.'.request('route'))) {
            abort(401, 'This action is unauthorized');
        }

        switch (request('route')) {
            case 'compose':
                return view('admin::mail.compose');

            default:
                if (request()->ajax()) {
                    return datagrid(EmailDataGrid::class)->process();
                }

                return view('admin::mail.index');
        }
    }

    /**
     * Display a resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $email = $this->emailRepository
            ->with(['emails', 'attachments', 'emails.attachments', 'lead', 'lead.person', 'lead.tags', 'lead.source', 'lead.type', 'person'])
            ->findOrFail(request('id'));

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
                ['user_id', 'IN', $userIds],
            ]);
        } else {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
            ]);
        }

        if (empty($results->toArray())) {
            unset($email->lead_id);
        }

        if (request('route') == 'draft') {
            return response()->json([
                'data' => new EmailResource($email),
            ]);
        }

        return view('admin::mail.view', compact('email'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'reply_to'   => 'required|array|min:1',
            'reply_to.*' => 'email',
            'reply'      => 'required',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        Event::dispatch('email.create.before');

        $data = request()->all();
        
        // Generate unique tracking hash for email tracking
        $data['tracking_hash'] = hash('sha256', uniqid('email_', true) . time());

        // Configure SMTP settings if email account is selected
        if (request()->filled('email_account_id')) {
            $emailAccountRepository = app(\Ispecia\Email\Repositories\EmailAccountRepository::class);
            $emailAccount = $emailAccountRepository->find(request('email_account_id'));
            
            if ($emailAccount && $emailAccount->is_active) {
                // Configure mail settings dynamically
                \Config::set('mail.mailers.smtp.host', $emailAccount->host);
                \Config::set('mail.mailers.smtp.port', $emailAccount->port);
                \Config::set('mail.mailers.smtp.username', $emailAccount->username);
                \Config::set('mail.mailers.smtp.password', $emailAccount->password);
                \Config::set('mail.mailers.smtp.encryption', $emailAccount->encryption);
                \Config::set('mail.from.address', $emailAccount->email);
                \Config::set('mail.from.name', $emailAccount->from_name ?? config('app.name'));
            }
        }

        $email = $this->emailRepository->create($data);

        if (! request('is_draft')) {
            try {
                // Check if email is scheduled
                if ($email->scheduled_at && $email->scheduled_at->isFuture()) {
                    // Dispatch job with delay
                    \Ispecia\Email\Jobs\SendScheduledEmail::dispatch($email->id)
                        ->delay($email->scheduled_at);
                    
                    // Mark as outbox instead of sent
                    $this->emailRepository->update([
                        'folders' => ['outbox'],
                    ], $email->id);
                } else {
                    // Send immediately
                    Mail::send(new Email($email));

                    $this->emailRepository->update([
                        'folders' => ['sent'],
                        'sent_at' => now(),
                    ], $email->id);
                }
            } catch (\Exception $e) {
            }
        }

        Event::dispatch('email.create.after', $email);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new EmailResource($email),
                'message' => trans('admin::app.mail.create-success'),
            ]);
        }

        if (request('is_draft')) {
            session()->flash('success', trans('admin::app.mail.saved-to-draft'));

            return redirect()->route('admin.mail.index', ['route' => 'draft']);
        }

        session()->flash('success', trans('admin::app.mail.create-success'));

        return redirect()->route('admin.mail.index', ['route'   => 'sent']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Event::dispatch('email.update.before', $id);

        $data = request()->all();

        if (! is_null(request('is_draft'))) {
            $data['folders'] = request('is_draft') ? ['draft'] : ['outbox'];
        }

        $email = $this->emailRepository->update($data, request('id') ?? $id);

        Event::dispatch('email.update.after', $email);

        if (! is_null(request('is_draft')) && ! request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['sent'],
                    'sent_at' => now(),
                ], $email->id);
            } catch (\Exception $e) {
            }
        }

        if (! is_null(request('is_draft'))) {
            if (request('is_draft')) {
                session()->flash('success', trans('admin::app.mail.saved-to-draft'));

                return redirect()->route('admin.mail.index', ['route' => 'draft']);
            } else {
                session()->flash('success', trans('admin::app.mail.create-success'));

                return redirect()->route('admin.mail.index', ['route' => 'inbox']);
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'data'    => new EmailResource($email->refresh()),
                'message' => trans('admin::app.mail.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.mail.update-success'));

        return redirect()->back();
    }

    /**
     * Run process inbound parse email.
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse(InboundEmailProcessor $inboundEmailProcessor)
    {
        $inboundEmailProcessor->processMessage(request('email'));

        return response()->json([], 200);
    }

    /**
     * Download file from storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function download($id)
    {
        $attachment = $this->attachmentRepository->findOrFail($id);

        try {
            return Storage::download($attachment->path);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $emails = $this->emailRepository->findWhereIn('id', $massUpdateRequest->input('indices'));

        try {
            foreach ($emails as $email) {
                Event::dispatch('email.update.before', $email->id);

                $this->emailRepository->update([
                    'folders' => request('folders'),
                ], $email->id);

                Event::dispatch('email.update.after', $email->id);
            }

            return response()->json([
                'message' => trans('admin::app.mail.mass-update-success'),
            ]);
        } catch (Exception) {
            return response()->json([
                'message' => trans('admin::app.mail.mass-update-success'),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $email = $this->emailRepository->findOrFail($id);

        try {
            Event::dispatch('email.'.request('type').'.before', $id);

            $parentId = $email->parent_id;

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $id);
            } else {
                $this->emailRepository->delete($id);
            }

            Event::dispatch('email.'.request('type').'.after', $id);

            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-success'),
                ], 200);
            }

            session()->flash('success', trans('admin::app.mail.delete-success'));

            if ($parentId) {
                return redirect()->back();
            }

            return redirect()->route('admin.mail.index', ['route' => 'inbox']);
        } catch (\Exception $exception) {
            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('admin::app.mail.delete-failed'),
                ], 400);
            }

            session()->flash('error', trans('admin::app.mail.delete-failed'));

            return redirect()->back();
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $mails = $this->emailRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($mails as $email) {
                Event::dispatch('email.'.$massDestroyRequest->input('type').'.before', $email->id);

                if ($massDestroyRequest->input('type') == 'trash') {
                    $this->emailRepository->update(['folders' => ['trash']], $email->id);
                } else {
                    $this->emailRepository->delete($email->id);
                }

                Event::dispatch('email.'.$massDestroyRequest->input('type').'.after', $email->id);
            }

            return response()->json([
                'message' => trans('admin::app.mail.delete-success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('admin::app.mail.delete-success'),
            ]);
        }
    }
}
