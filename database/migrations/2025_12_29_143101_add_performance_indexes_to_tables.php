<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add performance indexes to improve query speed for common operations.
     * These indexes are based on common query patterns in CRM systems.
     */
    public function up(): void
    {
        // Leads table indexes
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                // Index for status filtering (very common query)
                if (!$this->indexExists('leads', 'leads_status_index')) {
                    $table->index('status', 'leads_status_index');
                }
                
                // Index for date-based queries and sorting
                if (!$this->indexExists('leads', 'leads_created_at_index')) {
                    $table->index('created_at', 'leads_created_at_index');
                }
                
                // Index for user assignment queries
                if (!$this->indexExists('leads', 'leads_user_id_index')) {
                    $table->index('user_id', 'leads_user_id_index');
                }
                
                // Composite index for common filtered queries
                if (!$this->indexExists('leads', 'leads_status_user_id_index')) {
                    $table->index(['status', 'user_id'], 'leads_status_user_id_index');
                }
            });
        }

        // Persons (contacts) table indexes
        if (Schema::hasTable('persons')) {
            Schema::table('persons', function (Blueprint $table) {
                // Index for email lookups
                if (!$this->indexExists('persons', 'persons_email_index')) {
                    $table->index('email', 'persons_email_index');
                }
                
                // Index for phone lookups
                if (Schema::hasColumn('persons', 'phone') && !$this->indexExists('persons', 'persons_phone_index')) {
                    $table->index('phone', 'persons_phone_index');
                }
                
                // Index for organization relationships
                if (Schema::hasColumn('persons', 'organization_id') && !$this->indexExists('persons', 'persons_organization_id_index')) {
                    $table->index('organization_id', 'persons_organization_id_index');
                }
            });
        }

        // Activities table indexes
        if (Schema::hasTable('activities')) {
            Schema::table('activities', function (Blueprint $table) {
                // Polymorphic relationship index
                if (!$this->indexExists('activities', 'activities_entity_index')) {
                    $table->index(['entity_type', 'entity_id'], 'activities_entity_index');
                }
                
                // Date-based queries
                if (!$this->indexExists('activities', 'activities_created_at_index')) {
                    $table->index('created_at', 'activities_created_at_index');
                }
                
                // User assignment
                if (!$this->indexExists('activities', 'activities_user_id_index')) {
                    $table->index('user_id', 'activities_user_id_index');
                }
            });
        }

        // Emails table indexes
        if (Schema::hasTable('emails')) {
            Schema::table('emails', function (Blueprint $table) {
                // Scheduled emails query
                if (Schema::hasColumn('emails', 'scheduled_at') && !$this->indexExists('emails', 'emails_scheduled_at_index')) {
                    $table->index('scheduled_at', 'emails_scheduled_at_index');
                }
                
                // Status filtering
                if (Schema::hasColumn('emails', 'status') && !$this->indexExists('emails', 'emails_status_index')) {
                    $table->index('status', 'emails_status_index');
                }
                
                // Folder filtering
                if (Schema::hasColumn('emails', 'folder_id') && !$this->indexExists('emails', 'emails_folder_id_index')) {
                    $table->index('folder_id', 'emails_folder_id_index');
                }
            });
        }

        // Deals/Pipelines table indexes
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                // Stage filtering
                if (Schema::hasColumn('deals', 'stage_id') && !$this->indexExists('deals', 'deals_stage_id_index')) {
                    $table->index('stage_id', 'deals_stage_id_index');
                }
                
                // User assignment
                if (!$this->indexExists('deals', 'deals_user_id_index')) {
                    $table->index('user_id', 'deals_user_id_index');
                }
                
                // Date queries
                if (!$this->indexExists('deals', 'deals_created_at_index')) {
                    $table->index('created_at', 'deals_created_at_index');
                }
            });
        }

        // Quotes table indexes
        if (Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                // Lead relationship
                if (Schema::hasColumn('quotes', 'lead_id') && !$this->indexExists('quotes', 'quotes_lead_id_index')) {
                    $table->index('lead_id', 'quotes_lead_id_index');
                }
                
                // User assignment
                if (!$this->indexExists('quotes', 'quotes_user_id_index')) {
                    $table->index('user_id', 'quotes_user_id_index');
                }
                
                // Status filtering
                if (Schema::hasColumn('quotes', 'status') && !$this->indexExists('quotes', 'quotes_status_index')) {
                    $table->index('status', 'quotes_status_index');
                }
            });
        }

        // Products table indexes
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // SKU lookups
                if (Schema::hasColumn('products', 'sku') && !$this->indexExists('products', 'products_sku_index')) {
                    $table->index('sku', 'products_sku_index');
                }
                
                // Name searches
                if (!$this->indexExists('products', 'products_name_index')) {
                    $table->index('name', 'products_name_index');
                }
            });
        }

        // VoIP calls table indexes (if exists)
        if (Schema::hasTable('voip_calls')) {
            Schema::table('voip_calls', function (Blueprint $table) {
                // Status filtering
                if (Schema::hasColumn('voip_calls', 'status') && !$this->indexExists('voip_calls', 'voip_calls_status_index')) {
                    $table->index('status', 'voip_calls_status_index');
                }
                
                // Date queries
                if (!$this->indexExists('voip_calls', 'voip_calls_created_at_index')) {
                    $table->index('created_at', 'voip_calls_created_at_index');
                }
                
                // User queries
                if (Schema::hasColumn('voip_calls', 'user_id') && !$this->indexExists('voip_calls', 'voip_calls_user_id_index')) {
                    $table->index('user_id', 'voip_calls_user_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all indexes created in up() method
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropIndex('leads_status_index');
                $table->dropIndex('leads_created_at_index');
                $table->dropIndex('leads_user_id_index');
                $table->dropIndex('leads_status_user_id_index');
            });
        }

        if (Schema::hasTable('persons')) {
            Schema::table('persons', function (Blueprint $table) {
                $table->dropIndex('persons_email_index');
                if (Schema::hasColumn('persons', 'phone')) {
                    $table->dropIndex('persons_phone_index');
                }
                if (Schema::hasColumn('persons', 'organization_id')) {
                    $table->dropIndex('persons_organization_id_index');
                }
            });
        }

        if (Schema::hasTable('activities')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropIndex('activities_entity_index');
                $table->dropIndex('activities_created_at_index');
                $table->dropIndex('activities_user_id_index');
            });
        }

        if (Schema::hasTable('emails')) {
            Schema::table('emails', function (Blueprint $table) {
                if (Schema::hasColumn('emails', 'scheduled_at')) {
                    $table->dropIndex('emails_scheduled_at_index');
                }
                if (Schema::hasColumn('emails', 'status')) {
                    $table->dropIndex('emails_status_index');
                }
                if (Schema::hasColumn('emails', 'folder_id')) {
                    $table->dropIndex('emails_folder_id_index');
                }
            });
        }

        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                if (Schema::hasColumn('deals', 'stage_id')) {
                    $table->dropIndex('deals_stage_id_index');
                }
                $table->dropIndex('deals_user_id_index');
                $table->dropIndex('deals_created_at_index');
            });
        }

        if (Schema::hasTable('quotes')) {
            Schema::table('quotes', function (Blueprint $table) {
                if (Schema::hasColumn('quotes', 'lead_id')) {
                    $table->dropIndex('quotes_lead_id_index');
                }
                $table->dropIndex('quotes_user_id_index');
                if (Schema::hasColumn('quotes', 'status')) {
                    $table->dropIndex('quotes_status_index');
                }
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'sku')) {
                    $table->dropIndex('products_sku_index');
                }
                $table->dropIndex('products_name_index');
            });
        }

        if (Schema::hasTable('voip_calls')) {
            Schema::table('voip_calls', function (Blueprint $table) {
                if (Schema::hasColumn('voip_calls', 'status')) {
                    $table->dropIndex('voip_calls_status_index');
                }
                $table->dropIndex('voip_calls_created_at_index');
                if (Schema::hasColumn('voip_calls', 'user_id')) {
                    $table->dropIndex('voip_calls_user_id_index');
                }
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $doctrineTable = $doctrineSchemaManager->introspectTable($table);
        
        return $doctrineTable->hasIndex($index);
    }
};
