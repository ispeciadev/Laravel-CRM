<?php

namespace Ispecia\Email\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\Email\Models\EmailAccount;

class EmailAccountRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmailAccount::class;
    }

    /**
     * Get all active email accounts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAccounts()
    {
        return $this->model
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the default email account.
     *
     * @return \Ispecia\Email\Models\EmailAccount|null
     */
    public function getDefaultAccount()
    {
        return $this->model
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Set an account as default (and unset others).
     *
     * @param  int  $id
     * @return bool
     */
    public function setAsDefault($id)
    {
        // Unset all other defaults
        $this->model->where('is_default', true)->update(['is_default' => false]);

        // Set this one as default
        return $this->update(['is_default' => true], $id);
    }
}
