<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Registration;
use App\Models\Sport;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function dashboard()
    {
        $stats = [
            ['label' => 'Users', 'value' => User::count()],
            ['label' => 'Coaches', 'value' => Coach::count()],
            ['label' => 'Sports', 'value' => Sport::count()],
            ['label' => 'Trainings', 'value' => Training::count()],
            ['label' => 'Registrations', 'value' => Registration::count()],
        ];

        $statuses = Registration::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $upcomingTrainings = Training::with('sport')
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('time')
            ->limit(20)
            ->get();

        $rows = [];

        foreach ($stats as $item) {
            $rows[] = ['Metric', $item['label'], $item['value']];
        }

        foreach ($statuses as $item) {
            $rows[] = ['Registration status', $item->status, $item->total];
        }

        foreach ($upcomingTrainings as $training) {
            $rows[] = [
                'Upcoming training',
                $training->sport->name ?? '—',
                trim(sprintf('%s %s %s', $training->date, $training->time, $training->place ?? '')),
            ];
        }

        return $this->reportView(
            'Admin dashboard report',
            'Summary report for the whole system',
            ['Section', 'Name', 'Value'],
            $rows,
            'Dashboard'
        );
    }

    public function users(Request $request)
    {
        $query = User::query()
            ->search($request->input('search'))
            ->role($request->input('role'));

        $this->applySimpleSort($query, $request, ['id', 'name', 'email', 'phone', 'role'], 'id', 'desc');

        $users = $query->get();

        return $this->reportView(
            'Users report',
            'Full filtered users list',
            ['ID', 'Name', 'Email', 'Phone', 'Role'],
            $users->map(fn (User $user) => [
                $user->id,
                $user->name,
                $user->email,
                $user->phone ?: '—',
                $user->role,
            ])->all(),
            sprintf('Total rows: %d', $users->count())
        );
    }

    public function sports(Request $request)
    {
        $query = Sport::with('coach.user')
            ->when($request->input('search'), function (Builder $query) use ($request) {
                $search = $request->input('search');
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });

        $sort = $request->input('sort', 'id');
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));

        if ($sort === 'coachName') {
            $query->leftJoin('coaches', 'sports.coach_id', '=', 'coaches.id')
                ->leftJoin('users as coach_users', 'coaches.user_id', '=', 'coach_users.id')
                ->select('sports.*')
                ->orderBy('coach_users.name', $direction)
                ->orderBy('sports.id', 'desc');
        } else {
            $this->applySimpleSort($query, $request, ['id', 'name', 'location', 'description'], 'id', 'desc');
        }

        $sports = $query->get();

        return $this->reportView(
            'Sports report',
            'Full filtered sports list',
            ['ID', 'Name', 'Location', 'Coach', 'Description'],
            $sports->map(fn (Sport $sport) => [
                $sport->id,
                $sport->name,
                $sport->location ?: '—',
                $sport->coach?->user?->name ?: '—',
                $sport->description ?: '—',
            ])->all(),
            sprintf('Total rows: %d', $sports->count())
        );
    }

    public function trainings(Request $request)
    {
        $query = Training::with('sport')
            ->when($request->input('search'), function (Builder $query) use ($request) {
                $search = $request->input('search');
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('place', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%")
                        ->orWhereHas('sport', function (Builder $sportQuery) use ($search) {
                            $sportQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        $sort = $request->input('sort', 'id');
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));

        if ($sort === 'sportName') {
            $query->join('sports', 'trainings.sport_id', '=', 'sports.id')
                ->select('trainings.*')
                ->orderBy('sports.name', $direction)
                ->orderBy('trainings.id', 'desc');
        } else {
            $this->applySimpleSort($query, $request, ['id', 'date', 'time', 'place', 'notes'], 'id', 'desc');
        }

        $trainings = $query->get();

        return $this->reportView(
            'Trainings report',
            'Full filtered trainings list',
            ['ID', 'Sport', 'Date', 'Time', 'Place', 'Notes', 'Status'],
            $trainings->map(fn (Training $training) => [
                $training->id,
                $training->sport?->name ?: '—',
                $training->date,
                $training->time,
                $training->place ?: '—',
                $training->notes ?: '—',
                $this->resolveTrainingStatus($training),
            ])->all(),
            sprintf('Total rows: %d', $trainings->count())
        );
    }

    public function coaches(Request $request)
    {
        $query = Coach::with('user')
            ->when($request->input('search'), function (Builder $query) use ($request) {
                $search = $request->input('search');
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('phone', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        $sort = $request->input('sort', 'id');
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));

        if ($sort === 'userName') {
            $query->join('users', 'coaches.user_id', '=', 'users.id')
                ->select('coaches.*')
                ->orderBy('users.name', $direction)
                ->orderBy('coaches.id', 'desc');
        } else {
            $this->applySimpleSort($query, $request, ['id', 'phone', 'specialization'], 'id', 'desc');
        }

        $coaches = $query->get();

        return $this->reportView(
            'Coaches report',
            'Full filtered coaches list',
            ['ID', 'User', 'Phone', 'Specialization'],
            $coaches->map(fn (Coach $coach) => [
                $coach->id,
                $coach->user?->name ?: '—',
                $coach->phone ?: '—',
                $coach->specialization ?: '—',
            ])->all(),
            sprintf('Total rows: %d', $coaches->count())
        );
    }

    public function registrations(Request $request)
    {
        $query = Registration::with(['user', 'training.sport'])
            ->when($request->input('search'), function (Builder $query) use ($request) {
                $search = $request->input('search');
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('training.sport', function (Builder $sportQuery) use ($search) {
                        $sportQuery->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->input('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->input('status'));
            });

        $sort = $request->input('sort', 'created_at');
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));

        if ($sort === 'userName') {
            $query->join('users', 'registrations.user_id', '=', 'users.id')
                ->select('registrations.*')
                ->orderBy('users.name', $direction)
                ->orderBy('registrations.id', 'desc');
        } elseif ($sort === 'sportName') {
            $query->join('trainings', 'registrations.training_id', '=', 'trainings.id')
                ->join('sports', 'trainings.sport_id', '=', 'sports.id')
                ->select('registrations.*')
                ->orderBy('sports.name', $direction)
                ->orderBy('registrations.id', 'desc');
        } elseif ($sort === 'trainingDate') {
            $query->join('trainings', 'registrations.training_id', '=', 'trainings.id')
                ->select('registrations.*')
                ->orderBy('trainings.date', $direction)
                ->orderBy('trainings.time', $direction)
                ->orderBy('registrations.id', 'desc');
        } else {
            $this->applySimpleSort($query, $request, ['id', 'status', 'created_at'], 'created_at', 'desc');
        }

        $registrations = $query->get();

        return $this->reportView(
            'Registrations report',
            'Full filtered registrations list',
            ['ID', 'User', 'Sport', 'Training', 'Status', 'Created at'],
            $registrations->map(fn (Registration $registration) => [
                $registration->id,
                $registration->user?->name ?: '—',
                $registration->training?->sport?->name ?: '—',
                trim(sprintf('%s %s', $registration->training?->date ?: '—', $registration->training?->time ?: '')),
                $registration->status,
                optional($registration->created_at)->format('Y-m-d H:i') ?: '—',
            ])->all(),
            sprintf('Total rows: %d', $registrations->count())
        );
    }

    private function applySimpleSort(Builder $query, Request $request, array $allowedColumns, string $defaultColumn, string $defaultDirection): void
    {
        $sort = $request->input('sort', $defaultColumn);
        $direction = $this->normalizeDirection($request->input('direction', $defaultDirection));

        if (!in_array($sort, $allowedColumns, true)) {
            $sort = $defaultColumn;
        }

        $query->orderBy($sort, $direction);
    }

    private function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : 'desc';
    }

    private function resolveTrainingStatus(Training $training): string
    {
        if ($training->is_cancelled) {
            return 'cancelled';
        }

        if ($training->is_completed) {
            return 'completed';
        }

        return $training->date > now()->toDateString() ? 'planned' : 'active';
    }

    private function reportView(string $title, string $subtitle, array $columns, array $rows, string $summary)
    {
        return response()->view('admin.report', [
            'title' => $title,
            'subtitle' => $subtitle,
            'columns' => $columns,
            'rows' => $rows,
            'summary' => $summary,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
