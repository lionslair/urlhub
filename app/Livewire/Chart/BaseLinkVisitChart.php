<?php

namespace App\Livewire\Chart;

use App\Models\Url;
use App\Models\User;
use App\Models\Visit;
use Carbon\CarbonPeriod;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

/**
 * @codeCoverageIgnore
 */
abstract class BaseLinkVisitChart extends ChartWidget
{
    protected static ?string $maxHeight = '250px';

    protected static ?string $pollingInterval = null;

    public User|Url|null $model = null;

    /** {@inheritdoc} */
    protected function getType(): string
    {
        return 'line';
    }

    /** {@inheritdoc} */
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $this->chartData(),
                    'backgroundColor' => 'rgba('.Color::Blue[400].', 0.5)',
                    'borderColor' => 'rgb('.Color::Blue[400].')',
                    'fill' => true,
                ],
                [
                    'label' => 'Visitors',
                    'data' => $this->chartData(visitor: true),
                    'backgroundColor' => 'rgba('.Color::Emerald[400].', 0.7)',
                    'borderColor' => 'rgb('.Color::Emerald[400].')',
                    'fill' => true,
                ],
            ],
            'labels' => $this->chartLabel(),
        ];
    }

    abstract public function chartData(bool $visitor = false): array;

    abstract public function chartLabel(): array;

    /**
     * Return the visits data for the given period.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Visit>
     */
    protected function getPeriodData(CarbonPeriod $period)
    {
        return Visit::query()
            ->when($this->model instanceof User, function ($query) {
                $query->whereRelation('url', 'user_id', $this->model->id);
            })
            ->when($this->model instanceof Url, function ($query) {
                $query->where('url_id', $this->model->id);
            })
            ->whereBetween('created_at', [$period->getStartDate(), $period->getEndDate()])
            ->get(['user_uid', 'created_at']);
    }
}
