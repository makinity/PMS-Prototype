<?php

namespace App\Livewire;

use Livewire\Component;

class DeptHeadIdpSearch extends Component
{
    public string $searchTerm = '';

    public array $selectedEmployee = [];

    public array $employees = [];

    public string $objective = '';
    public string $activity = '';
    public string $targetDate = '';
    public string $support = '';
    public string $notes = '';

    public function mount(): void
    {
        // {{-- DUMMY_DATA: replace --}}
        $this->employees = [
            [
                'name' => 'Juan Dela Cruz',
                'id' => 'EMP-0012',
                'department' => 'HR Management',
                'role' => 'Administrative Assistant I',
                'rating' => '4.5',
                'status' => 'Submitted',
                'progress' => '72%',
                'objective' => 'Improve data analysis skills',
                'activity' => 'Attend advanced Excel and SQL workshop',
                'targetDate' => '2025-02-15',
                'support' => 'Manager approval and PHP 15,000 training budget',
            ],
            [
                'name' => 'Maria Santos',
                'id' => 'EMP-0045',
                'department' => 'Finance',
                'role' => 'Budget Officer',
                'rating' => '4.2',
                'status' => 'Draft',
                'progress' => '35%',
                'objective' => 'Strengthen budget forecasting accuracy',
                'activity' => 'Shadow senior budget reviews and complete forecasting course',
                'targetDate' => '2025-03-30',
                'support' => 'Access to forecasting tool sandbox and coaching',
            ],
            [
                'name' => 'Ramon Reyes',
                'id' => 'EMP-0078',
                'department' => 'IT Services',
                'role' => 'Systems Analyst',
                'rating' => '4.8',
                'status' => 'Approved',
                'progress' => '100%',
                'objective' => 'Lead automation rollouts for internal tools',
                'activity' => 'Pilot workflow automation and document standards',
                'targetDate' => '2025-01-20',
                'support' => 'Time allocation and infra approvals',
            ],
        ];
    }

    public function getFilteredEmployeesProperty(): array
    {
        $term = mb_strtolower(trim($this->searchTerm));

        return array_values(array_filter($this->employees, function (array $employee) use ($term) {
            if ($term === '') {
                return true;
            }

            $haystack = mb_strtolower($employee['name'] . ' ' . $employee['id']);

            return str_contains($haystack, $term);
        }));
    }

    public function selectEmployee(string $id): void
    {
        $match = collect($this->employees)->first(fn ($employee) => $employee['id'] === $id);

        if (!$match) {
            $this->resetSelection();
            return;
        }

        $this->selectedEmployee = $match;
        $this->objective = $match['objective'] ?? '';
        $this->activity = $match['activity'] ?? '';
        $this->targetDate = $match['targetDate'] ?? '';
        $this->support = $match['support'] ?? '';
        $this->notes = '';
    }

    public function resetSelection(): void
    {
        $this->selectedEmployee = [];
        $this->objective = '';
        $this->activity = '';
        $this->targetDate = '';
        $this->support = '';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.dept-head-idp-search', [
            'filteredEmployees' => $this->filteredEmployees,
        ]);
    }
}
