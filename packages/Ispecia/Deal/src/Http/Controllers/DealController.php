<?php

namespace Ispecia\Deal\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Deal\Repositories\DealRepository;
use Ispecia\Lead\Repositories\PipelineRepository;
use Ispecia\Lead\Repositories\StageRepository;
use Ispecia\User\Repositories\UserRepository;

class DealController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DealRepository $dealRepository,
        protected PipelineRepository $pipelineRepository,
        protected StageRepository $stageRepository,
        protected UserRepository $userRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pipeline = $this->pipelineRepository->getDefaultPipeline();

        $deals = $this->dealRepository->with(['user', 'person', 'pipeline', 'stage'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('deal::index', compact('deals', 'pipeline'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = $this->userRepository->all();
        $pipeline = $this->pipelineRepository->getDefaultPipeline();

        return view('deal::create', compact('users', 'pipeline'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $data = request()->validate([
            'title'                    => 'required',
            'description'              => 'nullable',
            'deal_value'               => 'required|numeric',
            'expected_close_date'      => 'nullable|date',
            'user_id'                  => 'required|exists:users,id',
            'person_id'                => 'nullable|exists:persons,id',
            'lead_id'                  => 'nullable|exists:leads,id',
            'lead_pipeline_id'         => 'nullable|exists:lead_pipelines,id',
            'lead_pipeline_stage_id'   => 'nullable|exists:lead_pipeline_stages,id',
        ]);

        $data['status'] = 'open';

        Event::dispatch('deal.create.before');

        $deal = $this->dealRepository->create($data);

        Event::dispatch('deal.create.after', $deal);

        session()->flash('success', 'Deal created successfully.');

        return redirect()->route('admin.deals.index');
    }

    /**
     * Display the specified resource.
     */
    public function view(int $id): View
    {
        $deal = $this->dealRepository->with(['user', 'person', 'lead', 'pipeline', 'stage'])->findOrFail($id);

        return view('deal::view', compact('deal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $deal = $this->dealRepository->findOrFail($id);
        $users = $this->userRepository->all();
        $pipeline = $this->pipelineRepository->getDefaultPipeline();

        return view('deal::edit', compact('deal', 'users', 'pipeline'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $data = request()->validate([
            'title'                    => 'required',
            'description'              => 'nullable',
            'deal_value'               => 'required|numeric',
            'status'                   => 'required|in:open,won,lost',
            'expected_close_date'      => 'nullable|date',
            'user_id'                  => 'required|exists:users,id',
            'lead_pipeline_stage_id'   => 'nullable|exists:lead_pipeline_stages,id',
        ]);

        Event::dispatch('deal.update.before', $id);

        $deal = $this->dealRepository->update($data, $id);

        Event::dispatch('deal.update.after', $deal);

        session()->flash('success', 'Deal updated successfully.');

        return redirect()->route('admin.deals.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $deal = $this->dealRepository->findOrFail($id);

        Event::dispatch('deal.delete.before', $id);

        $this->dealRepository->delete($id);

        Event::dispatch('deal.delete.after', $id);

        session()->flash('success', 'Deal deleted successfully.');

        return redirect()->route('admin.deals.index');
    }

    /**
     * Mass update the specified resources.
     */
    public function massUpdate(): JsonResponse
    {
        $data = request()->all();
        $indices = $data['indices'] ?? [];

        foreach ($indices as $index) {
            Event::dispatch('deal.update.before', $index);

            $deal = $this->dealRepository->update($data, $index);

            Event::dispatch('deal.update.after', $deal);
        }

        return response()->json([
            'message' => 'Deals updated successfully.',
        ]);
    }

    /**
     * Mass delete the specified resources.
     */
    public function massDestroy(): JsonResponse
    {
        $indices = request()->input('indices', []);

        foreach ($indices as $index) {
            Event::dispatch('deal.delete.before', $index);

            $this->dealRepository->delete($index);

            Event::dispatch('deal.delete.after', $index);
        }

        return response()->json([
            'message' => 'Deals deleted successfully.',
        ]);
    }

    /**
     * Returns a listing of the resource for kanban view.
     */
    public function get(): JsonResponse
    {
        $pipeline = $this->pipelineRepository->getDefaultPipeline();
        $stages = $pipeline->stages;
        $data = [];

        foreach ($stages as $stage) {
            $deals = $this->dealRepository
                ->where('lead_pipeline_stage_id', $stage->id)
                ->with(['user', 'person'])
                ->get();

            $data[$stage->id] = [
                'id'         => $stage->id,
                'name'       => $stage->name,
                'deals'      => $deals,
                'deal_value' => $deals->sum('deal_value'),
            ];
        }

        return response()->json($data);
    }
}
