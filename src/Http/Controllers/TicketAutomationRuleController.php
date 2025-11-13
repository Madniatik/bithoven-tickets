<?php

namespace Bithoven\Tickets\Http\Controllers;

use Bithoven\Tickets\DataTables\AutomationRulesDataTable;
use Bithoven\Tickets\DataTables\AutomationLogsDataTable;
use Bithoven\Tickets\Models\TicketAutomationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class TicketAutomationRuleController extends Controller
{
    public function index(AutomationRulesDataTable $dataTable)
    {
        Gate::authorize('manage-ticket-categories');

        // Stats for dashboard cards
        $stats = [
            'total' => TicketAutomationRule::count(),
            'active' => TicketAutomationRule::where('is_active', true)->count(),
            'inactive' => TicketAutomationRule::where('is_active', false)->count(),
            'total_executions' => TicketAutomationRule::sum('execution_count'),
        ];

        return $dataTable->render('tickets::automation.index', compact('stats'));
    }

    public function create()
    {
        Gate::authorize('manage-ticket-categories');

        return view('tickets::automation.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-ticket-categories');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:auto_close,auto_escalate,auto_assign,auto_response',
            'description' => 'nullable|string',
            'execution_order' => 'nullable|integer|min:0',
            'conditions' => 'required|json',
            'actions' => 'required|json',
            'config' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['name', 'type', 'description', 'execution_order']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['conditions'] = json_decode($request->conditions, true);
        $data['actions'] = json_decode($request->actions, true);
        $data['config'] = $request->filled('config') ? json_decode($request->config, true) : [];
        $data['execution_order'] = $request->filled('execution_order') ? $request->execution_order : 0;

        $rule = TicketAutomationRule::create($data);

        return redirect()->route('tickets.automation.index')
            ->with('success', 'Automation rule created successfully.');
    }

    public function edit(TicketAutomationRule $automation)
    {
        Gate::authorize('manage-ticket-categories');

        return view('tickets::automation.edit', compact('automation'));
    }

    public function update(Request $request, TicketAutomationRule $automation)
    {
        Gate::authorize('manage-ticket-categories');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:auto_close,auto_escalate,auto_assign,auto_response',
            'description' => 'nullable|string',
            'execution_order' => 'nullable|integer|min:0',
            'conditions' => 'required|json',
            'actions' => 'required|json',
            'config' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['name', 'type', 'description', 'execution_order']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['conditions'] = json_decode($request->conditions, true);
        $data['actions'] = json_decode($request->actions, true);
        $data['config'] = $request->filled('config') ? json_decode($request->config, true) : [];
        $data['execution_order'] = $request->filled('execution_order') ? $request->execution_order : 0;

        $automation->update($data);

        return redirect()->route('tickets.automation.index')
            ->with('success', 'Automation rule updated successfully.');
    }

    public function destroy(TicketAutomationRule $automation)
    {
        Gate::authorize('manage-ticket-categories');

        $automation->delete();

        return redirect()->route('tickets.automation.index')
            ->with('success', 'Automation rule deleted successfully.');
    }

    public function toggleActive(Request $request, TicketAutomationRule $automation)
    {
        Gate::authorize('manage-ticket-categories');

        $automation->update(['is_active' => !$automation->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $automation->is_active,
            'message' => $automation->is_active ? 'Rule activated.' : 'Rule deactivated.'
        ]);
    }

    public function logs(TicketAutomationRule $automation)
    {
        Gate::authorize('manage-ticket-categories');

        $dataTable = new AutomationLogsDataTable($automation->id);
        
        return $dataTable->render('tickets::automation.logs', compact('automation'));
    }
}
