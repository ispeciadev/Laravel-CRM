<?php

namespace Ispecia\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Ispecia\Admin\DataGrids\Settings\EmailAccountDataGrid;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Email\Repositories\EmailAccountRepository;

class EmailAccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected EmailAccountRepository $emailAccountRepository)
    {
    }

    /**
     * Display a listing of the email accounts.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(EmailAccountDataGrid::class)->process();
        }

        return view('admin::settings.email-accounts.index');
    }

    /**
     * Show the form for creating a new email account.
     */
    public function create(): View
    {
        return view('admin::settings.email-accounts.create');
    }

    /**
     * Store a newly created email account in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:email_accounts,email',
            'host'       => 'required|string|max:255',
            'port'       => 'required|integer',
            'username'   => 'required|string|max:255',
            'password'   => 'required|string',
            'encryption' => 'required|in:tls,ssl',
            'from_name'  => 'nullable|string|max:255',
        ]);

        Event::dispatch('settings.email_accounts.create.before');

        $data = request()->all();

        // If this is set as default, unset all others
        if (request()->boolean('is_default')) {
            $this->emailAccountRepository->getModel()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $emailAccount = $this->emailAccountRepository->create($data);

        Event::dispatch('settings.email_accounts.create.after', $emailAccount);

        session()->flash('success', trans('admin::app.settings.email-accounts.index.create-success'));

        return redirect()->route('admin.settings.email_accounts.index');
    }

    /**
     * Show the form for editing the specified email account.
     */
    public function edit(int $id): View
    {
        $emailAccount = $this->emailAccountRepository->findOrFail($id);

        return view('admin::settings.email-accounts.edit', compact('emailAccount'));
    }

    /**
     * Update the specified email account in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:email_accounts,email,' . $id,
            'host'       => 'required|string|max:255',
            'port'       => 'required|integer',
            'username'   => 'required|string|max:255',
            'password'   => 'nullable|string',
            'encryption' => 'required|in:tls,ssl',
            'from_name'  => 'nullable|string|max:255',
        ]);

        Event::dispatch('settings.email_accounts.update.before', $id);

        $data = request()->except('password');

        // Only update password if provided
        if (request()->filled('password')) {
            $data['password'] = request('password');
        }

        // If this is set as default, unset all others
        if (request()->boolean('is_default')) {
            $this->emailAccountRepository->getModel()
                ->where('is_default', true)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $emailAccount = $this->emailAccountRepository->update($data, $id);

        Event::dispatch('settings.email_accounts.update.after', $emailAccount);

        session()->flash('success', trans('admin::app.settings.email-accounts.index.update-success'));

        return redirect()->route('admin.settings.email_accounts.index');
    }

    /**
     * Remove the specified email account from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $emailAccount = $this->emailAccountRepository->findOrFail($id);

        // Prevent deletion of default account
        if ($emailAccount->is_default) {
            return response()->json([
                'message' => trans('admin::app.settings.email-accounts.index.default-delete-error'),
            ], 400);
        }

        try {
            Event::dispatch('settings.email_accounts.delete.before', $id);

            $emailAccount->delete();

            Event::dispatch('settings.email_accounts.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.email-accounts.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.email-accounts.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass delete email accounts.
     */
    public function massDestroy(): JsonResponse
    {
        $indices = request('indices', []);

        foreach ($indices as $id) {
            $emailAccount = $this->emailAccountRepository->find($id);

            if ($emailAccount && !$emailAccount->is_default) {
                Event::dispatch('settings.email_accounts.delete.before', $id);

                $emailAccount->delete();

                Event::dispatch('settings.email_accounts.delete.after', $id);
            }
        }

        return response()->json([
            'message' => trans('admin::app.settings.email-accounts.index.delete-success'),
        ]);
    }

    /**
     * Test SMTP connection for an email account.
     */
    public function testConnection(int $id): JsonResponse
    {
        $emailAccount = $this->emailAccountRepository->findOrFail($id);

        try {
            // Temporarily configure mail settings
            Config::set('mail.mailers.smtp.host', $emailAccount->host);
            Config::set('mail.mailers.smtp.port', $emailAccount->port);
            Config::set('mail.mailers.smtp.username', $emailAccount->username);
            Config::set('mail.mailers.smtp.password', $emailAccount->password);
            Config::set('mail.mailers.smtp.encryption', $emailAccount->encryption);
            Config::set('mail.from.address', $emailAccount->email);
            Config::set('mail.from.name', $emailAccount->from_name ?? config('app.name'));

            // Try to connect (this will throw an exception if it fails)
            Mail::mailer('smtp')->getSwiftMailer()->getTransport()->start();

            return response()->json([
                'message' => trans('admin::app.settings.email-accounts.index.test-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.email-accounts.index.test-failed') . ': ' . $exception->getMessage(),
            ], 400);
        }
    }
}
